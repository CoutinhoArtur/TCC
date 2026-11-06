<?php
/** @var array<string,mixed>|null $usuario */
/** @var array<int,array<string,mixed>>|null $propostas */
$usuario = $usuario ?? [];
$propostas = $propostas ?? [];
$mensagemExecucao = $mensagemExecucao ?? '';
$erroExecucao = $erroExecucao ?? '';
function eMinProp(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function moneyMinProp(float $v): string { return 'R$ '.number_format($v,2,',','.'); }
$statusNomes = [
    'preliminar'=>'Proposta preliminar','em_negociacao'=>'Em negociação',
    'aguardando_revisao'=>'Aguardando revisão','definitiva'=>'Proposta definitiva',
    'aceita'=>'Proposta aceita','recusada'=>'Proposta recusada',
    'encerrada'=>'Encerrada','cancelada'=>'Cancelada'
];
$contratacaoNomes = [
    'ativa'=>'Aguardando início','em_andamento'=>'Em andamento',
    'aguardando_confirmacao'=>'Aguardando confirmação',
    'ajustes_solicitados'=>'Ajustes solicitados','concluida'=>'Concluída','cancelada'=>'Cancelada'
];
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Minhas propostas - SiteMatch</title><link rel="stylesheet" href="/css/global.css"><link rel="stylesheet" href="/css/dashboard.css"><link rel="stylesheet" href="/css/functional.css"></head><body>
<div class="dashboard">
<aside class="sidebar"><div class="sidebar-logo">Site<span>Match</span></div><div class="sidebar-user"><strong><?= eMinProp((string)($usuario['nome']??'')) ?></strong><span>Desenvolvedor</span></div><nav class="sidebar-nav"><ul><li><a href="dashboard.php">Início</a></li><li><a href="explorar-projetos.php">Explorar projetos</a></li><li><a class="ativo" href="minhas-propostas.php">Minhas propostas</a></li><li><a href="/mensagens.php">Mensagens</a></li><li><a href="/historico.php">Histórico</a></li><li><a href="/perfil.php">Meu perfil</a></li><li class="menu-sair"><a href="../logout.php">Sair</a></li></ul></nav></aside>
<main class="dashboard-main"><header class="dashboard-header"><div class="dashboard-header-text"><h1>Minhas propostas</h1><p>Acompanhe negociações, propostas aceitas e contratações.</p></div><a class="botao-principal" href="explorar-projetos.php">Explorar projetos</a></header>
<section class="dashboard-section"><div class="resumo-grid"><article class="resumo-card"><strong><?= count($propostas) ?></strong><p>Propostas enviadas</p></article></div></section>
<?php if($mensagemExecucao!==''): ?><div class="alerta-sucesso"><?= eMinProp((string)$mensagemExecucao) ?></div><?php endif; ?><?php if($erroExecucao!==''): ?><div class="alerta-erro"><?= eMinProp((string)$erroExecucao) ?></div><?php endif; ?>
<section class="dashboard-section"><h2>Todas as propostas</h2><?php if(empty($propostas)): ?><div class="projetos-vazio">Você ainda não enviou nenhuma proposta.</div><?php else: ?><div class="lista-simples"><?php foreach($propostas as $p): ?><?php $status=(string)($p['status']??'');$cid=(int)($p['contratacao_id']??0);$cstatus=(string)($p['contratacao_status']??''); ?><article class="projeto-card"><div class="projeto-card-topo"><div><h3><?= eMinProp((string)$p['projeto_titulo']) ?></h3><p style="margin-top:5px;color:var(--cor-texto-secundario);">Cliente: <strong><?= eMinProp((string)$p['cliente_nome']) ?></strong></p></div><span class="projeto-status"><?= eMinProp($statusNomes[$status]??$status) ?></span></div><div class="projeto-meta" style="margin-top:18px;"><span>Valor: <strong><?= moneyMinProp((float)$p['valor']) ?></strong></span><span>Prazo: <strong><?= (int)$p['prazo_estimado'] ?> dias</strong></span><span>Mensagens: <strong><?= (int)$p['total_mensagens'] ?></strong></span><?php if($cid>0): ?><span>Contratação: <strong><?= eMinProp($contratacaoNomes[$cstatus]??$cstatus) ?></strong></span><?php endif; ?></div><p style="margin-top:18px;line-height:1.5;"><?= nl2br(eMinProp((string)$p['descricao'])) ?></p><div class="acoes-inline"><a class="botao-secundario" href="projeto.php?id=<?= (int)$p['projeto_id'] ?>">Ver projeto</a><a class="botao-secundario" href="/chat.php?proposta=<?= (int)$p['id'] ?>">Conversar</a><?php if(in_array($status,['preliminar','em_negociacao','aguardando_revisao'],true)): ?><a class="botao-principal" href="proposta-definitiva.php?proposta=<?= (int)$p['id'] ?>"><?= $status==='aguardando_revisao'?'Revisar proposta':'Finalizar proposta' ?></a><?php endif; ?><?php if($cid>0): ?><a class="botao-principal" href="/contratacao.php?id=<?= $cid ?>">Acompanhar contratação</a><?php endif; ?></div></article><?php endforeach; ?></div><?php endif; ?></section>
</main></div></body></html>
