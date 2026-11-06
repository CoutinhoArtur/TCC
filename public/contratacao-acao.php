<?php

// ==========================================================
// AÇÕES DA CONTRATAÇÃO
// ==========================================================
// Executa as transições permitidas de acordo com o perfil.
// ==========================================================

session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método não permitido.'); }

/** @var array<string,mixed> $usuario */
$usuario = $_SESSION['usuario'];
$usuarioId = (int) ($usuario['id'] ?? 0);
$tipo = (string) ($usuario['tipo'] ?? '');
$contratacaoId = filter_var($_POST['contratacao_id'] ?? null, FILTER_VALIDATE_INT);
$acao = trim((string) ($_POST['acao'] ?? ''));
if (!$contratacaoId) { http_response_code(400); exit('Contratação inválida.'); }

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/ExecucaoProjeto.php';
require_once __DIR__ . '/../app/models/Avaliacao.php';

try {
    $execucao = new ExecucaoProjeto($pdo);

    if ($acao === 'iniciar' && $tipo === 'desenvolvedor') {
        $execucao->iniciar((int)$contratacaoId, $usuarioId);
        $_SESSION['contratacao_sucesso'] = 'Projeto iniciado. A contratação agora está em andamento.';
    } elseif ($acao === 'entregar' && $tipo === 'desenvolvedor') {
        $execucao->enviarParaConfirmacao((int)$contratacaoId, $usuarioId);
        $_SESSION['contratacao_sucesso'] = 'Entrega sinalizada. Agora o cliente deve confirmar a conclusão ou solicitar ajustes.';
    } elseif ($acao === 'ajustes' && $tipo === 'cliente') {
        $execucao->solicitarAjustes((int)$contratacaoId, $usuarioId, trim($_POST['ajustes'] ?? ''));
        $_SESSION['contratacao_sucesso'] = 'Solicitação de ajustes registrada no chat. O projeto voltou para em andamento.';
    } elseif ($acao === 'concluir' && $tipo === 'cliente') {
        $execucao->confirmarConclusao((int)$contratacaoId, $usuarioId);
        $_SESSION['contratacao_sucesso'] = 'Conclusão confirmada com sucesso.';
    } elseif ($acao === 'avaliar' && $tipo === 'cliente') {
        $avaliacao = new Avaliacao($pdo);
        $nota = filter_var($_POST['nota'] ?? null, FILTER_VALIDATE_INT);
        if ($nota === false || $nota === null) {
            throw new InvalidArgumentException('Informe uma nota de 1 a 5.');
        }
        $avaliacao->criar((int)$contratacaoId, $usuarioId, (int)$nota, trim($_POST['comentario'] ?? ''));
        $_SESSION['contratacao_sucesso'] = 'Avaliação registrada com sucesso.';
    } else {
        throw new RuntimeException('Ação não permitida para este usuário.');
    }
} catch (Throwable $erro) {
    error_log($erro->getMessage());
    $_SESSION['contratacao_erro'] = $erro instanceof InvalidArgumentException
        ? $erro->getMessage()
        : 'Não foi possível concluir esta operação.';
}

header('Location: contratacao.php?id=' . (int)$contratacaoId);
exit;
