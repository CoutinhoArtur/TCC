<?php
// ==========================================================
// VIEW: ACOMPANHAMENTO DA CONTRATAÇÃO
// ==========================================================
// Mantém todas as regras existentes e apresenta o fluxo em
// blocos visuais mais próximos da linguagem do protótipo.
// ==========================================================
require_once __DIR__ . '/partials/fluxo-ajuda.php';
function eContrato(string $v): string { return htmlspecialchars($v,ENT_QUOTES,'UTF-8'); }
function moneyContrato(float $v): string { return 'R$ '.number_format($v,2,',','.'); }
function dataContrato(?string $v): string { if(!$v)return '—'; try{return (new DateTime($v))->format('d/m/Y H:i');}catch(Throwable){return $v;} }
$statusNomes=['ativa'=>'Aguardando início','em_andamento'=>'Em andamento','aguardando_confirmacao'=>'Aguardando confirmação','ajustes_solicitados'=>'Ajustes solicitados','concluida'=>'Concluída','cancelada'=>'Cancelada'];
$tipo=(string)($usuario['tipo']??'');$status=(string)($contratacao['contratacao_status']??'');
$voltar=$tipo==='cliente'?'/cliente/projetos-contratados.php':'/desenvolvedor/minhas-propostas.php';
// Informações exclusivamente visuais, derivadas dos status existentes.
$temAjustes=trim((string)($contratacao['ultima_solicitacao_ajustes']??''))!=='';
$temAvaliacao=!empty($contratacao['avaliacao_id']);
$orientacao=smOrientacaoContratacao($status,$tipo,$temAvaliacao,$temAjustes);
$passoAtual=smPassoContratacao($status);
$etapas=['Aceite','Em desenvolvimento','Entrega e revisão','Concluído'];
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Acompanhamento - SiteMatch</title><link rel="stylesheet" href="/css/global.css"><link rel="stylesheet" href="/css/dashboard.css"><link rel="stylesheet" href="/css/functional.css"><script src="/js/ui.js" defer></script>    <!-- Acabamento visual compartilhado; nenhuma regra de negócio é executada aqui. -->
    <link rel="stylesheet" href="/css/polish.css">
</head>
<body class="decorated-page"><main class="standalone-page"><div class="standalone-container">
<a class="back-link" href="<?= eContrato($voltar) ?>">← Voltar</a>
<header class="page-header"><div><h1><?= eContrato((string)$contratacao['projeto_titulo']) ?></h1><p>Acompanhamento da contratação #<?= (int)$contratacao['contratacao_id'] ?></p></div><span class="projeto-status"><?= eContrato($statusNomes[$status]??$status) ?></span></header>
<?php if($mensagemSucesso!==''): ?><div class="alerta-sucesso" data-auto-hide><?= eContrato($mensagemSucesso) ?></div><?php endif; ?><?php if($mensagemErro!==''): ?><div class="alerta-erro"><?= eContrato($mensagemErro) ?></div><?php endif; ?>
<!-- Linha do tempo informativa: os botões e transições permanecem no PHP original. -->
<section aria-label="Etapas do projeto">
    <h2 style="margin-bottom:10px;">Etapas da contratação</h2>
    <ol class="sm-flow-steps">
    <?php foreach($etapas as $indice=>$etapa): $numero=$indice+1; $classe=$passoAtual>$numero?'is-complete':($passoAtual===$numero?'is-current':''); ?>
        <li class="sm-flow-step <?= eContrato($classe) ?>" <?= $passoAtual===$numero?'aria-current="step"':'' ?>><span><?= $passoAtual>$numero?'✓':$numero ?></span><strong><?= eContrato($etapa) ?></strong></li>
    <?php endforeach; ?>
    </ol>
    <div class="sm-next-action" role="status"><div><strong>O que acontece agora?</strong><?= eContrato($orientacao) ?></div></div>
</section>
<section style="margin-bottom:34px;"><div class="resumo-grid"><article class="resumo-card"><strong><?= moneyContrato((float)$contratacao['valor_final']) ?></strong><p>Valor final</p></article><article class="resumo-card"><strong><?= (int)$contratacao['prazo_final_dias'] ?> dias</strong><p>Prazo acordado</p></article><article class="resumo-card"><strong><?= $tipo==='cliente'?eContrato((string)$contratacao['desenvolvedor_nome']):eContrato((string)$contratacao['cliente_nome']) ?></strong><p><?= $tipo==='cliente'?'Desenvolvedor':'Cliente' ?></p></article></div></section>
<section style="margin-bottom:32px;"><h2 style="margin-bottom:16px;">Condições contratadas</h2><div class="form-card"><h3>Proposta aceita</h3><p style="margin-top:8px;line-height:1.65;"><?= nl2br(eContrato((string)$contratacao['proposta_descricao'])) ?></p><h3 style="margin-top:22px;">Entregáveis</h3><p style="margin-top:8px;line-height:1.65;"><?= nl2br(eContrato((string)($contratacao['entregaveis']??'Não informado.'))) ?></p><div class="projeto-meta" style="margin-top:22px;"><span>Aceite: <strong><?= eContrato(dataContrato($contratacao['data_aceite']??null)) ?></strong></span><span>Início: <strong><?= eContrato(dataContrato($contratacao['data_inicio']??null)) ?></strong></span><span>Entrega: <strong><?= eContrato(dataContrato($contratacao['data_entrega']??null)) ?></strong></span><span>Conclusão: <strong><?= eContrato(dataContrato($contratacao['data_conclusao']??null)) ?></strong></span></div></div></section>
<section style="margin-bottom:32px;"><h2 style="margin-bottom:16px;">Ações</h2><div class="form-card"><div class="acoes-inline"><a class="botao-secundario" href="/chat.php?proposta=<?= (int)$contratacao['proposta_id'] ?>">Abrir conversa</a><?php if($tipo==='desenvolvedor'): ?><a class="botao-secundario" href="/desenvolvedor/projeto.php?id=<?= (int)$contratacao['projeto_id'] ?>#briefing-completo">Consultar briefing completo</a><?php endif; ?><?php if($tipo==='cliente'): ?><a class="botao-secundario" href="/desenvolvedor-publico.php?id=<?= (int)$contratacao['desenvolvedor_id'] ?>">Perfil do desenvolvedor</a><?php endif; ?>
<?php if($tipo==='desenvolvedor'&&$status==='ativa'): ?><form method="POST" action="/contratacao-acao.php" data-confirm="Iniciar esta contratação?"><input type="hidden" name="contratacao_id" value="<?= (int)$contratacao['contratacao_id'] ?>"><input type="hidden" name="acao" value="iniciar"><button class="botao-principal">Iniciar projeto</button></form><?php endif; ?>
<?php if($tipo==='desenvolvedor'&&$status==='em_andamento'): ?><form method="POST" action="/contratacao-acao.php" data-confirm="Sinalizar que o projeto foi entregue ao cliente?"><input type="hidden" name="contratacao_id" value="<?= (int)$contratacao['contratacao_id'] ?>"><input type="hidden" name="acao" value="entregar"><button class="botao-principal">Sinalizar entrega</button></form><?php endif; ?>
<?php if($tipo==='cliente'&&$status==='aguardando_confirmacao'): ?><form method="POST" action="/contratacao-acao.php" data-confirm="Confirmar a conclusão deste projeto?"><input type="hidden" name="contratacao_id" value="<?= (int)$contratacao['contratacao_id'] ?>"><input type="hidden" name="acao" value="concluir"><button class="botao-principal">Confirmar conclusão</button></form><?php endif; ?></div>
<?php if($tipo==='cliente'&&$status==='aguardando_confirmacao'): ?><form method="POST" action="/contratacao-acao.php" style="margin-top:22px;" data-confirm="Enviar a solicitação de ajustes e devolver o projeto para em andamento?"><input type="hidden" name="contratacao_id" value="<?= (int)$contratacao['contratacao_id'] ?>"><input type="hidden" name="acao" value="ajustes"><div class="form-campo"><label for="ajustes">Solicitar ajustes</label><textarea id="ajustes" name="ajustes" required data-autogrow placeholder="Descreva claramente o que precisa ser ajustado."></textarea></div><div class="form-acoes"><button class="botao-secundario" type="submit">Solicitar ajustes</button></div></form><?php endif; ?>
<?php $ajustePendente=trim((string)($contratacao['ultima_solicitacao_ajustes']??'')); if($tipo==='desenvolvedor'&&$status==='em_andamento'&&$ajustePendente!==''): $textoAjuste=preg_replace('/^Solicitação de ajustes:\s*/u','',$ajustePendente)??$ajustePendente; ?><div class="alerta-info" style="margin-top:18px;"><strong>O cliente solicitou ajustes.</strong><p style="margin-top:6px;"><?= eContrato($textoAjuste) ?></p><p style="margin-top:6px;">Após realizar os ajustes, sinalize uma nova entrega.</p></div><?php endif; ?>
<?php if($status==='aguardando_confirmacao'&&$tipo==='desenvolvedor'): ?><div class="alerta-info" style="margin-top:18px;">A entrega foi sinalizada. Aguarde a confirmação do cliente ou uma solicitação de ajustes.</div><?php endif; ?><?php if($status==='concluida'): ?><div class="alerta-sucesso" style="margin-top:18px;">Esta contratação foi concluída.</div><?php endif; ?></div></section>
<?php if($tipo==='cliente'&&$status==='concluida'): ?><section><h2 style="margin-bottom:16px;">Avaliação do desenvolvedor</h2><?php if(!empty($contratacao['avaliacao_id'])): ?><div class="form-card"><strong style="font-size:20px;">★ <?= (int)$contratacao['avaliacao_nota'] ?>/5</strong><p style="margin-top:8px;"><?= eContrato((string)($contratacao['avaliacao_comentario']??'')) ?></p></div><?php else: ?><form method="POST" action="/contratacao-acao.php" class="form-card" data-loading><input type="hidden" name="contratacao_id" value="<?= (int)$contratacao['contratacao_id'] ?>"><input type="hidden" name="acao" value="avaliar"><div class="form-campo"><label for="nota">Nota</label><select id="nota" name="nota" required><option value="">Selecione</option><option value="5">5 - Excelente</option><option value="4">4 - Muito bom</option><option value="3">3 - Bom</option><option value="2">2 - Regular</option><option value="1">1 - Ruim</option></select></div><div class="form-campo" style="margin-top:14px;"><label for="comentario">Comentário</label><textarea id="comentario" name="comentario" required data-autogrow></textarea></div><div class="form-acoes"><button class="botao-principal" type="submit">Enviar avaliação</button></div></form><?php endif; ?></section><?php endif; ?>
</div></main></body></html>
