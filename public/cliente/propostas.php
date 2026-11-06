<?php

// ==========================================================
// PROPOSTAS RECEBIDAS - CLIENTE
// ==========================================================

session_start();


// ----------------------------------------------------------
// LOGIN
// ----------------------------------------------------------

if (!isset($_SESSION['usuario'])) {

    header('Location: ../login.php');

    exit;
}


/**
 * @var array<string, mixed> $usuario
 */

$usuario = $_SESSION['usuario'];


// ----------------------------------------------------------
// SOMENTE CLIENTE
// ----------------------------------------------------------

if (($usuario['tipo'] ?? '') !== 'cliente') {

    header('Location: ../painel.php');

    exit;
}


$usuarioId = (int) (
    $usuario['id'] ?? 0
);


if ($usuarioId <= 0) {

    session_destroy();

    header('Location: ../login.php');

    exit;
}


// ----------------------------------------------------------
// PROJETO
// ----------------------------------------------------------

$projetoId = filter_input(
    INPUT_GET,
    'projeto',
    FILTER_VALIDATE_INT
);


if (
    $projetoId === false ||
    $projetoId === null ||
    $projetoId <= 0
) {

    http_response_code(400);

    exit('Projeto inválido.');
}


// ----------------------------------------------------------
// BANCO
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/config/database.php';


// ----------------------------------------------------------
// MODEL
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/models/PropostaCliente.php';


$propostaClienteModel =
    new PropostaCliente($pdo);


// ==========================================================
// BUSCAR PROJETO
// ==========================================================

$projeto =
    $propostaClienteModel
        ->buscarProjetoDoCliente(
            (int) $projetoId,
            $usuarioId
        );


if ($projeto === false) {

    http_response_code(404);

    exit(
        'Projeto não encontrado.'
    );
}


// ==========================================================
// BUSCAR PROPOSTAS
// ==========================================================

$propostas =
    $propostaClienteModel
        ->listarPorProjeto(
            (int) $projetoId,
            $usuarioId
        );


// ==========================================================
// MENSAGENS TEMPORÁRIAS
// ==========================================================

$mensagemSucesso =
    (string) (
        $_SESSION['mensagem_proposta']
        ?? ''
    );


$mensagemErro =
    (string) (
        $_SESSION['erro_proposta']
        ?? ''
    );


// Depois de recuperar, apagamos.
//
// Assim a mensagem aparece somente uma vez.
unset(
    $_SESSION['mensagem_proposta'],
    $_SESSION['erro_proposta']
);


// ----------------------------------------------------------
// VIEW
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/views/cliente/propostas.php';