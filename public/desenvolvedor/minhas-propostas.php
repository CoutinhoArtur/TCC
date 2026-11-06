<?php

// ==========================================================
// MINHAS PROPOSTAS - ÁREA DO DESENVOLVEDOR
// ==========================================================
//
// Esta página controla a listagem das propostas enviadas
// pelo desenvolvedor autenticado.
//
// Responsabilidades:
//
// 1. iniciar a sessão;
// 2. verificar login;
// 3. garantir que o usuário é desenvolvedor;
// 4. localizar desenvolvedores.id;
// 5. buscar todas as propostas enviadas;
// 6. carregar a View.
//
// RF10  - Envio de propostas
// RF11  - Gerenciamento de propostas
// RF13  - Comunicação
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


// ----------------------------------------------------------
// ID DA CONTA
// ----------------------------------------------------------

$usuarioId = (int) (
    $usuario['id'] ?? 0
);


if ($usuarioId <= 0) {

    session_destroy();

    header('Location: ../login.php');

    exit;
}


// ----------------------------------------------------------
// CARREGAR CONEXÃO
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/config/database.php';


// ----------------------------------------------------------
// CARREGAR MODEL
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/models/PropostaDesenvolvedor.php';


// ----------------------------------------------------------
// CRIAR MODEL
// ----------------------------------------------------------

$propostaDesenvolvedorModel =
    new PropostaDesenvolvedor($pdo);


// ==========================================================
// DESCOBRIR desenvolvedores.id
// ==========================================================

$desenvolvedorId =
    $propostaDesenvolvedorModel
        ->buscarDesenvolvedorIdPorUsuario(
            $usuarioId
        );


// ----------------------------------------------------------
// VALIDAR PERFIL
// ----------------------------------------------------------

if ($desenvolvedorId === null) {

    http_response_code(500);

    exit(
        'Não foi possível localizar o perfil do desenvolvedor.'
    );
}


// ==========================================================
// BUSCAR TODAS AS PROPOSTAS
// ==========================================================

$propostas =
    $propostaDesenvolvedorModel
        ->listarTodas(
            $desenvolvedorId
        );



        // ==========================================================
// MENSAGENS TEMPORÁRIAS
// ==========================================================

$mensagemExecucao =
    (string) (
        $_SESSION['mensagem_execucao']
        ?? ''
    );


$erroExecucao =
    (string) (
        $_SESSION['erro_execucao']
        ?? ''
    );


unset(
    $_SESSION['mensagem_execucao'],
    $_SESSION['erro_execucao']
);

// ----------------------------------------------------------
// CARREGAR VIEW
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/views/desenvolvedor/minhas-propostas.php';