<?php

// ==========================================================
// EDITAR PROJETO
// ==========================================================
// RF07 / RN06 - Somente o cliente dono pode editar.
// Para preservar propostas já enviadas, esta tela só permite
// editar projetos que ainda não receberam propostas.
// ==========================================================

session_start();
if (!isset($_SESSION['usuario'])) { header('Location: ../login.php'); exit; }
/** @var array<string,mixed> $usuario */
$usuario = $_SESSION['usuario'];
if (($usuario['tipo'] ?? '') !== 'cliente') { header('Location: ../painel.php'); exit; }
$usuarioId = (int) ($usuario['id'] ?? 0);
$projetoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$projetoId) { http_response_code(400); exit('Projeto inválido.'); }

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/models/ProjetoCliente.php';
$model = new ProjetoCliente($pdo);
$clienteId = $model->buscarClienteIdPorUsuario($usuarioId);
if ($clienteId === null) { http_response_code(500); exit('Perfil de cliente não encontrado.'); }

$mensagemSucesso = '';
$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $model->atualizarSemPropostas((int)$projetoId, $clienteId, $usuarioId, $_POST);
        $mensagemSucesso = 'Projeto atualizado com sucesso.';
    } catch (Throwable $erro) {
        error_log($erro->getMessage());
        $mensagemErro = $erro->getMessage();
    }
}

$projeto = $model->buscarPorId((int)$projetoId, $clienteId);
if ($projeto === false) { http_response_code(404); exit('Projeto não encontrado.'); }
$podeEditar = in_array((string)$projeto['status'], ['rascunho','aberto'], true) && (int)$projeto['total_propostas'] === 0;
require_once __DIR__ . '/../../app/views/cliente/editar-projeto.php';
