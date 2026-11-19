<?php
require_once __DIR__ . '/../partials/visual-helpers.php';
function eDevBusca(string $v): string { return htmlspecialchars($v,ENT_QUOTES,'UTF-8'); }
function dinheiroDevBusca(float $v): string { return 'R$ '.number_format($v,2,',','.'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Desenvolvedores - SiteMatch</title>
    <link rel="stylesheet" href="/css/global.css"><link rel="stylesheet" href="/css/dashboard.css"><link rel="stylesheet" href="/css/functional.css"><script src="/js/ui.js" defer></script>
    <!-- Acabamento visual compartilhado; nenhuma regra de negócio é executada aqui. -->
    <link rel="stylesheet" href="/css/polish.css">
</head>
<body>
<div class="dashboard">
    <aside class="sidebar">
        <a class="sidebar-logo" href="dashboard.php">Site<span>Match</span></a>
        <div class="sidebar-user has-avatar"><img class="sidebar-user-avatar" src="<?= htmlspecialchars(smAvatarFromRecord($usuario ?? [], 'cliente'), ENT_QUOTES, 'UTF-8') ?>" alt="Avatar do usuário" loading="lazy"><div><strong><?= eDevBusca((string)($usuario['nome']??'')) ?></strong><span>Empreendedor</span></div></div>
        <nav class="sidebar-nav"><ul><li><a data-icon="⌂" href="dashboard.php">Início</a></li><li><a data-icon="▣" href="meus-projetos.php">Meus projetos</a></li><li><a data-icon="◎" href="/mensagens.php">Mensagens</a></li><li><a class="ativo" data-icon="⌕" href="desenvolvedores.php">Desenvolvedores</a></li><li><a data-icon="◴" href="/historico.php">Histórico</a></li><li><a data-icon="♙" href="/perfil.php">Seu perfil</a></li><li class="menu-sair"><a data-icon="⇤" href="../logout.php">Sair</a></li></ul></nav>
    </aside>
    <main class="dashboard-main">
        <div class="dashboard-topbar"><button class="mobile-menu-button" type="button" data-menu-toggle>☰</button><div class="dashboard-search"><input type="search" placeholder="Procurar desenvolvedores..." data-live-search=".developer-card"></div></div>
        <header class="dashboard-header"><div class="dashboard-header-text"><h1>Encontre o profissional ideal</h1><p>Pesquise por experiência, habilidade ou faixa de preço.</p></div></header>
        <form method="GET" class="filter-panel filtros">
            <div class="form-campo"><label for="q">Nome ou experiência</label><input id="q" name="q" value="<?= eDevBusca((string)$filtros['q']) ?>" placeholder="Nome, PHP, e-commerce..."></div>
            <div class="form-campo"><label for="habilidade">Habilidade</label><input id="habilidade" name="habilidade" value="<?= eDevBusca((string)$filtros['habilidade']) ?>" placeholder="PHP, JavaScript..."></div>
            <div class="form-campo"><label for="preco_max">Preço máximo</label><input id="preco_max" name="preco_max" type="number" min="0" step="0.01" value="<?= eDevBusca((string)$filtros['preco_max']) ?>"></div>
            <div class="form-acoes"><button class="botao-principal">Pesquisar</button><a class="botao-secundario" href="desenvolvedores.php">Limpar</a></div>
        </form>
        <section class="dashboard-section" style="margin-top:30px;"><h2><?= count($desenvolvedores) ?> profissional(is)</h2>
            <?php if(empty($desenvolvedores)): ?><div class="projetos-vazio">Nenhum desenvolvedor encontrado.</div>
            <?php else: ?><div class="developer-grid">
                <?php foreach($desenvolvedores as $d): $nome=(string)$d['nome'];$iniciais='';foreach(array_slice(preg_split('/\s+/',trim($nome))?:[],0,2) as $parte){$iniciais.=mb_strtoupper(mb_substr($parte,0,1));} ?>
                <article class="developer-card">
                    <div class="developer-avatar developer-avatar-media"><img src="<?= eDevBusca(smAvatarFromRecord($d, 'desenvolvedor')) ?>" alt="Avatar de <?= eDevBusca($nome) ?>" loading="lazy"></div>
                    <div><h3><?= eDevBusca($nome) ?></h3><p class="developer-role"><?= eDevBusca((string)($d['titulo_profissional']?:'Perfil profissional em construção')) ?></p></div>
                    <?php if((int)$d['total_avaliacoes']>0): ?><span class="developer-rating">★ <?= eDevBusca((string)$d['media_avaliacoes']) ?> (<?= (int)$d['total_avaliacoes'] ?>)</span><?php endif; ?>
                    <?php if(!empty($d['bio'])): ?><p class="developer-bio"><?= eDevBusca((string)$d['bio']) ?></p><?php endif; ?>
                    <?php if(!empty($d['habilidades'])): ?><div class="tags"><?php foreach(explode(', ',(string)$d['habilidades']) as $h): ?><span class="tag"><?= eDevBusca($h) ?></span><?php endforeach; ?></div><?php endif; ?>
                    <div class="developer-footer"><div class="projeto-meta"><?php if($d['faixa_preco_min']!==null): ?><span>Faixa: <strong><?= dinheiroDevBusca((float)$d['faixa_preco_min']) ?><?= $d['faixa_preco_max']!==null?' a '.dinheiroDevBusca((float)$d['faixa_preco_max']):'' ?></strong></span><?php endif; ?><span>Disponibilidade: <strong><?= eDevBusca((string)($d['disponibilidade']??'Não informada')) ?></strong></span></div><a class="botao-principal" href="/desenvolvedor-publico.php?id=<?= (int)$d['desenvolvedor_id'] ?>">Ver perfil</a></div>
                </article>
                <?php endforeach; ?>
            </div><?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
