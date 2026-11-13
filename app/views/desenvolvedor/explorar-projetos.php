<?php
function eExplorar(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function dinheiroExplorar(float $v): string { return 'R$ '.number_format($v,2,',','.'); }
$categorias = ['site'=>'Site institucional','sistema_web'=>'Sistema web','aplicativo_mobile'=>'Aplicativo mobile','sistema_desktop'=>'Sistema desktop','outro'=>'Outro'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Explorar projetos - SiteMatch</title>
    <link rel="stylesheet" href="/css/global.css"><link rel="stylesheet" href="/css/dashboard.css"><link rel="stylesheet" href="/css/functional.css">
    <script src="/js/ui.js" defer></script>
</head>
<body>
<div class="dashboard">
    <aside class="sidebar">
        <a class="sidebar-logo" href="dashboard.php">Site<span>Match</span></a>
        <div class="sidebar-user"><strong><?= eExplorar((string)($usuario['nome']??'')) ?></strong><span>Desenvolvedor</span></div>
        <nav class="sidebar-nav"><ul>
            <li><a data-icon="⌂" href="dashboard.php">Início</a></li><li><a class="ativo" data-icon="⌕" href="explorar-projetos.php">Explorar projetos</a></li><li><a data-icon="▣" href="minhas-propostas.php">Minhas propostas</a></li><li><a data-icon="◎" href="/mensagens.php">Mensagens</a></li><li><a data-icon="◴" href="/historico.php">Histórico</a></li><li><a data-icon="♙" href="/perfil.php">Meu perfil</a></li><li class="menu-sair"><a data-icon="⇤" href="../logout.php">Sair</a></li>
        </ul></nav>
    </aside>
    <main class="dashboard-main">
        <div class="dashboard-topbar">
            <button class="mobile-menu-button" type="button" data-menu-toggle>☰</button>
            <div class="dashboard-search"><input type="search" placeholder="Filtrar resultados na tela..." data-live-search=".project-row"></div>
        </div>
        <header class="dashboard-header"><div class="dashboard-header-text"><h1>Explorar projetos</h1><p>Encontre oportunidades compatíveis com seu perfil.</p></div></header>

        <section class="dashboard-section">
            <form method="GET" class="filter-panel filtros">
                <div class="form-campo"><label for="q">Palavra-chave / tecnologia</label><input id="q" name="q" value="<?= eExplorar((string)$filtros['q']) ?>" placeholder="PHP, React, sistema..."></div>
                <div class="form-campo"><label for="categoria">Categoria</label><select id="categoria" name="categoria"><option value="">Todas</option><?php foreach($categorias as $v=>$r): ?><option value="<?= $v ?>" <?= $filtros['categoria']===$v?'selected':'' ?>><?= $r ?></option><?php endforeach; ?></select></div>
                <div class="form-campo"><label for="orcamento_min">Orçamento mínimo</label><input id="orcamento_min" name="orcamento_min" type="number" min="0" step="0.01" value="<?= eExplorar((string)$filtros['orcamento_min']) ?>"></div>
                <div class="form-campo"><label for="orcamento_max">Orçamento máximo</label><input id="orcamento_max" name="orcamento_max" type="number" min="0" step="0.01" value="<?= eExplorar((string)$filtros['orcamento_max']) ?>"></div>
                <div class="form-acoes" style="grid-column:1/-1;"><button class="botao-principal" type="submit">Pesquisar</button><a class="botao-secundario" href="explorar-projetos.php">Limpar filtros</a></div>
            </form>
        </section>

        <section class="dashboard-section">
            <h2><?= count($projetos) ?> projeto(s) encontrado(s)</h2>
            <?php if(empty($projetos)): ?><div class="projetos-vazio">Nenhum projeto aberto corresponde aos filtros.</div>
            <?php else: ?><div class="projetos-lista">
                <?php foreach($projetos as $p): $cat=(string)($p['categoria']??'outro'); ?>
                <article class="project-row">
                    <div class="project-thumb" data-category="<?= eExplorar($cat) ?>"><?= strtoupper(substr((string)$p['titulo'],0,1)) ?></div>
                    <div class="project-content">
                        <h3><?= eExplorar((string)$p['titulo']) ?></h3>
                        <div class="project-meta-line"><span>Cliente: <strong><?= eExplorar((string)$p['nome_cliente']) ?></strong></span><span>•</span><span><?= eExplorar($categorias[$cat]??$cat) ?></span><span>•</span><span><?= dinheiroExplorar((float)($p['orcamento_min']??0)) ?> - <?= dinheiroExplorar((float)($p['orcamento_max']??0)) ?></span></div>
                        <span class="project-chip"><?= (int)$p['total_propostas'] ?> proposta(s)</span>
                        <?php if(!empty($p['minha_proposta_id'])): ?><span class="project-chip" style="margin-left:6px;color:#6c23a8;background:#f4e9ff;">Você já enviou proposta</span><?php endif; ?>
                    </div>
                    <div class="project-actions"><a class="botao-principal" href="projeto.php?id=<?= (int)$p['id'] ?>">Ver projeto</a></div>
                </article>
                <?php endforeach; ?>
            </div><?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
