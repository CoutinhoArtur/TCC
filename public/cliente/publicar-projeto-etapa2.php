<?php

// ==========================================================
// PUBLICAÇÃO DE PROJETO - ETAPA 2
// ==========================================================
//
// Esta etapa coleta informações sobre:
//
// - quem utilizará o projeto;
// - o que essas pessoas precisam conseguir fazer.
//
// Utilizamos perguntas simples para que o cliente
// não precise conhecer termos como:
//
// "requisito funcional"
// "ator do sistema"
// "caso de uso"
//
// Os dados continuam armazenados temporariamente
// na sessão.
//
// RF06 - Publicação de projetos
// RF17 - Briefing guiado
// RN04 - Somente clientes podem publicar projetos
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


// ==========================================================
// VERIFICAR SE A ETAPA 1 FOI PREENCHIDA
// ==========================================================
//
// Não faz sentido acessar diretamente a Etapa 2
// sem possuir um rascunho iniciado.
// ==========================================================

if (
    !isset($_SESSION['rascunho_projeto']) ||
    empty($_SESSION['rascunho_projeto']['titulo'])
) {

    header('Location: publicar-projeto.php');

    exit;
}


// ----------------------------------------------------------
// VARIÁVEIS DA VIEW
// ----------------------------------------------------------

$mensagem = '';


$dadosFormulario = [
    'publico_alvo' => '',
    'funcionalidades' => ''
];


// ==========================================================
// RECUPERAR DADOS JÁ PREENCHIDOS
// ==========================================================
//
// Se o cliente voltar da Etapa 3,
// suas respostas continuam disponíveis.
// ==========================================================

$rascunho = $_SESSION['rascunho_projeto'];


$dadosFormulario['publico_alvo'] = (string) (
    $rascunho['publico_alvo'] ?? ''
);


$dadosFormulario['funcionalidades'] = (string) (
    $rascunho['funcionalidades'] ?? ''
);


// ==========================================================
// PROCESSAR FORMULÁRIO
// ==========================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // ------------------------------------------------------
    // RECEBER DADOS
    // ------------------------------------------------------

    $publicoAlvo = trim(
        $_POST['publico_alvo'] ?? ''
    );


    $funcionalidades = trim(
        $_POST['funcionalidades'] ?? ''
    );


    // Mantemos os valores no formulário caso exista erro.
    $dadosFormulario = [
        'publico_alvo' => $publicoAlvo,
        'funcionalidades' => $funcionalidades
    ];


    // ======================================================
    // VALIDAÇÕES
    // ======================================================


    // ------------------------------------------------------
    // CAMPOS OBRIGATÓRIOS
    // ------------------------------------------------------

    if (
        $publicoAlvo === '' ||
        $funcionalidades === ''
    ) {

        $mensagem =
            'Responda as duas perguntas antes de continuar.';
    }


    // ------------------------------------------------------
    // VALIDAÇÃO MÍNIMA DO PÚBLICO
    // ------------------------------------------------------
    //
    // Evita respostas extremamente curtas como:
    //
    // "eu"
    //
    // que não ajudariam na definição do projeto.
    // ------------------------------------------------------

    elseif (mb_strlen($publicoAlvo) < 5) {

        $mensagem =
            'Explique um pouco melhor quem utilizará o projeto.';
    }


    // ------------------------------------------------------
    // VALIDAÇÃO DAS FUNCIONALIDADES
    // ------------------------------------------------------

    elseif (mb_strlen($funcionalidades) < 10) {

        $mensagem =
            'Explique um pouco melhor o que essas pessoas precisam conseguir fazer.';
    }


    // ======================================================
    // SALVAR NA SESSÃO
    // ======================================================

    if ($mensagem === '') {


        // --------------------------------------------------
        // PÚBLICO-ALVO
        // --------------------------------------------------

        $_SESSION['rascunho_projeto']['publico_alvo'] =
            $publicoAlvo;


        // --------------------------------------------------
        // FUNCIONALIDADES
        // --------------------------------------------------

        $_SESSION['rascunho_projeto']['funcionalidades'] =
            $funcionalidades;


        // --------------------------------------------------
        // IR PARA ETAPA 3
        // --------------------------------------------------

        header(
            'Location: publicar-projeto-etapa3.php'
        );

        exit;
    }
}


// ----------------------------------------------------------
// CARREGAR VIEW
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/views/cliente/publicar-projeto-etapa2.php';