<?php

// ==========================================================
// MODEL: DASHBOARD DO CLIENTE
// ==========================================================
//
// Este Model reúne as consultas necessárias para montar
// o dashboard do cliente.
//
// Ele será responsável por:
//
// - descobrir o ID do perfil cliente;
// - contar projetos ativos;
// - contar propostas recebidas;
// - contar projetos em andamento;
// - buscar projetos recentes.
//
// Mantemos essas consultas fora da View para deixar
// o projeto mais organizado.
//
// RN03  - Acesso conforme o perfil
// RF07  - Gerenciamento de projetos
// RF11  - Gerenciamento de propostas
// RNF05 - Prepared statements
// ==========================================================

class DashboardCliente
{
    // ------------------------------------------------------
    // CONEXÃO COM O BANCO
    // ------------------------------------------------------

    private PDO $pdo;


    // ------------------------------------------------------
    // CONSTRUTOR
    // ------------------------------------------------------
    //
    // Recebe a conexão PDO criada no database.php.
    // ------------------------------------------------------

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    // ======================================================
    // BUSCAR ID DO CLIENTE
    // ======================================================
    //
    // Na sessão nós guardamos:
    //
    // usuarios.id
    //
    // Porém a tabela projetos utiliza:
    //
    // clientes.id
    //
    // Por isso precisamos fazer esta conversão:
    //
    // usuarios.id
    //      ↓
    // clientes.usuario_id
    //      ↓
    // clientes.id
    //
    // ======================================================

    public function buscarClienteIdPorUsuario(
        int $usuarioId
    ): ?int {

        $sql = "
            SELECT id
            FROM clientes
            WHERE usuario_id = ?
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $usuarioId
        ]);

        $cliente = $stmt->fetch();


        // Se nenhum perfil de cliente for encontrado,
        // retornamos null.
        if ($cliente === false) {
            return null;
        }


        // Convertemos explicitamente para inteiro.
        return (int) $cliente['id'];
    }


    // ======================================================
    // RESUMO DO DASHBOARD
    // ======================================================
    //
    // Retorna:
    //
    // - projetos ativos;
    // - propostas recebidas;
    // - projetos em andamento.
    //
    // ======================================================

    public function obterResumo(
        int $clienteId
    ): array {

        // --------------------------------------------------
        // PROJETOS ATIVOS
        // --------------------------------------------------
        //
        // Consideramos ativos projetos que ainda estão
        // procurando profissional ou sendo negociados.
        // --------------------------------------------------

        $sqlProjetosAtivos = "
            SELECT COUNT(*) AS total
            FROM projetos
            WHERE cliente_id = ?
            AND status IN (
                'aberto',
                'em_negociacao'
            )
        ";

        $stmt = $this->pdo->prepare(
            $sqlProjetosAtivos
        );

        $stmt->execute([
            $clienteId
        ]);

        $resultadoProjetos = $stmt->fetch();

        $projetosAtivos = (int) (
            $resultadoProjetos['total'] ?? 0
        );


        // --------------------------------------------------
        // PROPOSTAS RECEBIDAS
        // --------------------------------------------------
        //
        // Uma proposta pertence a um projeto.
        //
        // Então usamos JOIN para descobrir todas as
        // propostas dos projetos pertencentes ao cliente.
        // --------------------------------------------------

        $sqlPropostas = "
            SELECT COUNT(*) AS total

            FROM propostas AS proposta

            INNER JOIN projetos AS projeto
                ON projeto.id = proposta.projeto_id

            WHERE projeto.cliente_id = ?
        ";

        $stmt = $this->pdo->prepare(
            $sqlPropostas
        );

        $stmt->execute([
            $clienteId
        ]);

        $resultadoPropostas = $stmt->fetch();

        $propostasRecebidas = (int) (
            $resultadoPropostas['total'] ?? 0
        );


        // --------------------------------------------------
        // PROJETOS EM ANDAMENTO
        // --------------------------------------------------
        //
        // Aqui contamos projetos que já estão sendo
        // desenvolvidos ou aguardando confirmação
        // de entrega pelo cliente.
        // --------------------------------------------------

        $sqlEmAndamento = "
            SELECT COUNT(*) AS total
            FROM projetos
            WHERE cliente_id = ?
            AND status IN (
                'em_andamento',
                'aguardando_confirmacao'
            )
        ";

        $stmt = $this->pdo->prepare(
            $sqlEmAndamento
        );

        $stmt->execute([
            $clienteId
        ]);

        $resultadoAndamento = $stmt->fetch();

        $emAndamento = (int) (
            $resultadoAndamento['total'] ?? 0
        );


        // --------------------------------------------------
        // RETORNAR RESUMO
        // --------------------------------------------------

        return [
            'projetos_ativos' => $projetosAtivos,
            'propostas_recebidas' => $propostasRecebidas,
            'em_andamento' => $emAndamento
        ];
    }


    // ======================================================
    // PROJETOS RECENTES
    // ======================================================
    //
    // Busca os últimos 5 projetos publicados pelo cliente.
    //
    // Também contamos quantas propostas cada projeto possui.
    //
    // ======================================================

    public function listarProjetosRecentes(
        int $clienteId
    ): array {

        $sql = "
            SELECT
                projeto.id,
                projeto.titulo,
                projeto.categoria,
                projeto.orcamento_min,
                projeto.orcamento_max,
                projeto.prazo_desejado,
                projeto.status,
                projeto.criado_em,

                (
                    SELECT COUNT(*)
                    FROM propostas
                    WHERE propostas.projeto_id = projeto.id
                ) AS total_propostas,

                (
                    SELECT contratacoes.id
                    FROM contratacoes
                    WHERE contratacoes.projeto_id = projeto.id
                    LIMIT 1
                ) AS contratacao_id

            FROM projetos AS projeto

            WHERE projeto.cliente_id = ?

            ORDER BY projeto.criado_em DESC

            LIMIT 5
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $clienteId
        ]);


        // fetchAll() retorna todas as linhas encontradas.
        return $stmt->fetchAll();
    }
}