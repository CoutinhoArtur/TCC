<?php

// ==========================================================
// MODEL: PESQUISA DE PROJETOS
// ==========================================================
// RF08 - Pesquisa de projetos disponíveis com filtros.
// ==========================================================

class PesquisaProjeto
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @param array<string,mixed> $filtros */
    public function pesquisar(int $desenvolvedorId, array $filtros): array
    {
        $q = trim((string) ($filtros['q'] ?? ''));
        $categoria = trim((string) ($filtros['categoria'] ?? ''));
        $orcamentoMin = $filtros['orcamento_min'] ?? null;
        $orcamentoMax = $filtros['orcamento_max'] ?? null;

        $sql = "
            SELECT
                p.id, p.titulo, p.categoria, p.descricao_resumida,
                p.orcamento_min, p.orcamento_max, p.prazo_desejado,
                p.status, p.criado_em,
                u.nome AS nome_cliente,
                bv.funcionalidades,
                bv.observacoes,
                minha.id AS minha_proposta_id,
                minha.status AS minha_proposta_status,
                (SELECT COUNT(*) FROM propostas px WHERE px.projeto_id = p.id) AS total_propostas
            FROM projetos AS p
            INNER JOIN clientes AS c ON c.id = p.cliente_id
            INNER JOIN usuarios AS u ON u.id = c.usuario_id
            LEFT JOIN briefings AS b ON b.projeto_id = p.id
            LEFT JOIN briefing_versoes AS bv
                ON bv.briefing_id = b.id
                AND bv.numero_versao = (
                    SELECT MAX(bv2.numero_versao)
                    FROM briefing_versoes AS bv2
                    WHERE bv2.briefing_id = b.id
                )
            LEFT JOIN propostas AS minha
                ON minha.projeto_id = p.id
                AND minha.desenvolvedor_id = ?
            WHERE p.status = 'aberto'
        ";

        $params = [$desenvolvedorId];

        if ($q !== '') {
            $sql .= " AND (
                p.titulo LIKE ? OR
                p.descricao_resumida LIKE ? OR
                bv.funcionalidades LIKE ? OR
                bv.observacoes LIKE ?
            )";
            $like = '%' . $q . '%';
            array_push($params, $like, $like, $like, $like);
        }

        $categorias = ['site', 'sistema_web', 'aplicativo_mobile', 'sistema_desktop', 'outro'];
        if ($categoria !== '' && in_array($categoria, $categorias, true)) {
            $sql .= " AND p.categoria = ?";
            $params[] = $categoria;
        }

        if (is_numeric($orcamentoMin) && (float) $orcamentoMin >= 0) {
            $sql .= " AND (p.orcamento_max IS NULL OR p.orcamento_max >= ?)";
            $params[] = (float) $orcamentoMin;
        }

        if (is_numeric($orcamentoMax) && (float) $orcamentoMax >= 0) {
            $sql .= " AND (p.orcamento_min IS NULL OR p.orcamento_min <= ?)";
            $params[] = (float) $orcamentoMax;
        }

        $sql .= " ORDER BY p.criado_em DESC, p.id DESC LIMIT 100";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
