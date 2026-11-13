<?php
// ==========================================================
// VIEW: DASHBOARD DO DESENVOLVEDOR
// ==========================================================
// Mesma linguagem visual do dashboard do cliente, adaptada às
// oportunidades e contratações do desenvolvedor.
// ==========================================================

/** @var array<string,mixed> $usuario */
/** @var array<string,int> $resumo */
/** @var array<int,array<string,mixed>> $contratacoesAtivas */
/** @var array<int,array<string,mixed>> $projetosDisponiveis */

$contratacoesAtivas = $contratacoesAtivas ?? [];
$projetosDisponiveis = $projetosDisponiveis ?? [];
$nomeUsuario = (string)($usuario['nome'] ?? '');

function escaparDashboardDev(string $valor): string { return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); }
function formatarValorProjeto(float $valor): string { return 'R$ '.number_format($valor,2,',','.'); }
function formatarDataProjeto(string $data): string {
    if ($data==='') return 'Não informado';
    $d = DateTime::createFromFormat('Y-m-d',$data);
    return $d ? $d->format('d/m/Y') : $data;
}
$nomesCategorias = ['site'=>'Site institucional','sistema_web'=>'Sistema web','aplicativo_mobile'=>'Aplicativo mobile','sistema_desktop'=>'Sistema desktop','outro'=>'Outro'];
$statusContratacaoNomes = ['ativa'=>'Aguardando início','em_andamento'=>'Em andamento','aguardando_confirmacao'=>'Aguardando confirmação','ajustes_solicitados'=>'Ajustes solicitados'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Desenvolvedor - SiteMatch</title>
    <link rel="stylesheet" href="/css/global.css"><link rel="stylesheet" href="/css/dashboard.css"><link rel="stylesheet" href="/css/functional.css">
    <script src="/js/ui.js" defer></script>
</head>
<body>
<div class="dashboard">
    <aside class="sidebar">
        <a class="sidebar-logo" href="/desenvolvedor/dashboard.php">Site<span>Match</span></a>
        <div class="sidebar-user"><strong><?= escaparDashboardDev($nomeUsuario) ?></strong><span>Desenvolvedor</span></div>
        <nav class="sidebar-nav"><ul>
            <li><a class="ativo" data-icon="⌂" href="dashboard.php">Início</a></li>
            <li><a data-icon="⌕" href="explorar-projetos.php">Explorar projetos</a></li>
            <li><a data-icon="▣" href="minhas-propostas.php">Minhas propostas</a></li>
            <li><a data-icon="◎" href="/mensagens.php">Mensagens</a></li>
            <li><a data-icon="◴" href="/historico.php">Histórico</a></li>
            <li><a data-icon="♙" href="/perfil.php">Meu perfil</a></li>
            <li class="menu-sair"><a data-icon="⇤" href="../logout.php">Sair</a></li>
        </ul></nav>
    </aside>

    <main class="dashboard-main">
        <div class="dashboard-topbar">
            <button class="mobile-menu-button" type="button" data-menu-toggle aria-expanded="false">☰</button>
            <div class="dashboard-search"><input type="search" placeholder="Procurar projetos..." data-live-search=".project-row"></div>
            <a href="explorar-projetos.php" class="botao-principal">＋ Explorar projetos</a>
        </div>

        <header class="dashboard-header"><div class="dashboard-header-text"><h1>Olá, <?= escaparDashboardDev($nomeUsuario) ?></h1><p>Encontre projetos compatíveis com suas habilidades.</p></div></header>

        <section class="dashboard-section">
            <div class="resumo-grid">
                <article class="resumo-card"><strong><?= (int)($resumo['propostas_enviadas'] ?? 0) ?></strong><p>Propostas enviadas</p></article>
                <article class="resumo-card"><strong><?= (int)($resumo['em_negociacao'] ?? 0) ?></strong><p>Em negociação</p></article>
                <article class="resumo-card"><strong><?= (int)($resumo['projetos_em_andamento'] ?? 0) ?></strong><p>Em andamento</p></article>
            </div>
        </section>

        <?php if (!empty($contratacoesAtivas)): ?>
        <section class="dashboard-section" id="projetos-em-andamento">
            <h2>Meus projetos em andamento</h2>
            <div class="projetos-lista">
                <?php foreach ($contratacoesAtivas as $contratacao):
                    $status = (string)($contratacao['contratacao_status'] ?? '');
                    $ajuste = trim((string)($contratacao['ultima_solicitacao_ajustes'] ?? ''));
                    $categoria = (string)($contratacao['projeto_categoria'] ?? 'outro');
                ?>
                <article class="project-row">
                    <div class="project-thumb" data-category="<?= escaparDashboardDev($categoria) ?>"><?= strtoupper(substr((string)($contratacao['projeto_titulo'] ?? 'P'),0,1)) ?></div>
                    <div class="project-content">
                        <h3><?= escaparDashboardDev((string)($contratacao['projeto_titulo'] ?? '')) ?></h3>
                        <div class="project-meta-line"><span>Cliente: <strong><?= escaparDashboardDev((string)($contratacao['cliente_nome'] ?? '')) ?></strong></span><span>•</span><span><?= formatarValorProjeto((float)($contratacao['valor_final'] ?? 0)) ?></span><span>•</span><span>▣ <?= (int)($contratacao['prazo_final_dias'] ?? 0) ?> dias</span></div>
                        <span class="project-chip"><?= escaparDashboardDev($statusContratacaoNomes[$status] ?? $status) ?></span>
                        <?php if ($ajuste !== ''): ?><div class="alerta-info" style="margin:12px 0 0;"><strong>Ajustes solicitados:</strong> <?= escaparDashboardDev(preg_replace('/^Solicitação de ajustes:\s*/u','',$ajuste) ?? $ajuste) ?></div><?php endif; ?>
                    </div>
                    <div class="project-actions"><a class="botao-principal" href="/contratacao.php?id=<?= (int)($contratacao['contratacao_id'] ?? 0) ?>">Acompanhar</a><a class="botao-secundario" href="/chat.php?proposta=<?= (int)($contratacao['proposta_id'] ?? 0) ?>">Conversar</a></div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="dashboard-section" id="projetos-disponiveis">
            <h2>Projetos disponíveis</h2>
            <?php if (empty($projetosDisponiveis)): ?>
                <div class="projetos-vazio">Não existem projetos disponíveis no momento.</div>
            <?php else: ?>
                <div class="projetos-lista">
                    <?php foreach ($projetosDisponiveis as $projeto):
                        $categoria = (string)($projeto['categoria'] ?? 'outro');
                        $min = (float)($projeto['orcamento_min'] ?? 0);
                        $max = (float)($projeto['orcamento_max'] ?? 0);
                    ?>
                    <article class="project-row">
                        <div class="project-thumb" data-category="<?= escaparDashboardDev($categoria) ?>"><?= strtoupper(substr((string)($projeto['titulo'] ?? 'P'),0,1)) ?></div>
                        <div class="project-content">
                            <h3><?= escaparDashboardDev((string)($projeto['titulo'] ?? '')) ?></h3>
                            <div class="project-meta-line"><span>◉ <?= escaparDashboardDev($nomesCategorias[$categoria] ?? $categoria) ?></span><span>•</span><span><?= formatarValorProjeto($min) ?><?= $max > 0 ? ' - '.formatarValorProjeto($max) : '' ?></span><span>•</span><span>▣ <?= formatarDataProjeto((string)($projeto['prazo_desejado'] ?? '')) ?></span></div>
                            <span class="project-chip"><?= (int)($projeto['total_propostas'] ?? 0) ?> proposta(s)</span>
                        </div>
                        <div class="project-actions"><a class="botao-principal" href="projeto.php?id=<?= (int)($projeto['id'] ?? 0) ?>">Ver projeto</a></div>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
