<?php

// ==========================================================
// VIEW: PROPOSTA PRELIMINAR
// ==========================================================
//
// Esta tela possui dois estados:
//
// 1. ainda não existe proposta:
//    → mostra formulário para enviar.
//
// 2. proposta já existe:
//    → mostra os dados enviados;
//    → libera acesso ao chat.
//
// O chat fica vinculado diretamente à proposta.
//
// RF10 - Envio de propostas
// RF11 - Gerenciamento de propostas
// RF13 - Comunicação entre usuários
// ==========================================================


/**
 * Projeto selecionado.
 *
 * @var array<string, mixed> $projeto
 */


/**
 * Proposta já existente.
 *
 * false = ainda não existe proposta.
 *
 * array = proposta já enviada.
 *
 * @var array<string, mixed>|false $propostaExistente
 */


/**
 * Dados digitados no formulário.
 *
 * @var array<string, string> $dadosFormulario
 */


/**
 * Mensagem de validação.
 *
 * @var string $mensagem
 */


/**
 * Indica sucesso no envio.
 *
 * @var bool $sucesso
 */


// ----------------------------------------------------------
// FUNÇÃO DE SEGURANÇA
// ----------------------------------------------------------

function escaparProposta(
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

function formatarValorProposta(
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
// NOMES AMIGÁVEIS DOS STATUS
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


// ----------------------------------------------------------
// ORÇAMENTO DO PROJETO
// ----------------------------------------------------------

$orcamentoMin = (float) (
    $projeto['orcamento_min'] ?? 0
);


$orcamentoMax = (float) (
    $projeto['orcamento_max'] ?? 0
);

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
        Enviar proposta - SiteMatch
    </title>


    <!-- CSS GLOBAL -->

    <link
        rel="stylesheet"
        href="/css/global.css"
    >


    <!--
        Reutilizamos o CSS do Briefing porque
        formulário e cards possuem estrutura semelhante.
    -->

    <link
        rel="stylesheet"
        href="/css/publicar-projeto.css"
    >

</head>


<body>


    <main class="briefing-container">


        <!-- =================================================
             VOLTAR PARA O PROJETO
             ================================================= -->

        <a
            href="projeto.php?id=<?= (int) (
                $projeto['id'] ?? 0
            ) ?>"
            class="briefing-voltar"
        >
            ← Voltar para o projeto
        </a>


        <!-- =================================================
             CABEÇALHO
             ================================================= -->

        <header class="briefing-header">


            <p class="briefing-saudacao">
                Proposta preliminar
            </p>


            <h1>

                <?= escaparProposta(
                    (string) (
                        $projeto['titulo'] ?? ''
                    )
                ) ?>

            </h1>


            <p>

                Esta é sua proposta inicial para o cliente.

                Depois do envio, o chat fica disponível
                para negociação e alinhamento dos detalhes.

            </p>

        </header>


        <!-- =================================================
             REFERÊNCIA DO PROJETO
             ================================================= -->

        <section
            class="briefing-card"
            style="margin-bottom: 25px;"
        >

            <h2>
                Referência do projeto
            </h2>


            <!-- CLIENTE -->

            <div class="campo">

                <label>
                    Cliente
                </label>

                <p>

                    <?= escaparProposta(
                        (string) (
                            $projeto['nome_cliente'] ?? ''
                        )
                    ) ?>

                </p>

            </div>


            <!-- ORÇAMENTO -->

            <div class="campo">

                <label>
                    Faixa de orçamento
                </label>

                <p>

                    <?= formatarValorProposta(
                        $orcamentoMin
                    ) ?>

                    até

                    <?= formatarValorProposta(
                        $orcamentoMax
                    ) ?>

                </p>

            </div>

        </section>


        <!-- =================================================
             PROPOSTA JÁ EXISTENTE
             ================================================= -->

        <?php if ($propostaExistente !== false): ?>


            <?php

            // ------------------------------------------------
            // PREPARAR INFORMAÇÕES DA PROPOSTA
            // ------------------------------------------------

            $propostaId = (int) (
                $propostaExistente['id'] ?? 0
            );


            $statusProposta = (string) (
                $propostaExistente['status'] ?? ''
            );


            $statusExibicao =
                $statusAmigaveis[$statusProposta]
                ?? $statusProposta;

            ?>


            <section class="briefing-card">


                <!-- STATUS -->

                <span class="briefing-etapa">

                    <?= escaparProposta(
                        $statusExibicao
                    ) ?>

                </span>


                <h2>
                    Sua proposta
                </h2>


                <!-- VALOR -->

                <div class="campo">

                    <label>
                        Valor proposto
                    </label>

                    <p>

                        <?= formatarValorProposta(
                            (float) (
                                $propostaExistente['valor']
                                ?? 0
                            )
                        ) ?>

                    </p>

                </div>


                <!-- PRAZO -->

                <div class="campo">

                    <label>
                        Prazo estimado
                    </label>

                    <p>

                        <?= (int) (
                            $propostaExistente[
                                'prazo_estimado'
                            ]
                            ?? 0
                        ) ?>

                        dias

                    </p>

                </div>


                <!-- DESCRIÇÃO -->

                <div class="campo">

                    <label>
                        Apresentação da proposta
                    </label>

                    <p>

                        <?= nl2br(
                            escaparProposta(
                                (string) (
                                    $propostaExistente[
                                        'descricao'
                                    ]
                                    ?? ''
                                )
                            )
                        ) ?>

                    </p>

                </div>


                <!-- ENTREGÁVEIS -->

                <div class="campo">

                    <label>
                        Entregáveis
                    </label>

                    <p>

                        <?= nl2br(
                            escaparProposta(
                                (string) (
                                    $propostaExistente[
                                        'entregaveis'
                                    ]
                                    ?? ''
                                )
                            )
                        ) ?>

                    </p>

                </div>


                <!-- =========================================
                     AÇÕES DA PROPOSTA
                     ========================================= -->

                <div class="briefing-acoes">


                    <!--
                        RF13 - Chat

                        O chat só existe porque já existe
                        uma proposta válida.

                        Passamos propostas.id na URL.
                    -->

                    <?php if ($propostaId > 0): ?>
                        <a href="/chat.php?proposta=<?= $propostaId ?>" class="botao-continuar" style="text-decoration:none;text-align:center;">Conversar com o cliente</a>
                        <?php if (in_array($statusProposta, ['preliminar','em_negociacao','aguardando_revisao'], true)): ?>
                            <a href="proposta-definitiva.php?proposta=<?= $propostaId ?>" class="botao-continuar" style="text-decoration:none;text-align:center;">Finalizar proposta</a>
                        <?php endif; ?>
                    <?php endif; ?>


                </div>

            </section>


        <?php else: ?>


            <!-- =================================================
                 NOVA PROPOSTA
                 ================================================= -->

            <section class="briefing-card">


                <span class="briefing-etapa">
                    Nova proposta
                </span>


                <h2>
                    Monte sua proposta
                </h2>


                <!-- =============================================
                     MENSAGEM DE ERRO
                     ============================================= -->

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

                        <?= escaparProposta(
                            $mensagem
                        ) ?>

                    </div>

                <?php endif; ?>


                <!-- =============================================
                     FORMULÁRIO
                     ============================================= -->

                <form method="POST">


                    <!-- =========================================
                         VALOR
                         ========================================= -->

                    <div class="campo">

                        <label for="valor">
                            Qual valor você propõe?
                        </label>


                        <small>

                            Este é um valor inicial e ainda
                            poderá ser negociado com o cliente.

                        </small>


                        <input
                            type="number"
                            id="valor"
                            name="valor"
                            min="0.01"
                            step="0.01"
                            required
                            placeholder="Ex.: 1800.00"

                            value="<?= escaparProposta(
                                $dadosFormulario['valor']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <!-- =========================================
                         PRAZO
                         ========================================= -->

                    <div class="campo">

                        <label for="prazo_estimado">

                            Quantos dias você estima
                            para entregar?

                        </label>


                        <input
                            type="number"
                            id="prazo_estimado"
                            name="prazo_estimado"
                            min="1"
                            step="1"
                            required
                            placeholder="Ex.: 25"

                            value="<?= escaparProposta(
                                $dadosFormulario[
                                    'prazo_estimado'
                                ]
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <!-- =========================================
                         DESCRIÇÃO DA PROPOSTA
                         ========================================= -->

                    <div class="campo">

                        <label for="descricao">
                            Apresente sua proposta
                        </label>


                        <small>

                            Explique como pretende atender
                            à necessidade apresentada
                            no briefing.

                        </small>


                        <textarea
                            id="descricao"
                            name="descricao"
                            required
                            placeholder="Ex.: Posso desenvolver o aplicativo conforme o briefing apresentado, priorizando uma interface simples e responsiva."
                        ><?= escaparProposta(
                            $dadosFormulario['descricao']
                            ?? ''
                        ) ?></textarea>

                    </div>


                    <!-- =========================================
                         ENTREGÁVEIS
                         ========================================= -->

                    <div class="campo">

                        <label for="entregaveis">

                            O que você pretende entregar?

                        </label>


                        <small>

                            Liste os principais resultados
                            incluídos na proposta.

                        </small>


                        <textarea
                            id="entregaveis"
                            name="entregaveis"
                            required
                            placeholder="Ex.: Aplicativo funcional, autenticação de usuários, telas principais e documentação básica."
                        ><?= escaparProposta(
                            $dadosFormulario['entregaveis']
                            ?? ''
                        ) ?></textarea>

                    </div>


                    <!-- =========================================
                         ENVIAR
                         ========================================= -->

                    <div class="briefing-acoes">

                        <button
                            type="submit"
                            class="botao-continuar"
                        >

                            Enviar proposta

                        </button>

                    </div>

                </form>

            </section>


        <?php endif; ?>


    </main>

<script src="/js/ui.js" defer></script>
</body>

</html>