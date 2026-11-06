<?php

// ==========================================================
// DASHBOARD PÚBLICO DO DESENVOLVEDOR
// ==========================================================
//
// Este arquivo:
//
// 1. inicia a sessão;
// 2. verifica se existe usuário autenticado;
// 3. verifica se ele é desenvolvedor;
// 4. conecta com o banco;
// 5. localiza desenvolvedores.id;
// 6. busca os dados reais do dashboard;
// 7. carrega a View.
//
// RN03  - Acesso conforme o perfil
// RF08  - Pesquisa de projetos
// RF10  - Envio de propostas
// RF11  - Gerenciamento de propostas
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

    // Sem sessão, o usuário deve voltar para o login.
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
// VERIFICAR TIPO DO USUÁRIO
// ----------------------------------------------------------

$tipoUsuario = (string) (
    $usuario['tipo'] ?? ''
);


// Somente desenvolvedores podem permanecer nesta página.
if ($tipoUsuario !== 'desenvolvedor') {

    header('Location: ../painel.php');

    exit;
}


// ----------------------------------------------------------
// CARREGAR CONEXÃO COM O BANCO
// ----------------------------------------------------------
//
// Estamos dentro de:
//
// public/desenvolvedor/
//
// Por isso voltamos duas pastas para acessar /app.
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/config/database.php';


// ----------------------------------------------------------
// CARREGAR MODEL DO DASHBOARD
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/models/DashboardDesenvolvedor.php';


// ----------------------------------------------------------
// CRIAR MODEL
// ----------------------------------------------------------

$dashboardModel =
    new DashboardDesenvolvedor($pdo);


// ==========================================================
// DESCOBRIR O ID DO DESENVOLVEDOR
// ==========================================================
//
// A sessão possui:
//
// usuarios.id
//
// Mas propostas utiliza:
//
// desenvolvedores.id
//
// Portanto fazemos:
//
// usuarios.id
//      ↓
// desenvolvedores.usuario_id
//      ↓
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
// VALIDAR PERFIL
// ----------------------------------------------------------
//
// Todo usuário com tipo "desenvolvedor" deveria possuir
// um registro correspondente na tabela desenvolvedores.
//
// Se não existir, temos uma inconsistência no banco.
// ----------------------------------------------------------

if ($desenvolvedorId === null) {

    http_response_code(500);

    exit(
        'Não foi possível localizar o perfil do desenvolvedor.'
    );
}


// ==========================================================
// BUSCAR RESUMO
// ==========================================================
//
// Aqui buscamos:
//
// - propostas enviadas;
// - propostas em negociação;
// - projetos em andamento.
// ==========================================================

$resumo =
    $dashboardModel->obterResumo(
        $desenvolvedorId
    );


// ==========================================================
// BUSCAR PROJETOS DISPONÍVEIS
// ==========================================================
//
// Aqui buscamos todos os projetos com:
//
// status = aberto
//
// Portanto, o projeto da Isabelli deverá aparecer
// nesta lista.
// ==========================================================

$projetosDisponiveis =
    $dashboardModel
        ->listarProjetosDisponiveis();


// ----------------------------------------------------------
// CARREGAR VIEW
// ----------------------------------------------------------
//
// A View receberá:
//
// $usuario
// $resumo
// $projetosDisponiveis
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/views/desenvolvedor/dashboard.php';