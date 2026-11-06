<?php

// ==========================================================
// DETALHES DO PROJETO - ÁREA DO DESENVOLVEDOR
// ==========================================================
//
// Este arquivo:
//
// 1. inicia a sessão;
// 2. verifica se o usuário está autenticado;
// 3. verifica se ele é desenvolvedor;
// 4. recebe o ID do projeto pela URL;
// 5. valida esse ID;
// 6. busca o projeto no banco;
// 7. carrega a View.
//
// Exemplo de URL:
//
// /desenvolvedor/projeto.php?id=2
//
// RF08  - Visualização de projetos
// RF17  - Briefing estruturado
// RNF03 - Controle de acesso
// ==========================================================


// ----------------------------------------------------------
// INICIAR SESSÃO
// ----------------------------------------------------------

session_start();


// ----------------------------------------------------------
// VERIFICAR LOGIN
// ----------------------------------------------------------

if (!isset($_SESSION['usuario'])) {

    header('Location: ../login.php');

    exit;
}


// ----------------------------------------------------------
// RECUPERAR USUÁRIO
// ----------------------------------------------------------

/**
 * @var array<string, mixed> $usuario
 */

$usuario = $_SESSION['usuario'];


// ----------------------------------------------------------
// VERIFICAR PERFIL
// ----------------------------------------------------------

$tipoUsuario = (string) (
    $usuario['tipo'] ?? ''
);


if ($tipoUsuario !== 'desenvolvedor') {

    header('Location: ../painel.php');

    exit;
}


// ==========================================================
// RECEBER ID DO PROJETO
// ==========================================================
//
// O ID chegará pela URL:
//
// projeto.php?id=2
// ==========================================================

$projetoId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);


// ----------------------------------------------------------
// VALIDAR ID
// ----------------------------------------------------------

if (
    $projetoId === false ||
    $projetoId === null ||
    $projetoId <= 0
) {

    http_response_code(400);

    exit(
        'Projeto inválido.'
    );
}


// ----------------------------------------------------------
// CARREGAR CONEXÃO COM O BANCO
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/config/database.php';


// ----------------------------------------------------------
// CARREGAR MODEL
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/models/ProjetoDetalhe.php';


// ----------------------------------------------------------
// CRIAR MODEL
// ----------------------------------------------------------

$projetoModel =
    new ProjetoDetalhe($pdo);


// ----------------------------------------------------------
// BUSCAR PROJETO
// ----------------------------------------------------------

$projeto =
    $projetoModel
        ->buscarProjetoParaDesenvolvedor(
            (int) $projetoId,
            (int) ($usuario['id'] ?? 0)
        );


// ----------------------------------------------------------
// PROJETO NÃO ENCONTRADO
// ----------------------------------------------------------
//
// Isso pode acontecer se:
//
// - o ID não existir;
// - o projeto não estiver mais aberto;
// - o projeto tiver sido contratado ou cancelado.
// ----------------------------------------------------------

if ($projeto === false) {

    http_response_code(404);

    exit(
        'Projeto não encontrado ou você não possui acesso a ele.'
    );
}


// ----------------------------------------------------------
// CARREGAR VIEW
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/views/desenvolvedor/projeto.php';