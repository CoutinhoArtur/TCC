<?php
// ==========================================================
// VIEW: CAIXA DE MENSAGENS
// ==========================================================
// Mantém a lista de negociações e usa a mesma linguagem visual
// do chat do protótipo.
// ==========================================================
require_once __DIR__ . '/partials/visual-helpers.php';

$usuario=$usuario??[];$conversas=$conversas??[];$totalNaoLidas=$totalNaoLidas??0;
$nomeUsuario=(string)($usuario['nome']??'');$tipoUsuario=(string)($usuario['tipo']??'');
function escaparMensagens(string $valor): string { return htmlspecialchars($valor,ENT_QUOTES,'UTF-8'); }
function horaLista(string $data): string { if($data==='')return ''; try{return (new DateTime($data))->format('H:i');}catch(Throwable){return $data;} }
$dashboardUrl=$tipoUsuario==='cliente'?'/cliente/dashboard.php':'/desenvolvedor/dashboard.php';
$avatarPadrao = smAvatarPath('user');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Mensagens - SiteMatch</title>
    <link rel="stylesheet" href="/css/global.css"><link rel="stylesheet" href="/css/chat.css"><script src="/js/ui.js" defer></script>
    <!-- Acabamento visual compartilhado; nenhuma regra de negócio é executada aqui. -->
    <link rel="stylesheet" href="/css/polish.css">
</head>
<body>
<div class="chat-page">
    <header class="chat-brandbar"><a class="site-logo" href="<?= escaparMensagens($dashboardUrl) ?>"><span class="brand-mark"></span><span>Site<span>Match</span></span></a></header>
    <div class="chat-layout">
        <aside class="chat-sidebar" style="display:flex;">
            <div class="chat-sidebar-header">
                <div class="chat-sidebar-title"><h1>Conversas</h1><?php if($totalNaoLidas>0): ?><span class="chat-unread"><?= (int)$totalNaoLidas ?></span><?php endif; ?></div>
                <div class="chat-search"><input type="search" placeholder="Procurar conversas..." data-live-search=".chat-conversation-item"></div>
            </div>
            <div class="chat-conversation-list">
                <?php if(empty($conversas)): ?><div style="padding:24px;color:#6d6672;">Você ainda não possui conversas iniciadas.</div><?php else: ?>
                    <?php foreach($conversas as $conversa):
                        $nome=(string)($conversa['outro_usuario_nome']??'Contato');
                    ?>
                    <a class="chat-conversation-item" href="/chat.php?proposta=<?= (int)($conversa['proposta_id']??0) ?>">
                        <div class="chat-avatar chat-avatar-media"><img src="<?= escaparMensagens($avatarPadrao) ?>" alt="Avatar de <?= escaparMensagens($nome) ?>" loading="lazy"></div>
                        <div class="chat-conversation-text"><strong><?= escaparMensagens($nome) ?></strong><p><?= escaparMensagens((string)($conversa['ultima_mensagem']??'')) ?></p></div>
                        <div class="chat-conversation-time"><?= escaparMensagens(horaLista((string)($conversa['ultima_mensagem_em']??''))) ?><?php if((int)($conversa['nao_lidas']??0)>0): ?><div class="chat-unread"><?= (int)$conversa['nao_lidas'] ?></div><?php endif; ?></div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </aside>
        <main class="chat-main" style="display:grid;place-items:center;text-align:center;padding:30px;">
            <div><div class="profile-avatar profile-avatar-media" style="margin:0 auto 18px;"><img src="<?= escaparMensagens($avatarPadrao) ?>" alt="Avatar padrão" loading="lazy"></div><h2>Selecione uma conversa</h2><p class="muted" style="margin-top:8px;">Escolha uma negociação para continuar o contato.</p><a class="botao-secundario" style="margin-top:22px;" href="<?= escaparMensagens($dashboardUrl) ?>">Voltar ao painel</a></div>
        </main>
    </div>
</div>
</body>
</html>
