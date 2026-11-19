/* ==========================================================
   SITEMATCH · INTERAÇÕES DE APRESENTAÇÃO (V5)
   Apenas DOM/UX: o PHP mantém autenticação, autorização,
   validação, persistência e estados de todas as operações.
   ========================================================== */
(() => {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        configurarMenuMobile();
        configurarTextareas();
        configurarBuscaLocal();
        configurarAbas();
        configurarConfirmacoes();
        configurarFeedbackFormularios();
        configurarChat();
        configurarAvisos();
    });

    /* ======================================================
       NAVEGAÇÃO MOBILE · acessível por Escape e clique fora
       ====================================================== */
    function configurarMenuMobile() {
        const botao = document.querySelector('[data-menu-toggle]');
        const menu = document.querySelector('.sidebar');
        if (!botao || !menu) return;

        const sobreposicao = document.createElement('div');
        sobreposicao.className = 'sidebar-overlay';
        sobreposicao.hidden = true;
        document.body.appendChild(sobreposicao);

        function alternar(aberto) {
            menu.classList.toggle('aberta', aberto);
            document.body.classList.toggle('menu-aberto', aberto);
            botao.setAttribute('aria-expanded', String(aberto));
            sobreposicao.hidden = !aberto;
            if (!aberto) botao.focus({ preventScroll: true });
        }

        botao.setAttribute('aria-label', 'Abrir menu de navegação');
        botao.setAttribute('aria-controls', 'menu-principal');
        menu.id = 'menu-principal';
        botao.addEventListener('click', () => alternar(!menu.classList.contains('aberta')));
        sobreposicao.addEventListener('click', () => alternar(false));
        menu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                menu.classList.remove('aberta');
                document.body.classList.remove('menu-aberto');
                sobreposicao.hidden = true;
                botao.setAttribute('aria-expanded', 'false');
            });
        });
        document.addEventListener('keydown', (evento) => {
            if (evento.key === 'Escape' && menu.classList.contains('aberta')) alternar(false);
        });
        window.matchMedia('(min-width: 781px)').addEventListener('change', (evento) => {
            if (evento.matches && menu.classList.contains('aberta')) alternar(false);
        });
    }

    /* ======================================================
       TEXTAREAS · ajuste apenas de altura, sem mexer no valor
       ====================================================== */
    function configurarTextareas() {
        document.querySelectorAll('textarea[data-autogrow], .chat-form textarea').forEach((campo) => {
            const ajustar = () => {
                campo.style.height = 'auto';
                campo.style.height = `${Math.min(campo.scrollHeight, 220)}px`;
            };
            ajustar();
            campo.addEventListener('input', ajustar);
        });
    }

    /* ======================================================
       PESQUISA CLIENT-SIDE · apenas elementos já renderizados
       Filtros GET/SQL existentes não são substituídos.
       ====================================================== */
    function configurarBuscaLocal() {
        const normalizar = (texto) => String(texto || '')
            .toLocaleLowerCase('pt-BR').normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');

        document.querySelectorAll('[data-live-search]').forEach((campo) => {
            const seletor = campo.getAttribute('data-live-search');
            if (!seletor) return;
            const itens = [...document.querySelectorAll(seletor)];
            if (!itens.length) return;

            const contador = document.createElement('p');
            contador.className = 'ui-search-empty';
            contador.setAttribute('role', 'status');
            contador.hidden = true;
            const destino = itens[0].parentElement;
            destino?.appendChild(contador);

            const filtrar = () => {
                const termo = normalizar(campo.value.trim());
                let encontrados = 0;
                itens.forEach((item) => {
                    const corresponde = !termo || normalizar(item.textContent).includes(termo);
                    item.hidden = !corresponde;
                    if (corresponde) encontrados++;
                });
                contador.hidden = encontrados !== 0;
                contador.textContent = 'Nenhum resultado entre os itens exibidos.';
            };
            campo.addEventListener('input', filtrar);
            campo.setAttribute('aria-label', campo.getAttribute('placeholder') || 'Pesquisar nesta página');
        });
    }

    /* ======================================================
       ABAS · ocultação visual preserva HTML/PHP já produzido
       ====================================================== */
    function configurarAbas() {
        document.querySelectorAll('[data-tabs]').forEach((grupo, indice) => {
            const botoes = [...grupo.querySelectorAll('[data-tab-target]')];
            const paineis = [...grupo.querySelectorAll('[data-tab-panel]')];
            if (!botoes.length) return;
            botoes.forEach((botao, i) => {
                const alvo = botao.getAttribute('data-tab-target');
                const painel = paineis.find((p) => p.getAttribute('data-tab-panel') === alvo);
                const idBotao = `site-tab-${indice}-${i}`;
                botao.id = idBotao;
                botao.setAttribute('role', 'tab');
                botao.setAttribute('aria-selected', String(botao.classList.contains('ativa')));
                if (painel) {
                    painel.id = `site-painel-${indice}-${i}`;
                    painel.setAttribute('role', 'tabpanel');
                    painel.setAttribute('aria-labelledby', idBotao);
                    botao.setAttribute('aria-controls', painel.id);
                }
                botao.addEventListener('click', () => selecionar(botao));
                botao.addEventListener('keydown', (evento) => {
                    if (!['ArrowRight', 'ArrowLeft', 'Home', 'End'].includes(evento.key)) return;
                    evento.preventDefault();
                    const pos = evento.key === 'Home' ? 0 : evento.key === 'End' ? botoes.length - 1 :
                        (i + (evento.key === 'ArrowRight' ? 1 : -1) + botoes.length) % botoes.length;
                    selecionar(botoes[pos]);
                    botoes[pos].focus();
                });
            });
            const tablist = botoes[0].parentElement;
            tablist?.setAttribute('role', 'tablist');
            function selecionar(ativo) {
                const alvo = ativo.getAttribute('data-tab-target');
                botoes.forEach((b) => {
                    const selecionado = b === ativo;
                    b.classList.toggle('ativa', selecionado);
                    b.setAttribute('aria-selected', String(selecionado));
                    b.tabIndex = selecionado ? 0 : -1;
                });
                paineis.forEach((p) => { p.hidden = p.getAttribute('data-tab-panel') !== alvo; });
            }
            selecionar(botoes.find((b) => b.classList.contains('ativa')) || botoes[0]);
        });
    }

    /* ======================================================
       CONFIRMAÇÃO · só prossegue após a aprovação do usuário
       ====================================================== */
    function configurarConfirmacoes() {
        document.querySelectorAll('a[data-confirm], form[data-confirm]').forEach((elemento) => {
            if (elemento instanceof HTMLAnchorElement) {
                elemento.addEventListener('click', (evento) => {
                    if (elemento.dataset.confirmado === '1') return;
                    evento.preventDefault();
                    abrirConfirmacao(elemento.dataset.confirm || 'Deseja continuar?', () => {
                        elemento.dataset.confirmado = '1';
                        window.location.assign(elemento.href);
                    });
                });
            } else if (elemento instanceof HTMLFormElement) {
                elemento.addEventListener('submit', (evento) => {
                    if (elemento.dataset.confirmado === '1') return;
                    evento.preventDefault();
                    abrirConfirmacao(elemento.dataset.confirm || 'Deseja continuar?', () => {
                        elemento.dataset.confirmado = '1';
                        elemento.requestSubmit(evento.submitter || undefined);
                    });
                });
            }
        });
    }

    /* ======================================================
       ESTADO ENVIANDO · nunca trava formulário cancelado
       ====================================================== */
    function configurarFeedbackFormularios() {
        document.querySelectorAll('form[data-loading]').forEach((form) => {
            form.addEventListener('submit', (evento) => {
                // O modal de confirmação já impediu o envio: aguardar a escolha.
                if (evento.defaultPrevented || !form.checkValidity()) return;
                // Impedir vários envios enquanto a requisição HTTP navega.
                if (form.dataset.enviando === '1') { evento.preventDefault(); return; }
                form.dataset.enviando = '1';
                const botao = evento.submitter || form.querySelector('button[type="submit"]');
                if (!botao) return;
                botao.setAttribute('aria-busy', 'true');
                botao.classList.add('ui-is-loading');
                // Deferir o disable até que o browser monte os dados POST.
                window.setTimeout(() => { botao.disabled = true; }, 0);
            });
        });
        // Em navegação voltar/avançar, eliminar estados desatualizados do bfcache.
        window.addEventListener('pageshow', () => {
            document.querySelectorAll('form[data-loading]').forEach((form) => {
                delete form.dataset.enviando;
                form.querySelectorAll('.ui-is-loading').forEach((botao) => {
                    botao.disabled = false;
                    botao.removeAttribute('aria-busy');
                    botao.classList.remove('ui-is-loading');
                });
            });
        });
    }

    /* ======================================================
       CHAT · rolagem somente dentro da área das mensagens
       ====================================================== */
    function configurarChat() {
        const area = document.getElementById('chat-mensagens');
        if (!area) return;
        const rolarParaUltima = () => { area.scrollTop = area.scrollHeight; };
        rolarParaUltima();
        requestAnimationFrame(rolarParaUltima);
    }

    function configurarAvisos() {
        document.querySelectorAll('.alerta-sucesso[data-auto-hide], .auth-alert[data-auto-hide]').forEach((alerta) => {
            window.setTimeout(() => {
                alerta.style.transition = 'opacity .25s ease, transform .25s ease';
                alerta.style.opacity = '0';
                alerta.style.transform = 'translateY(-6px)';
            }, 4600);
        });
    }

    /* Modal acessível, com Escape, foco inicial e restauração de foco. */
    function abrirConfirmacao(mensagem, aoConfirmar) {
        const focoAnterior = document.activeElement;
        const fundo = document.createElement('div');
        fundo.className = 'ui-modal-backdrop';
        fundo.innerHTML = `
            <div class="ui-modal" role="dialog" aria-modal="true" aria-labelledby="ui-modal-titulo" aria-describedby="ui-modal-descricao" tabindex="-1">
                <h3 id="ui-modal-titulo">Confirmar ação</h3>
                <p id="ui-modal-descricao"></p>
                <div class="ui-modal-actions">
                    <button type="button" class="botao-secundario" data-modal-cancelar>Cancelar</button>
                    <button type="button" class="botao-principal" data-modal-confirmar>Confirmar</button>
                </div>
            </div>`;
        fundo.querySelector('p').textContent = mensagem;
        document.body.appendChild(fundo);
        const cancelar = fundo.querySelector('[data-modal-cancelar]');
        const confirmar = fundo.querySelector('[data-modal-confirmar]');
        const fechar = () => {
            document.removeEventListener('keydown', teclado);
            fundo.remove();
            focoAnterior?.focus?.();
        };
        const teclado = (evento) => {
            if (evento.key === 'Escape') { evento.preventDefault(); fechar(); }
            if (evento.key === 'Tab') {
                const itens = [cancelar, confirmar];
                const atual = itens.indexOf(document.activeElement);
                if (evento.shiftKey && atual === 0) { evento.preventDefault(); confirmar.focus(); }
                if (!evento.shiftKey && atual === 1) { evento.preventDefault(); cancelar.focus(); }
            }
        };
        document.addEventListener('keydown', teclado);
        cancelar.addEventListener('click', fechar);
        fundo.addEventListener('click', (evento) => { if (evento.target === fundo) fechar(); });
        confirmar.addEventListener('click', () => { fechar(); aoConfirmar(); });
        cancelar.focus();
    }
})();
