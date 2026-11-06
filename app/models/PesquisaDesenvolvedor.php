<?php

// ==========================================================
// MODEL: PESQUISA DE DESENVOLVEDORES
// ==========================================================
// RF09 - Pesquisa de desenvolvedores e consulta de perfil.
// ==========================================================

class PesquisaDesenvolvedor
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @param array<string,mixed> $filtros */
    public function pesquisar(array $filtros): array
    {
        $q = trim((string) ($filtros['q'] ?? ''));
        $habilidade = trim((string) ($filtros['habilidade'] ?? ''));
        $precoMax = $filtros['preco_max'] ?? null;

        $sql = "
            SELECT
                d.id AS desenvolvedor_id,
                u.id AS usuario_id,
                u.nome,
                d.titulo_profissional,
                d.bio,
                d.experiencia,
                d.disponibilidade,
                d.faixa_preco_min,
                d.faixa_preco_max,
                GROUP_CONCAT(DISTINCT h.nome ORDER BY h.nome SEPARATOR ', ') AS habilidades,
                ROUND(AVG(a.nota), 1) AS media_avaliacoes,
                COUNT(DISTINCT a.id) AS total_avaliacoes
            FROM desenvolvedores AS d
            INNER JOIN usuarios AS u ON u.id = d.usuario_id AND u.status = 'ativo'
            LEFT JOIN desenvolvedor_habilidades AS dh ON dh.desenvolvedor_id = d.id
            LEFT JOIN habilidades AS h ON h.id = dh.habilidade_id AND h.status = 'ativa'
            LEFT JOIN propostas AS p ON p.desenvolvedor_id = d.id
            LEFT JOIN contratacoes AS c ON c.proposta_id = p.id
            LEFT JOIN avaliacoes AS a ON a.contratacao_id = c.id
            WHERE 1 = 1
        ";
        $params = [];

        if ($q !== '') {
            $sql .= " AND (u.nome LIKE ? OR d.titulo_profissional LIKE ? OR d.bio LIKE ? OR d.experiencia LIKE ?)";
            $like = '%' . $q . '%';
            array_push($params, $like, $like, $like, $like);
        }

        if ($habilidade !== '') {
            $sql .= " AND EXISTS (
                SELECT 1
                FROM desenvolvedor_habilidades AS dhf
                INNER JOIN habilidades AS hf ON hf.id = dhf.habilidade_id
                WHERE dhf.desenvolvedor_id = d.id
                AND hf.status = 'ativa'
                AND hf.nome LIKE ?
            )";
            $params[] = '%' . $habilidade . '%';
        }

        if (is_numeric($precoMax) && (float) $precoMax >= 0) {
            $sql .= " AND (d.faixa_preco_min IS NULL OR d.faixa_preco_min <= ?)";
            $params[] = (float) $precoMax;
        }

        $sql .= "
            GROUP BY d.id, u.id, u.nome, d.titulo_profissional, d.bio, d.experiencia,
                     d.disponibilidade, d.faixa_preco_min, d.faixa_preco_max
            ORDER BY media_avaliacoes DESC, total_avaliacoes DESC, u.nome ASC
            LIMIT 100
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function buscarPerfilPublico(int $desenvolvedorId): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                d.id AS desenvolvedor_id, u.id AS usuario_id, u.nome,
                d.titulo_profissional, d.bio, d.experiencia, d.disponibilidade,
                d.faixa_preco_min, d.faixa_preco_max,
                ROUND(AVG(a.nota), 1) AS media_avaliacoes,
                COUNT(DISTINCT a.id) AS total_avaliacoes
             FROM desenvolvedores AS d
             INNER JOIN usuarios AS u ON u.id = d.usuario_id AND u.status = 'ativo'
             LEFT JOIN propostas AS p ON p.desenvolvedor_id = d.id
             LEFT JOIN contratacoes AS c ON c.proposta_id = p.id
             LEFT JOIN avaliacoes AS a ON a.contratacao_id = c.id
             WHERE d.id = ?
             GROUP BY d.id, u.id, u.nome, d.titulo_profissional, d.bio, d.experiencia,
                      d.disponibilidade, d.faixa_preco_min, d.faixa_preco_max
             LIMIT 1"
        );
        $stmt->execute([$desenvolvedorId]);
        $perfil = $stmt->fetch();
        if ($perfil === false) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "SELECT h.nome, dh.nivel
             FROM desenvolvedor_habilidades AS dh
             INNER JOIN habilidades AS h ON h.id = dh.habilidade_id
             WHERE dh.desenvolvedor_id = ? AND h.status = 'ativa'
             ORDER BY h.nome"
        );
        $stmt->execute([$desenvolvedorId]);
        $perfil['habilidades_lista'] = $stmt->fetchAll();

        $stmt = $this->pdo->prepare(
            "SELECT id, titulo, descricao, link_projeto, link_repositorio, imagem, criado_em
             FROM portfolios
             WHERE desenvolvedor_id = ?
             ORDER BY criado_em DESC, id DESC"
        );
        $stmt->execute([$desenvolvedorId]);
        $perfil['portfolio'] = $stmt->fetchAll();

        $stmt = $this->pdo->prepare(
            "SELECT a.nota, a.comentario, a.criado_em, pr.titulo AS projeto_titulo
             FROM avaliacoes AS a
             INNER JOIN contratacoes AS c ON c.id = a.contratacao_id
             INNER JOIN propostas AS p ON p.id = c.proposta_id
             INNER JOIN projetos AS pr ON pr.id = c.projeto_id
             WHERE p.desenvolvedor_id = ?
             ORDER BY a.criado_em DESC"
        );
        $stmt->execute([$desenvolvedorId]);
        $perfil['avaliacoes'] = $stmt->fetchAll();

        return $perfil;
    }
}
