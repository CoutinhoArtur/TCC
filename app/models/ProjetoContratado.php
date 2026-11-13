<?php

// ==========================================================
// MODEL: ACOMPANHAMENTO DE CONTRATAÇÕES
// ==========================================================
// RF14 / RF16 - Listagem e detalhes de contratações.
// ==========================================================

class ProjetoContratado
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarDoCliente(int $usuarioId): array
    {
        $sql = $this->selectBase() . "
            WHERE cli.usuario_id = ?
            ORDER BY c.id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    public function listarDoDesenvolvedor(int $usuarioId): array
    {
        $sql = $this->selectBase() . "
            WHERE d.usuario_id = ?
            ORDER BY c.id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    public function buscarPorIdEUsuario(int $contratacaoId, int $usuarioId): array|false
    {
        $sql = $this->selectBase() . "
            WHERE c.id = ?
            AND (cli.usuario_id = ? OR d.usuario_id = ?)
            LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$contratacaoId, $usuarioId, $usuarioId]);
        return $stmt->fetch();
    }

    private function selectBase(): string
    {
        return "
            SELECT
                c.id AS contratacao_id,
                c.projeto_id,
                c.proposta_id,
                c.briefing_versao_id,
                c.valor_final,
                c.prazo_final_dias,
                c.status AS contratacao_status,
                c.data_aceite,
                c.data_inicio,
                c.data_entrega,
                c.data_conclusao,
                c.data_cancelamento,
                p.titulo AS projeto_titulo,
                p.categoria AS projeto_categoria,
                p.descricao_resumida,
                p.status AS projeto_status,
                prop.descricao AS proposta_descricao,
                prop.entregaveis,
                prop.status AS proposta_status,
                uc.id AS cliente_usuario_id,
                uc.nome AS cliente_nome,
                ud.id AS desenvolvedor_usuario_id,
                ud.nome AS desenvolvedor_nome,
                d.id AS desenvolvedor_id,
                d.titulo_profissional,
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
                ) AS ultima_solicitacao_ajustes,
                a.id AS avaliacao_id,
                a.nota AS avaliacao_nota,
                a.comentario AS avaliacao_comentario
            FROM contratacoes AS c
            INNER JOIN projetos AS p ON p.id = c.projeto_id
            INNER JOIN clientes AS cli ON cli.id = p.cliente_id
            INNER JOIN usuarios AS uc ON uc.id = cli.usuario_id
            INNER JOIN propostas AS prop ON prop.id = c.proposta_id
            INNER JOIN desenvolvedores AS d ON d.id = prop.desenvolvedor_id
            INNER JOIN usuarios AS ud ON ud.id = d.usuario_id
            LEFT JOIN avaliacoes AS a ON a.contratacao_id = c.id
        ";
    }
}
