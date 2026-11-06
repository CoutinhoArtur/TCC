<?php

// ==========================================================
// MODEL: PERFIL
// ==========================================================
// RF03 - Gerenciamento de perfil
// RF04 - Perfil profissional do desenvolvedor
//
// Centraliza a leitura e edição das informações cadastrais
// de clientes e desenvolvedores. As atualizações são feitas
// com prepared statements e, quando necessário, transações.
// ==========================================================

class Perfil
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscarPorUsuario(int $usuarioId): array|false
    {
        $sql = "
            SELECT
                u.id,
                u.nome,
                u.email,
                u.tipo,
                u.status,
                c.id AS cliente_id,
                c.nome_empresa,
                c.descricao AS cliente_descricao,
                c.telefone,
                c.cidade,
                d.id AS desenvolvedor_id,
                d.titulo_profissional,
                d.bio,
                d.experiencia,
                d.disponibilidade,
                d.faixa_preco_min,
                d.faixa_preco_max
            FROM usuarios AS u
            LEFT JOIN clientes AS c ON c.usuario_id = u.id
            LEFT JOIN desenvolvedores AS d ON d.usuario_id = u.id
            WHERE u.id = ?
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuarioId]);
        return $stmt->fetch();
    }

    public function listarHabilidades(int $desenvolvedorId): array
    {
        $sql = "
            SELECT h.id, h.nome, dh.nivel
            FROM desenvolvedor_habilidades AS dh
            INNER JOIN habilidades AS h ON h.id = dh.habilidade_id
            WHERE dh.desenvolvedor_id = ?
            AND h.status = 'ativa'
            ORDER BY h.nome
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$desenvolvedorId]);
        return $stmt->fetchAll();
    }

    public function atualizarCliente(
        int $usuarioId,
        string $nome,
        string $email,
        string $nomeEmpresa,
        string $descricao,
        string $telefone,
        string $cidade
    ): void {
        $this->validarDadosBasicos($usuarioId, $nome, $email);

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "UPDATE usuarios
                 SET nome = ?, email = ?, atualizado_em = NOW()
                 WHERE id = ? AND tipo = 'cliente'"
            );
            $stmt->execute([$nome, $email, $usuarioId]);

            $stmt = $this->pdo->prepare(
                "UPDATE clientes
                 SET nome_empresa = ?, descricao = ?, telefone = ?, cidade = ?
                 WHERE usuario_id = ?"
            );
            $stmt->execute([
                $nomeEmpresa !== '' ? $nomeEmpresa : null,
                $descricao !== '' ? $descricao : null,
                $telefone !== '' ? $telefone : null,
                $cidade !== '' ? $cidade : null,
                $usuarioId
            ]);

            $this->pdo->commit();
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $erro;
        }
    }

    /**
     * @param array<int, array{nome:string,nivel:string}> $habilidades
     */
    public function atualizarDesenvolvedor(
        int $usuarioId,
        string $nome,
        string $email,
        string $tituloProfissional,
        string $bio,
        string $experiencia,
        string $disponibilidade,
        ?float $faixaMin,
        ?float $faixaMax,
        array $habilidades
    ): void {
        $this->validarDadosBasicos($usuarioId, $nome, $email);

        if ($faixaMin !== null && $faixaMin < 0) {
            throw new InvalidArgumentException('A faixa mínima não pode ser negativa.');
        }
        if ($faixaMax !== null && $faixaMax < 0) {
            throw new InvalidArgumentException('A faixa máxima não pode ser negativa.');
        }
        if ($faixaMin !== null && $faixaMax !== null && $faixaMin > $faixaMax) {
            throw new InvalidArgumentException('A faixa mínima não pode ser maior que a máxima.');
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "UPDATE usuarios
                 SET nome = ?, email = ?, atualizado_em = NOW()
                 WHERE id = ? AND tipo = 'desenvolvedor'"
            );
            $stmt->execute([$nome, $email, $usuarioId]);

            $stmt = $this->pdo->prepare(
                "UPDATE desenvolvedores
                 SET titulo_profissional = ?, bio = ?, experiencia = ?,
                     disponibilidade = ?, faixa_preco_min = ?, faixa_preco_max = ?
                 WHERE usuario_id = ?"
            );
            $stmt->execute([
                $tituloProfissional !== '' ? $tituloProfissional : null,
                $bio !== '' ? $bio : null,
                $experiencia !== '' ? $experiencia : null,
                $disponibilidade !== '' ? $disponibilidade : null,
                $faixaMin,
                $faixaMax,
                $usuarioId
            ]);

            $stmt = $this->pdo->prepare(
                "SELECT id FROM desenvolvedores WHERE usuario_id = ? LIMIT 1"
            );
            $stmt->execute([$usuarioId]);
            $dev = $stmt->fetch();
            if ($dev === false) {
                throw new RuntimeException('Perfil profissional não encontrado.');
            }
            $desenvolvedorId = (int) $dev['id'];

            // Substituímos a lista inteira para manter a edição simples e previsível.
            $stmt = $this->pdo->prepare(
                "DELETE FROM desenvolvedor_habilidades WHERE desenvolvedor_id = ?"
            );
            $stmt->execute([$desenvolvedorId]);

            $nomesUsados = [];
            foreach ($habilidades as $habilidade) {
                $nomeHabilidade = trim($habilidade['nome'] ?? '');
                $nivel = $habilidade['nivel'] ?? 'intermediario';
                if ($nomeHabilidade === '') {
                    continue;
                }
                if (!in_array($nivel, ['basico', 'intermediario', 'avancado'], true)) {
                    $nivel = 'intermediario';
                }

                $chave = mb_strtolower($nomeHabilidade);
                if (isset($nomesUsados[$chave])) {
                    continue;
                }
                $nomesUsados[$chave] = true;

                $stmt = $this->pdo->prepare(
                    "SELECT id FROM habilidades WHERE LOWER(nome) = LOWER(?) LIMIT 1"
                );
                $stmt->execute([$nomeHabilidade]);
                $registro = $stmt->fetch();

                if ($registro === false) {
                    $stmt = $this->pdo->prepare(
                        "INSERT INTO habilidades (nome, status) VALUES (?, 'ativa')"
                    );
                    $stmt->execute([$nomeHabilidade]);
                    $habilidadeId = (int) $this->pdo->lastInsertId();
                } else {
                    $habilidadeId = (int) $registro['id'];
                }

                $stmt = $this->pdo->prepare(
                    "INSERT INTO desenvolvedor_habilidades
                     (desenvolvedor_id, habilidade_id, nivel)
                     VALUES (?, ?, ?)"
                );
                $stmt->execute([$desenvolvedorId, $habilidadeId, $nivel]);
            }

            $this->pdo->commit();
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $erro;
        }
    }

    private function validarDadosBasicos(int $usuarioId, string $nome, string $email): void
    {
        $nome = trim($nome);
        $email = strtolower(trim($email));

        if ($nome === '' || mb_strlen($nome) > 150) {
            throw new InvalidArgumentException('Informe um nome válido.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
            throw new InvalidArgumentException('Informe um e-mail válido.');
        }

        $stmt = $this->pdo->prepare(
            "SELECT id FROM usuarios WHERE email = ? AND id <> ? LIMIT 1"
        );
        $stmt->execute([$email, $usuarioId]);
        if ($stmt->fetch() !== false) {
            throw new InvalidArgumentException('Este e-mail já está sendo utilizado.');
        }
    }
}
