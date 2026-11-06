<?php

// ==========================================================
// INICIAR PROJETO - DESENVOLVEDOR
// ==========================================================
//
// Esta ação transforma:
//
// contratação: ativa -> em_andamento
// projeto: contratado -> em_andamento
//
// Somente o desenvolvedor contratado pode executar.
//
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
// SOMENTE DESENVOLVEDOR
// ----------------------------------------------------------

if (($usuario['tipo'] ?? '') !== 'desenvolvedor') {

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


// ==========================================================
// SOMENTE POST
// ==========================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    exit(
        'Método não permitido.'
    );
}


// ----------------------------------------------------------
// RECEBER CONTRATAÇÃO
// ----------------------------------------------------------

$contratacaoId = filter_input(
    INPUT_POST,
    'contratacao_id',
    FILTER_VALIDATE_INT
);


if (
    $contratacaoId === false ||
    $contratacaoId === null ||
    $contratacaoId <= 0
) {

    http_response_code(400);

    exit(
        'Contratação inválida.'
    );
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
    . '/../../app/models/ExecucaoProjeto.php';


$execucaoProjetoModel =
    new ExecucaoProjeto($pdo);


// ==========================================================
// INICIAR
// ==========================================================

try {

    $execucaoProjetoModel->iniciar(
        (int) $contratacaoId,
        $usuarioId
    );


    $_SESSION['mensagem_execucao'] =
        'Projeto iniciado com sucesso.';


} catch (Throwable $erro) {

    error_log(
        $erro->getMessage()
    );


    $_SESSION['erro_execucao'] =
        'Não foi possível iniciar o projeto.';
}


// ----------------------------------------------------------
// VOLTAR
// ----------------------------------------------------------

header(
    'Location: minhas-propostas.php'
);

exit;