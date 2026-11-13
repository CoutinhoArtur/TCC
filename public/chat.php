<?php

// ==========================================================
// CHAT / NEGOCIAÇÃO
// ==========================================================
//
// Esta página controla a conversa entre:
//
// cliente
//    ↕
// desenvolvedor
//
// O chat sempre está ligado a uma proposta.
//
// Exemplo:
//
// chat.php?proposta=2
//
// REGRA:
// somente o cliente dono do projeto e o desenvolvedor
// responsável pela proposta podem acessar a conversa.
//
// RF13 - Comunicação entre usuários
// RN15 - Chat liberado após envio da proposta
// RNF03 - Controle de acesso
// ==========================================================


// ----------------------------------------------------------
// INICIAR SESSÃO
// ----------------------------------------------------------

session_start();


// ----------------------------------------------------------
// VERIFICAR LOGIN
// ----------------------------------------------------------

if (!isset($_SESSION['usuario'])) {

    header('Location: login.php');

    exit;
}


// ----------------------------------------------------------
// RECUPERAR USUÁRIO DA SESSÃO
// ----------------------------------------------------------

/**
 * @var array<string, mixed> $usuario
 */

$usuario = $_SESSION['usuario'];


// ID da conta autenticada.
//
// IMPORTANTE:
//
// Aqui usamos usuarios.id,
// porque mensagens.remetente_id também utiliza usuarios.id.
$usuarioId = (int) (
    $usuario['id'] ?? 0
);


// ----------------------------------------------------------
// VALIDAR USUÁRIO
// ----------------------------------------------------------

if ($usuarioId <= 0) {

    session_destroy();

    header('Location: login.php');

    exit;
}


// ==========================================================
// RECEBER ID DA PROPOSTA
// ==========================================================
//
// Exemplo:
//
// /chat.php?proposta=2
//
// ==========================================================

$propostaId = filter_input(
    INPUT_GET,
    'proposta',
    FILTER_VALIDATE_INT
);


// ----------------------------------------------------------
// VALIDAR ID DA PROPOSTA
// ----------------------------------------------------------

if (
    $propostaId === false ||
    $propostaId === null ||
    $propostaId <= 0
) {

    http_response_code(400);

    exit(
        'Proposta inválida.'
    );
}


// ----------------------------------------------------------
// CARREGAR CONEXÃO COM O BANCO
// ----------------------------------------------------------

require_once __DIR__
    . '/../app/config/database.php';


// ----------------------------------------------------------
// CARREGAR MODEL DE MENSAGENS
// ----------------------------------------------------------

require_once __DIR__
    . '/../app/models/Mensagem.php';

// Lista lateral de conversas usada apenas para compor a interface.
require_once __DIR__
    . '/../app/models/Conversa.php';


// ----------------------------------------------------------
// CRIAR MODEL
// ----------------------------------------------------------

$mensagemModel =
    new Mensagem($pdo);

$conversaModel =
    new Conversa($pdo);


// ==========================================================
// BUSCAR PARTICIPANTES
// ==========================================================
//
// Aqui descobrimos:
//
// - qual projeto pertence à conversa;
// - qual usuário é o cliente;
// - qual usuário é o desenvolvedor;
// - qual é o status da proposta.
//
// ==========================================================

$participantes =
    $mensagemModel
        ->buscarParticipantes(
            (int) $propostaId
        );


// ----------------------------------------------------------
// PROPOSTA NÃO EXISTE
// ----------------------------------------------------------

if ($participantes === false) {

    http_response_code(404);

    exit(
        'Conversa não encontrada.'
    );
}


// ==========================================================
// VERIFICAR AUTORIZAÇÃO
// ==========================================================
//
// Não basta saber o número da proposta.
//
// O usuário precisa participar dela.
//
// Isso impede que alguém tente acessar:
//
// /chat.php?proposta=1
// /chat.php?proposta=2
// /chat.php?proposta=3
//
// até encontrar uma conversa de outra pessoa.
// ==========================================================

$usuarioParticipa =
    $mensagemModel
        ->usuarioParticipa(
            (int) $propostaId,
            $usuarioId
        );


if (!$usuarioParticipa) {

    http_response_code(403);

    exit(
        'Você não possui acesso a esta conversa.'
    );
}


// ----------------------------------------------------------
// VARIÁVEIS DA VIEW
// ----------------------------------------------------------

$mensagemErro = '';


// ==========================================================
// ENVIAR NOVA MENSAGEM
// ==========================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // ------------------------------------------------------
    // RECEBER CONTEÚDO
    // ------------------------------------------------------

    $conteudo = trim(
        $_POST['conteudo'] ?? ''
    );


    // ------------------------------------------------------
    // VALIDAR MENSAGEM
    // ------------------------------------------------------

    if ($conteudo === '') {

        $mensagemErro =
            'Digite uma mensagem antes de enviar.';
    }


    // Limite simples para evitar textos exageradamente
    // grandes dentro de uma única mensagem.
    elseif (mb_strlen($conteudo) > 3000) {

        $mensagemErro =
            'A mensagem é muito longa.';
    }


    // ======================================================
    // GRAVAR MENSAGEM
    // ======================================================

    if ($mensagemErro === '') {

        try {

            $mensagemModel->enviar(

                (int) $propostaId,

                $usuarioId,

                $conteudo
            );


            // --------------------------------------------------
            // REDIRECIONAMENTO APÓS POST
            // --------------------------------------------------
            //
            // Isso evita que atualizar a página envie
            // novamente a mesma mensagem.
            //
            // Padrão:
            //
            // POST
            //   ↓
            // Redirect
            //   ↓
            // GET
            //
            // --------------------------------------------------

            header(
                'Location: chat.php?proposta='
                . (int) $propostaId
            );

            exit;


        } catch (Throwable $erro) {

            // Registramos o erro técnico no servidor.
            error_log(
                $erro->getMessage()
            );


            // Para o usuário mostramos algo simples.
            $mensagemErro =
                'Não foi possível enviar a mensagem.';
        }
    }
}


// ==========================================================
// MARCAR MENSAGENS RECEBIDAS COMO LIDAS
// ==========================================================
//
// Quando o usuário abre a conversa,
// mensagens enviadas pela outra pessoa passam a possuir:
//
// lida_em = data/hora atual
//
// ==========================================================

$mensagemModel
    ->marcarComoLidas(
        (int) $propostaId,
        $usuarioId
    );


// ==========================================================
// BUSCAR HISTÓRICO
// ==========================================================
//
// Retorna todas as mensagens da conversa,
// da mais antiga para a mais recente.
//
// ==========================================================

$mensagens =
    $mensagemModel
        ->listarPorProposta(
            (int) $propostaId
        );


// ----------------------------------------------------------
// INFORMAÇÕES AUXILIARES
// ----------------------------------------------------------

$tipoUsuario = (string) (
    $usuario['tipo'] ?? ''
);


$nomeUsuario = (string) (
    $usuario['nome'] ?? ''
);


$conversas =
    $conversaModel->listarPorUsuario($usuarioId);

$projetoTitulo = (string) (
    $participantes['projeto_titulo'] ?? ''
);


$propostaStatus = (string) (
    $participantes['proposta_status'] ?? ''
);


// ----------------------------------------------------------
// CARREGAR VIEW
// ----------------------------------------------------------
//
// A View receberá:
//
// $usuario
// $usuarioId
// $propostaId
// $participantes
// $mensagens
// $mensagemErro
// $tipoUsuario
// $nomeUsuario
// $projetoTitulo
// $propostaStatus
//
// ----------------------------------------------------------

require_once __DIR__
    . '/../app/views/chat.php';