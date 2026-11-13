<?php
// ==========================================================
// VIEW: DETALHE DO PROJETO
// ==========================================================
// Visual em duas colunas, seguindo o protótipo de detalhe.
// O briefing completo continua disponível na mesma página.
// ==========================================================
function escaparProjeto(string $valor): string { return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); }
function formatarDinheiroProjeto(float $valor): string { return 'R$ '.number_format($valor,2,',','.'); }
function formatarDataProjetoDetalhe(string $data): string { $o=DateTime::createFromFormat('Y-m-d',$data); return $o?$o->format('d/m/Y'):$data; }
$nomesCategorias=['site'=>'Site institucional','sistema_web'=>'Sistema web','aplicativo_mobile'=>'Aplicativo mobile','sistema_desktop'=>'Sistema desktop','outro'=>'Outro'];
$categoria=(string)($projeto['categoria']??'outro');
$nomeCategoria=$nomesCategorias[$categoria]??$categoria;
$orcamentoMin=(float)($projeto['orcamento_min']??0);
$orcamentoMax=(float)($projeto['orcamento_max']??0);
$prazo=(string)($projeto['prazo_desejado']??'');
$statusProjetoNomes=['rascunho'=>'Rascunho','aberto'=>'Aberto','em_negociacao'=>'Em negociação','contratado'=>'Contratado','em_andamento'=>'Em andamento','aguardando_confirmacao'=>'Aguardando confirmação','concluido'=>'Concluído','cancelado'=>'Cancelado'];
$requisitosTexto=(string)($projeto['funcionalidades']??'');
$requisitos=array_values(array_filter(array_map('trim', preg_split('/[;\n]+/u',$requisitosTexto) ?: [])));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Projeto - SiteMatch</title>
    <link rel="stylesheet" href="/css/global.css"><link rel="stylesheet" href="/css/functional.css"><script src="/js/ui.js" defer></script>
</head>
<body class="decorated-page">
    <header style="height:55px;border-bottom:1px solid #ddd9e0;background:#fff;display:flex;align-items:center;padding:0 38px;gap:58px;">
        <a class="site-logo" href="/desenvolvedor/dashboard.php"><span class="brand-mark"></span><span>Site<span>Match</span></span></a>
        <nav style="display:flex;gap:46px;font-weight:700;"><a href="/desenvolvedor/dashboard.php">Início</a><a href="/desenvolvedor/explorar-projetos.php">Explorar</a><a href="/perfil.php">Meu perfil</a></nav>
    </header>
    <main class="standalone-page" style="padding-top:54px;">
        <div class="standalone-container">
            <a class="back-link" href="explorar-projetos.php">← Voltar</a>
            <section class="project-detail-card">
                <div>
                    <div class="project-detail-title"><h1><?= escaparProjeto((string)($projeto['titulo']??'')) ?></h1><span class="projeto-status"><?= escaparProjeto($nomeCategoria) ?></span></div>
                    <div class="project-detail-visual"><?= strtoupper(substr((string)($projeto['titulo']??'P'),0,1)) ?></div>
                    <div class="acoes-inline" style="margin-top:30px;">
                        <?php if (($projeto['status']??'')==='aberto' && empty($projeto['minha_proposta_id'])): ?><a class="botao-principal" style="min-width:100%;font-size:18px;" href="enviar-proposta.php?id=<?= (int)($projeto['id']??0) ?>">Enviar proposta</a>
                        <?php elseif(!empty($projeto['minha_proposta_id'])): ?><a class="botao-principal" href="enviar-proposta.php?id=<?= (int)($projeto['id']??0) ?>">Ver minha proposta</a><a class="botao-secundario" href="/chat.php?proposta=<?= (int)$projeto['minha_proposta_id'] ?>">Conversar</a>
                        <?php else: ?><span class="muted">Este projeto não está mais recebendo propostas.</span><?php endif; ?>
                    </div>
                </div>
                <div>
                    <div class="project-detail-stats">
                        <div class="project-detail-stat money"><?= formatarDinheiroProjeto($orcamentoMin) ?> - <?= formatarDinheiroProjeto($orcamentoMax) ?></div>
                        <div class="project-detail-stat time"><?= escaparProjeto(formatarDataProjetoDetalhe($prazo)) ?></div>
                        <div class="project-detail-stat proposals"><?= (int)($projeto['total_propostas']??0) ?> proposta(s)</div>
                    </div>
                    <p class="project-detail-description"><?= nl2br(escaparProjeto((string)($projeto['descricao_resumida']??''))) ?></p>
                    <div class="project-requirements">
                        <h2>Requisitos</h2>
                        <?php if(empty($requisitos)): ?><p class="muted">Requisitos não informados.</p><?php else: ?><ul><?php foreach($requisitos as $req): ?><li><?= escaparProjeto($req) ?></li><?php endforeach; ?></ul><?php endif; ?>
                    </div>
                    <div class="form-card" style="margin-top:30px;padding:20px 22px;">
                        <strong>Publicado por <?= escaparProjeto((string)($projeto['nome_cliente']??'')) ?></strong>
                        <?php if(!empty($projeto['publico_alvo'])): ?><p style="margin-top:10px;"><strong>Público:</strong> <?= nl2br(escaparProjeto((string)$projeto['publico_alvo'])) ?></p><?php endif; ?>
                        <?php if(!empty($projeto['condicoes_regras'])): ?><p style="margin-top:10px;"><strong>Regras:</strong> <?= nl2br(escaparProjeto((string)$projeto['condicoes_regras'])) ?></p><?php endif; ?>
                        <?php if(!empty($projeto['identidade_visual'])): ?><p style="margin-top:10px;"><strong>Identidade visual:</strong> <?= nl2br(escaparProjeto((string)$projeto['identidade_visual'])) ?></p><?php endif; ?>
                        <?php if(!empty($projeto['referencias_visuais'])): ?><p style="margin-top:10px;"><strong>Referências:</strong> <?= nl2br(escaparProjeto((string)$projeto['referencias_visuais'])) ?></p><?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
