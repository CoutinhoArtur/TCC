<?php

// ==========================================================
// PROPOSTA DEFINITIVA - ÁREA DO DESENVOLVEDOR
// ==========================================================
//
// Esta página permite transformar uma proposta:
//
// preliminar / em_negociacao
//
// em:
//
// definitiva
//
// Depois disso, o cliente poderá:
//
// - aceitar;
// - recusar.
//
// RF10 - Envio de propostas
// RF11 - Gerenciamento de propostas
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
// GARANTIR QUE É DESENVOLVEDOR
// ----------------------------------------------------------

$tipoUsuario = (string) (
    $usuario['tipo'] ?? ''
);


if ($tipoUsuario !== 'desenvolvedor') {

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
// RECEBER ID DA PROPOSTA
// ==========================================================
//
// Exemplo:
//
// proposta-definitiva.php?proposta=2
// ==========================================================

$propostaId = filter_input(
    INPUT_GET,
    'proposta',
    FILTER_VALIDATE_INT
);


if (
    $propostaId === false ||
    $propostaId === null ||
    $propostaId <= 0
) {

    http_response_code(400);

    exit(
        'Proposta inválida.'
    );
}


// ----------------------------------------------------------
// CONEXÃO COM O BANCO
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/config/database.php';


// ----------------------------------------------------------
// MODELS
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/models/Proposta.php';

require_once __DIR__
    . '/../../app/models/PropostaDesenvolvedor.php';


// ----------------------------------------------------------
// CRIAR MODELS
// ----------------------------------------------------------

$propostaModel =
    new Proposta($pdo);


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


if ($desenvolvedorId === null) {

    http_response_code(500);

    exit(
        'Não foi possível localizar o perfil do desenvolvedor.'
    );
}


// ==========================================================
// BUSCAR PROPOSTA
// ==========================================================
//
// Utilizamos também desenvolvedor_id.
//
// Assim um desenvolvedor não consegue acessar
// a proposta pertencente a outro profissional.
// ==========================================================

$proposta =
    $propostaModel
        ->buscarPorIdEDesenvolvedor(
            (int) $propostaId,
            $desenvolvedorId
        );


if ($proposta === false) {

    http_response_code(404);

    exit(
        'Proposta não encontrada.'
    );
}


// ----------------------------------------------------------
// MENSAGEM
// ----------------------------------------------------------

$mensagem = '';

$sucesso =
    isset($_GET['sucesso'])
    && $_GET['sucesso'] === '1';


// ==========================================================
// VERIFICAR SE A PROPOSTA AINDA PODE SER FINALIZADA
// ==========================================================

$statusAtual = (string) (
    $proposta['status'] ?? ''
);


$podeFinalizar = in_array(
    $statusAtual,
    [
        'preliminar',
        'em_negociacao',
        'aguardando_revisao'
    ],
    true
);


// ==========================================================
// DADOS DO FORMULÁRIO
// ==========================================================
//
// Começamos usando os dados já existentes.
//
// Assim o desenvolvedor não precisa digitar tudo novamente.
// ==========================================================

$dadosFormulario = [

    'valor' => (string) (
        $proposta['valor'] ?? ''
    ),

    'prazo_estimado' => (string) (
        $proposta['prazo_estimado'] ?? ''
    ),

    'descricao' => (string) (
        $proposta['descricao'] ?? ''
    ),

    'entregaveis' => (string) (
        $proposta['entregaveis'] ?? ''
    )
];


// ==========================================================
// PROCESSAR FORMULÁRIO
// ==========================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && $podeFinalizar
) {


    // ------------------------------------------------------
    // RECEBER VALOR
    // ------------------------------------------------------

    $valorTexto = trim(
        $_POST['valor'] ?? ''
    );


    $valorTexto = str_replace(
        ',',
        '.',
        $valorTexto
    );


    // ------------------------------------------------------
    // RECEBER PRAZO
    // ------------------------------------------------------

    $prazoTexto = trim(
        $_POST['prazo_estimado'] ?? ''
    );


    // ------------------------------------------------------
    // RECEBER DESCRIÇÃO
    // ------------------------------------------------------

    $descricao = trim(
        $_POST['descricao'] ?? ''
    );


    // ------------------------------------------------------
    // RECEBER ENTREGÁVEIS
    // ------------------------------------------------------

    $entregaveis = trim(
        $_POST['entregaveis'] ?? ''
    );


    // ------------------------------------------------------
    // MANTER DADOS CASO EXISTA ERRO
    // ------------------------------------------------------

    $dadosFormulario = [

        'valor' =>
            $valorTexto,

        'prazo_estimado' =>
            $prazoTexto,

        'descricao' =>
            $descricao,

        'entregaveis' =>
            $entregaveis
    ];


    // ======================================================
    // VALIDAÇÕES
    // ======================================================

    if (
        $valorTexto === '' ||
        $prazoTexto === '' ||
        $descricao === '' ||
        $entregaveis === ''
    ) {

        $mensagem =
            'Preencha todos os campos.';
    }


    elseif (
        !is_numeric($valorTexto) ||
        (float) $valorTexto <= 0
    ) {

        $mensagem =
            'Informe um valor válido.';
    }


    elseif (
        filter_var(
            $prazoTexto,
            FILTER_VALIDATE_INT
        ) === false ||
        (int) $prazoTexto <= 0
    ) {

        $mensagem =
            'Informe um prazo válido em dias.';
    }


    elseif (mb_strlen($descricao) < 10) {

        $mensagem =
            'Explique melhor a proposta definitiva.';
    }


    elseif (mb_strlen($entregaveis) < 10) {

        $mensagem =
            'Descreva melhor os entregáveis.';
    }


    // ======================================================
    // TRANSFORMAR EM DEFINITIVA
    // ======================================================

    if ($mensagem === '') {

        try {

            $atualizou =
                $propostaModel
                    ->tornarDefinitiva(

                        (int) $propostaId,

                        $desenvolvedorId,

                        (float) $valorTexto,

                        (int) $prazoTexto,

                        $descricao,

                        $entregaveis
                    );


            if (!$atualizou) {

                throw new RuntimeException(
                    'A proposta não pode mais ser alterada.'
                );
            }


            // ----------------------------------------------
            // POST / REDIRECT / GET
            // ----------------------------------------------
            //
            // Evita reenviar o formulário caso o usuário
            // atualize a página.
            // ----------------------------------------------

            header(
                'Location: proposta-definitiva.php?proposta='
                . (int) $propostaId
                . '&sucesso=1'
            );

            exit;


        } catch (Throwable $erro) {

            error_log(
                $erro->getMessage()
            );


            $mensagem =
                'Não foi possível finalizar a proposta.';
        }
    }
}


// ==========================================================
// RECARREGAR PROPOSTA
// ==========================================================
//
// Isso é importante após o redirecionamento,
// pois queremos mostrar o status atualizado.
// ==========================================================

$proposta =
    $propostaModel
        ->buscarPorIdEDesenvolvedor(
            (int) $propostaId,
            $desenvolvedorId
        );


$statusAtual = (string) (
    $proposta['status'] ?? ''
);


$podeFinalizar = in_array(
    $statusAtual,
    [
        'preliminar',
        'em_negociacao',
        'aguardando_revisao'
    ],
    true
);


// ----------------------------------------------------------
// VIEW
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/views/desenvolvedor/proposta-definitiva.php';