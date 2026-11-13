<?php
// ==========================================================
// VIEW: LOGIN
// ==========================================================
// O PHP continua responsável pela autenticação. Esta View foi
// reorganizada apenas para reproduzir a linguagem do protótipo.
// ==========================================================
function escaparLogin(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - SiteMatch</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/auth.css">
    <script src="/js/auth.js" defer></script>
    <script src="/js/ui.js" defer></script>
</head>
<body>
<div class="auth-page">
    <header class="auth-top">
        <a href="/" class="auth-logo" aria-label="SiteMatch">Site<span>Match</span></a>
        <a href="/" class="auth-back" aria-label="Voltar">←</a>
    </header>

    <main class="auth-content">
        <header class="auth-card-header">
            <h1>Já tem uma conta?</h1>
            <p>Entre em sua conta</p>
        </header>

        <section class="auth-card">
            <?php if ($mensagem !== ''): ?>
                <div class="auth-alert" role="alert"><?= escaparLogin($mensagem) ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form" data-loading>
                <div class="auth-field">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" maxlength="150" autocomplete="email" placeholder="Exemplo123@gmail.com" required value="<?= escaparLogin($dadosFormulario['email'] ?? '') ?>">
                </div>

                <div class="auth-field">
                    <label for="senha">Senha</label>
                    <div class="auth-password-wrap">
                        <input type="password" id="senha" name="senha" autocomplete="current-password" placeholder="••••••••••••••••" required>
                        <button type="button" class="senha-toggle" data-toggle-password="senha" aria-pressed="false">Mostrar</button>
                    </div>
                </div>

                <div class="auth-options-line">
                    <span class="auth-forgot">Esqueceu a senha? <span style="color:#c16bf2">Recupere</span></span>
                    <div class="auth-submit-wrap"><button type="submit" class="auth-submit">Entrar</button></div>
                </div>
            </form>
        </section>

        <p class="auth-switch">Ainda não possui uma conta? <a href="cadastro.php">Cadastrar</a></p>
    </main>
</div>
</body>
</html>
