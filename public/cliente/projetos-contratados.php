<?php

// ==========================================================
// PROJETOS CONTRATADOS - CLIENTE
// ==========================================================
//
// Esta página lista todos os projetos que já possuem
// uma contratação.
//
// Fluxo:
//
// proposta aceita
//      ↓
// contratação
//      ↓
// projetos contratados
//
// ==========================================================

session_start();


// ----------------------------------------------------------
// LOGIN
// ----------------------------------------------------------

if (!isset($_SESSION['usuario'])) {

    header('Location: ../login.php');

    exit;
}


/**
 * @var array<string, mixed> $usuario
 */

$usuario = $_SESSION['usuario'];


// ----------------------------------------------------------
// SOMENTE CLIENTE
// ----------------------------------------------------------

if (($usuario['tipo'] ?? '') !== 'cliente') {

    header('Location: ../painel.php');

    exit;
}


// ----------------------------------------------------------
// ID DO USUÁRIO
// ----------------------------------------------------------

$usuarioId = (int) (
    $usuario['id'] ?? 0
);


if ($usuarioId <= 0) {

    session_destroy();

    header('Location: ../login.php');

    exit;
}


// ----------------------------------------------------------
// BANCO
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/config/database.php';


// ----------------------------------------------------------
// MODEL
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/models/ProjetoContratado.php';


$projetoContratadoModel =
    new ProjetoContratado($pdo);


// ==========================================================
// BUSCAR CONTRATAÇÕES
// ==========================================================

$contratacoes =
    $projetoContratadoModel
        ->listarDoCliente(
            $usuarioId
        );


// ----------------------------------------------------------
// VIEW
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/views/cliente/projetos-contratados.php';