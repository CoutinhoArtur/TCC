<?php
/** @var array<string,mixed>|null $usuario */
/** @var array<int,array<string,mixed>>|null $projetos */
$usuario=$usuario??[];$projetos=$projetos??[];
function eMeusProj(string $v): string { return htmlspecialchars($v,ENT_QUOTES,'UTF-8'); }
function moneyMeusProj(float $v): string { return 'R$ '.number_format($v,2,',','.'); }
function dateMeusProj(string $v): string { if($v==='')return 'Não informado';$d=DateTime::createFromFormat('Y-m-d',$v);return $d?$d->format('d/m/Y'):$v; }
$cat=['site'=>'Site institucional','sistema_web'=>'Sistema web','aplicativo_mobile'=>'Aplicativo mobile','sistema_desktop'=>'Sistema desktop','outro'=>'Outro'];
$status=['rascunho'=>'Rascunho','aberto'=>'Aberto','em_negociacao'=>'Em negociação','contratado'=>'Contratado','em_andamento'=>'Em andamento','aguardando_confirmacao'=>'Aguardando confirmação','concluido'=>'Concluído','cancelado'=>'Cancelado'];
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Meus projetos - SiteMatch</title><link rel="stylesheet" href="/css/global.css"><link rel="stylesheet" href="/css/dashboard.css"><link rel="stylesheet" href="/css/functional.css"><script src="/js/ui.js" defer></script></head><body>
<div class="dashboard">
<aside class="sidebar"><a class="sidebar-logo" href="dashboard.php">Site<span>Match</span></a><div class="sidebar-user"><strong><?= eMeusProj((string)($usuario['nome']??'')) ?></strong><span>Empreendedor</span></div><nav class="sidebar-nav"><ul><li><a data-icon="⌂" href="dashboard.php">Início</a></li><li><a class="ativo" data-icon="▣" href="meus-projetos.php">Meus projetos</a></li><li><a data-icon="◎" href="/mensagens.php">Mensagens</a></li><li><a data-icon="⌕" href="desenvolvedores.php">Desenvolvedores</a></li><li><a data-icon="◴" href="/historico.php">Histórico</a></li><li><a data-icon="♙" href="/perfil.php">Seu perfil</a></li><li class="menu-sair"><a data-icon="⇤" href="../logout.php">Sair</a></li></ul></nav></aside>
<main class="dashboard-main">
<div class="dashboard-topbar"><button class="mobile-menu-button" type="button" data-menu-toggle>☰</button><div class="dashboard-search"><input type="search" placeholder="Procurar nos seus projetos..." data-live-search=".project-row"></div><a class="botao-principal" href="publicar-projeto.php">＋ Publicar projeto</a></div>
<header class="dashboard-header"><div class="dashboard-header-text"><h1>Meus projetos</h1><p>Visualize, edite e acompanhe seus projetos publicados.</p></div></header>
<section class="dashboard-section"><div class="resumo-grid"><article class="resumo-card"><strong><?= count($projetos) ?></strong><p>Projetos publicados</p></article><article class="resumo-card"><strong><?= count(array_filter($projetos,fn($p)=>in_array($p['status'],['aberto','em_negociacao'],true))) ?></strong><p>Ativos</p></article><article class="resumo-card"><strong><?= count(array_filter($projetos,fn($p)=>($p['status']??'')==='concluido')) ?></strong><p>Finalizados</p></article></div></section>
<section class="dashboard-section"><h2>Todos os projetos</h2><?php if(empty($projetos)): ?><div class="projetos-vazio">Você ainda não possui projetos publicados.</div><?php else: ?><div class="projetos-lista">
<?php foreach($projetos as $p): $pid=(int)$p['id'];$total=(int)$p['total_propostas'];$cid=(int)($p['contratacao_id']??0);$categoria=(string)($p['categoria']??'outro'); ?>
<article class="project-row">
<div class="project-thumb" data-category="<?= eMeusProj($categoria) ?>"><?= strtoupper(substr((string)$p['titulo'],0,1)) ?></div>
<div class="project-content"><h3><?= eMeusProj((string)$p['titulo']) ?></h3><div class="project-meta-line"><span>◉ <?= eMeusProj($cat[$categoria]??$categoria) ?></span><span>•</span><span><?= moneyMeusProj((float)$p['orcamento_min']) ?> - <?= moneyMeusProj((float)$p['orcamento_max']) ?></span><span>•</span><span>▣ <?= eMeusProj(dateMeusProj((string)$p['prazo_desejado'])) ?></span></div><span class="project-chip"><?= $total ?> proposta(s) · <?= eMeusProj($status[$p['status']]??(string)$p['status']) ?></span></div>
<div class="project-actions"><a class="botao-principal" href="propostas.php?projeto=<?= $pid ?>">Ver propostas<?= $total>0?' ('.$total.')':'' ?></a><?php if(in_array($p['status'],['rascunho','aberto'],true)&&$total===0): ?><a class="botao-secundario" href="editar-projeto.php?id=<?= $pid ?>">Editar</a><?php endif; ?><?php if($cid>0): ?><a class="botao-secundario" href="/contratacao.php?id=<?= $cid ?>">Acompanhar</a><?php endif; ?></div>
</article>
<?php endforeach; ?></div><?php endif; ?></section>
</main></div></body></html>
