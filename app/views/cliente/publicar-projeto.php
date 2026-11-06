<?php

// ==========================================================
// VIEW: PUBLICAR PROJETO - ETAPA 1
// ==========================================================
//
// Primeira etapa do Briefing Guiado.
//
// A ideia é permitir que um cliente sem conhecimento
// técnico consiga explicar o que deseja desenvolver.
//
// Este arquivo NÃO grava informações no banco.
//
// A lógica de validação está em:
//
// public/cliente/publicar-projeto.php
//
// RF06 - Publicação de projetos
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
 * Mensagem de erro.
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
// FUNÇÃO PARA EXIBIR TEXTO COM SEGURANÇA
// ----------------------------------------------------------
//
// htmlspecialchars() ajuda a evitar XSS.
//
// Qualquer conteúdo digitado pelo usuário será exibido
// como texto e não interpretado como HTML.
// ----------------------------------------------------------

function escaparPublicacao(
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
        Publicar projeto - SiteMatch
    </title>


    <!-- =====================================================
         CSS GLOBAL
         ===================================================== -->

    <link
        rel="stylesheet"
        href="/css/global.css"
    >


    <!-- =====================================================
         CSS DO BRIEFING GUIADO
         ===================================================== -->

    <link
        rel="stylesheet"
        href="/css/publicar-projeto.css"
    >

</head>


<body>


    <!-- =====================================================
         CONTAINER PRINCIPAL
         ===================================================== -->

    <main class="briefing-container">


        <!-- =================================================
             VOLTAR
             ================================================= -->

        <a
            href="dashboard.php"
            class="briefing-voltar"
        >
            ← Voltar para o painel
        </a>


        <!-- =================================================
             CABEÇALHO
             ================================================= -->

        <header class="briefing-header">


            <!-- SAUDAÇÃO -->

            <p class="briefing-saudacao">

                Olá,
                <?= escaparPublicacao(
                    $nomeUsuario
                ) ?>!

            </p>


            <!-- TÍTULO -->

            <h1>
                Conte um pouco sobre seu projeto
            </h1>


            <!-- TEXTO DE APOIO -->

            <p>

                Não precisa conhecer termos técnicos.
                Responda com suas próprias palavras e nós
                organizaremos as informações do projeto.

            </p>

        </header>


        <!-- =================================================
             CARD DO FORMULÁRIO
             ================================================= -->

        <section class="briefing-card">


            <!-- =============================================
                 ETAPA ATUAL
                 ============================================= -->

            <span class="briefing-etapa">
                Etapa 1 de 4
            </span>


            <h2>
                Informações iniciais
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

                    <?= escaparPublicacao(
                        $mensagem
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- =============================================
                 FORMULÁRIO
                 =============================================
                 method="POST":
                 envia os dados para a própria página pública.
                 ============================================= -->

            <form method="POST">


                <!-- =========================================
                     TÍTULO DO PROJETO
                     ========================================= -->

                <div class="campo">

                    <label for="titulo">

                        Como você gostaria de chamar
                        seu projeto?

                    </label>


                    <input
                        type="text"
                        id="titulo"
                        name="titulo"
                        maxlength="150"
                        required
                        placeholder="Ex.: Sistema para minha clínica"

                        value="<?= escaparPublicacao(
                            $dadosFormulario['titulo'] ?? ''
                        ) ?>"
                    >

                </div>


                <!-- =========================================
                     CATEGORIA
                     ========================================= -->

                <div class="campo">

                    <label for="categoria">

                        O que você deseja criar?

                    </label>


                    <select
                        id="categoria"
                        name="categoria"
                        required
                    >


                        <!-- OPÇÃO INICIAL -->

                        <option value="">

                            Selecione uma opção

                        </option>


                        <!-- SITE -->

                        <option
                            value="site"

                            <?= (
                                $dadosFormulario['categoria'] ?? ''
                            ) === 'site'
                                ? 'selected'
                                : '' ?>
                        >

                            Site

                        </option>


                        <!-- SISTEMA WEB -->

                        <option
                            value="sistema_web"

                            <?= (
                                $dadosFormulario['categoria'] ?? ''
                            ) === 'sistema_web'
                                ? 'selected'
                                : '' ?>
                        >

                            Sistema web

                        </option>


                        <!-- APLICATIVO MOBILE -->

                        <option
                            value="aplicativo_mobile"

                            <?= (
                                $dadosFormulario['categoria'] ?? ''
                            ) === 'aplicativo_mobile'
                                ? 'selected'
                                : '' ?>
                        >

                            Aplicativo mobile

                        </option>


                        <!-- SISTEMA DESKTOP -->

                        <option
                            value="sistema_desktop"

                            <?= (
                                $dadosFormulario['categoria'] ?? ''
                            ) === 'sistema_desktop'
                                ? 'selected'
                                : '' ?>
                        >

                            Sistema para computador

                        </option>


                        <!-- OUTRO -->

                        <option
                            value="outro"

                            <?= (
                                $dadosFormulario['categoria'] ?? ''
                            ) === 'outro'
                                ? 'selected'
                                : '' ?>
                        >

                            Outro

                        </option>

                    </select>

                </div>


                <!-- =========================================
                     DESCRIÇÃO INICIAL
                     ========================================= -->

                <div class="campo">

                    <label for="descricao">

                        Explique rapidamente o que
                        você precisa.

                    </label>


                    <small>

                        Pense no problema que deseja resolver
                        ou no resultado que gostaria de alcançar.

                    </small>


                    <textarea
                        id="descricao"
                        name="descricao"
                        required
                        placeholder="Ex.: Tenho uma clínica e quero permitir que os pacientes agendem consultas pela internet."
                    ><?= escaparPublicacao(
                        $dadosFormulario['descricao'] ?? ''
                    ) ?></textarea>

                </div>


                <!-- =========================================
                     BOTÃO
                     ========================================= -->

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