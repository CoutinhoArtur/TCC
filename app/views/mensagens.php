<?php

// ==========================================================
// VIEW: CAIXA DE MENSAGENS
// ==========================================================
//
// Mostra todas as conversas do usuário.
//
// Cada item mostra:
//
// - outra pessoa;
// - projeto;
// - última mensagem;
// - horário;
// - mensagens não lidas.
//
// RF13 - Comunicação entre usuários
// ==========================================================


/**
 * @var array<string, mixed>|null $usuario
 */

/**
 * @var array<int, array<string, mixed>>|null $conversas
 */

/**
 * @var int|null $totalNaoLidas
 */


// ----------------------------------------------------------
// NORMALIZAR VARIÁVEIS
// ----------------------------------------------------------

$usuario = $usuario ?? [];

$conversas = $conversas ?? [];

$totalNaoLidas = $totalNaoLidas ?? 0;


// ----------------------------------------------------------
// DADOS DO USUÁRIO
// ----------------------------------------------------------

$nomeUsuario = (string) (
    $usuario['nome'] ?? ''
);


$tipoUsuario = (string) (
    $usuario['tipo'] ?? ''
);


// ----------------------------------------------------------
// ESCAPE
// ----------------------------------------------------------

function escaparMensagens(
    string $valor
): string {

    return htmlspecialchars(
        $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}


// ----------------------------------------------------------
// FORMATAR DATA/HORA
// ----------------------------------------------------------

function formatarHorarioConversa(
    string $data
): string {

    if ($data === '') {

        return '';
    }


    try {

        $objeto =
            new DateTime($data);


        return $objeto->format(
            'd/m H:i'
        );

    } catch (Throwable $erro) {

        return $data;
    }
}


// ----------------------------------------------------------
// STATUS AMIGÁVEIS
// ----------------------------------------------------------

$statusAmigaveis = [

    'preliminar' =>
        'Proposta preliminar',

    'em_negociacao' =>
        'Em negociação',

    'aguardando_revisao' =>
        'Aguardando revisão',

    'definitiva' =>
        'Proposta definitiva',

    'aceita' =>
        'Proposta aceita',

    'recusada' =>
        'Proposta recusada',

    'encerrada' =>
        'Proposta encerrada',

    'cancelada' =>
        'Proposta cancelada'
];


// ==========================================================
// LINK DE VOLTA
// ==========================================================

if ($tipoUsuario === 'cliente') {

    $dashboardUrl =
        '/cliente/dashboard.php';

} else {

    $dashboardUrl =
        '/desenvolvedor/dashboard.php';
}

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
        Mensagens - SiteMatch
    </title>


    <link
        rel="stylesheet"
        href="/css/global.css"
    >


    <link
        rel="stylesheet"
        href="/css/dashboard.css"
    >


    <link
        rel="stylesheet"
        href="/css/functional.css"
    >


    <link
        rel="stylesheet"
        href="/css/chat.css"
    >

</head>


<body>


    <main
        class="dashboard-main"
        style="
            max-width: 1000px;
            margin: 0 auto;
        "
    >


        <!-- =================================================
             VOLTAR
             ================================================= -->

        <p style="margin-bottom: 28px;">

            <a
                href="<?= escaparMensagens(
                    $dashboardUrl
                ) ?>"
                style="
                    color: var(--cor-primaria);
                    font-weight: 600;
                "
            >
                ← Voltar para o painel
            </a>

        </p>


        <!-- =================================================
             CABEÇALHO
             ================================================= -->

        <header class="dashboard-header">


            <div class="dashboard-header-text">


                <h1>
                    Mensagens
                </h1>


                <p>

                    Olá,
                    <?= escaparMensagens(
                        $nomeUsuario
                    ) ?>.

                    Aqui estão suas conversas
                    de negociação.

                </p>

            </div>


            <?php if ($totalNaoLidas > 0): ?>

                <span class="projeto-status">

                    <?= (int) $totalNaoLidas ?>

                    não lida(s)

                </span>

            <?php endif; ?>


        </header>


        <!-- =================================================
             LISTA DE CONVERSAS
             ================================================= -->

        <section class="dashboard-section">


            <?php if (empty($conversas)): ?>


                <!-- =========================================
                     SEM CONVERSAS
                     ========================================= -->

                <div class="projetos-vazio">

                    <p>

                        Você ainda não possui
                        conversas iniciadas.

                    </p>

                </div>


            <?php else: ?>


                <div class="projetos-lista">


                    <?php foreach (
                        $conversas
                        as $conversa
                    ): ?>


                        <?php

                        // ----------------------------------
                        // PROPOSTA
                        // ----------------------------------

                        $propostaId = (int) (
                            $conversa[
                                'proposta_id'
                            ]
                            ?? 0
                        );


                        // ----------------------------------
                        // NÃO LIDAS
                        // ----------------------------------

                        $naoLidas = (int) (
                            $conversa[
                                'nao_lidas'
                            ]
                            ?? 0
                        );


                        // ----------------------------------
                        // STATUS
                        // ----------------------------------

                        $status = (string) (
                            $conversa[
                                'proposta_status'
                            ]
                            ?? ''
                        );


                        $statusExibicao =
                            $statusAmigaveis[
                                $status
                            ]
                            ?? $status;


                        // ----------------------------------
                        // ÚLTIMA MENSAGEM
                        // ----------------------------------

                        $ultimaMensagem = (string) (
                            $conversa[
                                'ultima_mensagem'
                            ]
                            ?? ''
                        );


                        // ----------------------------------
                        // LIMITAR TEXTO VISUAL
                        // ----------------------------------

                        if (
                            mb_strlen(
                                $ultimaMensagem
                            ) > 120
                        ) {

                            $ultimaMensagem =
                                mb_substr(
                                    $ultimaMensagem,
                                    0,
                                    120
                                )
                                . '...';
                        }

                        ?>


                        <article class="projeto-card">


                            <!-- =============================
                                 TOPO
                                 ============================= -->

                            <div class="projeto-card-topo">


                                <div>


                                    <!-- OUTRA PESSOA -->

                                    <h3>

                                        <?= escaparMensagens(
                                            (string) (
                                                $conversa[
                                                    'outro_usuario_nome'
                                                ]
                                                ?? ''
                                            )
                                        ) ?>

                                    </h3>


                                    <!-- PROJETO -->

                                    <p
                                        style="
                                            margin-top: 5px;
                                            color: var(--cor-texto-secundario);
                                        "
                                    >

                                        Projeto:

                                        <strong>

                                            <?= escaparMensagens(
                                                (string) (
                                                    $conversa[
                                                        'projeto_titulo'
                                                    ]
                                                    ?? ''
                                                )
                                            ) ?>

                                        </strong>

                                    </p>

                                </div>


                                <!-- STATUS -->

                                <span class="projeto-status">

                                    <?= escaparMensagens(
                                        $statusExibicao
                                    ) ?>

                                </span>

                            </div>


                            <!-- =============================
                                 ÚLTIMA MENSAGEM
                                 ============================= -->

                            <div
                                style="
                                    margin-top: 20px;
                                "
                            >

                                <p
                                    style="
                                        font-size: 16px;
                                        line-height: 1.5;
                                    "
                                >

                                    <?= escaparMensagens(
                                        $ultimaMensagem
                                    ) ?>

                                </p>

                            </div>


                            <!-- =============================
                                 RODAPÉ DA CONVERSA
                                 ============================= -->

                            <div
                                style="
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                    gap: 20px;
                                    flex-wrap: wrap;
                                    margin-top: 18px;
                                "
                            >


                                <!-- DATA -->

                                <span
                                    style="
                                        color: var(--cor-texto-secundario);
                                        font-size: 14px;
                                    "
                                >

                                    <?= escaparMensagens(
                                        formatarHorarioConversa(
                                            (string) (
                                                $conversa[
                                                    'ultima_mensagem_em'
                                                ]
                                                ?? ''
                                            )
                                        )
                                    ) ?>

                                </span>


                                <!-- NÃO LIDAS -->

                                <?php if ($naoLidas > 0): ?>

                                    <strong
                                        style="
                                            color: var(--cor-primaria);
                                        "
                                    >

                                        <?= $naoLidas ?>

                                        nova(s)

                                    </strong>

                                <?php endif; ?>


                            </div>


                            <!-- =============================
                                 ABRIR CONVERSA
                                 ============================= -->

                            <?php if ($propostaId > 0): ?>

                                <div
                                    style="
                                        margin-top: 20px;
                                    "
                                >

                                    <a
                                        href="/chat.php?proposta=<?= $propostaId ?>"
                                        class="botao-principal"
                                    >
                                        Abrir conversa
                                    </a>

                                </div>

                            <?php endif; ?>


                        </article>


                    <?php endforeach; ?>


                </div>


            <?php endif; ?>


        </section>

    </main>

</body>

</html>