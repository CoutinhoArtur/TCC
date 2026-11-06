<?php

// ==========================================================
// MODEL: EXECUÇÃO DO PROJETO
// ==========================================================
// RF14 / RN18 / RN19
// Controla início legado, sinalização de entrega, solicitação
// de ajustes e confirmação da conclusão.
// ==========================================================

class ExecucaoProjeto
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Compatibilidade com contratações antigas que ainda estejam 'ativa'.
    public function iniciar(int $contratacaoId, int $usuarioId): bool
    {
        try {
            $this->pdo->beginTransaction();
            $dados = $this->buscarParaDesenvolvedor($contratacaoId, $usuarioId, true);
            if (($dados['contratacao_status'] ?? '') !== 'ativa') {
                throw new RuntimeException('Esta contratação não precisa ser iniciada.');
            }

            $stmt = $this->pdo->prepare(
                "UPDATE contratacoes
                 SET status = 'em_andamento', data_inicio = COALESCE(data_inicio, NOW())
                 WHERE id = ? AND status = 'ativa'"
            );
            $stmt->execute([$contratacaoId]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Não foi possível iniciar a contratação.');
            }

            $stmt = $this->pdo->prepare(
                "UPDATE projetos
                 SET status = 'em_andamento', atualizado_em = NOW()
                 WHERE id = ? AND status = 'contratado'"
            );
            $stmt->execute([(int) $dados['projeto_id']]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Não foi possível atualizar o projeto.');
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $erro;
        }
    }

    public function enviarParaConfirmacao(int $contratacaoId, int $usuarioId): bool
    {
        try {
            $this->pdo->beginTransaction();
            $dados = $this->buscarParaDesenvolvedor($contratacaoId, $usuarioId, true);

            if (($dados['contratacao_status'] ?? '') !== 'em_andamento') {
                throw new RuntimeException('Somente um projeto em andamento pode ser entregue.');
            }

            $stmt = $this->pdo->prepare(
                "UPDATE contratacoes
                 SET status = 'aguardando_confirmacao', data_entrega = NOW()
                 WHERE id = ? AND status = 'em_andamento'"
            );
            $stmt->execute([$contratacaoId]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Não foi possível registrar a entrega.');
            }

            $stmt = $this->pdo->prepare(
                "UPDATE projetos
                 SET status = 'aguardando_confirmacao', atualizado_em = NOW()
                 WHERE id = ? AND status = 'em_andamento'"
            );
            $stmt->execute([(int) $dados['projeto_id']]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Não foi possível atualizar o projeto para aguardando confirmação.');
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $erro;
        }
    }

    public function solicitarAjustes(
        int $contratacaoId,
        int $clienteUsuarioId,
        string $descricaoAjustes
    ): bool {
        $descricaoAjustes = trim($descricaoAjustes);
        if ($descricaoAjustes === '') {
            throw new InvalidArgumentException('Descreva os ajustes necessários.');
        }

        try {
            $this->pdo->beginTransaction();
            $dados = $this->buscarParaCliente($contratacaoId, $clienteUsuarioId, true);

            if (($dados['contratacao_status'] ?? '') !== 'aguardando_confirmacao') {
                throw new RuntimeException('Esta contratação não está aguardando confirmação.');
            }

            // RN19: ao solicitar ajustes a contratação volta para em andamento.
            $stmt = $this->pdo->prepare(
                "UPDATE contratacoes
                 SET status = 'em_andamento'
                 WHERE id = ? AND status = 'aguardando_confirmacao'"
            );
            $stmt->execute([$contratacaoId]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Não foi possível reabrir a contratação para ajustes.');
            }

            $stmt = $this->pdo->prepare(
                "UPDATE projetos
                 SET status = 'em_andamento', atualizado_em = NOW()
                 WHERE id = ? AND status = 'aguardando_confirmacao'"
            );
            $stmt->execute([(int) $dados['projeto_id']]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Não foi possível reabrir o projeto para ajustes.');
            }

            // O texto dos ajustes fica registrado no mesmo histórico de conversa.
            $stmt = $this->pdo->prepare(
                "INSERT INTO mensagens (proposta_id, remetente_id, conteudo)
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([
                (int) $dados['proposta_id'],
                $clienteUsuarioId,
                'Solicitação de ajustes: ' . $descricaoAjustes
            ]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $erro;
        }
    }

    public function confirmarConclusao(int $contratacaoId, int $clienteUsuarioId): bool
    {
        try {
            $this->pdo->beginTransaction();
            $dados = $this->buscarParaCliente($contratacaoId, $clienteUsuarioId, true);

            if (($dados['contratacao_status'] ?? '') !== 'aguardando_confirmacao') {
                throw new RuntimeException('Esta contratação não está aguardando confirmação.');
            }

            $stmt = $this->pdo->prepare(
                "UPDATE contratacoes
                 SET status = 'concluida', data_conclusao = NOW()
                 WHERE id = ? AND status = 'aguardando_confirmacao'"
            );
            $stmt->execute([$contratacaoId]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Não foi possível concluir a contratação.');
            }

            $stmt = $this->pdo->prepare(
                "UPDATE projetos
                 SET status = 'concluido', atualizado_em = NOW()
                 WHERE id = ? AND status = 'aguardando_confirmacao'"
            );
            $stmt->execute([(int) $dados['projeto_id']]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Não foi possível concluir o projeto.');
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $erro;
        }
    }

    private function buscarParaDesenvolvedor(int $contratacaoId, int $usuarioId, bool $bloquear): array
    {
        $sql = "
            SELECT c.id, c.projeto_id, c.proposta_id,
                   c.status AS contratacao_status,
                   p.status AS projeto_status
            FROM contratacoes AS c
            INNER JOIN projetos AS p ON p.id = c.projeto_id
            INNER JOIN propostas AS prop ON prop.id = c.proposta_id
            INNER JOIN desenvolvedores AS d ON d.id = prop.desenvolvedor_id
            WHERE c.id = ? AND d.usuario_id = ?
            LIMIT 1" . ($bloquear ? " FOR UPDATE" : "");
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$contratacaoId, $usuarioId]);
        $dados = $stmt->fetch();
        if ($dados === false) {
            throw new RuntimeException('Contratação não encontrada ou não autorizada.');
        }
        return $dados;
    }

    private function buscarParaCliente(int $contratacaoId, int $usuarioId, bool $bloquear): array
    {
        $sql = "
            SELECT c.id, c.projeto_id, c.proposta_id,
                   c.status AS contratacao_status,
                   p.status AS projeto_status
            FROM contratacoes AS c
            INNER JOIN projetos AS p ON p.id = c.projeto_id
            INNER JOIN clientes AS cli ON cli.id = p.cliente_id
            WHERE c.id = ? AND cli.usuario_id = ?
            LIMIT 1" . ($bloquear ? " FOR UPDATE" : "");
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$contratacaoId, $usuarioId]);
        $dados = $stmt->fetch();
        if ($dados === false) {
            throw new RuntimeException('Contratação não encontrada ou não autorizada.');
        }
        return $dados;
    }
}
