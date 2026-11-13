<?php
function ePubDev(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function moneyPubDev(float $v): string { return 'R$ '.number_format($v,2,',','.'); }
$voltar=(($usuario['tipo']??'')==='cliente')?'/cliente/desenvolvedores.php':'/desenvolvedor/dashboard.php';
$nome=(string)($perfilPublico['nome']??'Desenvolvedor');
$iniciais=''; foreach(array_slice(preg_split('/\s+/',trim($nome)) ?: [],0,2) as $parte){$iniciais.=mb_strtoupper(mb_substr($parte,0,1));}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Perfil de desenvolvedor - SiteMatch</title>
    <link rel="stylesheet" href="/css/global.css"><link rel="stylesheet" href="/css/functional.css"><script src="/js/ui.js" defer></script>
</head>
<body class="decorated-page">
<header style="height:55px;border-bottom:1px solid #e2dee5;display:flex;align-items:center;padding:0 38px;gap:60px;background:#fff;"><a class="site-logo" href="<?= ePubDev($voltar) ?>"><span class="brand-mark"></span><span>Site<span>Match</span></span></a><nav style="display:flex;gap:46px;font-weight:700;"><a href="<?= ePubDev($voltar) ?>">Início</a><a href="/cliente/desenvolvedores.php">Explorar</a><a href="/historico.php">Avaliações</a></nav></header>
<main class="public-profile-layout" data-tabs>
    <aside class="public-profile-aside">
        <a class="back-link" href="<?= ePubDev($voltar) ?>">← Voltar</a>
        <div class="public-profile-identity"><div class="profile-avatar"><?= ePubDev($iniciais?:'DEV') ?></div><div><h1><?= ePubDev($nome) ?></h1><p><?= ePubDev((string)($perfilPublico['titulo_profissional']?:'Desenvolvedor')) ?></p><?php if((int)($perfilPublico['total_avaliacoes']??0)>0): ?><p style="margin-top:6px;color:#6c27b1;">★ <?= ePubDev((string)$perfilPublico['media_avaliacoes']) ?> (<?= (int)$perfilPublico['total_avaliacoes'] ?> avaliações)</p><?php endif; ?></div></div>
        <?php if(($usuario['tipo']??'')==='cliente'): ?><div class="public-profile-actions"><a class="botao-principal" href="/mensagens.php">Entrar em contato</a><button class="botao-secundario" type="button">Salvar contato</button></div><?php endif; ?>
        <section class="public-profile-about"><h2>Sobre mim</h2><p><?= nl2br(ePubDev((string)($perfilPublico['bio']??'Descrição não informada.'))) ?></p>
            <?php if(!empty($perfilPublico['habilidades_lista'])): ?><h2 style="margin-top:30px;">Habilidades</h2><div class="tags"><?php foreach($perfilPublico['habilidades_lista'] as $h): ?><span class="tag"><?= ePubDev((string)$h['nome']) ?></span><?php endforeach; ?></div><?php endif; ?>
        </section>
    </aside>
    <section class="public-profile-main">
        <div class="profile-tabs"><button class="profile-tab ativa" type="button" data-tab-target="projetos">Projetos</button><button class="profile-tab" type="button" data-tab-target="sobre">Sobre</button><button class="profile-tab" type="button" data-tab-target="avaliacoes">Avaliações</button></div>
        <div class="profile-tab-panel" data-tab-panel="projetos">
            <h2 style="margin-bottom:24px;">Projetos recentes</h2>
            <?php if(empty($perfilPublico['portfolio'])): ?><div class="projetos-vazio">Nenhum trabalho publicado.</div><?php else: ?><div class="portfolio-grid"><?php foreach($perfilPublico['portfolio'] as $i=>$item): ?><article class="portfolio-card"><div class="portfolio-card-visual" style="background:linear-gradient(145deg,<?= $i%2===0?'#4a1a37,#d9954a':'#5e3b79,#c8a0e8' ?>);"></div><div class="portfolio-card-body"><h3><?= ePubDev((string)$item['titulo']) ?></h3><p><?= ePubDev((string)$item['descricao']) ?></p><div class="acoes-inline"><?php if(!empty($item['link_projeto'])): ?><a class="botao-principal" target="_blank" rel="noopener" href="<?= ePubDev((string)$item['link_projeto']) ?>">Ver projeto →</a><?php endif; ?></div></div></article><?php endforeach; ?></div><?php endif; ?>
        </div>
        <div class="profile-tab-panel" data-tab-panel="sobre" hidden><h2>Experiência</h2><p style="margin-top:12px;line-height:1.7;"><?= nl2br(ePubDev((string)($perfilPublico['experiencia']??'Experiência não informada.'))) ?></p><div class="form-card" style="margin-top:24px;"><div class="projeto-meta"><span>Disponibilidade: <strong><?= ePubDev((string)($perfilPublico['disponibilidade']??'Não informada')) ?></strong></span><?php if($perfilPublico['faixa_preco_min']!==null): ?><span>Faixa: <strong><?= moneyPubDev((float)$perfilPublico['faixa_preco_min']) ?><?= $perfilPublico['faixa_preco_max']!==null?' a '.moneyPubDev((float)$perfilPublico['faixa_preco_max']):'' ?></strong></span><?php endif; ?></div></div></div>
        <div class="profile-tab-panel" data-tab-panel="avaliacoes" hidden><h2 style="margin-bottom:22px;">Avaliações</h2><?php if(empty($perfilPublico['avaliacoes'])): ?><div class="projetos-vazio">Este desenvolvedor ainda não possui avaliações.</div><?php else: ?><div class="lista-simples"><?php foreach($perfilPublico['avaliacoes'] as $a): ?><article class="projeto-card"><strong>★ <?= (int)$a['nota'] ?>/5 · <?= ePubDev((string)$a['projeto_titulo']) ?></strong><p style="margin-top:8px;"><?= ePubDev((string)($a['comentario']??'')) ?></p></article><?php endforeach; ?></div><?php endif; ?></div>
    </section>
</main>
</body>
</html>
