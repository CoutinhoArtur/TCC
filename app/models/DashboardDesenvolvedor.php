<?php

// ==========================================================
// MODEL: DASHBOARD DO DESENVOLVEDOR
// ==========================================================
//
// Este Model concentra as consultas utilizadas
// no dashboard do desenvolvedor.
//
// Nesta primeira etapa ele será responsável por:
//
// - localizar o perfil do desenvolvedor;
// - contar propostas enviadas;
// - contar negociações em andamento;
// - contar projetos em andamento;
// - listar projetos disponíveis.
//
// RF08  - Pesquisa de projetos
// RF10  - Envio de propostas
// RF11  - Gerenciamento de propostas
// RNF05 - Consultas com prepared statements
// ==========================================================

class DashboardDesenvolvedor
{
    // ------------------------------------------------------
    // CONEXÃO PDO
    // ------------------------------------------------------

    private PDO $pdo;


    // ------------------------------------------------------
    // CONSTRUTOR
    // ------------------------------------------------------
    //
    // Recebe a conexão criada no database.php.
    // ------------------------------------------------------

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    // ======================================================
    // BUSCAR ID DO DESENVOLVEDOR
    // ======================================================
    //
    // Na sessão temos:
    //
    // usuarios.id
    //
    // Mas as tabelas relacionadas às propostas utilizam:
    //
    // desenvolvedores.id
    //
    // Portanto fazemos:
    //
    // usuarios.id
    //      ↓
    // desenvolvedores.usuario_id
    //      ↓
    // desenvolvedores.id
    //
    // ======================================================

    public function buscarDesenvolvedorIdPorUsuario(
        int $usuarioId
    ): ?int {

        $sql = "
            SELECT id
            FROM desenvolvedores
            WHERE usuario_id = ?
            LIMIT 1
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([
            $usuarioId
        ]);


        $desenvolvedor = $stmt->fetch();


        // Caso não exista perfil de desenvolvedor.
        if ($desenvolvedor === false) {

            return null;
        }


        return (int) $desenvolvedor['id'];
    }


    // ======================================================
    // RESUMO DO DASHBOARD
    // ======================================================
    //
    // Retorna:
    //
    // - propostas enviadas;
    // - negociações em andamento;
    // - projetos em andamento.
    //
    // ======================================================

    public function obterResumo(
        int $desenvolvedorId
    ): array {

        // --------------------------------------------------
        // PROPOSTAS ENVIADAS
        // --------------------------------------------------

        $sqlPropostas = "
            SELECT COUNT(*) AS total
            FROM propostas
            WHERE desenvolvedor_id = ?
        ";


        $stmt = $this->pdo->prepare(
            $sqlPropostas
        );


        $stmt->execute([
            $desenvolvedorId
        ]);


        $resultadoPropostas = $stmt->fetch();


        $propostasEnviadas = (int) (
            $resultadoPropostas['total'] ?? 0
        );


        // --------------------------------------------------
        // NEGOCIAÇÕES
        // --------------------------------------------------
        //
        // Aqui contamos propostas que estão
        // atualmente em negociação.
        // --------------------------------------------------

        $sqlNegociacoes = "
            SELECT COUNT(*) AS total
            FROM propostas
            WHERE desenvolvedor_id = ?
            AND status = 'em_negociacao'
        ";


        $stmt = $this->pdo->prepare(
            $sqlNegociacoes
        );


        $stmt->execute([
            $desenvolvedorId
        ]);


        $resultadoNegociacoes = $stmt->fetch();


        $emNegociacao = (int) (
            $resultadoNegociacoes['total'] ?? 0
        );


        // --------------------------------------------------
        // PROJETOS EM ANDAMENTO
        // --------------------------------------------------
        //
        // A contratação liga:
        //
        // proposta
        //      ↓
        // desenvolvedor
        //
        // Então precisamos utilizar JOIN.
        // --------------------------------------------------

        $sqlAndamento = "
            SELECT COUNT(*) AS total

            FROM contratacoes AS contratacao

            INNER JOIN propostas AS proposta
                ON proposta.id = contratacao.proposta_id

            WHERE proposta.desenvolvedor_id = ?

            AND contratacao.status IN (
                'ativa',
                'em_andamento',
                'aguardando_confirmacao',
                'ajustes_solicitados'
            )
        ";


        $stmt = $this->pdo->prepare(
            $sqlAndamento
        );


        $stmt->execute([
            $desenvolvedorId
        ]);


        $resultadoAndamento = $stmt->fetch();


        $projetosEmAndamento = (int) (
            $resultadoAndamento['total'] ?? 0
        );


        // --------------------------------------------------
        // RETORNAR RESUMO
        // --------------------------------------------------

        return [

            'propostas_enviadas' =>
                $propostasEnviadas,

            'em_negociacao' =>
                $emNegociacao,

            'projetos_em_andamento' =>
                $projetosEmAndamento
        ];
    }


    // ======================================================
    // LISTAR PROJETOS DISPONÍVEIS
    // ======================================================
    //
    // O desenvolvedor deve enxergar projetos que estão:
    //
    // status = aberto
    //
    // Ou seja, projetos que ainda estão aceitando
    // propostas de profissionais.
    //
    // Também buscamos algumas informações do cliente
    // responsável pela publicação.
    // ======================================================

    public function listarProjetosDisponiveis(): array
    {
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
                projeto.criado_em,

                usuario.nome AS nome_cliente,

                (
                    SELECT COUNT(*)
                    FROM propostas
                    WHERE propostas.projeto_id = projeto.id
                ) AS total_propostas

            FROM projetos AS projeto

            INNER JOIN clientes AS cliente
                ON cliente.id = projeto.cliente_id

            INNER JOIN usuarios AS usuario
                ON usuario.id = cliente.usuario_id

            WHERE projeto.status = 'aberto'

            ORDER BY projeto.criado_em DESC

            LIMIT 20
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute();


        return $stmt->fetchAll();
    }
}