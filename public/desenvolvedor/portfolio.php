<?php

// ==========================================================
// PORTFÓLIO DO DESENVOLVEDOR
// ==========================================================
// RF05 - Cadastro, edição e remoção de trabalhos.
// ==========================================================

session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

/** @var array<string,mixed> $usuario */
$usuario = $_SESSION['usuario'];
if (($usuario['tipo'] ?? '') !== 'desenvolvedor') {
    header('Location: ../painel.php');
    exit;
}

$usuarioId = (int) ($usuario['id'] ?? 0);
require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../../app/models/Portfolio.php';

$model = new Portfolio($pdo);
$desenvolvedorId = $model->buscarDesenvolvedorIdPorUsuario($usuarioId);
if ($desenvolvedorId === null) {
    http_response_code(500);
    exit('Perfil de desenvolvedor não encontrado.');
}

$mensagemSucesso = (string) ($_SESSION['portfolio_sucesso'] ?? '');
$mensagemErro = (string) ($_SESSION['portfolio_erro'] ?? '');
unset($_SESSION['portfolio_sucesso'], $_SESSION['portfolio_erro']);

$editarId = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
$itemEdicao = false;
if ($editarId) {
    $itemEdicao = $model->buscar((int) $editarId, $desenvolvedorId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = (string) ($_POST['acao'] ?? 'salvar');
    try {
        if ($acao === 'excluir') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            if (!$id || !$model->excluir((int) $id, $desenvolvedorId)) {
                throw new RuntimeException('Trabalho não encontrado.');
            }
            $_SESSION['portfolio_sucesso'] = 'Trabalho removido do portfólio.';
        } else {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            $dados = [
                trim($_POST['titulo'] ?? ''),
                trim($_POST['descricao'] ?? ''),
                trim($_POST['link_projeto'] ?? ''),
                trim($_POST['link_repositorio'] ?? ''),
                trim($_POST['imagem'] ?? '')
            ];
            if ($id) {
                if (!$model->atualizar((int) $id, $desenvolvedorId, ...$dados)) {
                    throw new RuntimeException('Não foi possível atualizar este trabalho.');
                }
                $_SESSION['portfolio_sucesso'] = 'Trabalho atualizado com sucesso.';
            } else {
                $model->criar($desenvolvedorId, ...$dados);
                $_SESSION['portfolio_sucesso'] = 'Trabalho adicionado ao portfólio.';
            }
        }
    } catch (Throwable $erro) {
        error_log($erro->getMessage());
        $_SESSION['portfolio_erro'] = $erro instanceof InvalidArgumentException
            ? $erro->getMessage()
            : 'Não foi possível concluir a operação no portfólio.';
    }
    header('Location: portfolio.php');
    exit;
}

$itens = $model->listar($desenvolvedorId);
require_once __DIR__ . '/../../app/views/desenvolvedor/portfolio.php';
