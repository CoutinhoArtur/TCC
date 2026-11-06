<?php

// ==========================================================
// DIRECIONADOR DE PAINEL
// ==========================================================
// Este arquivo recebe o usuário após o login
// e decide qual dashboard ele poderá acessar.
//
// RN03  - Acesso conforme o perfil
// RNF03 - Controle de acesso
// ==========================================================


// ----------------------------------------------------------
// INICIAR SESSÃO
// ----------------------------------------------------------

session_start();


// ----------------------------------------------------------
// VERIFICAR SE EXISTE USUÁRIO AUTENTICADO
// ----------------------------------------------------------
//
// Se não existir $_SESSION['usuario'],
// ninguém está logado.
//
// Nesse caso, voltamos para o login.
// ----------------------------------------------------------

if (!isset($_SESSION['usuario'])) {

    header('Location: login.php');

    exit;
}


// ----------------------------------------------------------
// RECUPERAR TIPO DO USUÁRIO
// ----------------------------------------------------------

$tipoUsuario = $_SESSION['usuario']['tipo'];


// ----------------------------------------------------------
// REDIRECIONAR CLIENTE
// ----------------------------------------------------------

if ($tipoUsuario === 'cliente') {

    header('Location: cliente/dashboard.php');

    exit;
}


// ----------------------------------------------------------
// REDIRECIONAR DESENVOLVEDOR
// ----------------------------------------------------------

if ($tipoUsuario === 'desenvolvedor') {

    header('Location: desenvolvedor/dashboard.php');

    exit;
}


// ----------------------------------------------------------
// SITUAÇÃO INVÁLIDA
// ----------------------------------------------------------
//
// Se por algum motivo o banco ou a sessão possuir
// um tipo diferente dos permitidos, encerramos a sessão.
//
// Isso impede que uma conta inválida continue navegando.
// ----------------------------------------------------------

$_SESSION = [];

session_destroy();

header('Location: login.php');

exit;