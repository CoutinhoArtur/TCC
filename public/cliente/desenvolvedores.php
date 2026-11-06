<?php

// ==========================================================
// PESQUISA DE DESENVOLVEDORES
// ==========================================================
// RF09 - Clientes pesquisam e consultam perfis profissionais.
// ==========================================================

session_start();
if (!isset($_SESSION['usuario'])) { header('Location: ../login.php'); exit; }
/** @var array<string,mixed> $usuario */
$usuario = $_SESSION['usuario'];
if (($usuario['tipo'] ?? '') !== 'cliente') { header('Location: ../painel.php'); exit; }

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/models/PesquisaDesenvolvedor.php';
$filtros = ['q'=>trim($_GET['q']??''),'habilidade'=>trim($_GET['habilidade']??''),'preco_max'=>trim($_GET['preco_max']??'')];
$model = new PesquisaDesenvolvedor($pdo);
$desenvolvedores = $model->pesquisar($filtros);
require_once __DIR__ . '/../../app/views/cliente/desenvolvedores.php';
