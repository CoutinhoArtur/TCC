<?php

// ==========================================================
// MODEL: PROPOSTAS DO DESENVOLVEDOR
// ==========================================================
// RF10 / RF13 / RF14 - Propostas, chat e acompanhamento.
// ==========================================================

class PropostaDesenvolvedor
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscarDesenvolvedorIdPorUsuario(int $usuarioId): ?int
    {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM desenvolvedores WHERE usuario_id = ? LIMIT 1"
        );
        $stmt->execute([$usuarioId]);
        $registro = $stmt->fetch();
        return $registro === false ? null : (int) $registro['id'];
    }

    public function listarTodas(int $desenvolvedorId): array
    {
        $sql = "
            SELECT
                prop.id,
                prop.projeto_id,
                prop.valor,
                prop.prazo_estimado,
                prop.descricao,
                prop.entregaveis,
                prop.briefing_versao_id,
                prop.status,
                prop.criado_em,
                prop.atualizado_em,
                proj.titulo AS projeto_titulo,
                proj.categoria AS projeto_categoria,
                proj.status AS projeto_status,
                proj.orcamento_min,
                proj.orcamento_max,
                proj.prazo_desejado,
                u.nome AS cliente_nome,
                c.id AS contratacao_id,
                c.status AS contratacao_status,
                c.data_inicio,
                c.data_entrega,
                c.data_conclusao,
                (SELECT COUNT(*) FROM mensagens AS m WHERE m.proposta_id = prop.id) AS total_mensagens
            FROM propostas AS prop
            INNER JOIN projetos AS proj ON proj.id = prop.projeto_id
            INNER JOIN clientes AS cli ON cli.id = proj.cliente_id
            INNER JOIN usuarios AS u ON u.id = cli.usuario_id
            LEFT JOIN contratacoes AS c ON c.proposta_id = prop.id
            WHERE prop.desenvolvedor_id = ?
            ORDER BY prop.criado_em DESC, prop.id DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$desenvolvedorId]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $propostaId, int $desenvolvedorId): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT prop.*, proj.titulo AS projeto_titulo, proj.status AS projeto_status,
                    c.id AS contratacao_id, c.status AS contratacao_status
             FROM propostas AS prop
             INNER JOIN projetos AS proj ON proj.id = prop.projeto_id
             LEFT JOIN contratacoes AS c ON c.proposta_id = prop.id
             WHERE prop.id = ? AND prop.desenvolvedor_id = ?
             LIMIT 1"
        );
        $stmt->execute([$propostaId, $desenvolvedorId]);
        return $stmt->fetch();
    }
}
