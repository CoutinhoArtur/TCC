// ==========================================================
// DECISÃO DE PROPOSTA - CLIENTE
// ==========================================================
//
// Confirma ações importantes antes de enviar
// o formulário para o PHP.
//
// O JavaScript NÃO substitui as validações do servidor.
// ==========================================================

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const formularios =
            document.querySelectorAll(
                '.form-decisao-proposta'
            );


        formularios.forEach(
            function (formulario) {

                formulario.addEventListener(
                    'submit',
                    function (evento) {

                        const acao =
                            formulario.dataset.acao;


                        // ----------------------------------
                        // ACEITAR
                        // ----------------------------------

                        if (acao === 'aceitar') {

                            const confirmou =
                                window.confirm(
                                    'Deseja aceitar esta proposta?\n\n'
                                    + 'Ao confirmar, uma contratação será criada '
                                    + 'e as demais propostas deste projeto serão recusadas.'
                                );


                            if (!confirmou) {

                                evento.preventDefault();
                            }
                        }


                        // ----------------------------------
                        // RECUSAR
                        // ----------------------------------

                        if (acao === 'recusar') {

                            const confirmou =
                                window.confirm(
                                    'Deseja realmente recusar esta proposta?'
                                );


                            if (!confirmou) {

                                evento.preventDefault();
                            }
                        }
                    }
                );
            }
        );
    }
);