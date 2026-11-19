<?php
// ==========================================================
// VIEW: DASHBOARD DO CLIENTE
// ==========================================================
// O layout foi aproximado do protótipo: sidebar fixa, busca no
// topo, indicadores em linha e projetos recentes horizontais.
// Os dados e regras continuam vindo dos mesmos Models.
// ==========================================================

/** @var array<string,mixed>|null $usuario */
/** @var array<string,int>|null $resumo */
/** @var array<int,array<string,mixed>>|null $projetosRecentes */

require_once __DIR__ . '/../partials/visual-helpers.php';

$usuario = $usuario ?? [];
$resumo = $resumo ?? [];
$projetosRecentes = $projetosRecentes ?? [];
$nomeUsuario = (string) ($usuario['nome'] ?? '');

function escaparDashboardCliente(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
function dinheiroDashboardCliente(?float $valor): string
{
    return $valor === null ? 'A combinar' : 'R$ ' . number_format($valor, 2, ',', '.');
}

$nomesCategorias = [
    'site'=>'Site institucional',
    'sistema_web'=>'Sistema web',
    'aplicativo_mobile'=>'Aplicativo mobile',
    'sistema_desktop'=>'Sistema desktop',
    'outro'=>'Outro'
];
$statusAmigaveis = [
    'aberto'=>'Aberto','em_negociacao'=>'Em negociação','contratado'=>'Contratado',
    'em_andamento'=>'Em andamento','aguardando_confirmacao'=>'Aguardando confirmação',
    'concluido'=>'Concluído','cancelado'=>'Cancelado'
];
$avatarSidebar = smAvatarFromRecord($usuario, 'cliente');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Cliente - SiteMatch</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="/css/functional.css">
    <script src="/js/ui.js" defer></script>
    <!-- Acabamento visual compartilhado; nenhuma regra de negócio é executada aqui. -->
    <link rel="stylesheet" href="/css/polish.css">
</head>
<body>
<div class="dashboard">
    <aside class="sidebar">
        <a class="sidebar-logo" href="/cliente/dashboard.php">Site<span>Match</span></a>
        <div class="sidebar-user has-avatar">
            <img class="sidebar-user-avatar" src="<?= escaparDashboardCliente($avatarSidebar) ?>" alt="Avatar de <?= escaparDashboardCliente($nomeUsuario) ?>" loading="lazy">
            <div>
                <strong><?= escaparDashboardCliente($nomeUsuario) ?></strong>
                <span>Empreendedor</span>
            </div>
        </div>
        <nav class="sidebar-nav"><ul>
            <li><a class="ativo" data-icon="⌂" href="dashboard.php">Início</a></li>
            <li><a data-icon="▣" href="meus-projetos.php">Meus projetos</a></li>
            <li><a data-icon="◎" href="/mensagens.php">Mensagens</a></li>
            <li><a data-icon="⌕" href="desenvolvedores.php">Desenvolvedores</a></li>
            <li><a data-icon="◴" href="/historico.php">Histórico</a></li>
            <li><a data-icon="♙" href="/perfil.php">Seu perfil</a></li>
            <li class="menu-sair"><a data-icon="⇤" href="../logout.php">Sair</a></li>
        </ul></nav>
    </aside>

    <main class="dashboard-main">
        <!-- Barra superior inspirada no protótipo. A busca filtra apenas cards já carregados. -->
        <div class="dashboard-topbar">
            <button class="mobile-menu-button" type="button" data-menu-toggle aria-expanded="false">☰</button>
            <div class="dashboard-search"><input type="search" placeholder="Procurar nos seus projetos..." data-live-search=".project-row"></div>
            <a href="publicar-projeto.php" class="botao-principal">＋ Publicar projeto</a>
        </div>

        <header class="dashboard-header">
            <div class="dashboard-header-text">
                <h1>Olá, <?= escaparDashboardCliente($nomeUsuario) ?></h1>
                <p>Aqui você acompanha seus projetos e encontra o profissional ideal.</p>
            </div>
        </header>

        <section class="dashboard-section">
            <div class="resumo-grid">
                <article class="resumo-card"><strong><?= (int)($resumo['projetos_ativos'] ?? 0) ?></strong><p>Projetos ativos</p></article>
                <article class="resumo-card"><strong><?= (int)($resumo['propostas_recebidas'] ?? 0) ?></strong><p>Propostas recebidas</p></article>
                <article class="resumo-card"><strong><?= (int)($resumo['em_andamento'] ?? 0) ?></strong><p>Em andamento</p></article>
            </div>
        </section>

        <section class="dashboard-section" id="projetos-recentes">
            <h2>Projetos recentes</h2>
            <?php if (empty($projetosRecentes)): ?>
                <div class="projetos-vazio"><p>Você ainda não possui projetos publicados.</p></div>
            <?php else: ?>
                <div class="projetos-lista">
                    <?php foreach ($projetosRecentes as $projeto):
                        $projetoId = (int)($projeto['id'] ?? 0);
                        $categoria = (string)($projeto['categoria'] ?? 'outro');
                        $nomeCategoria = $nomesCategorias[$categoria] ?? $categoria;
                        $status = (string)($projeto['status'] ?? '');
                        $totalPropostas = (int)($projeto['total_propostas'] ?? 0);
                        $contratacaoId = (int)($projeto['contratacao_id'] ?? 0);
                        $min = isset($projeto['orcamento_min']) ? (float)$projeto['orcamento_min'] : null;
                        $max = isset($projeto['orcamento_max']) ? (float)$projeto['orcamento_max'] : null;
                        $visualProjeto = smProjectVisual(
                            (string)($projeto['titulo'] ?? ''),
                            $categoria,
                            (string)($projeto['descricao_resumida'] ?? '')
                        );
                    ?>
                    <article class="project-row" data-project-card>
                        <div class="project-thumb project-thumb-media" data-category="<?= escaparDashboardCliente($categoria) ?>">
                            <img src="<?= escaparDashboardCliente($visualProjeto['src']) ?>" alt="<?= escaparDashboardCliente($visualProjeto['alt']) ?>" loading="lazy">
                        </div>
                        <div class="project-content">
                            <h3><?= escaparDashboardCliente((string)($projeto['titulo'] ?? '')) ?></h3>
                            <div class="project-meta-line">
                                <span>◉ <?= escaparDashboardCliente($nomeCategoria) ?></span>
                                <span>•</span>
                                <span><?= dinheiroDashboardCliente($min) ?><?= $max !== null ? ' - '.dinheiroDashboardCliente($max) : '' ?></span>
                                <?php if (!empty($projeto['prazo_desejado'])): ?><span>•</span><span>▣ <?= escaparDashboardCliente((string)$projeto['prazo_desejado']) ?></span><?php endif; ?>
                            </div>
                            <span class="project-chip"><?= $totalPropostas ?> proposta(s) · <?= escaparDashboardCliente($statusAmigaveis[$status] ?? $status) ?></span>
                        </div>
                        <div class="project-actions">
                            <a class="botao-principal" href="propostas.php?projeto=<?= $projetoId ?>"><?= $totalPropostas > 0 ? 'Ver propostas' : 'Ver projeto' ?></a>
                            <?php if ($contratacaoId > 0): ?><a class="botao-secundario" href="/contratacao.php?id=<?= $contratacaoId ?>">Acompanhar</a><?php endif; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
