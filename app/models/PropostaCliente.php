<?php

// ==========================================================
// MODEL: PROPOSTAS RECEBIDAS PELO CLIENTE
// ==========================================================
//
// Este Model concentra consultas utilizadas pelo cliente.
//
// Ele será responsável por:
//
// - verificar se determinado projeto pertence ao cliente;
// - listar propostas recebidas naquele projeto;
// - trazer informações do desenvolvedor;
// - informar quantas mensagens existem na negociação.
//
// SEGURANÇA:
//
// Não basta receber:
//
// projeto.php?id=2
//
// Precisamos confirmar que o projeto realmente pertence
// ao usuário cliente atualmente autenticado.
//
// RF11  - Gerenciamento de propostas
// RF13  - Comunicação entre usuários
// RNF03 - Controle de acesso
// ==========================================================

class PropostaCliente
{
    // ------------------------------------------------------
    // CONEXÃO COM O BANCO
    // ------------------------------------------------------

    private PDO $pdo;


    // ------------------------------------------------------
    // CONSTRUTOR
    // ------------------------------------------------------

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    // ======================================================
    // BUSCAR PROJETO PERTENCENTE AO CLIENTE
    // ======================================================
    //
    // Recebe:
    //
    // projetos.id
    // usuarios.id do cliente logado
    //
    // Só retorna o projeto se existir a relação:
    //
    // usuário logado
    //      ↓
    // clientes.usuario_id
    //      ↓
    // projetos.cliente_id
    //
    // ======================================================

    public function buscarProjetoDoCliente(
        int $projetoId,
        int $usuarioId
    ): array|false {

        $sql = "
            SELECT
                projeto.id,
                projeto.titulo,
                projeto.categoria,
                projeto.descricao_resumida,
                projeto.orcamento_min,
                projeto.orcamento_max,
                projeto.prazo_desejado,
                projeto.status,
                projeto.criado_em

            FROM projetos AS projeto

            INNER JOIN clientes AS cliente
                ON cliente.id = projeto.cliente_id

            WHERE projeto.id = ?

            AND cliente.usuario_id = ?

            LIMIT 1
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([
            $projetoId,
            $usuarioId
        ]);


        return $stmt->fetch();
    }


    // ======================================================
    // LISTAR PROPOSTAS RECEBIDAS
    // ======================================================
    //
    // Esta consulta retorna todas as propostas enviadas
    // para um projeto pertencente ao cliente.
    //
    // Também buscamos:
    //
    // - nome do desenvolvedor;
    // - e-mail;
    // - título profissional;
    // - quantidade de mensagens da negociação.
    //
    // ======================================================

    public function listarPorProjeto(
        int $projetoId,
        int $usuarioId
    ): array {

        // --------------------------------------------------
        // SEGURANÇA
        // --------------------------------------------------
        //
        // Primeiro confirmamos que o projeto pertence
        // realmente ao cliente autenticado.
        // --------------------------------------------------

        $projeto =
            $this->buscarProjetoDoCliente(
                $projetoId,
                $usuarioId
            );


        if ($projeto === false) {

            // Retornamos lista vazia caso o projeto
            // não pertença ao cliente.
            return [];
        }


        // --------------------------------------------------
        // CONSULTAR PROPOSTAS
        // --------------------------------------------------

        $sql = "
            SELECT
                proposta.id,
                proposta.projeto_id,
                proposta.desenvolvedor_id,
                proposta.valor,
                proposta.prazo_estimado,
                proposta.descricao,
                proposta.entregaveis,
                proposta.briefing_versao_id,
                proposta.status,
                proposta.criado_em,
                proposta.atualizado_em,

                usuario.id AS desenvolvedor_usuario_id,
                usuario.nome AS desenvolvedor_nome,
                usuario.email AS desenvolvedor_email,

                desenvolvedor.titulo_profissional,

                contratacao.id AS contratacao_id,
                contratacao.status AS contratacao_status,

                (
                    SELECT COUNT(*)

                    FROM mensagens

                    WHERE mensagens.proposta_id =
                        proposta.id

                ) AS total_mensagens

            FROM propostas AS proposta

            INNER JOIN desenvolvedores AS desenvolvedor
                ON desenvolvedor.id =
                    proposta.desenvolvedor_id

            INNER JOIN usuarios AS usuario
                ON usuario.id =
                    desenvolvedor.usuario_id

            LEFT JOIN contratacoes AS contratacao
                ON contratacao.proposta_id = proposta.id

            WHERE proposta.projeto_id = ?

            ORDER BY proposta.criado_em DESC
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([
            $projetoId
        ]);


        return $stmt->fetchAll();
    }


    // ======================================================
    // BUSCAR UMA PROPOSTA ESPECÍFICA DO CLIENTE
    // ======================================================
    //
    // Será utilizada depois quando a Isabelli clicar
    // na proposta do Artur.
    //
    // Além da proposta existir, confirmamos novamente
    // que o projeto pertence ao cliente autenticado.
    //
    // ======================================================

    public function buscarPropostaDoCliente(
        int $propostaId,
        int $usuarioId
    ): array|false {

        $sql = "
            SELECT
                proposta.id,
                proposta.projeto_id,
                proposta.desenvolvedor_id,
                proposta.valor,
                proposta.prazo_estimado,
                proposta.descricao,
                proposta.entregaveis,
                proposta.briefing_versao_id,
                proposta.status,
                proposta.criado_em,
                proposta.atualizado_em,

                projeto.titulo AS projeto_titulo,

                usuario.id AS desenvolvedor_usuario_id,
                usuario.nome AS desenvolvedor_nome,
                usuario.email AS desenvolvedor_email,

                desenvolvedor.titulo_profissional,
                desenvolvedor.bio,
                desenvolvedor.experiencia,
                desenvolvedor.disponibilidade,
                desenvolvedor.faixa_preco_min,
                desenvolvedor.faixa_preco_max

            FROM propostas AS proposta

            INNER JOIN projetos AS projeto
                ON projeto.id = proposta.projeto_id

            INNER JOIN clientes AS cliente
                ON cliente.id = projeto.cliente_id

            INNER JOIN desenvolvedores AS desenvolvedor
                ON desenvolvedor.id =
                    proposta.desenvolvedor_id

            INNER JOIN usuarios AS usuario
                ON usuario.id =
                    desenvolvedor.usuario_id

            WHERE proposta.id = ?

            AND cliente.usuario_id = ?

            LIMIT 1
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([
            $propostaId,
            $usuarioId
        ]);


        return $stmt->fetch();
    }
}