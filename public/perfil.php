<?php

// ==========================================================
// PERFIL DO USUÁRIO
// ==========================================================
// RF03 / RF04
// Permite que clientes e desenvolvedores editem seus dados.
// ==========================================================

session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

/** @var array<string,mixed> $usuario */
$usuario = $_SESSION['usuario'];
$usuarioId = (int) ($usuario['id'] ?? 0);
$tipoUsuario = (string) ($usuario['tipo'] ?? '');

if ($usuarioId <= 0 || !in_array($tipoUsuario, ['cliente', 'desenvolvedor'], true)) {
    session_destroy();
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/Perfil.php';

$perfilModel = new Perfil($pdo);
$mensagemSucesso = '';
$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome = trim($_POST['nome'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));

        if ($tipoUsuario === 'cliente') {
            $perfilModel->atualizarCliente(
                $usuarioId,
                $nome,
                $email,
                trim($_POST['nome_empresa'] ?? ''),
                trim($_POST['descricao'] ?? ''),
                trim($_POST['telefone'] ?? ''),
                trim($_POST['cidade'] ?? '')
            );
        } else {
            $nomes = $_POST['habilidade_nome'] ?? [];
            $niveis = $_POST['habilidade_nivel'] ?? [];
            $habilidades = [];
            if (is_array($nomes)) {
                foreach ($nomes as $indice => $nomeHabilidade) {
                    $habilidades[] = [
                        'nome' => trim((string) $nomeHabilidade),
                        'nivel' => is_array($niveis) ? (string) ($niveis[$indice] ?? 'intermediario') : 'intermediario'
                    ];
                }
            }

            $faixaMinTexto = trim($_POST['faixa_preco_min'] ?? '');
            $faixaMaxTexto = trim($_POST['faixa_preco_max'] ?? '');

            // RNF04: a validação precisa existir também no servidor.
            // Não fazemos cast de texto inválido para 0 silenciosamente.
            if ($faixaMinTexto !== '' && !is_numeric(str_replace(',', '.', $faixaMinTexto))) {
                throw new InvalidArgumentException('Informe um valor mínimo válido.');
            }
            if ($faixaMaxTexto !== '' && !is_numeric(str_replace(',', '.', $faixaMaxTexto))) {
                throw new InvalidArgumentException('Informe um valor máximo válido.');
            }

            $perfilModel->atualizarDesenvolvedor(
                $usuarioId,
                $nome,
                $email,
                trim($_POST['titulo_profissional'] ?? ''),
                trim($_POST['bio'] ?? ''),
                trim($_POST['experiencia'] ?? ''),
                trim($_POST['disponibilidade'] ?? ''),
                $faixaMinTexto === '' ? null : (float) str_replace(',', '.', $faixaMinTexto),
                $faixaMaxTexto === '' ? null : (float) str_replace(',', '.', $faixaMaxTexto),
                $habilidades
            );
        }

        $_SESSION['usuario']['nome'] = $nome;
        $_SESSION['usuario']['email'] = $email;
        $usuario = $_SESSION['usuario'];
        $mensagemSucesso = 'Perfil atualizado com sucesso.';
    } catch (Throwable $erro) {
        error_log($erro->getMessage());
        $mensagemErro = $erro instanceof InvalidArgumentException
            ? $erro->getMessage()
            : 'Não foi possível atualizar o perfil.';
    }
}

$perfil = $perfilModel->buscarPorUsuario($usuarioId);
if ($perfil === false) {
    http_response_code(404);
    exit('Perfil não encontrado.');
}

$habilidades = [];
if ($tipoUsuario === 'desenvolvedor') {
    $desenvolvedorId = (int) ($perfil['desenvolvedor_id'] ?? 0);
    if ($desenvolvedorId > 0) {
        $habilidades = $perfilModel->listarHabilidades($desenvolvedorId);
    }
}

require_once __DIR__ . '/../app/views/perfil.php';
