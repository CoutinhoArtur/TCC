<?php

// ==========================================================
// PUBLICAÇÃO DE PROJETO - ETAPA 1
// ==========================================================
//
// Responsabilidades:
//
// 1. verificar se o usuário está autenticado;
// 2. garantir que o usuário é cliente;
// 3. receber os dados da Etapa 1;
// 4. validar os dados;
// 5. guardar o rascunho na sessão;
// 6. redirecionar para a Etapa 2.
//
// IMPORTANTE:
// Nada é gravado no MySQL ainda.
//
// RF06 - Publicação de projetos
// RF17 - Briefing guiado
// RN04 - Somente clientes podem publicar projetos
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
// RECUPERAR USUÁRIO DA SESSÃO
// ----------------------------------------------------------

/**
 * @var array<string, mixed> $usuario
 */

$usuario = $_SESSION['usuario'];


// ----------------------------------------------------------
// VERIFICAR TIPO DE USUÁRIO
// ----------------------------------------------------------

$tipoUsuario = (string) (
    $usuario['tipo'] ?? ''
);


// Apenas clientes podem publicar projetos.
if ($tipoUsuario !== 'cliente') {

    header('Location: ../painel.php');

    exit;
}


// ==========================================================
// VARIÁVEIS UTILIZADAS PELA VIEW
// ==========================================================

// Mensagem de erro.
$mensagem = '';


// Dados exibidos no formulário.
$dadosFormulario = [
    'titulo' => '',
    'categoria' => '',
    'descricao' => ''
];


// ==========================================================
// RECUPERAR RASCUNHO EXISTENTE
// ==========================================================
//
// Se o cliente voltar da Etapa 2,
// os dados da Etapa 1 continuarão preenchidos.
// ==========================================================

if (isset($_SESSION['rascunho_projeto'])) {

    /**
     * @var array<string, mixed> $rascunho
     */

    $rascunho = $_SESSION['rascunho_projeto'];


    $dadosFormulario['titulo'] = (string) (
        $rascunho['titulo'] ?? ''
    );


    $dadosFormulario['categoria'] = (string) (
        $rascunho['categoria'] ?? ''
    );


    $dadosFormulario['descricao'] = (string) (
        $rascunho['descricao_resumida'] ?? ''
    );
}


// ==========================================================
// PROCESSAR O FORMULÁRIO
// ==========================================================
//
// Este bloco só executa quando o formulário é enviado
// através do método POST.
// ==========================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // ------------------------------------------------------
    // RECEBER DADOS
    // ------------------------------------------------------

    $titulo = trim(
        $_POST['titulo'] ?? ''
    );


    $categoria = trim(
        $_POST['categoria'] ?? ''
    );


    $descricao = trim(
        $_POST['descricao'] ?? ''
    );


    // ------------------------------------------------------
    // MANTER OS DADOS PREENCHIDOS
    // ------------------------------------------------------
    //
    // Caso ocorra algum erro de validação,
    // o cliente não precisará preencher tudo novamente.
    // ------------------------------------------------------

    $dadosFormulario = [
        'titulo' => $titulo,
        'categoria' => $categoria,
        'descricao' => $descricao
    ];


    // ======================================================
    // VALIDAÇÕES
    // ======================================================


    // ------------------------------------------------------
    // CAMPOS OBRIGATÓRIOS
    // ------------------------------------------------------

    if (
        $titulo === '' ||
        $categoria === '' ||
        $descricao === ''
    ) {

        $mensagem =
            'Preencha todos os campos antes de continuar.';
    }


    // ------------------------------------------------------
    // TAMANHO DO TÍTULO
    // ------------------------------------------------------

    elseif (mb_strlen($titulo) > 150) {

        $mensagem =
            'O título do projeto é muito longo.';
    }


    // ------------------------------------------------------
    // VALIDAR CATEGORIA
    // ------------------------------------------------------

    else {

        $categoriasPermitidas = [
            'site',
            'sistema_web',
            'aplicativo_mobile',
            'sistema_desktop',
            'outro'
        ];


        if (
            !in_array(
                $categoria,
                $categoriasPermitidas,
                true
            )
        ) {

            $mensagem =
                'Selecione um tipo de projeto válido.';
        }
    }


    // ======================================================
    // SALVAR RASCUNHO
    // ======================================================

    if ($mensagem === '') {


        // Guarda o título.
        $_SESSION['rascunho_projeto']['titulo'] =
            $titulo;


        // Guarda a categoria.
        $_SESSION['rascunho_projeto']['categoria'] =
            $categoria;


        // Guarda a descrição resumida.
        $_SESSION['rascunho_projeto']['descricao_resumida'] =
            $descricao;


        // --------------------------------------------------
        // REDIRECIONAR PARA ETAPA 2
        // --------------------------------------------------

        header(
            'Location: publicar-projeto-etapa2.php'
        );

        exit;
    }
}


// ----------------------------------------------------------
// CARREGAR A VIEW DA ETAPA 1
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/views/cliente/publicar-projeto.php';