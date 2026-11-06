<?php

// ==========================================================
// ENVIAR PROPOSTA PRELIMINAR
// ==========================================================
//
// Este arquivo controla o envio da primeira proposta
// do desenvolvedor para um projeto.
//
// Fluxo:
//
// desenvolvedor vê projeto
//          ↓
// enviar-proposta.php
//          ↓
// valida dados
//          ↓
// Proposta.php
//          ↓
// tabela propostas
//
// Após uma proposta preliminar válida,
// o chat fica disponível para a negociação.
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


// ==========================================================
// RECEBER ID DO PROJETO
// ==========================================================
//
// Exemplo:
//
// enviar-proposta.php?id=2
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

    exit('Projeto inválido.');
}


// ----------------------------------------------------------
// CARREGAR CONEXÃO
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/config/database.php';


// ----------------------------------------------------------
// CARREGAR MODELS
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/models/DashboardDesenvolvedor.php';

require_once __DIR__
    . '/../../app/models/ProjetoDetalhe.php';

require_once __DIR__
    . '/../../app/models/Proposta.php';


// ----------------------------------------------------------
// CRIAR MODELS
// ----------------------------------------------------------

$dashboardModel =
    new DashboardDesenvolvedor($pdo);


$projetoModel =
    new ProjetoDetalhe($pdo);


$propostaModel =
    new Proposta($pdo);


// ==========================================================
// DESCOBRIR desenvolvedores.id
// ==========================================================
//
// A sessão possui:
//
// usuarios.id
//
// Mas a tabela propostas utiliza:
//
// desenvolvedores.id
// ==========================================================

$usuarioId = (int) (
    $usuario['id'] ?? 0
);


$desenvolvedorId =
    $dashboardModel
        ->buscarDesenvolvedorIdPorUsuario(
            $usuarioId
        );


// ----------------------------------------------------------
// VALIDAR PERFIL PROFISSIONAL
// ----------------------------------------------------------

if ($desenvolvedorId === null) {

    http_response_code(500);

    exit(
        'Não foi possível localizar o perfil do desenvolvedor.'
    );
}


// ==========================================================
// BUSCAR PROJETO
// ==========================================================
//
// ProjetoDetalhe só retorna projetos disponíveis,
// ou seja, com status = aberto.
// ==========================================================

$projeto =
    $projetoModel
        ->buscarProjetoDisponivel(
            (int) $projetoId
        );


if ($projeto === false) {

    http_response_code(404);

    exit(
        'Projeto não encontrado ou indisponível.'
    );
}


// ==========================================================
// VERIFICAR SE JÁ EXISTE PROPOSTA
// ==========================================================

$propostaExistente =
    $propostaModel
        ->buscarPorProjetoEDesenvolvedor(
            (int) $projetoId,
            $desenvolvedorId
        );


// ----------------------------------------------------------
// VARIÁVEIS DA VIEW
// ----------------------------------------------------------

$mensagem = '';

$sucesso = false;


$dadosFormulario = [
    'valor' => '',
    'prazo_estimado' => '',
    'descricao' => '',
    'entregaveis' => ''
];


// ==========================================================
// PROCESSAR FORMULÁRIO
// ==========================================================
//
// Só permitimos envio se ainda não existir uma proposta.
// ==========================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $propostaExistente === false
) {


    // ------------------------------------------------------
    // RECEBER VALOR
    // ------------------------------------------------------

    $valorTexto = trim(
        $_POST['valor'] ?? ''
    );


    // Caso seja digitado:
    //
    // 1800,50
    //
    // transformamos em:
    //
    // 1800.50
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
    // MANTER DADOS NO FORMULÁRIO EM CASO DE ERRO
    // ------------------------------------------------------

    $dadosFormulario = [
        'valor' => $valorTexto,
        'prazo_estimado' => $prazoTexto,
        'descricao' => $descricao,
        'entregaveis' => $entregaveis
    ];


    // ======================================================
    // VALIDAÇÕES
    // ======================================================


    // ------------------------------------------------------
    // CAMPOS OBRIGATÓRIOS
    // ------------------------------------------------------

    if (
        $valorTexto === '' ||
        $prazoTexto === '' ||
        $descricao === '' ||
        $entregaveis === ''
    ) {

        $mensagem =
            'Preencha todos os campos da proposta.';
    }


    // ------------------------------------------------------
    // VALOR
    // ------------------------------------------------------

    elseif (
        !is_numeric($valorTexto) ||
        (float) $valorTexto <= 0
    ) {

        $mensagem =
            'Informe um valor válido para a proposta.';
    }


    // ------------------------------------------------------
    // PRAZO
    // ------------------------------------------------------

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


    // ------------------------------------------------------
    // DESCRIÇÃO MUITO CURTA
    // ------------------------------------------------------

    elseif (mb_strlen($descricao) < 10) {

        $mensagem =
            'Explique melhor sua proposta.';
    }


    // ------------------------------------------------------
    // ENTREGÁVEIS MUITO CURTOS
    // ------------------------------------------------------

    elseif (mb_strlen($entregaveis) < 10) {

        $mensagem =
            'Descreva melhor o que será entregue.';
    }


    // ======================================================
    // CRIAR PROPOSTA
    // ======================================================

    if ($mensagem === '') {

        try {

            // ----------------------------------------------
            // VERSÃO DO BRIEFING
            // ----------------------------------------------
            //
            // Não recebemos esse ID diretamente do formulário.
            //
            // Pegamos da consulta do projeto para impedir que
            // alguém altere manualmente o HTML.
            // ----------------------------------------------

            $briefingVersaoId = (int) (
                $projeto['briefing_versao_id'] ?? 0
            );


            if ($briefingVersaoId <= 0) {

                throw new RuntimeException(
                    'Versão do briefing não encontrada.'
                );
            }


            // ----------------------------------------------
            // GRAVAR PROPOSTA
            // ----------------------------------------------

            $propostaId =
                $propostaModel
                    ->criarPreliminar(

                        (int) $projetoId,

                        $desenvolvedorId,

                        $briefingVersaoId,

                        (float) $valorTexto,

                        (int) $prazoTexto,

                        $descricao,

                        $entregaveis
                    );


            // ----------------------------------------------
            // MARCAR SUCESSO
            // ----------------------------------------------

            $sucesso = true;


            $mensagem =
                'Proposta preliminar enviada com sucesso!';


            // ----------------------------------------------
            // RECUPERAR A PROPOSTA CRIADA
            // ----------------------------------------------

            $propostaExistente =
                $propostaModel
                    ->buscarPorProjetoEDesenvolvedor(

                        (int) $projetoId,

                        $desenvolvedorId
                    );


            // Limpamos o formulário após o sucesso.
            $dadosFormulario = [
                'valor' => '',
                'prazo_estimado' => '',
                'descricao' => '',
                'entregaveis' => ''
            ];


        } catch (Throwable $erro) {

            // O erro técnico aparece no terminal/log.
            error_log(
                $erro->getMessage()
            );


            $mensagem =
                'Não foi possível enviar a proposta.';
        }
    }
}


// ----------------------------------------------------------
// CARREGAR VIEW
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/views/desenvolvedor/enviar-proposta.php';