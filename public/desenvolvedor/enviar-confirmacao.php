<?php

// ==========================================================
// ENVIAR PROJETO PARA CONFIRMAÇÃO
// ==========================================================
//
// Esta ação é utilizada pelo desenvolvedor quando
// considera o trabalho concluído.
//
// Alterações:
//
// contratacao:
// em_andamento -> aguardando_confirmacao
//
// projeto:
// em_andamento -> aguardando_confirmacao
//
// ==========================================================

session_start();


// ----------------------------------------------------------
// VERIFICAR LOGIN
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


// ----------------------------------------------------------
// ID DO USUÁRIO
// ----------------------------------------------------------

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


// ----------------------------------------------------------
// VALIDAR
// ----------------------------------------------------------

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
// CONEXÃO
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
// ENVIAR PARA CONFIRMAÇÃO
// ==========================================================

try {

    $execucaoProjetoModel
        ->enviarParaConfirmacao(
            (int) $contratacaoId,
            $usuarioId
        );


    $_SESSION['mensagem_execucao'] =
        'Projeto enviado para confirmação do cliente.';


} catch (Throwable $erro) {

    // Erro técnico fica registrado no servidor.
    error_log(
        $erro->getMessage()
    );


    $_SESSION['erro_execucao'] =
        'Não foi possível enviar o projeto para confirmação.';
}


// ----------------------------------------------------------
// VOLTAR
// ----------------------------------------------------------

header(
    'Location: minhas-propostas.php'
);

exit;