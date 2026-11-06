// ==========================================================
// PROPOSTA DEFINITIVA - JAVASCRIPT
// ==========================================================
//
// Responsabilidade:
//
// pedir confirmação antes que o desenvolvedor envie
// a proposta definitiva.
//
// IMPORTANTE:
//
// Este JavaScript melhora a experiência do usuário.
//
// A segurança e as regras continuam sendo validadas
// pelo PHP no servidor.
// ==========================================================


// ----------------------------------------------------------
// AGUARDAR HTML CARREGAR
// ----------------------------------------------------------

document.addEventListener(
    'DOMContentLoaded',
    function () {

        // --------------------------------------------------
        // LOCALIZAR FORMULÁRIO
        // --------------------------------------------------

        const formulario =
            document.querySelector(
                '#form-proposta-definitiva'
            );


        // Se o formulário não estiver na página,
        // não fazemos nada.
        //
        // Isso acontece quando a proposta já está
        // definitiva, aceita ou recusada.
        if (!formulario) {

            return;
        }


        // --------------------------------------------------
        // INTERCEPTAR ENVIO
        // --------------------------------------------------

        formulario.addEventListener(
            'submit',
            function (evento) {

                // Exibe a confirmação padrão do navegador.
                const confirmou =
                    window.confirm(
                        'Após enviar a proposta definitiva, '
                        + 'ela ficará disponível para o cliente '
                        + 'aceitar ou recusar.\n\n'
                        + 'Deseja continuar?'
                    );


                // Caso clique em "Cancelar",
                // impedimos o envio do formulário.
                if (!confirmou) {

                    evento.preventDefault();
                }
            }
        );
    }
);