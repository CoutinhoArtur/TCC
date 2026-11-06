<?php

// ==========================================================
// PÁGINA PÚBLICA: LOGIN
// ==========================================================
//
// RF02 - Login de usuários
//
// Responsabilidades:
//
// - iniciar sessão;
// - receber formulário;
// - chamar AuthController;
// - armazenar usuário autenticado na sessão;
// - redirecionar para o painel.
// ==========================================================


// ----------------------------------------------------------
// INICIAR SESSÃO PHP
// ----------------------------------------------------------
//
// A sessão permitirá que o sistema lembre quem está
// autenticado durante a navegação.
// ----------------------------------------------------------

session_start();


// ----------------------------------------------------------
// CARREGAR DEPENDÊNCIAS
// ----------------------------------------------------------

require_once __DIR__ . '/../app/config/database.php';

require_once __DIR__ . '/../app/controllers/AuthController.php';


// ----------------------------------------------------------
// CRIAR CONTROLLER
// ----------------------------------------------------------

$authController = new AuthController($pdo);


// ----------------------------------------------------------
// VARIÁVEIS DA VIEW
// ----------------------------------------------------------

$mensagem = '';

$dadosFormulario = [];


// ==========================================================
// PROCESSAR LOGIN
// ==========================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Guardamos apenas o e-mail para preencher novamente
    // caso exista algum erro.
    $dadosFormulario = [
        'email' => $_POST['email'] ?? ''
    ];


    // Controller verifica as credenciais.
    $resultado = $authController->login($_POST);


    // ------------------------------------------------------
    // LOGIN CORRETO
    // ------------------------------------------------------

    if ($resultado['sucesso']) {

        // --------------------------------------------------
        // REGENERAR ID DA SESSÃO
        // --------------------------------------------------
        // Medida importante após autenticação.
        // Ajuda a reduzir riscos de session fixation.
        // --------------------------------------------------

        session_regenerate_id(true);


        // --------------------------------------------------
        // SALVAR USUÁRIO NA SESSÃO
        // --------------------------------------------------

        $_SESSION['usuario'] = $resultado['usuario'];


        // --------------------------------------------------
        // REDIRECIONAR PARA PAINEL
        // --------------------------------------------------

        header('Location: painel.php');

        exit;
    }


    // ------------------------------------------------------
    // LOGIN INVÁLIDO
    // ------------------------------------------------------

    $mensagem = $resultado['mensagem'];
}


// ----------------------------------------------------------
// CARREGAR VIEW
// ----------------------------------------------------------

require_once __DIR__ . '/../app/views/auth/login.php';