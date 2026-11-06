<?php

session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit('Desenvolvedor inválido.'); }
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/PesquisaDesenvolvedor.php';
$model = new PesquisaDesenvolvedor($pdo);
$perfilPublico = $model->buscarPerfilPublico((int)$id);
if ($perfilPublico === false) { http_response_code(404); exit('Desenvolvedor não encontrado.'); }
$usuario = $_SESSION['usuario'];
require_once __DIR__ . '/../app/views/desenvolvedor-publico.php';
