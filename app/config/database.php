<?php

// ==========================================================
// CONEXÃO COM O BANCO DE DADOS
// ==========================================================
//
// Este arquivo cria a conexão PDO utilizada pelo sistema.
//
// As credenciais particulares da máquina ficam em:
//
// database.local.php
//
// Assim evitamos enviar senhas para o GitHub.
// ==========================================================


// ----------------------------------------------------------
// CONFIGURAÇÕES GERAIS
// ----------------------------------------------------------

// Servidor onde o MySQL está rodando.
$hostBanco = 'localhost';

// Porta padrão do MySQL.
$portaBanco = '3306';

// Banco utilizado pelo SiteMatch.
$nomeBanco = 'projeto_match';


// ----------------------------------------------------------
// CARREGAR CREDENCIAIS LOCAIS
// ----------------------------------------------------------
//
// O arquivo database.local.php contém:
//
// $usuarioBanco
// $senhaBanco
//
// Ele não será enviado ao GitHub.
// ----------------------------------------------------------

$arquivoConfiguracaoLocal =
    __DIR__ . '/database.local.php';


// Verificamos se o arquivo existe antes de carregá-lo.
if (!file_exists($arquivoConfiguracaoLocal)) {

    http_response_code(500);

    exit(
        'Arquivo de configuração local do banco não encontrado.'
    );
}


// Carrega usuário e senha locais.
require $arquivoConfiguracaoLocal;


// ----------------------------------------------------------
// CRIAR CONEXÃO PDO
// ----------------------------------------------------------

try {

    $pdo = new PDO(

        "mysql:host={$hostBanco};port={$portaBanco};dbname={$nomeBanco};charset=utf8mb4",

        $usuarioBanco,

        $senhaBanco,

        [

            // Faz o PDO lançar exceções quando ocorre erro.
            PDO::ATTR_ERRMODE =>
                PDO::ERRMODE_EXCEPTION,


            // Retorna resultados como arrays associativos.
            //
            // Exemplo:
            //
            // $registro['nome']
            //
            PDO::ATTR_DEFAULT_FETCH_MODE =>
                PDO::FETCH_ASSOC,


            // Utiliza prepared statements reais.
            //
            // Importante para segurança das consultas.
            PDO::ATTR_EMULATE_PREPARES =>
                false
        ]
    );

} catch (PDOException $erro) {

    // Registra o erro técnico no terminal/log.
    error_log($erro->getMessage());


    // Retorna erro interno do servidor.
    http_response_code(500);


    // Não mostramos detalhes técnicos ao usuário.
    exit(
        'Erro ao conectar com o banco de dados.'
    );
}