<?php

// ==========================================================
// VIEW: PROPOSTA DEFINITIVA
// ==========================================================
//
// Permite ao desenvolvedor revisar a negociação
// antes de enviar a proposta definitiva.
//
// Depois dessa etapa o cliente poderá aceitar
// ou recusar.
//
// RF10 - Envio de propostas
// RF11 - Gerenciamento de propostas
// ==========================================================


/**
 * @var array<string, mixed>|null $proposta
 */

/**
 * @var array<string, string>|null $dadosFormulario
 */

/**
 * @var string|null $mensagem
 */

/**
 * @var bool|null $sucesso
 */

/**
 * @var bool|null $podeFinalizar
 */


// ----------------------------------------------------------
// NORMALIZAR VARIÁVEIS
// ----------------------------------------------------------

$proposta =
    $proposta ?? [];


$dadosFormulario =
    $dadosFormulario ?? [];


$mensagem =
    $mensagem ?? '';


$sucesso =
    $sucesso ?? false;


$podeFinalizar =
    $podeFinalizar ?? false;


// ----------------------------------------------------------
// ESCAPE
// ----------------------------------------------------------

function escaparPropostaDefinitiva(
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

function formatarValorDefinitivo(
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
// STATUS
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


$statusAtual = (string) (
    $proposta['status'] ?? ''
);


$statusExibicao =
    $statusAmigaveis[$statusAtual]
    ?? $statusAtual;

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
        Proposta definitiva - SiteMatch
    </title>


    <link
        rel="stylesheet"
        href="/css/global.css"
    >


    <link
        rel="stylesheet"
        href="/css/publicar-projeto.css"
    >


    <!-- JavaScript desta funcionalidade -->

    <script
        src="/js/proposta-definitiva.js"
        defer
    ></script>

    <!-- Acabamento visual compartilhado; nenhuma regra de negócio é executada aqui. -->
    <link rel="stylesheet" href="/css/polish.css">
</head>


<body>


    <main class="briefing-container">


        <!-- =================================================
             VOLTAR
             ================================================= -->

        <a
            href="minhas-propostas.php"
            class="briefing-voltar"
        >
            ← Voltar para minhas propostas
        </a>


        <!-- =================================================
             CABEÇALHO
             ================================================= -->

        <header class="briefing-header">


            <p class="briefing-saudacao">

                <?= escaparPropostaDefinitiva(
                    $statusExibicao
                ) ?>

            </p>


            <h1>
                Proposta definitiva
            </h1>


            <p>

                Revise os valores e entregáveis definidos
                durante a negociação antes de enviar
                a versão final ao cliente.

            </p>

        </header>


        <!-- =================================================
             SUCESSO
             ================================================= -->

        <?php if ($sucesso): ?>


            <section class="briefing-card">


                <span class="briefing-etapa">
                    Enviada
                </span>


                <h2>
                    Proposta definitiva enviada!
                </h2>


                <p>

                    Agora o cliente poderá analisar
                    e aceitar ou recusar sua proposta.

                </p>


                <div
                    class="briefing-acoes"
                    style="margin-top: 30px;"
                >


                    <a
                        href="/chat.php?proposta=<?= (int) (
                            $proposta['id'] ?? 0
                        ) ?>"
                        class="botao-continuar"
                        style="text-decoration: none;"
                    >
                        Voltar para a conversa
                    </a>


                </div>

            </section>


        <?php elseif (!$podeFinalizar): ?>


            <!-- =================================================
                 JÁ FOI FINALIZADA
                 ================================================= -->

            <section class="briefing-card">


                <span class="briefing-etapa">

                    <?= escaparPropostaDefinitiva(
                        $statusExibicao
                    ) ?>

                </span>


                <h2>
                    Esta proposta já foi finalizada
                </h2>


                <div class="campo">

                    <label>
                        Valor
                    </label>

                    <p>

                        <?= formatarValorDefinitivo(
                            (float) (
                                $proposta['valor'] ?? 0
                            )
                        ) ?>

                    </p>

                </div>


                <div class="campo">

                    <label>
                        Prazo
                    </label>

                    <p>

                        <?= (int) (
                            $proposta['prazo_estimado']
                            ?? 0
                        ) ?>

                        dias

                    </p>

                </div>


                <div class="campo">

                    <label>
                        Proposta
                    </label>

                    <p>

                        <?= nl2br(
                            escaparPropostaDefinitiva(
                                (string) (
                                    $proposta['descricao']
                                    ?? ''
                                )
                            )
                        ) ?>

                    </p>

                </div>


                <div class="campo">

                    <label>
                        Entregáveis
                    </label>

                    <p>

                        <?= nl2br(
                            escaparPropostaDefinitiva(
                                (string) (
                                    $proposta['entregaveis']
                                    ?? ''
                                )
                            )
                        ) ?>

                    </p>

                </div>

            </section>


        <?php else: ?>


            <!-- =================================================
                 FORMULÁRIO
                 ================================================= -->

            <section class="briefing-card">


                <span class="briefing-etapa">
                    Revisão final
                </span>


                <h2>
                    Confirme sua proposta
                </h2>


                <?php if ($mensagem !== ''): ?>


                    <div
                        role="alert"
                        style="
                            margin-bottom: 20px;
                            padding: 12px 15px;
                            border-radius: 8px;
                            background: #fff0f0;
                            color: #b42318;
                        "
                    >

                        <?= escaparPropostaDefinitiva(
                            $mensagem
                        ) ?>

                    </div>


                <?php endif; ?>


                <form
                    method="POST"
                    id="form-proposta-definitiva"
                >


                    <!-- VALOR -->

                    <div class="campo">

                        <label for="valor">

                            Valor definitivo

                        </label>


                        <input
                            type="number"
                            id="valor"
                            name="valor"
                            min="0.01"
                            step="0.01"
                            required

                            value="<?= escaparPropostaDefinitiva(
                                $dadosFormulario['valor']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <!-- PRAZO -->

                    <div class="campo">

                        <label for="prazo_estimado">

                            Prazo definitivo em dias

                        </label>


                        <input
                            type="number"
                            id="prazo_estimado"
                            name="prazo_estimado"
                            min="1"
                            step="1"
                            required

                            value="<?= escaparPropostaDefinitiva(
                                $dadosFormulario[
                                    'prazo_estimado'
                                ]
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <!-- DESCRIÇÃO -->

                    <div class="campo">

                        <label for="descricao">

                            Proposta definitiva

                        </label>


                        <textarea
                            id="descricao"
                            name="descricao"
                            required
                        ><?= escaparPropostaDefinitiva(
                            $dadosFormulario['descricao']
                            ?? ''
                        ) ?></textarea>

                    </div>


                    <!-- ENTREGÁVEIS -->

                    <div class="campo">

                        <label for="entregaveis">

                            Entregáveis definitivos

                        </label>


                        <textarea
                            id="entregaveis"
                            name="entregaveis"
                            required
                        ><?= escaparPropostaDefinitiva(
                            $dadosFormulario[
                                'entregaveis'
                            ]
                            ?? ''
                        ) ?></textarea>

                    </div>


                    <!-- ENVIAR -->

                    <div class="briefing-acoes">

                        <button
                            type="submit"
                            class="botao-continuar"
                        >
                            Enviar proposta definitiva
                        </button>

                    </div>


                </form>

            </section>


        <?php endif; ?>


    </main>

<script src="/js/ui.js" defer></script>
</body>

</html>