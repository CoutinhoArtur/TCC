<?php

// ==========================================================
// MEUS PROJETOS - ÁREA DO CLIENTE
// ==========================================================
//
// Esta página controla a listagem dos projetos publicados
// pelo cliente autenticado.
//
// Responsabilidades:
//
// 1. iniciar a sessão;
// 2. verificar login;
// 3. garantir que o usuário é cliente;
// 4. localizar clientes.id;
// 5. buscar todos os projetos desse cliente;
// 6. carregar a View.
//
// RF07  - Gerenciamento de projetos
// RF11  - Gerenciamento de propostas
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


if ($tipoUsuario !== 'cliente') {

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
    . '/../../app/models/ProjetoCliente.php';


// ----------------------------------------------------------
// CRIAR MODEL
// ----------------------------------------------------------

$projetoClienteModel =
    new ProjetoCliente($pdo);


// ==========================================================
// DESCOBRIR clientes.id
// ==========================================================
//
// A sessão guarda:
//
// usuarios.id
//
// Projetos utiliza:
//
// clientes.id
// ==========================================================

$clienteId =
    $projetoClienteModel
        ->buscarClienteIdPorUsuario(
            $usuarioId
        );


// ----------------------------------------------------------
// VALIDAR PERFIL
// ----------------------------------------------------------

if ($clienteId === null) {

    http_response_code(500);

    exit(
        'Não foi possível localizar o perfil do cliente.'
    );
}


// ==========================================================
// BUSCAR TODOS OS PROJETOS
// ==========================================================

$projetos =
    $projetoClienteModel
        ->listarTodos(
            $clienteId
        );


// ----------------------------------------------------------
// CARREGAR VIEW
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/views/cliente/meus-projetos.php';