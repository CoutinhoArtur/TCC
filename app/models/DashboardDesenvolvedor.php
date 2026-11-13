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
    // LISTAR CONTRATAÇÕES ATIVAS DO DESENVOLVEDOR
    // ======================================================
    //
    // O resumo já conta os projetos em execução, mas o
    // dashboard também precisa mostrar QUAIS são eles.
    //
    // Incluímos contratações ainda não concluídas/canceladas.
    // Quando existir uma mensagem de solicitação de ajustes
    // posterior à última entrega, exibimos esse aviso no card.
    //
    // RN19 mantém a contratação em "em_andamento" depois
    // dos ajustes; por isso o aviso é informativo e não altera
    // o status oficial da contratação.
    // ======================================================

    public function listarContratacoesAtivas(
        int $desenvolvedorId
    ): array {

        $sql = "
            SELECT
                c.id AS contratacao_id,
                c.status AS contratacao_status,
                c.data_inicio,
                c.data_entrega,
                c.valor_final,
                c.prazo_final_dias,

                p.id AS projeto_id,
                p.titulo AS projeto_titulo,
                p.categoria AS projeto_categoria,
                p.status AS projeto_status,

                prop.id AS proposta_id,
                u.nome AS cliente_nome,

                (
                    SELECT m.conteudo
                    FROM mensagens AS m
                    WHERE m.proposta_id = prop.id
                    AND m.conteudo LIKE 'Solicitação de ajustes:%'
                    AND (
                        c.data_entrega IS NULL
                        OR m.enviada_em >= c.data_entrega
                    )
                    ORDER BY m.enviada_em DESC, m.id DESC
                    LIMIT 1
                ) AS ultima_solicitacao_ajustes

            FROM contratacoes AS c

            INNER JOIN propostas AS prop
                ON prop.id = c.proposta_id

            INNER JOIN projetos AS p
                ON p.id = c.projeto_id

            INNER JOIN clientes AS cli
                ON cli.id = p.cliente_id

            INNER JOIN usuarios AS u
                ON u.id = cli.usuario_id

            WHERE prop.desenvolvedor_id = ?

            AND c.status IN (
                'ativa',
                'em_andamento',
                'aguardando_confirmacao',
                'ajustes_solicitados'
            )

            ORDER BY c.id DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$desenvolvedorId]);

        return $stmt->fetchAll();
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