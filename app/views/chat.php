<?php

// ==========================================================
// VIEW: CHAT / NEGOCIAÇÃO
// ==========================================================
//
// Esta interface mostra a conversa relacionada
// a uma proposta.
//
// As mensagens do usuário logado ficam à direita.
//
// As mensagens da outra pessoa ficam à esquerda.
//
// RF13 - Comunicação entre usuários
// RN15 - Chat liberado após proposta
// ==========================================================


/**
 * Usuário autenticado.
 *
 * @var array<string, mixed> $usuario
 */


/**
 * ID de usuarios.id do usuário atual.
 *
 * @var int $usuarioId
 */


/**
 * ID da proposta.
 *
 * @var int $propostaId
 */


/**
 * Participantes da negociação.
 *
 * @var array<string, mixed> $participantes
 */


/**
 * Histórico da conversa.
 *
 * @var array<int, array<string, mixed>> $mensagens
 */


/**
 * Mensagem de erro.
 *
 * @var string $mensagemErro
 */


/**
 * Tipo do usuário.
 *
 * @var string $tipoUsuario
 */


/**
 * Nome do usuário.
 *
 * @var string $nomeUsuario
 */


/**
 * Título do projeto.
 *
 * @var string $projetoTitulo
 */


/**
 * Status da proposta.
 *
 * @var string $propostaStatus
 */


// ----------------------------------------------------------
// ESCAPE
// ----------------------------------------------------------

function escaparChat(
    string $valor
): string {

    return htmlspecialchars(
        $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}


// ----------------------------------------------------------
// FORMATAR DATA DA MENSAGEM
// ----------------------------------------------------------

function formatarHorarioChat(
    string $data
): string {

    if ($data === '') {

        return '';
    }


    try {

        $objeto =
            new DateTime($data);


        return $objeto->format(
            'd/m H:i'
        );


    } catch (Throwable $erro) {

        return $data;
    }
}


// ----------------------------------------------------------
// STATUS AMIGÁVEL
// ----------------------------------------------------------

$statusAmigaveis = [

    'preliminar' =>
        'Proposta preliminar',

    'em_negociacao' =>
        'Em negociação',

    'aguardando_revisao' =>
        'Aguardando revisão',

    'definitiva' =>
        'Proposta definitiva',

    'aceita' =>
        'Proposta aceita',

    'recusada' =>
        'Proposta recusada',

    'encerrada' =>
        'Proposta encerrada',

    'cancelada' =>
        'Proposta cancelada'
];


$statusExibicao =
    $statusAmigaveis[$propostaStatus]
    ?? $propostaStatus;


// ==========================================================
// LINK DE VOLTA
// ==========================================================
//
// O desenvolvedor volta para sua proposta.
//
// O cliente volta para seu dashboard por enquanto.
// ==========================================================

if ($tipoUsuario === 'desenvolvedor') {

    $projetoId = (int) (
        $participantes['projeto_id'] ?? 0
    );


    $voltarUrl =
        '/desenvolvedor/enviar-proposta.php?id='
        . $projetoId;

} else {

    $voltarUrl =
        '/cliente/dashboard.php';
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
        Conversa - SiteMatch
    </title>


    <!-- CSS GLOBAL -->

    <link
        rel="stylesheet"
        href="/css/global.css"
    >


    <!-- CSS DO CHAT -->

    <link
        rel="stylesheet"
        href="/css/chat.css"
    >

</head>


<body>


    <main class="chat-container">


        <!-- =================================================
             VOLTAR
             ================================================= -->

        <a
            href="<?= escaparChat(
                $voltarUrl
            ) ?>"
            class="chat-voltar"
        >
            ← Voltar
        </a>


        <!-- =================================================
             CARD DA CONVERSA
             ================================================= -->

        <section class="chat-card">


            <!-- =============================================
                 CABEÇALHO
                 ============================================= -->

            <header class="chat-header">


                <div class="chat-header-info">

                    <h1>

                        <?= escaparChat(
                            $projetoTitulo
                        ) ?>

                    </h1>


                    <p>

                        Negociação da proposta
                        #<?= (int) $propostaId ?>

                    </p>

                </div>


                <span class="chat-status">

                    <?= escaparChat(
                        $statusExibicao
                    ) ?>

                </span>

            </header>


            <!-- =============================================
                 MENSAGENS
                 ============================================= -->

            <div class="chat-mensagens">


                <?php if (empty($mensagens)): ?>


                    <!-- =====================================
                         NENHUMA MENSAGEM
                         ===================================== -->

                    <div class="chat-vazio">

                        <div class="chat-vazio-conteudo">

                            <h2>
                                Inicie a negociação
                            </h2>


                            <p>

                                A proposta preliminar já foi
                                enviada. Agora cliente e
                                desenvolvedor podem conversar
                                sobre detalhes, dúvidas e
                                possíveis ajustes.

                            </p>

                        </div>

                    </div>


                <?php else: ?>


                    <!-- =====================================
                         HISTÓRICO
                         ===================================== -->

                    <?php foreach (
                        $mensagens as $mensagem
                    ): ?>


                        <?php

                        // ----------------------------------
                        // DESCOBRIR SE A MENSAGEM É MINHA
                        // ----------------------------------

                        $remetenteId = (int) (
                            $mensagem['remetente_id'] ?? 0
                        );


                        $ehMinha =
                            $remetenteId === $usuarioId;


                        // Classe visual.
                        $classeMensagem =
                            $ehMinha
                                ? 'minha'
                                : 'outra';

                        ?>


                        <div
                            class="mensagem-linha <?= $classeMensagem ?>"
                        >


                            <div class="mensagem-bolha">


                                <!-- REMETENTE -->

                                <span class="mensagem-remetente">

                                    <?php if ($ehMinha): ?>

                                        Você

                                    <?php else: ?>

                                        <?= escaparChat(
                                            (string) (
                                                $mensagem[
                                                    'remetente_nome'
                                                ]
                                                ?? ''
                                            )
                                        ) ?>

                                    <?php endif; ?>

                                </span>


                                <!-- CONTEÚDO -->

                                <div class="mensagem-conteudo">

                                    <?= nl2br(
                                        escaparChat(
                                            (string) (
                                                $mensagem[
                                                    'conteudo'
                                                ]
                                                ?? ''
                                            )
                                        )
                                    ) ?>

                                </div>


                                <!-- HORÁRIO -->

                                <span class="mensagem-horario">

                                    <?= escaparChat(
                                        formatarHorarioChat(
                                            (string) (
                                                $mensagem[
                                                    'enviada_em'
                                                ]
                                                ?? ''
                                            )
                                        )
                                    ) ?>

                                </span>

                            </div>

                        </div>


                    <?php endforeach; ?>


                <?php endif; ?>


            </div>


            <!-- =============================================
                 ERRO
                 ============================================= -->

            <?php if ($mensagemErro !== ''): ?>

                <div class="chat-erro">

                    <?= escaparChat(
                        $mensagemErro
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- =============================================
                 CAMPO DE ENVIO
                 ============================================= -->

            <div class="chat-form-area">


                <form
                    method="POST"
                    class="chat-form"
                >


                    <textarea
                        name="conteudo"
                        maxlength="3000"
                        required
                        placeholder="Digite sua mensagem..."
                    ></textarea>


                    <button
                        type="submit"
                        class="chat-enviar"
                    >
                        Enviar
                    </button>


                </form>

            </div>


        </section>

    </main>


    <!-- =====================================================
         ROLAR PARA A MENSAGEM MAIS RECENTE
         =====================================================
         Pequeno JavaScript apenas para posicionar o chat
         automaticamente no final da conversa.
         ===================================================== -->

    <script>

        const areaMensagens =
            document.querySelector('.chat-mensagens');


        if (areaMensagens) {

            areaMensagens.scrollTop =
                areaMensagens.scrollHeight;
        }

    </script>

</body>

</html>