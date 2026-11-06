<?php

// ==========================================================
// DECIDIR PROPOSTA - CLIENTE
// ==========================================================
//
// Processa a decisão do cliente sobre uma
// proposta definitiva.
//
// Ações:
//
// - aceitar;
// - recusar.
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


// ==========================================================
// SOMENTE POST
// ==========================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    exit('Método não permitido.');
}


// ----------------------------------------------------------
// RECEBER DADOS
// ----------------------------------------------------------

$propostaId = filter_input(
    INPUT_POST,
    'proposta_id',
    FILTER_VALIDATE_INT
);


$acao = trim(
    $_POST['acao'] ?? ''
);


// ----------------------------------------------------------
// VALIDAR
// ----------------------------------------------------------

if (
    $propostaId === false ||
    $propostaId === null ||
    $propostaId <= 0
) {

    http_response_code(400);

    exit('Proposta inválida.');
}


if (
    !in_array(
        $acao,
        ['aceitar', 'recusar'],
        true
    )
) {

    http_response_code(400);

    exit('Ação inválida.');
}


// ----------------------------------------------------------
// BANCO
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/config/database.php';


// ----------------------------------------------------------
// MODELS
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/models/Contratacao.php';

require_once __DIR__
    . '/../../app/models/PropostaCliente.php';


$contratacaoModel =
    new Contratacao($pdo);


$propostaClienteModel =
    new PropostaCliente($pdo);


// ==========================================================
// CONFIRMAR QUE A PROPOSTA É DO CLIENTE
// ==========================================================
//
// Isso também serve para descobrir o projeto.
//
// Nunca confiamos somente em um ID recebido
// pelo formulário.
// ==========================================================

$proposta =
    $propostaClienteModel
        ->buscarPropostaDoCliente(
            (int) $propostaId,
            $usuarioId
        );


if ($proposta === false) {

    http_response_code(404);

    exit(
        'Proposta não encontrada.'
    );
}


$projetoId = (int) (
    $proposta['projeto_id'] ?? 0
);


if ($projetoId <= 0) {

    http_response_code(500);

    exit(
        'Não foi possível identificar o projeto.'
    );
}


// ==========================================================
// PROCESSAR
// ==========================================================

try {


    // ------------------------------------------------------
    // ACEITAR
    // ------------------------------------------------------

    if ($acao === 'aceitar') {

        $contratacaoId =
            $contratacaoModel
                ->aceitarProposta(
                    (int) $propostaId,
                    $usuarioId
                );


        $_SESSION['mensagem_proposta'] =
            'Proposta aceita com sucesso. A contratação foi criada.';


        $_SESSION['contratacao_criada_id'] =
            $contratacaoId;
    }


    // ------------------------------------------------------
    // RECUSAR
    // ------------------------------------------------------

    else {

        $recusou =
            $contratacaoModel
                ->recusarProposta(
                    (int) $propostaId,
                    $usuarioId
                );


        if (!$recusou) {

            throw new RuntimeException(
                'A proposta não pode ser recusada.'
            );
        }


        $_SESSION['mensagem_proposta'] =
            'Proposta recusada.';
    }


} catch (Throwable $erro) {

    // Erro técnico fica no servidor.
    error_log(
        $erro->getMessage()
    );


    $_SESSION['erro_proposta'] =
        'Não foi possível concluir a operação.';
}


// ==========================================================
// VOLTAR AO PROJETO
// ==========================================================

header(
    'Location: propostas.php?projeto='
    . $projetoId
);

exit;