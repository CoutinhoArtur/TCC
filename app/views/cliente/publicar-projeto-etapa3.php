<?php

// ==========================================================
// VIEW: PUBLICAR PROJETO - ETAPA 3
// ==========================================================
//
// Terceira etapa do Briefing Guiado.
//
// Aqui coletamos:
//
// - regras importantes;
// - aparência;
// - referências;
// - observações.
//
// RF17 - Briefing guiado
// ==========================================================


/**
 * Usuário autenticado.
 *
 * @var array<string, mixed> $usuario
 */


/**
 * Dados do formulário.
 *
 * @var array<string, string> $dadosFormulario
 */


/**
 * Mensagem de validação.
 *
 * @var string $mensagem
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

function escaparEtapa3(
    string $valor
): string {

    return htmlspecialchars(
        $valor,
        ENT_QUOTES,
        'UTF-8'
    );
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
        Briefing - Etapa 3 - SiteMatch
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
            href="publicar-projeto-etapa2.php"
            class="briefing-voltar"
        >
            ← Voltar para a Etapa 2
        </a>


        <!-- =================================================
             CABEÇALHO
             ================================================= -->

        <header class="briefing-header">


            <p class="briefing-saudacao">

                Estamos quase lá,
                <?= escaparEtapa3(
                    $nomeUsuario
                ) ?>.

            </p>


            <h1>
                Agora vamos aos detalhes
            </h1>


            <p>

                Essas informações ajudam o desenvolvedor
                a entender melhor como o projeto deve
                funcionar e qual aparência você espera.

            </p>

        </header>


        <!-- =================================================
             CARD
             ================================================= -->

        <section class="briefing-card">


            <span class="briefing-etapa">
                Etapa 3 de 4
            </span>


            <h2>
                Regras e aparência
            </h2>


            <!-- =================================================
                 MENSAGEM
                 ================================================= -->

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

                    <?= escaparEtapa3(
                        $mensagem
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 FORMULÁRIO
                 ================================================= -->

            <form method="POST">


                <!-- =============================================
                     REGRAS
                     ============================================= -->

                <div class="campo">

                    <label for="condicoes_regras">

                        Existe alguma regra ou condição
                        importante?

                    </label>


                    <small>

                        Ex.: cancelamentos só podem ser feitos
                        com 24 horas de antecedência, somente
                        administradores podem excluir dados,
                        pedidos acima de determinado valor
                        recebem desconto.

                    </small>


                    <textarea
                        id="condicoes_regras"
                        name="condicoes_regras"
                        placeholder="Ex.: Os pacientes só podem cancelar consultas até 24 horas antes do horário marcado."
                    ><?= escaparEtapa3(
                        $dadosFormulario['condicoes_regras'] ?? ''
                    ) ?></textarea>

                </div>


                <!-- =============================================
                     IDENTIDADE VISUAL
                     ============================================= -->

                <div class="campo">

                    <label for="identidade_visual">

                        Como você imagina a aparência
                        do projeto?

                    </label>


                    <small>

                        Pode falar sobre cores, estilo,
                        simplicidade, modernidade ou qualquer
                        preferência visual.

                    </small>


                    <textarea
                        id="identidade_visual"
                        name="identidade_visual"
                        placeholder="Ex.: Quero algo moderno, simples, com cores azul e branco."
                    ><?= escaparEtapa3(
                        $dadosFormulario['identidade_visual'] ?? ''
                    ) ?></textarea>

                </div>


                <!-- =============================================
                     REFERÊNCIAS
                     ============================================= -->

                <div class="campo">

                    <label for="referencias_visuais">

                        Tem algum site, aplicativo ou sistema
                        como referência?

                    </label>


                    <small>

                        Se quiser, escreva nomes ou links
                        que ajudam a mostrar o estilo que
                        você imagina.

                    </small>


                    <textarea
                        id="referencias_visuais"
                        name="referencias_visuais"
                        placeholder="Ex.: Gosto do estilo visual do site X ou do aplicativo Y."
                    ><?= escaparEtapa3(
                        $dadosFormulario['referencias_visuais'] ?? ''
                    ) ?></textarea>

                </div>


                <!-- =============================================
                     OBSERVAÇÕES
                     ============================================= -->

                <div class="campo">

                    <label for="observacoes">

                        Quer acrescentar mais alguma informação?

                    </label>


                    <small>

                        Este campo é livre para qualquer detalhe
                        que ainda não tenha aparecido nas
                        perguntas anteriores.

                    </small>


                    <textarea
                        id="observacoes"
                        name="observacoes"
                        placeholder="Escreva aqui qualquer outro detalhe que considere importante."
                    ><?= escaparEtapa3(
                        $dadosFormulario['observacoes'] ?? ''
                    ) ?></textarea>

                </div>


                <!-- =============================================
                     AÇÕES
                     ============================================= -->

                <div class="briefing-acoes">

                    <button
                        type="submit"
                        class="botao-continuar"
                    >

                        Continuar →

                    </button>

                </div>

            </form>

        </section>

    </main>

</body>

</html>