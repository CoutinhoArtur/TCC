<?php

// ==========================================================
// MODEL: DETALHES DO PROJETO
// ==========================================================
// RF08 - Oportunidade aberta ou projeto já relacionado ao
// desenvolvedor através de uma proposta própria.
// ==========================================================

class ProjetoDetalhe
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscarProjetoParaDesenvolvedor(int $projetoId, int $usuarioId): array|false
    {
        $sql = "
            SELECT
                proj.id, proj.titulo, proj.categoria, proj.descricao_resumida,
                proj.orcamento_min, proj.orcamento_max, proj.prazo_desejado,
                proj.status, proj.criado_em,
                u.nome AS nome_cliente,
                b.id AS briefing_id, b.status AS briefing_status,
                bv.id AS briefing_versao_id, bv.numero_versao, bv.objetivo,
                bv.publico_alvo, bv.funcionalidades, bv.condicoes_regras,
                bv.identidade_visual, bv.referencias_visuais, bv.observacoes,
                minha.id AS minha_proposta_id,
                minha.status AS minha_proposta_status,
                (SELECT COUNT(*) FROM propostas AS px WHERE px.projeto_id = proj.id) AS total_propostas
            FROM projetos AS proj
            INNER JOIN clientes AS cli ON cli.id = proj.cliente_id
            INNER JOIN usuarios AS u ON u.id = cli.usuario_id
            INNER JOIN briefings AS b ON b.projeto_id = proj.id
            INNER JOIN briefing_versoes AS bv
                ON bv.briefing_id = b.id
                AND bv.numero_versao = (
                    SELECT MAX(bv2.numero_versao)
                    FROM briefing_versoes AS bv2
                    WHERE bv2.briefing_id = b.id
                )
            LEFT JOIN desenvolvedores AS d ON d.usuario_id = ?
            LEFT JOIN propostas AS minha
                ON minha.projeto_id = proj.id
                AND minha.desenvolvedor_id = d.id
            WHERE proj.id = ?
            AND (proj.status = 'aberto' OR minha.id IS NOT NULL)
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuarioId, $projetoId]);
        return $stmt->fetch();
    }

    // Mantido para compatibilidade com chamadas antigas.
    public function buscarProjetoDisponivel(int $projetoId): array|false
    {
        $sql = "
            SELECT
                proj.id, proj.titulo, proj.categoria, proj.descricao_resumida,
                proj.orcamento_min, proj.orcamento_max, proj.prazo_desejado,
                proj.status, proj.criado_em,
                u.nome AS nome_cliente,
                b.id AS briefing_id, b.status AS briefing_status,
                bv.id AS briefing_versao_id, bv.numero_versao, bv.objetivo,
                bv.publico_alvo, bv.funcionalidades, bv.condicoes_regras,
                bv.identidade_visual, bv.referencias_visuais, bv.observacoes,
                NULL AS minha_proposta_id,
                NULL AS minha_proposta_status,
                (SELECT COUNT(*) FROM propostas AS px WHERE px.projeto_id = proj.id) AS total_propostas
            FROM projetos AS proj
            INNER JOIN clientes AS cli ON cli.id = proj.cliente_id
            INNER JOIN usuarios AS u ON u.id = cli.usuario_id
            INNER JOIN briefings AS b ON b.projeto_id = proj.id
            INNER JOIN briefing_versoes AS bv
                ON bv.briefing_id = b.id
                AND bv.numero_versao = (
                    SELECT MAX(bv2.numero_versao)
                    FROM briefing_versoes AS bv2
                    WHERE bv2.briefing_id = b.id
                )
            WHERE proj.id = ? AND proj.status = 'aberto'
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$projetoId]);
        return $stmt->fetch();
    }
}
