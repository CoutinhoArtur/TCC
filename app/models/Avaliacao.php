<?php

// ==========================================================
// MODEL: AVALIAÇÃO
// ==========================================================
// RF15 / RN20 / RN21
// Apenas o cliente da contratação concluída pode avaliar,
// e cada contratação recebe no máximo uma avaliação.
// ==========================================================

class Avaliacao
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscarPorContratacao(int $contratacaoId): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, contratacao_id, nota, comentario, criado_em
             FROM avaliacoes WHERE contratacao_id = ? LIMIT 1"
        );
        $stmt->execute([$contratacaoId]);
        return $stmt->fetch();
    }

    public function criar(
        int $contratacaoId,
        int $clienteUsuarioId,
        int $nota,
        string $comentario
    ): int {
        $comentario = trim($comentario);
        if ($nota < 1 || $nota > 5) {
            throw new InvalidArgumentException('A nota deve estar entre 1 e 5.');
        }
        if ($comentario === '') {
            throw new InvalidArgumentException('O comentário da avaliação é obrigatório.');
        }

        $sql = "
            SELECT c.id
            FROM contratacoes AS c
            INNER JOIN projetos AS p ON p.id = c.projeto_id
            INNER JOIN clientes AS cli ON cli.id = p.cliente_id
            WHERE c.id = ?
            AND cli.usuario_id = ?
            AND c.status = 'concluida'
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$contratacaoId, $clienteUsuarioId]);
        if ($stmt->fetch() === false) {
            throw new RuntimeException('A contratação não pode ser avaliada por este usuário.');
        }

        if ($this->buscarPorContratacao($contratacaoId) !== false) {
            throw new RuntimeException('Esta contratação já possui uma avaliação.');
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO avaliacoes (contratacao_id, nota, comentario)
             VALUES (?, ?, ?)"
        );
        $stmt->execute([$contratacaoId, $nota, $comentario]);
        return (int) $this->pdo->lastInsertId();
    }
}
