<?php

// ==========================================================
// ACOMPANHAMENTO DA CONTRATAÇÃO
// ==========================================================
// RF14 / RN18 / RN19 / RF15
// Tela compartilhada entre cliente e desenvolvedor.
// ==========================================================

session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
/** @var array<string,mixed> $usuario */
$usuario = $_SESSION['usuario'];
$usuarioId = (int) ($usuario['id'] ?? 0);
$contratacaoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$contratacaoId) { http_response_code(400); exit('Contratação inválida.'); }

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/ProjetoContratado.php';

$model = new ProjetoContratado($pdo);
$contratacao = $model->buscarPorIdEUsuario((int)$contratacaoId, $usuarioId);
if ($contratacao === false) { http_response_code(404); exit('Contratação não encontrada ou acesso não autorizado.'); }

$mensagemSucesso = (string) ($_SESSION['contratacao_sucesso'] ?? '');
$mensagemErro = (string) ($_SESSION['contratacao_erro'] ?? '');
unset($_SESSION['contratacao_sucesso'], $_SESSION['contratacao_erro']);

require_once __DIR__ . '/../app/views/contratacao.php';
