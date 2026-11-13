<?php

// ==========================================================
// VIEW: PUBLICAR PROJETO - ETAPA 4
// ==========================================================
//
// Última etapa do Briefing Guiado.
//
// Existem dois momentos nesta tela:
//
// 1. cliente informa orçamento e prazo;
// 2. cliente revisa tudo antes da publicação.
//
// RF06 - Publicação de projetos
// RF17 - Briefing guiado
// ==========================================================


/**
 * @var array<string, mixed> $usuario
 */

/**
 * @var array<string, mixed> $rascunho
 */

/**
 * @var array<string, string> $dadosFormulario
 */

/**
 * @var string $mensagem
 */

/**
 * @var bool $modoRevisao
 */


// ----------------------------------------------------------
// NOME DO CLIENTE
// ----------------------------------------------------------

$nomeUsuario = (string) (
    $usuario['nome'] ?? ''
);


// ----------------------------------------------------------
// FUNÇÃO DE ESCAPE
// ----------------------------------------------------------

function escaparEtapa4(
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
//
// Exemplo:
//
// 1500
//
// vira:
//
// R$ 1.500,00
// ----------------------------------------------------------

function formatarDinheiro(
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
// NOMES AMIGÁVEIS DAS CATEGORIAS
// ----------------------------------------------------------

$nomesCategorias = [
    'site' => 'Site',
    'sistema_web' => 'Sistema web',
    'aplicativo_mobile' => 'Aplicativo mobile',
    'sistema_desktop' => 'Sistema para computador',
    'outro' => 'Outro'
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
        Briefing - Etapa 4 - SiteMatch
    </title>


    <link
        rel="stylesheet"
        href="/css/global.css"
    >


    <link
        rel="stylesheet"
        href="/css/publicar-projeto.css"
    >

</head>


<body>


    <main class="briefing-container">


        <!-- =================================================
             VOLTAR
             ================================================= -->

        <a
            href="publicar-projeto-etapa3.php"
            class="briefing-voltar"
        >
            ← Voltar para a Etapa 3
        </a>


        <!-- =================================================
             CABEÇALHO
             ================================================= -->

        <header class="briefing-header">


            <p class="briefing-saudacao">

                Última etapa,
                <?= escaparEtapa4(
                    $nomeUsuario
                ) ?>!

            </p>


            <h1>
                Vamos finalizar seu projeto
            </h1>


            <p>

                Informe o orçamento e o prazo desejado.
                Depois você poderá revisar todas as informações
                antes de publicar.

            </p>

        </header>


        <!-- =================================================
             CARD PRINCIPAL
             ================================================= -->

        <section class="briefing-card">


            <span class="briefing-etapa">
                Etapa 4 de 4
            </span>


            <?php if (!$modoRevisao): ?>


                <!-- =========================================
                     FORMULÁRIO DE ORÇAMENTO E PRAZO
                     ========================================= -->

                <h2>
                    Orçamento e prazo
                </h2>


                <!-- MENSAGEM DE ERRO -->

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

                        <?= escaparEtapa4(
                            $mensagem
                        ) ?>

                    </div>

                <?php endif; ?>


                <form method="POST">


                    <!-- =====================================
                         ORÇAMENTO MÍNIMO
                         ===================================== -->

                    <div class="campo">

                        <label for="orcamento_min">
                            Qual o orçamento mínimo?
                        </label>


                        <small>
                            Informe uma estimativa em reais.
                        </small>


                        <input
                            type="number"
                            id="orcamento_min"
                            name="orcamento_min"
                            min="0"
                            step="0.01"
                            required
                            placeholder="Ex.: 1500.00"

                            value="<?= escaparEtapa4(
                                $dadosFormulario['orcamento_min'] ?? ''
                            ) ?>"
                        >

                    </div>


                    <!-- =====================================
                         ORÇAMENTO MÁXIMO
                         ===================================== -->

                    <div class="campo">

                        <label for="orcamento_max">
                            Qual o orçamento máximo?
                        </label>


                        <small>
                            Esse valor ajuda os desenvolvedores
                            a enviarem propostas compatíveis.
                        </small>


                        <input
                            type="number"
                            id="orcamento_max"
                            name="orcamento_max"
                            min="0"
                            step="0.01"
                            required
                            placeholder="Ex.: 3000.00"

                            value="<?= escaparEtapa4(
                                $dadosFormulario['orcamento_max'] ?? ''
                            ) ?>"
                        >

                    </div>


                    <!-- =====================================
                         PRAZO
                         ===================================== -->

                    <div class="campo">

                        <label for="prazo_desejado">
                            Até quando você gostaria de receber o projeto?
                        </label>


                        <small>

                            Esse prazo é inicialmente uma preferência.
                            O prazo definitivo poderá ser negociado
                            com o desenvolvedor.

                        </small>


                        <input
                            type="date"
                            id="prazo_desejado"
                            name="prazo_desejado"
                            required

                            value="<?= escaparEtapa4(
                                $dadosFormulario['prazo_desejado'] ?? ''
                            ) ?>"
                        >

                    </div>


                    <!-- =====================================
                         CONTINUAR PARA REVISÃO
                         ===================================== -->

                    <div class="briefing-acoes">

                        <button
                            type="submit"
                            class="botao-continuar"
                        >

                            Revisar projeto →

                        </button>

                    </div>

                </form>


            <?php else: ?>


                <!-- =========================================
                     REVISÃO FINAL
                     ========================================= -->

                <h2>
                    Revise seu projeto
                </h2>


                <p style="margin-bottom: 30px; color: #666;">

                    Confira as informações antes de publicar.
                    Se alguma coisa estiver errada, você pode
                    voltar para a etapa correspondente.

                </p>


                <!-- =========================================
                     INFORMAÇÕES INICIAIS
                     ========================================= -->

                <div class="campo">

                    <label>
                        Projeto
                    </label>

                    <p>

                        <?= escaparEtapa4(
                            (string) (
                                $rascunho['titulo'] ?? ''
                            )
                        ) ?>

                    </p>

                </div>


                <div class="campo">

                    <label>
                        Tipo
                    </label>

                    <p>

                        <?php

                        $categoria = (string) (
                            $rascunho['categoria'] ?? ''
                        );

                        echo escaparEtapa4(
                            $nomesCategorias[$categoria]
                            ?? $categoria
                        );

                        ?>

                    </p>

                </div>


                <div class="campo">

                    <label>
                        Descrição
                    </label>

                    <p>

                        <?= nl2br(
                            escaparEtapa4(
                                (string) (
                                    $rascunho['descricao_resumida'] ?? ''
                                )
                            )
                        ) ?>

                    </p>

                </div>


                <!-- =========================================
                     PESSOAS E FUNCIONALIDADES
                     ========================================= -->

                <div class="campo">

                    <label>
                        Quem utilizará
                    </label>

                    <p>

                        <?= nl2br(
                            escaparEtapa4(
                                (string) (
                                    $rascunho['publico_alvo'] ?? ''
                                )
                            )
                        ) ?>

                    </p>

                </div>


                <div class="campo">

                    <label>
                        Funcionalidades
                    </label>

                    <p>

                        <?= nl2br(
                            escaparEtapa4(
                                (string) (
                                    $rascunho['funcionalidades'] ?? ''
                                )
                            )
                        ) ?>

                    </p>

                </div>


                <!-- =========================================
                     REGRAS
                     ========================================= -->

                <div class="campo">

                    <label>
                        Regras importantes
                    </label>

                    <p>

                        <?= nl2br(
                            escaparEtapa4(
                                (string) (
                                    $rascunho['condicoes_regras']
                                    ?? 'Não informado.'
                                )
                            )
                        ) ?>

                    </p>

                </div>


                <!-- =========================================
                     APARÊNCIA
                     ========================================= -->

                <div class="campo">

                    <label>
                        Aparência desejada
                    </label>

                    <p>

                        <?= nl2br(
                            escaparEtapa4(
                                (string) (
                                    $rascunho['identidade_visual']
                                    ?? 'Não informado.'
                                )
                            )
                        ) ?>

                    </p>

                </div>


                <!-- =========================================
                     REFERÊNCIAS
                     ========================================= -->

                <div class="campo">

                    <label>
                        Referências
                    </label>

                    <p>

                        <?= nl2br(
                            escaparEtapa4(
                                (string) (
                                    $rascunho['referencias_visuais']
                                    ?? 'Não informado.'
                                )
                            )
                        ) ?>

                    </p>

                </div>


                <!-- =========================================
                     ORÇAMENTO
                     ========================================= -->

                <div class="campo">

                    <label>
                        Orçamento
                    </label>

                    <p>

                        <?= formatarDinheiro(
                            (float) (
                                $rascunho['orcamento_min'] ?? 0
                            )
                        ) ?>

                        até

                        <?= formatarDinheiro(
                            (float) (
                                $rascunho['orcamento_max'] ?? 0
                            )
                        ) ?>

                    </p>

                </div>


                <!-- =========================================
                     PRAZO
                     ========================================= -->

                <div class="campo">

                    <label>
                        Prazo desejado
                    </label>

                    <p>

                        <?php

                        $data = (string) (
                            $rascunho['prazo_desejado'] ?? ''
                        );


                        if ($data !== '') {

                            $dataObjeto =
                                DateTime::createFromFormat(
                                    'Y-m-d',
                                    $data
                                );


                            echo $dataObjeto !== false
                                ? escaparEtapa4(
                                    $dataObjeto->format(
                                        'd/m/Y'
                                    )
                                )
                                : escaparEtapa4($data);

                        }

                        ?>

                    </p>

                </div>


                <!-- =========================================
                     AÇÕES
                     ========================================= -->

                <!-- =========================================
     PUBLICAÇÃO DEFINITIVA
     =========================================
     Este formulário envia uma requisição POST
     para o arquivo responsável pela gravação
     definitiva no banco.
     ========================================= -->

<form
    method="POST"
    action="publicar-projeto-finalizar.php"
>

    <div class="briefing-acoes">

        <button
            type="submit"
            class="botao-continuar"
        >

            Publicar projeto

        </button>

    </div>

</form>


            <?php endif; ?>

        </section>

    </main>

<script src="/js/ui.js" defer></script>
</body>

</html>