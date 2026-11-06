<?php
function eEditProj(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
$categorias = ['site'=>'Site','sistema_web'=>'Sistema web','aplicativo_mobile'=>'Aplicativo mobile','sistema_desktop'=>'Sistema desktop','outro'=>'Outro'];
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Editar projeto - SiteMatch</title><link rel="stylesheet" href="/css/global.css"><link rel="stylesheet" href="/css/dashboard.css"><link rel="stylesheet" href="/css/functional.css"></head><body><main class="dashboard-main" style="max-width:1000px;margin:0 auto;"><p style="margin-bottom:20px;"><a href="meus-projetos.php">← Voltar para meus projetos</a></p><header class="dashboard-header"><div class="dashboard-header-text"><h1>Editar projeto</h1><p>Atualize os dados antes que o projeto receba propostas.</p></div></header>
<?php if($mensagemSucesso!==''): ?><div class="alerta-sucesso"><?= eEditProj($mensagemSucesso) ?></div><?php endif; ?><?php if($mensagemErro!==''): ?><div class="alerta-erro"><?= eEditProj($mensagemErro) ?></div><?php endif; ?>
<?php if(!$podeEditar): ?><div class="alerta-info">Este projeto já recebeu proposta ou avançou no fluxo. Para preservar as condições negociadas, a edição do briefing fica bloqueada.</div><?php else: ?>
<form method="POST" class="form-card"><div class="form-grid">
<div class="form-campo full"><label for="titulo">Título</label><input id="titulo" name="titulo" maxlength="150" required value="<?= eEditProj((string)$projeto['titulo']) ?>"></div>
<div class="form-campo"><label for="categoria">Categoria</label><select id="categoria" name="categoria" required><?php foreach($categorias as $v=>$r): ?><option value="<?= $v ?>" <?= $projeto['categoria']===$v?'selected':'' ?>><?= $r ?></option><?php endforeach; ?></select></div>
<div class="form-campo"><label for="prazo_desejado">Prazo desejado</label><input id="prazo_desejado" name="prazo_desejado" type="date" required value="<?= eEditProj((string)$projeto['prazo_desejado']) ?>"></div>
<div class="form-campo"><label for="orcamento_min">Orçamento mínimo</label><input id="orcamento_min" name="orcamento_min" type="number" min="0" step="0.01" required value="<?= eEditProj((string)$projeto['orcamento_min']) ?>"></div>
<div class="form-campo"><label for="orcamento_max">Orçamento máximo</label><input id="orcamento_max" name="orcamento_max" type="number" min="0" step="0.01" required value="<?= eEditProj((string)$projeto['orcamento_max']) ?>"></div>
<div class="form-campo full"><label for="descricao_resumida">Descrição / objetivo</label><textarea id="descricao_resumida" name="descricao_resumida" required><?= eEditProj((string)$projeto['descricao_resumida']) ?></textarea></div>
<div class="form-campo full"><label for="publico_alvo">Público-alvo</label><textarea id="publico_alvo" name="publico_alvo" required><?= eEditProj((string)($projeto['publico_alvo']??'')) ?></textarea></div>
<div class="form-campo full"><label for="funcionalidades">Funcionalidades desejadas</label><textarea id="funcionalidades" name="funcionalidades" required><?= eEditProj((string)($projeto['funcionalidades']??'')) ?></textarea></div>
<div class="form-campo full"><label for="condicoes_regras">Condições e regras</label><textarea id="condicoes_regras" name="condicoes_regras"><?= eEditProj((string)($projeto['condicoes_regras']??'')) ?></textarea></div>
<div class="form-campo full"><label for="identidade_visual">Identidade visual</label><textarea id="identidade_visual" name="identidade_visual"><?= eEditProj((string)($projeto['identidade_visual']??'')) ?></textarea></div>
<div class="form-campo full"><label for="referencias_visuais">Referências visuais</label><textarea id="referencias_visuais" name="referencias_visuais"><?= eEditProj((string)($projeto['referencias_visuais']??'')) ?></textarea></div>
<div class="form-campo full"><label for="observacoes">Observações</label><textarea id="observacoes" name="observacoes"><?= eEditProj((string)($projeto['observacoes']??'')) ?></textarea></div>
</div><div class="form-acoes"><button class="botao-principal" type="submit">Salvar alterações</button></div></form>
<?php endif; ?></main></body></html>
