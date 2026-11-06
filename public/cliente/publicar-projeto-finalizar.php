<?php

// ==========================================================
// FINALIZAR PUBLICAÇÃO DO PROJETO
// ==========================================================
//
// Este arquivo é executado quando o cliente confirma
// a publicação na tela de revisão.
//
// Responsabilidades:
//
// 1. verificar autenticação;
// 2. garantir que o usuário é cliente;
// 3. verificar se existe um rascunho completo;
// 4. localizar clientes.id;
// 5. chamar o Model Projeto;
// 6. criar projeto + briefing + versão 1;
// 7. limpar o rascunho da sessão;
// 8. voltar para o dashboard.
//
// RF06 - Publicação de projetos
// RF17 - Briefing guiado
// RN04 - Somente clientes publicam projetos
// ==========================================================


// ----------------------------------------------------------
// INICIAR SESSÃO
// ----------------------------------------------------------

session_start();


// ----------------------------------------------------------
// ACEITAR APENAS POST
// ----------------------------------------------------------
//
// Não queremos que alguém publique um projeto apenas
// digitando esta URL no navegador.
//
// O botão da revisão enviará uma requisição POST.
// ----------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: publicar-projeto.php');

    exit;
}


// ----------------------------------------------------------
// VERIFICAR LOGIN
// ----------------------------------------------------------

if (!isset($_SESSION['usuario'])) {

    header('Location: ../login.php');

    exit;
}


// ----------------------------------------------------------
// RECUPERAR USUÁRIO
// ----------------------------------------------------------

/**
 * @var array<string, mixed> $usuario
 */

$usuario = $_SESSION['usuario'];


// ----------------------------------------------------------
// VERIFICAR PERFIL
// ----------------------------------------------------------

$tipoUsuario = (string) (
    $usuario['tipo'] ?? ''
);


if ($tipoUsuario !== 'cliente') {

    header('Location: ../painel.php');

    exit;
}


// ----------------------------------------------------------
// VERIFICAR RASCUNHO
// ----------------------------------------------------------

if (!isset($_SESSION['rascunho_projeto'])) {

    header('Location: publicar-projeto.php');

    exit;
}


/**
 * @var array<string, mixed> $rascunho
 */

$rascunho = $_SESSION['rascunho_projeto'];


// ==========================================================
// VALIDAR INFORMAÇÕES ESSENCIAIS
// ==========================================================
//
// Mesmo que as etapas anteriores já tenham validado,
// conferimos novamente antes de gravar no banco.
// ==========================================================

$camposObrigatorios = [
    'titulo',
    'categoria',
    'descricao_resumida',
    'publico_alvo',
    'funcionalidades',
    'orcamento_min',
    'orcamento_max',
    'prazo_desejado'
];


foreach ($camposObrigatorios as $campo) {

    if (
        !isset($rascunho[$campo]) ||
        $rascunho[$campo] === ''
    ) {

        // Se faltar alguma informação importante,
        // voltamos para o início do briefing.
        header('Location: publicar-projeto.php');

        exit;
    }
}


// ----------------------------------------------------------
// CARREGAR CONEXÃO COM O BANCO
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/config/database.php';


// ----------------------------------------------------------
// CARREGAR MODELS
// ----------------------------------------------------------

require_once __DIR__
    . '/../../app/models/DashboardCliente.php';

require_once __DIR__
    . '/../../app/models/Projeto.php';


// ----------------------------------------------------------
// ID DO USUÁRIO
// ----------------------------------------------------------

$usuarioId = (int) (
    $usuario['id'] ?? 0
);


// ==========================================================
// DESCOBRIR clientes.id
// ==========================================================
//
// A sessão possui usuarios.id.
//
// Mas projetos utiliza clientes.id.
// ==========================================================

$dashboardClienteModel =
    new DashboardCliente($pdo);


$clienteId =
    $dashboardClienteModel
        ->buscarClienteIdPorUsuario(
            $usuarioId
        );


// ----------------------------------------------------------
// VALIDAR PERFIL CLIENTE
// ----------------------------------------------------------

if ($clienteId === null) {

    http_response_code(500);

    exit(
        'Não foi possível localizar o perfil do cliente.'
    );
}


// ==========================================================
// PUBLICAR PROJETO
// ==========================================================

try {

    // Criamos o Model de projetos.
    $projetoModel =
        new Projeto($pdo);


    // ------------------------------------------------------
    // CHAMAR TRANSAÇÃO
    // ------------------------------------------------------
    //
    // Este método criará:
    //
    // projetos
    // briefings
    // briefing_versoes
    //
    // em uma única transação.
    // ------------------------------------------------------

    $projetoId =
        $projetoModel->publicarCompleto(
            $clienteId,
            $usuarioId,
            $rascunho
        );


    // ======================================================
    // LIMPAR RASCUNHO
    // ======================================================
    //
    // Só apagamos os dados temporários DEPOIS
    // que o banco confirmou a publicação.
    // ======================================================

    unset(
        $_SESSION['rascunho_projeto']
    );


    // ------------------------------------------------------
    // GUARDAR MENSAGEM TEMPORÁRIA
    // ------------------------------------------------------
    //
    // Depois podemos usar esta mensagem no dashboard.
    // ------------------------------------------------------

    $_SESSION['mensagem_sucesso'] =
        'Projeto publicado com sucesso!';


    // ------------------------------------------------------
    // GUARDAR ID PUBLICADO
    // ------------------------------------------------------
    //
    // Pode ser útil futuramente para abrir diretamente
    // os detalhes do projeto.
    // ------------------------------------------------------

    $_SESSION['projeto_publicado_id'] =
        $projetoId;


    // ------------------------------------------------------
    // VOLTAR PARA O DASHBOARD
    // ------------------------------------------------------

    header('Location: dashboard.php');

    exit;


} catch (Throwable $erro) {

    // ------------------------------------------------------
    // REGISTRAR ERRO REAL
    // ------------------------------------------------------
    //
    // O usuário não verá detalhes técnicos,
    // mas o erro aparecerá no terminal/log do PHP.
    // ------------------------------------------------------

    error_log(
        $erro->getMessage()
    );


    http_response_code(500);


    exit(
        'Não foi possível publicar o projeto. Tente novamente.'
    );
}