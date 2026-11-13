<?php
// ==========================================================
// VIEW: CADASTRO
// ==========================================================
// Mantém RF01/RN02, porém a escolha de perfil foi transformada
// nos grandes cartões previstos no protótipo.
// ==========================================================
function escapar(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

$tipoPreSelecionado = (string) ($_GET['tipo'] ?? ($dadosFormulario['tipo'] ?? ''));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta - SiteMatch</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/auth.css">
    <script src="/js/auth.js" defer></script>
    <script src="/js/ui.js" defer></script>
</head>
<body>
<div class="auth-page">
    <header class="auth-top">
        <a href="/" class="auth-logo" aria-label="SiteMatch">Site<span>Match</span></a>
        <a href="/login.php" class="auth-back" aria-label="Voltar">←</a>
    </header>

    <main class="auth-content">
        <header class="auth-card-header">
            <h1>Qual seu perfil?</h1>
            <p>Escolha como você quer usar o SiteMatch</p>
        </header>

        <?php if ($mensagem !== ''): ?>
            <div class="auth-alert" role="alert" <?= $sucesso ? 'style="color:#0e684d;background:#eaf8f2;border-color:#ccecdf"' : '' ?>><?= escapar($mensagem) ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form" data-loading>
            <fieldset class="auth-fieldset">
                <div class="auth-profile-options">
                    <label class="auth-profile-option">
                        <input type="radio" name="tipo" value="cliente" required <?= $tipoPreSelecionado === 'cliente' ? 'checked' : '' ?>>
                        <span class="auth-profile-icon">▣</span>
                        <strong>Sou empreendedor</strong>
                        <small>Preciso de um projeto digital para o meu negócio</small>
                        <span class="auth-profile-arrow">→</span>
                    </label>

                    <label class="auth-profile-option">
                        <input type="radio" name="tipo" value="desenvolvedor" required <?= $tipoPreSelecionado === 'desenvolvedor' ? 'checked' : '' ?>>
                        <span class="auth-profile-icon">↔</span>
                        <strong>Sou desenvolvedor</strong>
                        <small>Quero encontrar projetos e novos clientes</small>
                        <span class="auth-profile-arrow">→</span>
                    </label>
                </div>
            </fieldset>

            <section class="auth-card" style="margin-top:22px;">
                <div class="auth-field"><label for="nome">Nome</label><input type="text" id="nome" name="nome" maxlength="150" autocomplete="name" placeholder="Seu nome completo" required value="<?= escapar($dadosFormulario['nome'] ?? '') ?>"></div>
                <div class="auth-field"><label for="email">E-mail</label><input type="email" id="email" name="email" maxlength="150" autocomplete="email" placeholder="Exemplo123@gmail.com" required value="<?= escapar($dadosFormulario['email'] ?? '') ?>"></div>
                <div class="auth-field"><label for="senha">Senha</label><div class="auth-password-wrap"><input type="password" id="senha" name="senha" minlength="8" autocomplete="new-password" placeholder="Mínimo de 8 caracteres" required><button type="button" class="senha-toggle" data-toggle-password="senha">Mostrar</button></div></div>
                <div class="auth-field"><label for="confirmar_senha">Confirmar senha</label><div class="auth-password-wrap"><input type="password" id="confirmar_senha" name="confirmar_senha" minlength="8" autocomplete="new-password" placeholder="Digite novamente" required><button type="button" class="senha-toggle" data-toggle-password="confirmar_senha">Mostrar</button></div></div>
                <div class="auth-submit-wrap" style="margin-top:18px;"><button type="submit" class="auth-submit">Criar conta</button></div>
            </section>
        </form>

        <p class="auth-switch">Já tem uma conta? <a href="login.php">Entrar</a></p>
    </main>
</div>
</body>
</html>
