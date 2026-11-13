<?php
// ==========================================================
// VIEW: CHAT / NEGOCIAÇÃO
// ==========================================================
// A tela foi reorganizada para seguir o protótipo: lista de
// conversas à esquerda e conversa ativa à direita.
// ==========================================================
function escaparChat(string $valor): string { return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); }
function horarioChat(string $data): string {
    if ($data==='') return '';
    try { return (new DateTime($data))->format('H:i'); } catch(Throwable){ return $data; }
}
$statusAmigaveis=['preliminar'=>'Proposta preliminar','em_negociacao'=>'Em negociação','aguardando_revisao'=>'Aguardando revisão','definitiva'=>'Proposta definitiva','aceita'=>'Proposta aceita','recusada'=>'Proposta recusada','encerrada'=>'Proposta encerrada','cancelada'=>'Proposta cancelada'];
$statusExibicao=$statusAmigaveis[$propostaStatus]??$propostaStatus;
$conversas=$conversas??[];
$outroNome='Contato';
foreach($conversas as $c){ if((int)($c['proposta_id']??0)===$propostaId){$outroNome=(string)($c['outro_usuario_nome']??'Contato');break;} }
$iniciais=''; foreach(array_slice(preg_split('/\s+/',trim($outroNome)) ?: [],0,2) as $parte){$iniciais.=mb_strtoupper(mb_substr($parte,0,1));}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Conversas - SiteMatch</title>
    <link rel="stylesheet" href="/css/global.css"><link rel="stylesheet" href="/css/chat.css"><script src="/js/ui.js" defer></script>
</head>
<body>
<div class="chat-page">
    <header class="chat-brandbar"><a class="site-logo" href="/painel.php"><span class="brand-mark"></span><span>Site<span>Match</span></span></a></header>
    <div class="chat-layout">
        <aside class="chat-sidebar">
            <div class="chat-sidebar-header">
                <div class="chat-sidebar-title"><h1>Conversas</h1><a class="chat-new-button" href="/mensagens.php" aria-label="Ver conversas">＋</a></div>
                <div class="chat-search"><input type="search" placeholder="Procurar conversas..." data-live-search=".chat-conversation-item"></div>
            </div>
            <div class="chat-conversation-list">
                <?php if(empty($conversas)): ?><div style="padding:22px;color:#6b6470;">Nenhuma conversa iniciada.</div><?php else: ?>
                    <?php foreach($conversas as $conversa):
                        $pid=(int)($conversa['proposta_id']??0);
                        $nome=(string)($conversa['outro_usuario_nome']??'Contato');
                        $in=''; foreach(array_slice(preg_split('/\s+/',trim($nome)) ?: [],0,2) as $parte){$in.=mb_strtoupper(mb_substr($parte,0,1));}
                    ?>
                    <a class="chat-conversation-item <?= $pid===$propostaId?'ativa':'' ?>" href="/chat.php?proposta=<?= $pid ?>">
                        <div class="chat-avatar"><?= escaparChat($in?:'SM') ?></div>
                        <div class="chat-conversation-text"><strong><?= escaparChat($nome) ?></strong><p><?= escaparChat((string)($conversa['ultima_mensagem']??'Conversa do projeto')) ?></p></div>
                        <div class="chat-conversation-time"><?= escaparChat(horarioChat((string)($conversa['ultima_mensagem_em']??''))) ?><?php if((int)($conversa['nao_lidas']??0)>0): ?><div class="chat-unread"><?= (int)$conversa['nao_lidas'] ?></div><?php endif; ?></div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </aside>

        <main class="chat-main">
            <header class="chat-header">
                <div class="chat-header-person"><div class="chat-avatar"><?= escaparChat($iniciais?:'SM') ?></div><div class="chat-header-info"><h1><?= escaparChat($outroNome) ?></h1><p><?= escaparChat($projetoTitulo) ?> · <?= escaparChat($statusExibicao) ?></p></div></div>
                <a class="botao-secundario" href="/painel.php">Voltar ao painel</a>
            </header>

            <?php if($mensagemErro!==''): ?><div class="chat-erro"><?= escaparChat($mensagemErro) ?></div><?php endif; ?>

            <div class="chat-mensagens" id="chat-mensagens">
                <?php if(empty($mensagens)): ?>
                    <div class="chat-vazio"><div class="chat-vazio-conteudo"><h2>Inicie a negociação</h2><p>Converse sobre detalhes, dúvidas e possíveis ajustes da proposta.</p></div></div>
                <?php else: ?>
                    <?php foreach($mensagens as $mensagem):
                        $ehMinha=(int)($mensagem['remetente_id']??0)===$usuarioId;
                    ?>
                    <div class="mensagem-linha <?= $ehMinha?'minha':'outra' ?>"><div class="mensagem-bolha"><span class="mensagem-remetente"><?= $ehMinha?'Você':escaparChat((string)($mensagem['remetente_nome']??'')) ?></span><div class="mensagem-conteudo"><?= nl2br(escaparChat((string)($mensagem['conteudo']??''))) ?></div><span class="mensagem-horario"><?= escaparChat(horarioChat((string)($mensagem['enviada_em']??''))) ?></span></div></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="chat-form-area">
                <form method="POST" class="chat-form" data-loading>
                    <textarea name="conteudo" placeholder="Digitar..." maxlength="3000" required data-autogrow></textarea>
                    <button type="submit" class="chat-enviar">Enviar</button>
                </form>
            </div>
        </main>
    </div>
</div>
<script>
// Mantém a conversa posicionada na mensagem mais recente.
document.addEventListener('DOMContentLoaded',()=>{const area=document.querySelector('#chat-mensagens');if(area)area.scrollTop=area.scrollHeight;});
</script>
</body>
</html>
