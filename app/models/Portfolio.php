<?php

// ==========================================================
// MODEL: PORTFÓLIO
// ==========================================================
// RF05 - Cadastro, edição e remoção de trabalhos do portfólio.
// ==========================================================

class Portfolio
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

    public function listar(int $desenvolvedorId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, titulo, descricao, link_projeto, link_repositorio, imagem,
                    criado_em, atualizado_em
             FROM portfolios
             WHERE desenvolvedor_id = ?
             ORDER BY criado_em DESC, id DESC"
        );
        $stmt->execute([$desenvolvedorId]);
        return $stmt->fetchAll();
    }

    public function buscar(int $portfolioId, int $desenvolvedorId): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, titulo, descricao, link_projeto, link_repositorio, imagem
             FROM portfolios
             WHERE id = ? AND desenvolvedor_id = ?
             LIMIT 1"
        );
        $stmt->execute([$portfolioId, $desenvolvedorId]);
        return $stmt->fetch();
    }

    public function criar(
        int $desenvolvedorId,
        string $titulo,
        string $descricao,
        string $linkProjeto,
        string $linkRepositorio,
        string $imagem
    ): int {
        $this->validar($titulo, $descricao, $linkProjeto, $linkRepositorio, $imagem);

        $stmt = $this->pdo->prepare(
            "INSERT INTO portfolios
             (desenvolvedor_id, titulo, descricao, link_projeto, link_repositorio, imagem)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $desenvolvedorId,
            trim($titulo),
            trim($descricao),
            $linkProjeto !== '' ? trim($linkProjeto) : null,
            $linkRepositorio !== '' ? trim($linkRepositorio) : null,
            $imagem !== '' ? trim($imagem) : null
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function atualizar(
        int $portfolioId,
        int $desenvolvedorId,
        string $titulo,
        string $descricao,
        string $linkProjeto,
        string $linkRepositorio,
        string $imagem
    ): bool {
        $this->validar($titulo, $descricao, $linkProjeto, $linkRepositorio, $imagem);

        $stmt = $this->pdo->prepare(
            "UPDATE portfolios
             SET titulo = ?, descricao = ?, link_projeto = ?, link_repositorio = ?,
                 imagem = ?, atualizado_em = NOW()
             WHERE id = ? AND desenvolvedor_id = ?"
        );
        $stmt->execute([
            trim($titulo),
            trim($descricao),
            $linkProjeto !== '' ? trim($linkProjeto) : null,
            $linkRepositorio !== '' ? trim($linkRepositorio) : null,
            $imagem !== '' ? trim($imagem) : null,
            $portfolioId,
            $desenvolvedorId
        ]);
        // UPDATE pode retornar rowCount() = 0 quando os dados enviados
        // são exatamente iguais aos dados já gravados. Nesse caso a operação
        // continua válida, desde que o item realmente pertença ao desenvolvedor.
        return $this->buscar($portfolioId, $desenvolvedorId) !== false;
    }

    public function excluir(int $portfolioId, int $desenvolvedorId): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM portfolios WHERE id = ? AND desenvolvedor_id = ?"
        );
        $stmt->execute([$portfolioId, $desenvolvedorId]);
        return $stmt->rowCount() === 1;
    }

    private function validar(
        string $titulo,
        string $descricao,
        string $linkProjeto,
        string $linkRepositorio,
        string $imagem
    ): void {
        if (trim($titulo) === '' || mb_strlen(trim($titulo)) > 150) {
            throw new InvalidArgumentException('Informe um título válido para o trabalho.');
        }
        if (trim($descricao) === '') {
            throw new InvalidArgumentException('Informe uma descrição do trabalho.');
        }
        foreach ([$linkProjeto, $linkRepositorio, $imagem] as $url) {
            if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
                throw new InvalidArgumentException('Os links informados devem ser URLs válidas.');
            }
        }
    }
}
