/* ==========================================================
   INTERAÇÕES GERAIS - SITEMATCH
   ==========================================================
   Este arquivo melhora a experiência visual sem substituir
   nenhuma validação de segurança feita no PHP.
   ========================================================== */

document.addEventListener('DOMContentLoaded', () => {
    // ------------------------------------------------------
    // MENU LATERAL NO CELULAR
    // ------------------------------------------------------
    const menuButton = document.querySelector('[data-menu-toggle]');
    const sidebar = document.querySelector('.sidebar');

    menuButton?.addEventListener('click', () => {
        const aberto = sidebar?.classList.toggle('aberta') ?? false;
        document.body.classList.toggle('menu-aberto', aberto);
        menuButton.setAttribute('aria-expanded', aberto ? 'true' : 'false');
    });

    document.querySelectorAll('.sidebar a').forEach((link) => {
        link.addEventListener('click', () => {
            sidebar?.classList.remove('aberta');
            document.body.classList.remove('menu-aberto');
        });
    });

    // ------------------------------------------------------
    // TEXTAREAS CRESCEM AUTOMATICAMENTE
    // ------------------------------------------------------
    const ajustarTextarea = (textarea) => {
        textarea.style.height = 'auto';
        textarea.style.height = `${Math.min(textarea.scrollHeight, 220)}px`;
    };

    document.querySelectorAll('textarea[data-autogrow], .chat-form textarea').forEach((textarea) => {
        ajustarTextarea(textarea);
        textarea.addEventListener('input', () => ajustarTextarea(textarea));
    });

    // ------------------------------------------------------
    // FILTRO VISUAL DE CARDS JÁ CARREGADOS
    // ------------------------------------------------------
    document.querySelectorAll('[data-live-search]').forEach((input) => {
        const seletor = input.getAttribute('data-live-search');
        if (!seletor) return;

        const itens = [...document.querySelectorAll(seletor)];
        const normalizar = (valor) => valor
            .toLocaleLowerCase('pt-BR')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');

        input.addEventListener('input', () => {
            const busca = normalizar(input.value.trim());
            itens.forEach((item) => {
                const texto = normalizar(item.textContent || '');
                item.hidden = busca !== '' && !texto.includes(busca);
            });
        });
    });

    // ------------------------------------------------------
    // ABAS DO PERFIL PÚBLICO
    // ------------------------------------------------------
    document.querySelectorAll('[data-tabs]').forEach((grupo) => {
        const botoes = grupo.querySelectorAll('[data-tab-target]');
        const paineis = grupo.querySelectorAll('[data-tab-panel]');

        botoes.forEach((botao) => {
            botao.addEventListener('click', () => {
                const alvo = botao.getAttribute('data-tab-target');
                botoes.forEach((b) => b.classList.toggle('ativa', b === botao));
                paineis.forEach((painel) => {
                    painel.hidden = painel.getAttribute('data-tab-panel') !== alvo;
                });
            });
        });
    });

    // ------------------------------------------------------
    // CONFIRMAÇÕES MAIS BONITAS QUE window.confirm()
    // ------------------------------------------------------
    document.querySelectorAll('form[data-confirm], a[data-confirm]').forEach((elemento) => {
        // Links são confirmados no clique.
        if (elemento instanceof HTMLAnchorElement) {
            elemento.addEventListener('click', (evento) => {
                if (elemento.dataset.confirmado === '1') return;
                evento.preventDefault();
                const mensagem = elemento.getAttribute('data-confirm') || 'Deseja continuar?';
                abrirConfirmacao(mensagem, () => {
                    elemento.dataset.confirmado = '1';
                    window.location.href = elemento.href;
                });
            });
        }

        // Formulários são confirmados apenas no submit. Assim clicar
        // em um textarea/campo do formulário não abre o modal.
        if (elemento instanceof HTMLFormElement) {
            elemento.addEventListener('submit', (evento) => {
                if (elemento.dataset.confirmado === '1') return;
                evento.preventDefault();
                const mensagem = elemento.getAttribute('data-confirm') || 'Deseja continuar?';
                abrirConfirmacao(mensagem, () => {
                    elemento.dataset.confirmado = '1';
                    elemento.requestSubmit();
                });
            });
        }
    });

    // ------------------------------------------------------
    // FEEDBACK DE ENVIO / EVITA DUPLO CLIQUE
    // ------------------------------------------------------
    document.querySelectorAll('form[data-loading]').forEach((form) => {
        form.addEventListener('submit', () => {
            const botao = form.querySelector('button[type="submit"]');
            if (!botao) return;
            botao.disabled = true;
            botao.dataset.textoOriginal = botao.textContent || '';
            botao.textContent = 'Enviando...';
        });
    });

    // ------------------------------------------------------
    // ALERTAS/TÓSTES DESAPARECEM SUAVEMENTE
    // ------------------------------------------------------
    document.querySelectorAll('.alerta-sucesso[data-auto-hide], .auth-alert[data-auto-hide]').forEach((alerta) => {
        window.setTimeout(() => {
            alerta.style.transition = 'opacity .25s ease, transform .25s ease';
            alerta.style.opacity = '0';
            alerta.style.transform = 'translateY(-6px)';
        }, 4200);
    });
});

function abrirConfirmacao(mensagem, aoConfirmar) {
    const backdrop = document.createElement('div');
    backdrop.className = 'ui-modal-backdrop';
    backdrop.innerHTML = `
        <div class="ui-modal" role="dialog" aria-modal="true" aria-labelledby="ui-modal-titulo">
            <h3 id="ui-modal-titulo">Confirmar ação</h3>
            <p></p>
            <div class="ui-modal-actions">
                <button type="button" class="botao-secundario" data-modal-cancelar>Cancelar</button>
                <button type="button" class="botao-principal" data-modal-confirmar>Confirmar</button>
            </div>
        </div>`;
    backdrop.querySelector('p').textContent = mensagem;
    document.body.appendChild(backdrop);

    const fechar = () => backdrop.remove();
    backdrop.querySelector('[data-modal-cancelar]').addEventListener('click', fechar);
    backdrop.addEventListener('click', (evento) => {
        if (evento.target === backdrop) fechar();
    });
    backdrop.querySelector('[data-modal-confirmar]').addEventListener('click', () => {
        fechar();
        aoConfirmar();
    });
}
