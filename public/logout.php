<?php

// ==========================================================
// LOGOUT
// ==========================================================
//
// RF02 - Logout de usuários
//
// Responsabilidade:
// encerrar a sessão do usuário e retornar ao login.
// ==========================================================


// ----------------------------------------------------------
// INICIAR / RECUPERAR A SESSÃO
// ----------------------------------------------------------

session_start();


// ----------------------------------------------------------
// LIMPAR VARIÁVEIS DA SESSÃO
// ----------------------------------------------------------

$_SESSION = [];


// ----------------------------------------------------------
// REMOVER COOKIE DA SESSÃO
// ----------------------------------------------------------
//
// Se o PHP estiver utilizando cookies de sessão,
// removemos também o cookie armazenado no navegador.
// ----------------------------------------------------------

if (ini_get('session.use_cookies')) {

    $parametros = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $parametros['path'],
        $parametros['domain'],
        $parametros['secure'],
        $parametros['httponly']
    );
}


// ----------------------------------------------------------
// DESTRUIR A SESSÃO
// ----------------------------------------------------------

session_destroy();


// ----------------------------------------------------------
// VOLTAR PARA LOGIN
// ----------------------------------------------------------

header('Location: login.php');

exit;