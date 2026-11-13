// ==========================================================
// AUTENTICAÇÃO - INTERAÇÕES DE INTERFACE
// ==========================================================
// Este arquivo não valida credenciais e não substitui o PHP.
// Ele apenas permite mostrar/ocultar senhas para melhorar a
// experiência de uso dos formulários de login e cadastro.
// ==========================================================

document.addEventListener('DOMContentLoaded', function () {
    const botoes = document.querySelectorAll('[data-toggle-password]');

    botoes.forEach(function (botao) {
        botao.addEventListener('click', function () {
            const alvoId = botao.getAttribute('data-toggle-password');
            const campo = document.getElementById(alvoId);

            if (!campo) {
                return;
            }

            const exibindo = campo.type === 'text';

            campo.type = exibindo ? 'password' : 'text';
            botao.textContent = exibindo ? 'Mostrar' : 'Ocultar';
            botao.setAttribute('aria-pressed', exibindo ? 'false' : 'true');
        });
    });
});
