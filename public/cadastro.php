<?php

// ==========================================================
// PÁGINA PÚBLICA: CADASTRO
// ==========================================================
// Este arquivo recebe a requisição do navegador.
//
// Fluxo:
//
// Navegador
//    ↓
// public/cadastro.php
//    ↓
// AuthController.php
//    ↓
// Usuario.php
//    ↓
// Banco de dados
//
// RF01 - Cadastro de usuários
// ==========================================================


// ----------------------------------------------------------
// CARREGAR A CONEXÃO COM O BANCO
// ----------------------------------------------------------

require_once __DIR__ . '/../app/config/database.php';


// ----------------------------------------------------------
// CARREGAR O CONTROLLER DE AUTENTICAÇÃO
// ----------------------------------------------------------

require_once __DIR__ . '/../app/controllers/AuthController.php';


// ----------------------------------------------------------
// CRIAR O CONTROLLER
// ----------------------------------------------------------
// Entregamos a conexão PDO para o controller.
// ----------------------------------------------------------

$authController = new AuthController($pdo);


// ----------------------------------------------------------
// VARIÁVEIS QUE SERÃO UTILIZADAS PELA VIEW
// ----------------------------------------------------------

$mensagem = '';

$sucesso = false;

$dadosFormulario = [];


// ==========================================================
// VERIFICAR SE O FORMULÁRIO FOI ENVIADO
// ==========================================================
//
// GET:
// usuário apenas abriu a página.
//
// POST:
// usuário clicou no botão "Criar conta".
// ==========================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ------------------------------------------------------
    // GUARDAR DADOS DO FORMULÁRIO
    // ------------------------------------------------------
    // Se ocorrer algum erro, nome, e-mail e tipo continuam
    // preenchidos para o usuário não precisar digitar tudo
    // novamente.
    //
    // As senhas não são armazenadas novamente por segurança.
    // ------------------------------------------------------

    $dadosFormulario = [
        'nome' => $_POST['nome'] ?? '',
        'email' => $_POST['email'] ?? '',
        'tipo' => $_POST['tipo'] ?? ''
    ];


    // ------------------------------------------------------
    // CHAMAR O CONTROLLER
    // ------------------------------------------------------
    // O Controller será responsável por validar os dados
    // e solicitar o cadastro ao Model Usuario.
    // ------------------------------------------------------

    $resultado = $authController->cadastrar($_POST);


    // ------------------------------------------------------
    // RECEBER O RESULTADO DO CONTROLLER
    // ------------------------------------------------------

    $sucesso = $resultado['sucesso'];

    $mensagem = $resultado['mensagem'];


    // ------------------------------------------------------
    // LIMPAR O FORMULÁRIO APÓS SUCESSO
    // ------------------------------------------------------

    if ($sucesso) {

        $dadosFormulario = [];
    }
}


// ----------------------------------------------------------
// CARREGAR A VIEW
// ----------------------------------------------------------
//
// Depois de preparar as informações,
// carregamos a interface.
//
// A View terá acesso às variáveis:
//
// $mensagem
// $sucesso
// $dadosFormulario
// ----------------------------------------------------------

require_once __DIR__ . '/../app/views/auth/cadastro.php';