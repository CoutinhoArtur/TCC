<?php

// ==========================================================
// PUBLICAÇÃO DE PROJETO - ETAPA 4
// ==========================================================
//
// Última etapa do Briefing Guiado.
//
// Nesta etapa o cliente informa:
//
// - orçamento mínimo;
// - orçamento máximo;
// - prazo desejado.
//
// Depois mostramos um resumo completo de tudo
// que foi informado nas etapas anteriores.
//
// IMPORTANTE:
//
// Ainda NÃO gravamos nada no MySQL.
// Primeiro o cliente revisará o projeto.
//
// RF06 - Publicação de projetos
// RF17 - Briefing guiado
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
// VERIFICAR SE O RASCUNHO EXISTE
// ==========================================================
//
// Para chegar à Etapa 4,
// precisamos ter passado pelas etapas anteriores.
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
// RASCUNHO ATUAL
// ----------------------------------------------------------

/**
 * @var array<string, mixed> $rascunho
 */

$rascunho = $_SESSION['rascunho_projeto'];


// ----------------------------------------------------------
// VARIÁVEIS DA VIEW
// ----------------------------------------------------------

$mensagem = '';


// Indica se devemos mostrar a revisão final.
$modoRevisao = false;


// Dados dos campos da Etapa 4.
$dadosFormulario = [
    'orcamento_min' => '',
    'orcamento_max' => '',
    'prazo_desejado' => ''
];


// ==========================================================
// RECUPERAR DADOS JÁ PREENCHIDOS
// ==========================================================

$dadosFormulario['orcamento_min'] = (string) (
    $rascunho['orcamento_min'] ?? ''
);


$dadosFormulario['orcamento_max'] = (string) (
    $rascunho['orcamento_max'] ?? ''
);


$dadosFormulario['prazo_desejado'] = (string) (
    $rascunho['prazo_desejado'] ?? ''
);


// ==========================================================
// PROCESSAR FORMULÁRIO
// ==========================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // ------------------------------------------------------
    // RECEBER DADOS
    // ------------------------------------------------------

    $orcamentoMin = trim(
        $_POST['orcamento_min'] ?? ''
    );


    $orcamentoMax = trim(
        $_POST['orcamento_max'] ?? ''
    );


    $prazoDesejado = trim(
        $_POST['prazo_desejado'] ?? ''
    );


    // ------------------------------------------------------
    // NORMALIZAR VALORES
    // ------------------------------------------------------
    //
    // Caso algum valor chegue usando vírgula,
    // convertemos para ponto.
    //
    // Exemplo:
    //
    // 1500,50
    //
    // vira:
    //
    // 1500.50
    // ------------------------------------------------------

    $orcamentoMin = str_replace(
        ',',
        '.',
        $orcamentoMin
    );


    $orcamentoMax = str_replace(
        ',',
        '.',
        $orcamentoMax
    );


    // Mantemos os campos preenchidos caso exista erro.
    $dadosFormulario = [
        'orcamento_min' => $orcamentoMin,
        'orcamento_max' => $orcamentoMax,
        'prazo_desejado' => $prazoDesejado
    ];


    // ======================================================
    // VALIDAÇÕES
    // ======================================================


    // ------------------------------------------------------
    // CAMPOS OBRIGATÓRIOS
    // ------------------------------------------------------

    if (
        $orcamentoMin === '' ||
        $orcamentoMax === '' ||
        $prazoDesejado === ''
    ) {

        $mensagem =
            'Informe o orçamento e o prazo desejado.';
    }


    // ------------------------------------------------------
    // VALIDAR NÚMEROS
    // ------------------------------------------------------

    elseif (
        !is_numeric($orcamentoMin) ||
        !is_numeric($orcamentoMax)
    ) {

        $mensagem =
            'Informe valores válidos para o orçamento.';
    }


    // ------------------------------------------------------
    // IMPEDIR VALORES NEGATIVOS
    // ------------------------------------------------------

    elseif (
        (float) $orcamentoMin < 0 ||
        (float) $orcamentoMax < 0
    ) {

        $mensagem =
            'O orçamento não pode possuir valores negativos.';
    }


    // ------------------------------------------------------
    // ORÇAMENTO MÍNIMO NÃO PODE SER MAIOR QUE O MÁXIMO
    // ------------------------------------------------------

    elseif (
        (float) $orcamentoMin >
        (float) $orcamentoMax
    ) {

        $mensagem =
            'O orçamento mínimo não pode ser maior que o orçamento máximo.';
    }


    // ------------------------------------------------------
    // VALIDAR DATA
    // ------------------------------------------------------

    elseif (
        DateTime::createFromFormat(
            'Y-m-d',
            $prazoDesejado
        ) === false
    ) {

        $mensagem =
            'Informe uma data válida para o prazo.';
    }


    // ======================================================
    // SALVAR DADOS DA ETAPA 4
    // ======================================================

    if ($mensagem === '') {


        $_SESSION['rascunho_projeto']['orcamento_min'] =
            (float) $orcamentoMin;


        $_SESSION['rascunho_projeto']['orcamento_max'] =
            (float) $orcamentoMax;


        $_SESSION['rascunho_projeto']['prazo_desejado'] =
            $prazoDesejado;


        // Atualizamos a variável local.
        $rascunho =
            $_SESSION['rascunho_projeto'];


        // Agora a própria página passa
        // para o modo de revisão.
        $modoRevisao = true;
    }
}


// ----------------------------------------------------------
// CARREGAR VIEW
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/views/cliente/publicar-projeto-etapa4.php';