<?php

// ==========================================================
// CONTROLLER: AUTENTICAÇÃO
// ==========================================================
// Este controller recebe os dados enviados pelo usuário,
// realiza as validações necessárias e chama o Model Usuario.
//
// Fluxo:
//
// Formulário
//    ↓
// AuthController
//    ↓
// Usuario.php
//    ↓
// Banco de dados
//
// RF01  - Cadastro de usuários
// RN01  - E-mail deve ser único
// RN02  - Perfil deve ser cliente ou desenvolvedor
// RNF04 - Dados devem ser validados no servidor
// ==========================================================


// ----------------------------------------------------------
// CARREGAR O MODEL DE USUÁRIO
// ----------------------------------------------------------
//
// __DIR__ representa:
// app/controllers
//
// "../models" volta para a pasta app e entra em models.
// ----------------------------------------------------------

require_once __DIR__ . '/../models/Usuario.php';


class AuthController
{
    // Model utilizado para acessar os dados dos usuários.
    private Usuario $usuarioModel;


    // ------------------------------------------------------
    // CONSTRUTOR
    // ------------------------------------------------------
    //
    // Recebe a conexão PDO criada no database.php.
    //
    // Depois cria uma instância do Model Usuario.
    // ------------------------------------------------------

    public function __construct(PDO $pdo)
    {
        $this->usuarioModel = new Usuario($pdo);
    }


    // ======================================================
    // CADASTRAR USUÁRIO
    // ======================================================
    //
    // Recebe os dados enviados pelo formulário.
    //
    // Retorna um array contendo:
    //
    // sucesso  -> true ou false
    // mensagem -> texto para exibir ao usuário
    //
    // ======================================================

    public function cadastrar(array $dados): array
    {
        // --------------------------------------------------
        // RECEBER E LIMPAR OS DADOS
        // --------------------------------------------------
        //
        // trim() remove espaços no começo e no fim.
        //
        // Exemplo:
        // "   João Silva   "
        //
        // vira:
        // "João Silva"
        // --------------------------------------------------

        $nome = trim($dados['nome'] ?? '');

        $email = trim($dados['email'] ?? '');

        // E-mails não diferenciam maiúsculas/minúsculas
        // para nossa aplicação.
        $email = strtolower($email);

        // Não usamos trim na senha porque espaços podem
        // fazer parte de uma senha escolhida pelo usuário.
        $senha = $dados['senha'] ?? '';

        $confirmarSenha = $dados['confirmar_senha'] ?? '';

        $tipo = $dados['tipo'] ?? '';


        // ==================================================
        // VALIDAÇÕES
        // ==================================================


        // --------------------------------------------------
        // CAMPOS OBRIGATÓRIOS
        // --------------------------------------------------

        if (
            $nome === '' ||
            $email === '' ||
            $senha === '' ||
            $confirmarSenha === '' ||
            $tipo === ''
        ) {
            return [
                'sucesso' => false,
                'mensagem' => 'Preencha todos os campos obrigatórios.'
            ];
        }


        // --------------------------------------------------
        // TAMANHO DO NOME
        // --------------------------------------------------
        //
        // O banco permite no máximo 150 caracteres.
        // --------------------------------------------------

        if (mb_strlen($nome) > 150) {
            return [
                'sucesso' => false,
                'mensagem' => 'O nome informado é muito longo.'
            ];
        }


        // --------------------------------------------------
        // VALIDAR O FORMATO DO E-MAIL
        // --------------------------------------------------

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Informe um e-mail válido.'
            ];
        }


        // --------------------------------------------------
        // TAMANHO DO E-MAIL
        // --------------------------------------------------

        if (mb_strlen($email) > 150) {
            return [
                'sucesso' => false,
                'mensagem' => 'O e-mail informado é muito longo.'
            ];
        }


        // --------------------------------------------------
        // VALIDAR A SENHA
        // --------------------------------------------------
        //
        // Por enquanto, exigiremos no mínimo 8 caracteres.
        // Depois podemos acrescentar requisitos extras,
        // caso o grupo considere necessário.
        // --------------------------------------------------

        if (strlen($senha) < 8) {
            return [
                'sucesso' => false,
                'mensagem' => 'A senha deve possuir pelo menos 8 caracteres.'
            ];
        }


        // --------------------------------------------------
        // CONFIRMAR SENHA
        // --------------------------------------------------

        if ($senha !== $confirmarSenha) {
            return [
                'sucesso' => false,
                'mensagem' => 'As senhas informadas não são iguais.'
            ];
        }


        // --------------------------------------------------
        // VALIDAR TIPO DE USUÁRIO
        // --------------------------------------------------
        //
        // RN02:
        // só existem dois perfis nesta versão do sistema.
        //
        // Mesmo que alguém altere manualmente o HTML,
        // o servidor continuará bloqueando valores inválidos.
        // --------------------------------------------------

        $tiposPermitidos = [
            'cliente',
            'desenvolvedor'
        ];

        if (!in_array($tipo, $tiposPermitidos, true)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Tipo de usuário inválido.'
            ];
        }


        // --------------------------------------------------
        // VERIFICAR E-MAIL DUPLICADO
        // --------------------------------------------------
        //
        // RN01:
        // cada e-mail pode estar associado a apenas uma conta.
        // --------------------------------------------------

        if ($this->usuarioModel->emailExiste($email)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Este e-mail já está cadastrado.'
            ];
        }


        // ==================================================
        // CADASTRO NO BANCO
        // ==================================================

        try {

            // Chama o Model Usuario.
            //
            // O Model:
            // 1. cria o usuário;
            // 2. gera o hash da senha;
            // 3. cria cliente ou desenvolvedor;
            // 4. controla a transação.
            $usuarioId = $this->usuarioModel->cadastrar(
                $nome,
                $email,
                $senha,
                $tipo
            );


            // Cadastro realizado corretamente.
            return [
                'sucesso' => true,
                'mensagem' => 'Cadastro realizado com sucesso!',
                'usuario_id' => $usuarioId
            ];


        } catch (Throwable $erro) {

            // Registramos o erro real no log do servidor.
            //
            // Não mostramos detalhes internos para o usuário.
            error_log($erro->getMessage());


            return [
                'sucesso' => false,
                'mensagem' => 'Não foi possível concluir o cadastro.'
            ];
        }
    }


    // ==========================================================
// LOGIN
// ==========================================================
//
// RF02 - Autenticação de usuários
// RN03 - Acesso conforme o perfil
// RNF02 - Senha armazenada com hash
// RNF03 - Controle de acesso
// ==========================================================

public function login(array $dados): array
{
    // ------------------------------------------------------
    // RECEBER OS DADOS
    // ------------------------------------------------------

    $email = strtolower(
        trim($dados['email'] ?? '')
    );

    $senha = $dados['senha'] ?? '';


    // ------------------------------------------------------
    // VALIDAR CAMPOS OBRIGATÓRIOS
    // ------------------------------------------------------

    if ($email === '' || $senha === '') {

        return [
            'sucesso' => false,
            'mensagem' => 'Informe seu e-mail e sua senha.'
        ];
    }


    // ------------------------------------------------------
    // VALIDAR FORMATO DO E-MAIL
    // ------------------------------------------------------

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        return [
            'sucesso' => false,
            'mensagem' => 'Informe um e-mail válido.'
        ];
    }


    // ------------------------------------------------------
    // PROCURAR O USUÁRIO
    // ------------------------------------------------------

    $usuario = $this->usuarioModel->buscarPorEmail($email);


    // ------------------------------------------------------
    // USUÁRIO NÃO ENCONTRADO
    // ------------------------------------------------------
    //
    // Utilizamos uma mensagem genérica para não revelar
    // se determinado e-mail está ou não cadastrado.
    // ------------------------------------------------------

    if ($usuario === false) {

        return [
            'sucesso' => false,
            'mensagem' => 'E-mail ou senha inválidos.'
        ];
    }


    // ------------------------------------------------------
    // VERIFICAR SENHA
    // ------------------------------------------------------
    //
    // password_verify() compara:
    //
    // senha digitada
    //          VS
    // hash armazenado no banco
    //
    // Nós nunca precisamos recuperar a senha original.
    // ------------------------------------------------------

    if (!password_verify($senha, $usuario['senha_hash'])) {

        return [
            'sucesso' => false,
            'mensagem' => 'E-mail ou senha inválidos.'
        ];
    }


    // ------------------------------------------------------
    // VERIFICAR STATUS DA CONTA
    // ------------------------------------------------------

    if ($usuario['status'] !== 'ativo') {

        return [
            'sucesso' => false,
            'mensagem' => 'Esta conta está inativa.'
        ];
    }


    // ------------------------------------------------------
    // LOGIN AUTORIZADO
    // ------------------------------------------------------

    return [
        'sucesso' => true,
        'mensagem' => 'Login realizado com sucesso.',
        'usuario' => [
            'id' => (int) $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'tipo' => $usuario['tipo']
        ]
    ];
}
}