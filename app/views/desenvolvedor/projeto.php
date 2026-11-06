<?php

// ==========================================================
// VIEW: DETALHES DO PROJETO
// ==========================================================
//
// Mostra:
//
// - dados principais;
// - cliente;
// - orçamento;
// - prazo;
// - briefing;
// - funcionalidades;
// - regras;
// - identidade visual;
// - referências.
//
// RF08 - Visualização de projetos
// RF17 - Briefing estruturado
// ==========================================================


/**
 * Projeto recebido do Model.
 *
 * @var array<string, mixed> $projeto
 */


// ----------------------------------------------------------
// FUNÇÃO DE ESCAPE
// ----------------------------------------------------------

function escaparProjeto(
    string $valor
): string {

    return htmlspecialchars(
        $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}


// ----------------------------------------------------------
// FORMATAR DINHEIRO
// ----------------------------------------------------------

function formatarDinheiroProjeto(
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

function formatarDataProjetoDetalhe(
    string $data
): string {

    $objeto = DateTime::createFromFormat(
        'Y-m-d',
        $data
    );


    if ($objeto === false) {
        return $data;
    }


    return $objeto->format(
        'd/m/Y'
    );
}


// ----------------------------------------------------------
// CATEGORIAS
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
// PREPARAR ALGUNS DADOS
// ----------------------------------------------------------

$categoria = (string) (
    $projeto['categoria'] ?? ''
);


$nomeCategoria =
    $nomesCategorias[$categoria]
    ?? $categoria;


$orcamentoMin = (float) (
    $projeto['orcamento_min'] ?? 0
);


$orcamentoMax = (float) (
    $projeto['orcamento_max'] ?? 0
);


$prazo = (string) (
    $projeto['prazo_desejado'] ?? ''
);

$statusProjetoNomes = [
    'rascunho' => 'Rascunho',
    'aberto' => 'Aberto',
    'em_negociacao' => 'Em negociação',
    'contratado' => 'Contratado',
    'em_andamento' => 'Em andamento',
    'aguardando_confirmacao' => 'Aguardando confirmação',
    'concluido' => 'Concluído',
    'cancelado' => 'Cancelado'
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
        Projeto - SiteMatch
    </title>


    <link
        rel="stylesheet"
        href="/css/global.css"
    >


    <link
        rel="stylesheet"
        href="/css/dashboard.css"
    >
    <link rel="stylesheet" href="/css/functional.css">

</head>


<body>


    <main
        class="dashboard-main"
        style="max-width: 1100px; margin: 0 auto;"
    >


        <!-- =================================================
             VOLTAR
             ================================================= -->

        <p style="margin-bottom: 25px;">

            <a
                href="explorar-projetos.php"
                style="color: var(--cor-primaria);"
            >
                ← Voltar para projetos
            </a>

        </p>


        <!-- =================================================
             TÍTULO
             ================================================= -->

        <header
            class="dashboard-header"
            style="align-items: flex-start;"
        >

            <div class="dashboard-header-text">

                <h1>

                    <?= escaparProjeto(
                        (string) (
                            $projeto['titulo'] ?? ''
                        )
                    ) ?>

                </h1>


                <p>

                    Publicado por

                    <strong>

                        <?= escaparProjeto(
                            (string) (
                                $projeto['nome_cliente'] ?? ''
                            )
                        ) ?>

                    </strong>

                </p>

            </div>


            <span class="projeto-status">
                <?= escaparProjeto($statusProjetoNomes[(string) ($projeto['status'] ?? '')] ?? (string) ($projeto['status'] ?? '')) ?>
            </span>

        </header>


        <!-- =================================================
             INFORMAÇÕES PRINCIPAIS
             ================================================= -->

        <section class="dashboard-section">

            <div class="resumo-grid">


                <article class="resumo-card">

                    <strong>
                        <?= escaparProjeto(
                            $nomeCategoria
                        ) ?>
                    </strong>

                    <p>
                        Tipo de projeto
                    </p>

                </article>


                <article class="resumo-card">

                    <strong>

                        <?= formatarDinheiroProjeto(
                            $orcamentoMin
                        ) ?>

                        -

                        <?= formatarDinheiroProjeto(
                            $orcamentoMax
                        ) ?>

                    </strong>

                    <p>
                        Orçamento
                    </p>

                </article>


                <article class="resumo-card">

                    <strong>

                        <?= escaparProjeto(
                            formatarDataProjetoDetalhe(
                                $prazo
                            )
                        ) ?>

                    </strong>

                    <p>
                        Prazo desejado
                    </p>

                </article>

            </div>

        </section>


        <!-- =================================================
             DESCRIÇÃO
             ================================================= -->

        <section class="dashboard-section">

            <h2>
                Sobre o projeto
            </h2>


            <div class="projeto-card">

                <p>

                    <?= nl2br(
                        escaparProjeto(
                            (string) (
                                $projeto['descricao_resumida']
                                ?? ''
                            )
                        )
                    ) ?>

                </p>

            </div>

        </section>


        <!-- =================================================
             BRIEFING
             ================================================= -->

        <section class="dashboard-section">

            <h2>
                Briefing do projeto
            </h2>


            <!-- OBJETIVO -->

            <div class="projeto-card">

                <h3>
                    Objetivo
                </h3>

                <p>

                    <?= nl2br(
                        escaparProjeto(
                            (string) (
                                $projeto['objetivo'] ?? ''
                            )
                        )
                    ) ?>

                </p>

            </div>


            <!-- PÚBLICO -->

            <div
                class="projeto-card"
                style="margin-top: 15px;"
            >

                <h3>
                    Quem utilizará
                </h3>

                <p>

                    <?= nl2br(
                        escaparProjeto(
                            (string) (
                                $projeto['publico_alvo'] ?? ''
                            )
                        )
                    ) ?>

                </p>

            </div>


            <!-- FUNCIONALIDADES -->

            <div
                class="projeto-card"
                style="margin-top: 15px;"
            >

                <h3>
                    Funcionalidades esperadas
                </h3>

                <p>

                    <?= nl2br(
                        escaparProjeto(
                            (string) (
                                $projeto['funcionalidades']
                                ?? ''
                            )
                        )
                    ) ?>

                </p>

            </div>


            <!-- REGRAS -->

            <div
                class="projeto-card"
                style="margin-top: 15px;"
            >

                <h3>
                    Regras importantes
                </h3>

                <p>

                    <?= nl2br(
                        escaparProjeto(
                            (string) (
                                $projeto['condicoes_regras']
                                ?? 'Não informado.'
                            )
                        )
                    ) ?>

                </p>

            </div>


            <!-- APARÊNCIA -->

            <div
                class="projeto-card"
                style="margin-top: 15px;"
            >

                <h3>
                    Aparência desejada
                </h3>

                <p>

                    <?= nl2br(
                        escaparProjeto(
                            (string) (
                                $projeto['identidade_visual']
                                ?? 'Não informado.'
                            )
                        )
                    ) ?>

                </p>

            </div>


            <!-- REFERÊNCIAS -->

            <div
                class="projeto-card"
                style="margin-top: 15px;"
            >

                <h3>
                    Referências
                </h3>

                <p>

                    <?= nl2br(
                        escaparProjeto(
                            (string) (
                                $projeto['referencias_visuais']
                                ?? 'Não informado.'
                            )
                        )
                    ) ?>

                </p>

            </div>

        </section>


        <!-- =================================================
             PROPOSTAS
             ================================================= -->

        <section class="dashboard-section">

            <h2>
                Propostas
            </h2>


            <div class="projeto-card">

                <p>

                    Este projeto possui

                    <strong>

                        <?= (int) (
                            $projeto['total_propostas'] ?? 0
                        ) ?>

                    </strong>

                    proposta(s).

                </p>

            </div>

        </section>


        <!-- =================================================
             AÇÃO
             ================================================= -->

        <section class="dashboard-section">

            <!--
                Próxima funcionalidade:
                proposta preliminar.
            -->

            <?php if (($projeto['status'] ?? '') === 'aberto' && empty($projeto['minha_proposta_id'])): ?>
                <a href="enviar-proposta.php?id=<?= (int) ($projeto['id'] ?? 0) ?>" class="botao-principal">Enviar proposta</a>
            <?php elseif (!empty($projeto['minha_proposta_id'])): ?>
                <a href="enviar-proposta.php?id=<?= (int) ($projeto['id'] ?? 0) ?>" class="botao-principal">Ver minha proposta</a>
                <a href="/chat.php?proposta=<?= (int) $projeto['minha_proposta_id'] ?>" class="botao-secundario">Conversar</a>
            <?php else: ?>
                <p>Este projeto não está mais recebendo propostas.</p>
            <?php endif; ?>

        </section>

    </main>

</body>

</html>