<?php

// ==========================================================
// DASHBOARD PÚBLICO DO CLIENTE
// ==========================================================
//
// Este arquivo:
//
// 1. inicia a sessão;
// 2. verifica se existe usuário autenticado;
// 3. verifica se ele é cliente;
// 4. conecta com o banco;
// 5. localiza clientes.id;
// 6. busca informações reais do dashboard;
// 7. carrega a View.
//
// RN03  - Acesso conforme o perfil
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
// RECUPERAR USUÁRIO AUTENTICADO
// ----------------------------------------------------------

/**
 * @var array<string, mixed> $usuario
 */

$usuario = $_SESSION['usuario'];


// ----------------------------------------------------------
// VERIFICAR O TIPO DA CONTA
// ----------------------------------------------------------

$tipoUsuario = (string) (
    $usuario['tipo'] ?? ''
);


// Somente clientes podem permanecer nesta página.
if ($tipoUsuario !== 'cliente') {

    header('Location: ../painel.php');

    exit;
}


// ----------------------------------------------------------
// CARREGAR CONEXÃO COM O BANCO
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/config/database.php';


// ----------------------------------------------------------
// CARREGAR MODEL DO DASHBOARD
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/models/DashboardCliente.php';


// ----------------------------------------------------------
// CRIAR MODEL
// ----------------------------------------------------------

$dashboardModel = new DashboardCliente(
    $pdo
);


// ==========================================================
// DESCOBRIR O PERFIL DO CLIENTE
// ==========================================================
//
// Sessão:
//
// usuarios.id = 6
//
// Banco:
//
// clientes.usuario_id = 6
//
// Resultado:
//
// clientes.id = 2
//
// Esse clientes.id será utilizado para encontrar
// os projetos pertencentes à Isabelli.
// ==========================================================

$usuarioId = (int) (
    $usuario['id'] ?? 0
);


$clienteId = $dashboardModel
    ->buscarClienteIdPorUsuario(
        $usuarioId
    );


// ----------------------------------------------------------
// VALIDAR PERFIL
// ----------------------------------------------------------

if ($clienteId === null) {

    http_response_code(500);

    exit(
        'Não foi possível localizar o perfil do cliente.'
    );
}


// ----------------------------------------------------------
// BUSCAR RESUMO
// ----------------------------------------------------------

$resumo = $dashboardModel
    ->obterResumo(
        $clienteId
    );


// ----------------------------------------------------------
// BUSCAR PROJETOS RECENTES
// ----------------------------------------------------------

$projetosRecentes = $dashboardModel
    ->listarProjetosRecentes(
        $clienteId
    );


// ----------------------------------------------------------
// CARREGAR VIEW
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/views/cliente/dashboard.php';