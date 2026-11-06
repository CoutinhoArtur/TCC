<?php

// ==========================================================
// EXPLORAR PROJETOS
// ==========================================================
// RF08 - Pesquisa de projetos com filtros.
// ==========================================================

session_start();
if (!isset($_SESSION['usuario'])) { header('Location: ../login.php'); exit; }
/** @var array<string,mixed> $usuario */
$usuario = $_SESSION['usuario'];
if (($usuario['tipo'] ?? '') !== 'desenvolvedor') { header('Location: ../painel.php'); exit; }
$usuarioId = (int) ($usuario['id'] ?? 0);

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/models/PropostaDesenvolvedor.php';
require_once __DIR__ . '/../../app/models/PesquisaProjeto.php';

$propostaModel = new PropostaDesenvolvedor($pdo);
$desenvolvedorId = $propostaModel->buscarDesenvolvedorIdPorUsuario($usuarioId);
if ($desenvolvedorId === null) { http_response_code(500); exit('Perfil de desenvolvedor não encontrado.'); }

$filtros = [
    'q' => trim($_GET['q'] ?? ''),
    'categoria' => trim($_GET['categoria'] ?? ''),
    'orcamento_min' => trim($_GET['orcamento_min'] ?? ''),
    'orcamento_max' => trim($_GET['orcamento_max'] ?? '')
];
$model = new PesquisaProjeto($pdo);
$projetos = $model->pesquisar($desenvolvedorId, $filtros);
require_once __DIR__ . '/../../app/views/desenvolvedor/explorar-projetos.php';
