<?php

// ==========================================================
// VIEW: CADASTRO DE USUÁRIO
// ==========================================================
// Esta página é responsável pela interface do cadastro.
//
// Ela utiliza variáveis preparadas pelo arquivo:
//
// public/cadastro.php
//
// Variáveis disponíveis:
//
// $mensagem
// $sucesso
// $dadosFormulario
//
// A View não realiza INSERT diretamente no banco.
// ==========================================================


// ----------------------------------------------------------
// FUNÇÃO PARA EXIBIR TEXTO COM SEGURANÇA
// ----------------------------------------------------------
// htmlspecialchars() impede que textos digitados pelo
// usuário sejam interpretados como HTML.
//
// Isso ajuda a evitar XSS.
//
// Exemplo:
//
// <script>alert('teste')</script>
//
// será exibido como texto na página.
// ----------------------------------------------------------

function escapar(string $valor): string
{
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

    <title>Cadastro - Projeto Match</title>

</head>

<body>

    <!-- =====================================================
         CABEÇALHO
         ===================================================== -->

    <header>

        <h1>Criar conta</h1>

        <p>
            Escolha seu perfil e comece a utilizar a plataforma.
        </p>

    </header>


    <main>

        <!-- =================================================
             MENSAGEM DE SUCESSO OU ERRO
             =================================================
             A mensagem vem do AuthController.
             ================================================= -->

        <?php if ($mensagem !== ''): ?>

            <div role="alert">

                <?= escapar($mensagem) ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
             FORMULÁRIO DE CADASTRO
             =================================================
             method="POST":
             envia os dados no corpo da requisição.
             ================================================= -->

        <form method="POST">


            <!-- =============================================
                 NOME COMPLETO
                 ============================================= -->

            <div>

                <label for="nome">
                    Nome completo
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    maxlength="150"
                    required
                    value="<?= escapar($dadosFormulario['nome'] ?? '') ?>"
                >

            </div>


            <!-- =============================================
                 E-MAIL
                 ============================================= -->

            <div>

                <label for="email">
                    E-mail
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="150"
                    required
                    value="<?= escapar($dadosFormulario['email'] ?? '') ?>"
                >

            </div>


            <!-- =============================================
                 SENHA
                 ============================================= -->

            <div>

                <label for="senha">
                    Senha
                </label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    minlength="8"
                    required
                >

                <small>
                    Utilize pelo menos 8 caracteres.
                </small>

            </div>


            <!-- =============================================
                 CONFIRMAÇÃO DA SENHA
                 ============================================= -->

            <div>

                <label for="confirmar_senha">
                    Confirmar senha
                </label>

                <input
                    type="password"
                    id="confirmar_senha"
                    name="confirmar_senha"
                    minlength="8"
                    required
                >

            </div>


            <!-- =============================================
                 TIPO DE USUÁRIO
                 =============================================
                 RN02:
                 cada conta deve ser cliente ou desenvolvedor.
                 ============================================= -->

            <fieldset>

                <legend>
                    Como você deseja utilizar a plataforma?
                </legend>


                <!-- CLIENTE -->

                <label>

                    <input
                        type="radio"
                        name="tipo"
                        value="cliente"
                        required

                        <?= ($dadosFormulario['tipo'] ?? '') === 'cliente'
                            ? 'checked'
                            : '' ?>
                    >

                    Sou cliente

                </label>


                <!-- DESENVOLVEDOR -->

                <label>

                    <input
                        type="radio"
                        name="tipo"
                        value="desenvolvedor"
                        required

                        <?= ($dadosFormulario['tipo'] ?? '') === 'desenvolvedor'
                            ? 'checked'
                            : '' ?>
                    >

                    Sou desenvolvedor

                </label>

            </fieldset>


            <!-- =============================================
                 BOTÃO DE ENVIO
                 ============================================= -->

            <button type="submit">

                Criar conta

            </button>

        </form>


        <!-- =================================================
             LINK PARA LOGIN
             =================================================
             A página login.php será criada na próxima etapa.
             ================================================= -->

        <p>

            Já possui uma conta?

            <a href="login.php">
                Entrar
            </a>

        </p>

    </main>

</body>

</html>