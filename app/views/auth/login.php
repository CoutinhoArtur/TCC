<?php

// ==========================================================
// VIEW: LOGIN
// ==========================================================
//
// Responsável somente pela interface.
//
// RF02 - Autenticação
// ==========================================================


// ----------------------------------------------------------
// FUNÇÃO DE ESCAPE
// ----------------------------------------------------------
//
// cadastro.php já possui uma função com esse nome,
// mas cada página é executada separadamente.
// ----------------------------------------------------------

function escaparLogin(string $valor): string
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

    <title>Entrar - SiteMatch</title>

</head>

<body>

    <!-- =====================================================
         IDENTIDADE
         ===================================================== -->

    <header>

        <strong>
            SiteMatch
        </strong>

    </header>


    <main>

        <h1>
            Já tem uma conta?
        </h1>

        <p>
            Entre em sua conta
        </p>


        <!-- =================================================
             ERRO DE LOGIN
             ================================================= -->

        <?php if ($mensagem !== ''): ?>

            <div role="alert">

                <?= escaparLogin($mensagem) ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
             FORMULÁRIO
             ================================================= -->

        <form method="POST">

            <!-- E-MAIL -->

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
                    value="<?= escaparLogin(
                        $dadosFormulario['email'] ?? ''
                    ) ?>"
                >

            </div>


            <!-- SENHA -->

            <div>

                <label for="senha">
                    Senha
                </label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    required
                >

            </div>


            <!-- ENTRAR -->

            <button type="submit">
                Entrar
            </button>

        </form>


        <p>

            Ainda não possui uma conta?

            <a href="cadastro.php">
                Cadastrar
            </a>

        </p>

    </main>

</body>

</html>