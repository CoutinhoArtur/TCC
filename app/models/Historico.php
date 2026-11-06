<?php

// ==========================================================
// MODEL: HISTÓRICO
// ==========================================================
// RF16 - Histórico de projetos, propostas e contratações.
// ==========================================================

class Historico
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarCliente(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                p.id AS projeto_id, p.titulo AS projeto_titulo, p.status AS projeto_status,
                p.categoria, p.criado_em,
                c.id AS contratacao_id, c.status AS contratacao_status,
                c.valor_final, c.prazo_final_dias, c.data_conclusao,
                ud.nome AS desenvolvedor_nome,
                a.nota, a.comentario
             FROM projetos AS p
             INNER JOIN clientes AS cli ON cli.id = p.cliente_id
             LEFT JOIN contratacoes AS c ON c.projeto_id = p.id
             LEFT JOIN propostas AS prop ON prop.id = c.proposta_id
             LEFT JOIN desenvolvedores AS d ON d.id = prop.desenvolvedor_id
             LEFT JOIN usuarios AS ud ON ud.id = d.usuario_id
             LEFT JOIN avaliacoes AS a ON a.contratacao_id = c.id
             WHERE cli.usuario_id = ?
             AND (p.status IN ('concluido', 'cancelado') OR c.status IN ('concluida', 'cancelada'))
             ORDER BY COALESCE(c.data_conclusao, p.atualizado_em, p.criado_em) DESC"
        );
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    public function listarDesenvolvedor(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                prop.id AS proposta_id, prop.status AS proposta_status,
                prop.valor, prop.prazo_estimado, prop.criado_em,
                p.id AS projeto_id, p.titulo AS projeto_titulo, p.status AS projeto_status,
                c.id AS contratacao_id, c.status AS contratacao_status,
                c.valor_final, c.prazo_final_dias, c.data_conclusao,
                uc.nome AS cliente_nome,
                a.nota, a.comentario
             FROM propostas AS prop
             INNER JOIN desenvolvedores AS d ON d.id = prop.desenvolvedor_id
             INNER JOIN projetos AS p ON p.id = prop.projeto_id
             INNER JOIN clientes AS cli ON cli.id = p.cliente_id
             INNER JOIN usuarios AS uc ON uc.id = cli.usuario_id
             LEFT JOIN contratacoes AS c ON c.proposta_id = prop.id
             LEFT JOIN avaliacoes AS a ON a.contratacao_id = c.id
             WHERE d.usuario_id = ?
             AND (
                prop.status IN ('recusada', 'encerrada', 'cancelada')
                OR c.status IN ('concluida', 'cancelada')
             )
             ORDER BY COALESCE(c.data_conclusao, prop.atualizado_em, prop.criado_em) DESC"
        );
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }
}
