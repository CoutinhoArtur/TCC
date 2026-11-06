<?php

// ==========================================================
// TESTE TEMPORÁRIO DA CONEXÃO COM O BANCO
// ==========================================================
// Este arquivo existe somente durante o desenvolvimento.
//
// Sua função é confirmar duas coisas:
//
// 1. O PHP consegue carregar o arquivo database.php.
// 2. O PHP consegue executar uma consulta no MySQL.
//
// Depois que a conexão estiver totalmente validada,
// este arquivo poderá ser removido.
// ==========================================================


// ----------------------------------------------------------
// CARREGANDO A CONEXÃO
// ----------------------------------------------------------

// __DIR__ representa a pasta atual deste arquivo.
//
// Como teste-banco.php está dentro de /public,
// usamos "../" para voltar uma pasta e acessar:
//
// /app/config/database.php
//
// require_once garante que o arquivo seja carregado
// apenas uma vez durante a execução.
require_once __DIR__ . '/../app/config/database.php';


// ----------------------------------------------------------
// CONSULTA DE TESTE
// ----------------------------------------------------------

// Consulta SQL que conta quantos usuários existem
// atualmente na tabela usuarios.
$sql = "SELECT COUNT(*) AS total FROM usuarios";


// ----------------------------------------------------------
// EXECUTANDO A CONSULTA
// ----------------------------------------------------------

// Como essa consulta não recebe nenhum dado do usuário,
// podemos utilizar query() diretamente.
//
// Nas funcionalidades reais, principalmente formulários,
// utilizaremos prepare() e execute().
$stmt = $pdo->query($sql);


// ----------------------------------------------------------
// PEGANDO O RESULTADO
// ----------------------------------------------------------

// fetch() pega uma linha retornada pela consulta.
//
// Como configuramos FETCH_ASSOC em database.php,
// podemos acessar os valores pelo nome da coluna.
//
// Neste caso:
// $resultado['total']
$resultado = $stmt->fetch();


// ----------------------------------------------------------
// EXIBINDO O RESULTADO
// ----------------------------------------------------------

echo 'Conexão com o banco realizada com sucesso!';

echo '<br>';

echo 'Usuários cadastrados: ' . $resultado['total'];