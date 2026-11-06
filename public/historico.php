<?php

// ==========================================================
// HISTÓRICO
// ==========================================================
// RF16 - Histórico de projetos, propostas e contratações.
// ==========================================================

session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
/** @var array<string,mixed> $usuario */
$usuario = $_SESSION['usuario'];
$usuarioId = (int) ($usuario['id'] ?? 0);
$tipo = (string) ($usuario['tipo'] ?? '');
if ($usuarioId <= 0 || !in_array($tipo, ['cliente','desenvolvedor'], true)) {
    session_destroy(); header('Location: login.php'); exit;
}

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/Historico.php';
$model = new Historico($pdo);
$registros = $tipo === 'cliente' ? $model->listarCliente($usuarioId) : $model->listarDesenvolvedor($usuarioId);
require_once __DIR__ . '/../app/views/historico.php';
