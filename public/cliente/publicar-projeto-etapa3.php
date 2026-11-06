<?php

// ==========================================================
// PUBLICAÇÃO DE PROJETO - ETAPA 3
// ==========================================================
//
// Nesta etapa coletamos informações sobre:
//
// - regras importantes do projeto;
// - aparência desejada;
// - referências visuais;
// - observações adicionais.
//
// Os dados continuam armazenados temporariamente
// na sessão.
//
// Ainda NÃO gravamos nada no MySQL.
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
// VERIFICAR SE AS ETAPAS ANTERIORES EXISTEM
// ==========================================================
//
// Para chegar aqui precisamos ter pelo menos:
//
// - título;
// - público-alvo;
// - funcionalidades.
//
// Se não existir, voltamos para o início.
// ==========================================================

if (
    !isset($_SESSION['rascunho_projeto']) ||
    empty($_SESSION['rascunho_projeto']['titulo']) ||
    empty($_SESSION['rascunho_projeto']['publico_alvo']) ||
    empty($_SESSION['rascunho_projeto']['funcionalidades'])
) {

    header('Location: publicar-projeto.php');

    exit;
}


// ----------------------------------------------------------
// VARIÁVEIS DA VIEW
// ----------------------------------------------------------

$mensagem = '';


$dadosFormulario = [
    'condicoes_regras' => '',
    'identidade_visual' => '',
    'referencias_visuais' => '',
    'observacoes' => ''
];


// ==========================================================
// RECUPERAR RASCUNHO EXISTENTE
// ==========================================================

$rascunho = $_SESSION['rascunho_projeto'];


$dadosFormulario['condicoes_regras'] = (string) (
    $rascunho['condicoes_regras'] ?? ''
);


$dadosFormulario['identidade_visual'] = (string) (
    $rascunho['identidade_visual'] ?? ''
);


$dadosFormulario['referencias_visuais'] = (string) (
    $rascunho['referencias_visuais'] ?? ''
);


$dadosFormulario['observacoes'] = (string) (
    $rascunho['observacoes'] ?? ''
);


// ==========================================================
// PROCESSAR FORMULÁRIO
// ==========================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // ------------------------------------------------------
    // RECEBER DADOS
    // ------------------------------------------------------

    $condicoesRegras = trim(
        $_POST['condicoes_regras'] ?? ''
    );


    $identidadeVisual = trim(
        $_POST['identidade_visual'] ?? ''
    );


    $referenciasVisuais = trim(
        $_POST['referencias_visuais'] ?? ''
    );


    $observacoes = trim(
        $_POST['observacoes'] ?? ''
    );


    // Mantemos os dados caso exista erro.
    $dadosFormulario = [
        'condicoes_regras' => $condicoesRegras,
        'identidade_visual' => $identidadeVisual,
        'referencias_visuais' => $referenciasVisuais,
        'observacoes' => $observacoes
    ];


    // ======================================================
    // VALIDAÇÕES
    // ======================================================
    //
    // Nesta etapa os campos são opcionais.
    //
    // Mesmo assim, colocamos limite de tamanho
    // para evitar textos exageradamente grandes.
    // ======================================================

    if (mb_strlen($condicoesRegras) > 3000) {

        $mensagem =
            'O campo de regras possui informações demais.';
    }

    elseif (mb_strlen($identidadeVisual) > 2000) {

        $mensagem =
            'A descrição da aparência está muito longa.';
    }

    elseif (mb_strlen($referenciasVisuais) > 2000) {

        $mensagem =
            'O campo de referências está muito longo.';
    }

    elseif (mb_strlen($observacoes) > 3000) {

        $mensagem =
            'O campo de observações está muito longo.';
    }


    // ======================================================
    // SALVAR NA SESSÃO
    // ======================================================

    if ($mensagem === '') {


        $_SESSION['rascunho_projeto']['condicoes_regras'] =
            $condicoesRegras;


        $_SESSION['rascunho_projeto']['identidade_visual'] =
            $identidadeVisual;


        $_SESSION['rascunho_projeto']['referencias_visuais'] =
            $referenciasVisuais;


        $_SESSION['rascunho_projeto']['observacoes'] =
            $observacoes;


        // --------------------------------------------------
        // IR PARA ETAPA 4
        // --------------------------------------------------

        header(
            'Location: publicar-projeto-etapa4.php'
        );

        exit;
    }
}


// ----------------------------------------------------------
// CARREGAR VIEW
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/views/cliente/publicar-projeto-etapa3.php';