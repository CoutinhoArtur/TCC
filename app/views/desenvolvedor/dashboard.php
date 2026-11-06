<?php

// ==========================================================
// VIEW: DASHBOARD DO DESENVOLVEDOR
// ==========================================================
//
// Esta é a interface principal do desenvolvedor.
//
// Os dados são preparados anteriormente em:
//
// public/desenvolvedor/dashboard.php
//
// Variáveis recebidas:
//
// $usuario
// $resumo
// $projetosDisponiveis
//
// RF08 - Pesquisa de projetos
// RF10 - Envio de propostas
// RF11 - Gerenciamento de propostas
// ==========================================================


/**
 * Usuário autenticado.
 *
 * @var array<string, mixed> $usuario
 */


/**
 * Resumo do dashboard.
 *
 * @var array<string, int> $resumo
 */


/**
 * Projetos disponíveis.
 *
 * @var array<int, array<string, mixed>> $projetosDisponiveis
 */


// ----------------------------------------------------------
// NOME DO USUÁRIO
// ----------------------------------------------------------

$nomeUsuario = (string) (
    $usuario['nome'] ?? ''
);


// ----------------------------------------------------------
// FUNÇÃO DE SEGURANÇA
// ----------------------------------------------------------
//
// Evita que textos vindos do banco sejam interpretados
// como código HTML.
// ----------------------------------------------------------

function escaparDashboardDev(
    string $valor
): string {

    return htmlspecialchars(
        $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}


// ----------------------------------------------------------
// FORMATAR VALORES EM REAL
// ----------------------------------------------------------
//
// Exemplo:
//
// 1500
//
// vira:
//
// R$ 1.500,00
// ----------------------------------------------------------

function formatarValorProjeto(
    float $valor
): string {

    return 'R$ ' . number_format(
        $valor,
        2,
        ',',
        '.'
    );
}


// ----------------------------------------------------------
// FORMATAR DATA
// ----------------------------------------------------------
//
// O MySQL armazena:
//
// 2026-11-19
//
// E mostramos:
//
// 19/11/2026
// ----------------------------------------------------------

function formatarDataProjeto(
    string $data
): string {

    if ($data === '') {
        return 'Não informado';
    }


    $dataObjeto = DateTime::createFromFormat(
        'Y-m-d',
        $data
    );


    if ($dataObjeto === false) {
        return $data;
    }


    return $dataObjeto->format(
        'd/m/Y'
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
        Área do Desenvolvedor - SiteMatch
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


    <!-- =====================================================
         ESTRUTURA PRINCIPAL
         ===================================================== -->

    <div class="dashboard">


        <!-- =================================================
             MENU LATERAL
             ================================================= -->

        <aside class="sidebar">


            <!-- LOGO -->

            <div class="sidebar-logo">

                Site<span>Match</span>

            </div>


            <!-- =============================================
                 DESENVOLVEDOR AUTENTICADO
                 ============================================= -->

            <div class="sidebar-user">

                <strong>

                    <?= escaparDashboardDev(
                        $nomeUsuario
                    ) ?>

                </strong>


                <span>
                    Desenvolvedor
                </span>

            </div>


            <!-- =============================================
                 MENU
                 ============================================= -->

            <nav class="sidebar-nav">

                <ul>


                    <!-- INÍCIO -->

                    <li>

                        <a
                            href="dashboard.php"
                            class="ativo"
                        >
                            Início
                        </a>

                    </li>


                    <!-- RF08 -->

                    <li>

                        <a href="explorar-projetos.php">
                            Explorar projetos
                        </a>

                    </li>


                    <!-- RF10 / RF11 -->

                    <li>

                        <a href="minhas-propostas.php">
    Minhas propostas
</a>

                    </li>


                    <!-- RF13 -->

                    <li>

                        <a href="/mensagens.php">
    Mensagens
</a>

                    </li>


                    <!-- PERFIL -->

                    <li><a href="/historico.php">Histórico</a></li>
                    <li><a href="/perfil.php">Meu perfil</a></li>


                    <!-- LOGOUT -->

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
                        <?= escaparDashboardDev(
                            $nomeUsuario
                        ) ?>!

                    </h1>


                    <p>
                        Encontre projetos compatíveis
                        com suas habilidades.
                    </p>

                </div>


                <a
                    href="explorar-projetos.php"
                    class="botao-principal"
                >
                    Explorar projetos
                </a>

            </header>


            <!-- =================================================
                 RESUMO
                 =================================================
                 Agora os números vêm do banco.
                 ================================================= -->

            <section class="dashboard-section">

                <h2>
                    Resumo
                </h2>


                <div class="resumo-grid">


                    <!-- PROPOSTAS ENVIADAS -->

                    <article class="resumo-card">

                        <strong>

                            <?= (int) (
                                $resumo['propostas_enviadas']
                                ?? 0
                            ) ?>

                        </strong>


                        <p>
                            Propostas enviadas
                        </p>

                    </article>


                    <!-- EM NEGOCIAÇÃO -->

                    <article class="resumo-card">

                        <strong>

                            <?= (int) (
                                $resumo['em_negociacao']
                                ?? 0
                            ) ?>

                        </strong>


                        <p>
                            Em negociação
                        </p>

                    </article>


                    <!-- PROJETOS EM ANDAMENTO -->

                    <article class="resumo-card">

                        <strong>

                            <?= (int) (
                                $resumo['projetos_em_andamento']
                                ?? 0
                            ) ?>

                        </strong>


                        <p>
                            Projetos em andamento
                        </p>

                    </article>

                </div>

            </section>


            <!-- =================================================
                 PROJETOS DISPONÍVEIS
                 ================================================= -->

            <section
                class="dashboard-section"
                id="projetos-disponiveis"
            >

                <h2>
                    Projetos disponíveis
                </h2>


                <?php if (empty($projetosDisponiveis)): ?>


                    <!-- =========================================
                         NENHUM PROJETO ABERTO
                         ========================================= -->

                    <div class="projetos-vazio">

                        <p>
                            Não existem projetos disponíveis
                            no momento.
                        </p>

                    </div>


                <?php else: ?>


                    <!-- =========================================
                         LISTA DE OPORTUNIDADES
                         ========================================= -->

                    <div class="projetos-lista">


                        <?php foreach (
                            $projetosDisponiveis
                            as $projeto
                        ): ?>


                            <?php

                            // ----------------------------------
                            // PREPARAR DADOS DO PROJETO
                            // ----------------------------------

                            $categoria = (string) (
                                $projeto['categoria'] ?? ''
                            );


                            $nomeCategoria =
                                $nomesCategorias[$categoria]
                                ?? $categoria;


                            $orcamentoMin = (float) (
                                $projeto['orcamento_min']
                                ?? 0
                            );


                            $orcamentoMax = (float) (
                                $projeto['orcamento_max']
                                ?? 0
                            );


                            $prazo = (string) (
                                $projeto['prazo_desejado']
                                ?? ''
                            );

                            ?>


                            <article class="projeto-card">


                                <!-- =============================
                                     TOPO
                                     ============================= -->

                                <div class="projeto-card-topo">


                                    <h3>

                                        <?= escaparDashboardDev(
                                            (string) (
                                                $projeto['titulo']
                                                ?? ''
                                            )
                                        ) ?>

                                    </h3>


                                    <span class="projeto-status">
                                        Aberto
                                    </span>

                                </div>


                                <!-- =============================
                                     CLIENTE
                                     ============================= -->

                                <p>

                                    Publicado por:

                                    <strong>

                                        <?= escaparDashboardDev(
                                            (string) (
                                                $projeto['nome_cliente']
                                                ?? ''
                                            )
                                        ) ?>

                                    </strong>

                                </p>


                                <!-- =============================
                                     DESCRIÇÃO
                                     ============================= -->

                                <p style="margin-top: 12px;">

                                    <?= escaparDashboardDev(
                                        (string) (
                                            $projeto['descricao_resumida']
                                            ?? ''
                                        )
                                    ) ?>

                                </p>


                                <!-- =============================
                                     INFORMAÇÕES
                                     ============================= -->

                                <div
                                    class="projeto-meta"
                                    style="margin-top: 15px;"
                                >


                                    <span>

                                        Categoria:

                                        <strong>

                                            <?= escaparDashboardDev(
                                                $nomeCategoria
                                            ) ?>

                                        </strong>

                                    </span>


                                    <span>

                                        Orçamento:

                                        <strong>

                                            <?= formatarValorProjeto(
                                                $orcamentoMin
                                            ) ?>

                                            até

                                            <?= formatarValorProjeto(
                                                $orcamentoMax
                                            ) ?>

                                        </strong>

                                    </span>


                                    <span>

                                        Prazo:

                                        <strong>

                                            <?= escaparDashboardDev(
                                                formatarDataProjeto(
                                                    $prazo
                                                )
                                            ) ?>

                                        </strong>

                                    </span>


                                    <span>

                                        Propostas:

                                        <strong>

                                            <?= (int) (
                                                $projeto['total_propostas']
                                                ?? 0
                                            ) ?>

                                        </strong>

                                    </span>

                                </div>


                                <!-- =============================
                                     AÇÃO
                                     ============================= -->

                                <div style="margin-top: 20px;">

                                    <!--
                                        Em breve esse link abrirá
                                        os detalhes completos do
                                        projeto + briefing.
                                    -->

                                    <a
    href="projeto.php?id=<?= (int) (
        $projeto['id'] ?? 0
    ) ?>"
    class="botao-principal"
>
    Ver projeto
</a>

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