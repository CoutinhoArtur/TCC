<?php

// ==========================================================
// VIEW: DASHBOARD DO CLIENTE
// ==========================================================
//
// Interface principal do cliente.
//
// Os dados são preparados em:
//
// public/cliente/dashboard.php
//
// Variáveis recebidas:
//
// $usuario
// $resumo
// $projetosRecentes
//
// RF06 - Publicação de projetos
// RF07 - Gerenciamento de projetos
// RF11 - Gerenciamento de propostas
// RF13 - Comunicação
// ==========================================================


/**
 * @var array<string, mixed>|null $usuario
 */

/**
 * @var array<string, int>|null $resumo
 */

/**
 * @var array<int, array<string, mixed>>|null $projetosRecentes
 */


// ----------------------------------------------------------
// NORMALIZAR VARIÁVEIS
// ----------------------------------------------------------

$usuario = $usuario ?? [];

$resumo = $resumo ?? [];

$projetosRecentes = $projetosRecentes ?? [];


// ----------------------------------------------------------
// NOME DO CLIENTE
// ----------------------------------------------------------

$nomeUsuario = (string) (
    $usuario['nome'] ?? ''
);


// ----------------------------------------------------------
// FUNÇÃO DE SEGURANÇA
// ----------------------------------------------------------

function escaparDashboardCliente(
    string $valor
): string {

    return htmlspecialchars(
        $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}


// ----------------------------------------------------------
// NOMES AMIGÁVEIS DAS CATEGORIAS
// ----------------------------------------------------------

$nomesCategorias = [

    'site' =>
        'Site',

    'sistema_web' =>
        'Sistema web',

    'aplicativo_mobile' =>
        'Aplicativo mobile',

    'sistema_desktop' =>
        'Sistema para computador',

    'outro' =>
        'Outro'
];


// ----------------------------------------------------------
// NOMES AMIGÁVEIS DOS STATUS
// ----------------------------------------------------------

$statusAmigaveis = [

    'aberto' =>
        'Aberto',

    'em_negociacao' =>
        'Em negociação',

    'contratado' =>
        'Contratado',

    'em_andamento' =>
        'Em andamento',

    'aguardando_confirmacao' =>
        'Aguardando confirmação',

    'concluido' =>
        'Concluído',

    'cancelado' =>
        'Cancelado'
];

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Área do Cliente - SiteMatch
    </title>


    <!-- CSS GLOBAL -->

    <link
        rel="stylesheet"
        href="/css/global.css"
    >


    <!-- CSS DO DASHBOARD -->

    <link
        rel="stylesheet"
        href="/css/dashboard.css"
    >
    <link rel="stylesheet" href="/css/functional.css">

</head>


<body>


    <div class="dashboard">


        <!-- =================================================
             MENU LATERAL
             ================================================= -->

        <aside class="sidebar">


            <!-- LOGO -->

            <div class="sidebar-logo">

                Site<span>Match</span>

            </div>


            <!-- USUÁRIO -->

            <div class="sidebar-user">

                <strong>

                    <?= escaparDashboardCliente(
                        $nomeUsuario
                    ) ?>

                </strong>


                <span>
                    Cliente
                </span>

            </div>


            <!-- NAVEGAÇÃO -->

            <nav class="sidebar-nav">

                <ul>


                    <li>

                        <a
                            href="dashboard.php"
                            class="ativo"
                        >
                            Início
                        </a>

                    </li>


                    <li>

                        <a href="meus-projetos.php">
    Meus projetos
</a>

                    </li>

                    <li>
    <a href="projetos-contratados.php">
        Contratações
    </a>
</li>


                    <li>

                        <a href="/mensagens.php">
    Mensagens
</a>

                    </li>


                    <li><a href="desenvolvedores.php">Desenvolvedores</a></li>
                    <li><a href="/historico.php">Histórico</a></li>
                    <li><a href="/perfil.php">Meu perfil</a></li>


                    <li class="menu-sair">

                        <a href="../logout.php">
                            Sair
                        </a>

                    </li>

                </ul>

            </nav>

        </aside>


        <!-- =================================================
             CONTEÚDO PRINCIPAL
             ================================================= -->

        <main class="dashboard-main">


            <!-- =============================================
                 CABEÇALHO
                 ============================================= -->

            <header class="dashboard-header">


                <div class="dashboard-header-text">

                    <h1>

                        Olá,
                        <?= escaparDashboardCliente(
                            $nomeUsuario
                        ) ?>!

                    </h1>


                    <p>

                        Encontre profissionais para transformar
                        sua ideia em um projeto de software.

                    </p>

                </div>


                <!-- RF06 - PUBLICAR PROJETO -->

                <a
                    href="publicar-projeto.php"
                    class="botao-principal"
                >

                    + Publicar projeto

                </a>

            </header>


            <!-- =================================================
                 RESUMO
                 ================================================= -->

            <section class="dashboard-section">

                <h2>
                    Resumo
                </h2>


                <div class="resumo-grid">


                    <!-- PROJETOS ATIVOS -->

                    <article class="resumo-card">

                        <strong>

                            <?= (int) (
                                $resumo['projetos_ativos']
                                ?? 0
                            ) ?>

                        </strong>

                        <p>
                            Projetos ativos
                        </p>

                    </article>


                    <!-- PROPOSTAS RECEBIDAS -->

                    <article class="resumo-card">

                        <strong>

                            <?= (int) (
                                $resumo['propostas_recebidas']
                                ?? 0
                            ) ?>

                        </strong>

                        <p>
                            Propostas recebidas
                        </p>

                    </article>


                    <!-- EM ANDAMENTO -->

                    <article class="resumo-card">

                        <strong>

                            <?= (int) (
                                $resumo['em_andamento']
                                ?? 0
                            ) ?>

                        </strong>

                        <p>
                            Em andamento
                        </p>

                    </article>

                </div>

            </section>


            <!-- =================================================
                 PROJETOS RECENTES
                 ================================================= -->

            <section
                class="dashboard-section"
                id="projetos-recentes"
            >

                <h2>
                    Projetos recentes
                </h2>


                <?php if (empty($projetosRecentes)): ?>


                    <!-- SEM PROJETOS -->

                    <div class="projetos-vazio">

                        <p>
                            Você ainda não possui projetos publicados.
                        </p>

                    </div>


                <?php else: ?>


                    <div class="projetos-lista">


                        <?php foreach (
                            $projetosRecentes
                            as $projeto
                        ): ?>


                            <?php

                            // ----------------------------------
                            // PREPARAR DADOS
                            // ----------------------------------

                            $projetoId = (int) (
                                $projeto['id'] ?? 0
                            );


                            $categoria = (string) (
                                $projeto['categoria'] ?? ''
                            );


                            $nomeCategoria =
                                $nomesCategorias[$categoria]
                                ?? $categoria;


                            $status = (string) (
                                $projeto['status'] ?? ''
                            );


                            $statusExibicao =
                                $statusAmigaveis[$status]
                                ?? $status;


                            $totalPropostas = (int) (
                                $projeto['total_propostas']
                                ?? 0
                            );

                            $contratacaoId = (int) (
                                $projeto['contratacao_id']
                                ?? 0
                            );

                            ?>


                            <article class="projeto-card">


                                <!-- =========================
                                     CABEÇALHO DO PROJETO
                                     ========================= -->

                                <div class="projeto-card-topo">


                                    <h3>

                                        <?= escaparDashboardCliente(
                                            (string) (
                                                $projeto['titulo']
                                                ?? ''
                                            )
                                        ) ?>

                                    </h3>


                                    <span class="projeto-status">

                                        <?= escaparDashboardCliente(
                                            $statusExibicao
                                        ) ?>

                                    </span>

                                </div>


                                <!-- =========================
                                     INFORMAÇÕES
                                     ========================= -->

                                <div class="projeto-meta">


                                    <span>

                                        Categoria:

                                        <strong>

                                            <?= escaparDashboardCliente(
                                                $nomeCategoria
                                            ) ?>

                                        </strong>

                                    </span>


                                    <span>

                                        Propostas:

                                        <strong>

                                            <?= $totalPropostas ?>

                                        </strong>

                                    </span>

                                </div>


                                <!-- =========================
                                     AÇÕES
                                     ========================= -->

                                <div
                                    style="
                                        display: flex;
                                        gap: 12px;
                                        flex-wrap: wrap;
                                        margin-top: 20px;
                                    "
                                >


                                    <!--
                                        RF11 - Propostas recebidas

                                        Enviamos projetos.id
                                        através da URL.
                                    -->

                                    <?php if ($projetoId > 0): ?>

                                        <a
                                            href="propostas.php?projeto=<?= $projetoId ?>"
                                            class="botao-principal"
                                        >

                                            <?php if ($totalPropostas > 0): ?>

                                                Ver propostas
                                                (<?= $totalPropostas ?>)

                                            <?php else: ?>

                                                Ver projeto

                                            <?php endif; ?>

                                        </a>

                                    <?php endif; ?>

                                    <?php if ($contratacaoId > 0): ?>

                                        <a
                                            href="/contratacao.php?id=<?= $contratacaoId ?>"
                                            class="botao-principal"
                                        >
                                            Acompanhar contratação
                                        </a>

                                    <?php endif; ?>


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