<?php
// ==========================================================
// VIEW: DETALHE DO PROJETO
// ==========================================================
// Visual em duas colunas, seguindo o protótipo de detalhe.
// O briefing completo continua disponível na mesma página.
// ==========================================================
require_once __DIR__ . '/../partials/visual-helpers.php';

function escaparProjeto(string $valor): string { return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); }
function formatarDinheiroProjeto(float $valor): string { return 'R$ '.number_format($valor,2,',','.'); }
function formatarDataProjetoDetalhe(string $data): string { $o=DateTime::createFromFormat('Y-m-d',$data); return $o?$o->format('d/m/Y'):$data; }
$nomesCategorias=['site'=>'Site institucional','sistema_web'=>'Sistema web','aplicativo_mobile'=>'Aplicativo mobile','sistema_desktop'=>'Sistema desktop','outro'=>'Outro'];
$categoria=(string)($projeto['categoria']??'outro');
$nomeCategoria=$nomesCategorias[$categoria]??$categoria;
$orcamentoMin=(float)($projeto['orcamento_min']??0);
$orcamentoMax=(float)($projeto['orcamento_max']??0);
$prazo=(string)($projeto['prazo_desejado']??'');
$requisitosTexto=(string)($projeto['funcionalidades']??'');
$requisitos=array_values(array_filter(array_map('trim', preg_split('/[;\n]+/u',$requisitosTexto) ?: [])));
$visualProjeto = smProjectVisual(
    (string)($projeto['titulo'] ?? ''),
    $categoria,
    (string)($projeto['descricao_resumida'] ?? '')
);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Projeto - SiteMatch</title>
    <link rel="stylesheet" href="/css/global.css"><link rel="stylesheet" href="/css/functional.css"><script src="/js/ui.js" defer></script>
    <!-- Acabamento visual compartilhado; nenhuma regra de negócio é executada aqui. -->
    <link rel="stylesheet" href="/css/polish.css">
</head>
<body class="decorated-page">
    <header style="height:55px;border-bottom:1px solid #ddd9e0;background:#fff;display:flex;align-items:center;padding:0 38px;gap:58px;">
        <a class="site-logo" href="/desenvolvedor/dashboard.php"><span class="brand-mark"></span><span>Site<span>Match</span></span></a>
        <nav style="display:flex;gap:46px;font-weight:700;"><a href="/desenvolvedor/dashboard.php">Início</a><a href="/desenvolvedor/explorar-projetos.php">Explorar</a><a href="/perfil.php">Meu perfil</a></nav>
    </header>
    <main class="standalone-page" style="padding-top:54px;">
        <div class="standalone-container">
            <a class="back-link" href="explorar-projetos.php">← Voltar</a>
            <!-- Acesso rápido ao briefing completo; dados da versão mais recente já carregados pelo Model. -->
            <div class="sm-briefing-shortcut">
                <div><strong>Briefing completo disponível</strong><p>Consulte o objetivo, público-alvo, funcionalidades, regras, referências e observações antes de desenvolver.</p></div>
                <a class="botao-secundario" href="#briefing-completo">Ler briefing completo ↓</a>
            </div>
            <section class="project-detail-card">
                <div>
                    <div class="project-detail-title"><h1><?= escaparProjeto((string)($projeto['titulo']??'')) ?></h1><span class="projeto-status"><?= escaparProjeto($nomeCategoria) ?></span></div>
                    <div class="project-detail-visual project-detail-visual-image"><img src="<?= escaparProjeto($visualProjeto['src']) ?>" alt="<?= escaparProjeto($visualProjeto['alt']) ?>" loading="lazy"></div>
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
                    </div>
                </div>
            </section>

            <!-- =====================================================
                 BRIEFING COMPLETO DO PROJETO
                 Apenas apresenta os dados já consultados pelo Model.
                 Não permite edição, não altera o briefing nem a proposta.
                 ===================================================== -->
            <section class="sm-briefing-section" id="briefing-completo" aria-labelledby="titulo-briefing">
                <header class="sm-briefing-heading">
                    <div>
                        <span class="sm-briefing-eyebrow">Documento do projeto · Versão <?= (int)($projeto['numero_versao'] ?? 1) ?></span>
                        <h2 id="titulo-briefing">Briefing completo</h2>
                        <p>Informações fornecidas pelo cliente para orientar a proposta e o desenvolvimento.</p>
                    </div>
                    <span class="projeto-status">Somente leitura</span>
                </header>
                <?php
                // Usar somente dados que já existem na versão do briefing retornada pelo Model.
                $camposBriefing = [
                    ['titulo' => 'Objetivo do projeto', 'campo' => 'objetivo', 'destaque' => true],
                    ['titulo' => 'Público-alvo', 'campo' => 'publico_alvo'],
                    ['titulo' => 'Funcionalidades desejadas', 'campo' => 'funcionalidades'],
                    ['titulo' => 'Condições e regras', 'campo' => 'condicoes_regras'],
                    ['titulo' => 'Identidade visual', 'campo' => 'identidade_visual'],
                    ['titulo' => 'Referências visuais', 'campo' => 'referencias_visuais'],
                    ['titulo' => 'Observações adicionais', 'campo' => 'observacoes'],
                ];
                ?>
                <div class="sm-briefing-grid">
                    <?php foreach ($camposBriefing as $campo):
                        $conteudo = trim((string)($projeto[$campo['campo']] ?? ''));
                        $classe = !empty($campo['destaque']) ? ' sm-briefing-field-featured' : '';
                    ?>
                    <article class="sm-briefing-field<?= $classe ?>">
                        <h3><?= escaparProjeto($campo['titulo']) ?></h3>
                        <p<?= $conteudo === '' ? ' class="muted"' : '' ?>><?= $conteudo !== '' ? nl2br(escaparProjeto($conteudo)) : 'Não informado pelo cliente.' ?></p>
                    </article>
                    <?php endforeach; ?>
                </div>
                <div class="sm-briefing-footer">
                    <span><strong>Orçamento:</strong> <?= formatarDinheiroProjeto($orcamentoMin) ?> – <?= formatarDinheiroProjeto($orcamentoMax) ?></span>
                    <span><strong>Prazo desejado:</strong> <?= $prazo !== '' ? escaparProjeto(formatarDataProjetoDetalhe($prazo)) : 'Não informado' ?></span>
                </div>
                <p class="sm-briefing-note">Este briefing descreve a solicitação original do cliente. Após a contratação, confira também os entregáveis e o prazo acordados na proposta aceita.</p>
            </section>
        </div>
    </main>
</body>
</html>
