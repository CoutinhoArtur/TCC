<?php

// ==========================================================
// VIEW: PUBLICAR PROJETO - ETAPA 2
// ==========================================================
//
// Segunda etapa do Briefing Guiado.
//
// Nesta etapa traduzimos conceitos técnicos para
// perguntas simples.
//
// Em vez de:
//
// "Informe os atores e requisitos funcionais."
//
// utilizamos:
//
// "Quem vai utilizar?"
//
// "O que essas pessoas precisam conseguir fazer?"
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
// NOME DO USUÁRIO
// ----------------------------------------------------------

$nomeUsuario = (string) (
    $usuario['nome'] ?? ''
);


// ----------------------------------------------------------
// FUNÇÃO DE ESCAPE
// ----------------------------------------------------------

function escaparEtapa2(
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
        Briefing - Etapa 2 - SiteMatch
    </title>


    <!-- CSS GLOBAL -->

    <link
        rel="stylesheet"
        href="/css/global.css"
    >


    <!-- CSS DO BRIEFING -->

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
            href="publicar-projeto.php"
            class="briefing-voltar"
        >
            ← Voltar para a Etapa 1
        </a>


        <!-- =================================================
             CABEÇALHO
             ================================================= -->

        <header class="briefing-header">


            <p class="briefing-saudacao">

                Continuando,
                <?= escaparEtapa2(
                    $nomeUsuario
                ) ?>.

            </p>


            <h1>
                Vamos entender quem usará seu projeto
            </h1>


            <p>

                Conte com suas próprias palavras.
                Não precisa saber nomes técnicos ou
                definir requisitos de software.

            </p>

        </header>


        <!-- =================================================
             CARD
             ================================================= -->

        <section class="briefing-card">


            <!-- ETAPA -->

            <span class="briefing-etapa">
                Etapa 2 de 4
            </span>


            <h2>
                Pessoas e funcionalidades
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

                    <?= escaparEtapa2(
                        $mensagem
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 FORMULÁRIO
                 ================================================= -->

            <form method="POST">


                <!-- =============================================
                     PÚBLICO-ALVO
                     ============================================= -->

                <div class="campo">

                    <label for="publico_alvo">

                        Quem vai utilizar esse projeto?

                    </label>


                    <small>

                        Pense nas pessoas que terão contato com
                        o sistema, site ou aplicativo.

                        Ex.: clientes, funcionários,
                        administradores, pacientes ou alunos.

                    </small>


                    <textarea
                        id="publico_alvo"
                        name="publico_alvo"
                        required
                        placeholder="Ex.: Pacientes, recepcionistas e administradores da clínica."
                    ><?= escaparEtapa2(
                        $dadosFormulario['publico_alvo'] ?? ''
                    ) ?></textarea>

                </div>


                <!-- =============================================
                     FUNCIONALIDADES
                     ============================================= -->

                <div class="campo">

                    <label for="funcionalidades">

                        O que essas pessoas precisam
                        conseguir fazer?

                    </label>


                    <small>

                        Liste as principais ações que imagina.

                        Ex.: criar conta, fazer login,
                        agendar horário, cancelar agendamento,
                        consultar informações ou gerar relatórios.

                    </small>


                    <textarea
                        id="funcionalidades"
                        name="funcionalidades"
                        required
                        placeholder="Ex.: O paciente deve conseguir criar uma conta, entrar no sistema, consultar horários disponíveis e realizar agendamentos."
                    ><?= escaparEtapa2(
                        $dadosFormulario['funcionalidades'] ?? ''
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