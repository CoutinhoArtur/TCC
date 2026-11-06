<?php

// ==========================================================
// MODEL: CONTRATAÇÃO
// ==========================================================
// RF11 / RF12 - Aceite, recusa e registro da contratação.
// RN12 / RN13 / RN14 - Uma proposta aceita por projeto e
// contratação iniciando em andamento.
// ==========================================================

class Contratacao
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function aceitarProposta(int $propostaId, int $clienteUsuarioId): int
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "
                SELECT
                    prop.id, prop.projeto_id, prop.valor, prop.prazo_estimado,
                    prop.briefing_versao_id, prop.status,
                    proj.status AS projeto_status
                FROM propostas AS prop
                INNER JOIN projetos AS proj ON proj.id = prop.projeto_id
                INNER JOIN clientes AS cli ON cli.id = proj.cliente_id
                WHERE prop.id = ? AND cli.usuario_id = ?
                LIMIT 1
                FOR UPDATE
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$propostaId, $clienteUsuarioId]);
            $proposta = $stmt->fetch();

            if ($proposta === false) {
                throw new RuntimeException('Proposta não encontrada ou não autorizada.');
            }
            if (($proposta['status'] ?? '') !== 'definitiva') {
                throw new RuntimeException('Somente uma proposta definitiva pode ser aceita.');
            }
            if (!in_array((string) ($proposta['projeto_status'] ?? ''), ['aberto', 'em_negociacao'], true)) {
                throw new RuntimeException('Este projeto não está disponível para contratação.');
            }

            $projetoId = (int) $proposta['projeto_id'];
            $briefingVersaoId = (int) ($proposta['briefing_versao_id'] ?? 0);
            if ($briefingVersaoId <= 0) {
                throw new RuntimeException('A proposta não possui uma versão válida do briefing.');
            }

            // RN14: a contratação nasce em andamento.
            $stmt = $this->pdo->prepare(
                "INSERT INTO contratacoes
                 (projeto_id, proposta_id, briefing_versao_id, valor_final,
                  prazo_final_dias, status, data_inicio)
                 VALUES (?, ?, ?, ?, ?, 'em_andamento', NOW())"
            );
            $stmt->execute([
                $projetoId,
                $propostaId,
                $briefingVersaoId,
                (float) $proposta['valor'],
                (int) $proposta['prazo_estimado']
            ]);
            $contratacaoId = (int) $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare(
                "UPDATE propostas
                 SET status = 'aceita', atualizado_em = NOW()
                 WHERE id = ? AND status = 'definitiva'"
            );
            $stmt->execute([$propostaId]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Não foi possível marcar a proposta como aceita.');
            }

            // RN12: propostas concorrentes são encerradas, não apagadas.
            $stmt = $this->pdo->prepare(
                "UPDATE propostas
                 SET status = 'encerrada', atualizado_em = NOW()
                 WHERE projeto_id = ? AND id <> ?
                 AND status IN ('preliminar','em_negociacao','aguardando_revisao','definitiva')"
            );
            $stmt->execute([$projetoId, $propostaId]);

            // Como a contratação já começa em andamento, o projeto acompanha o mesmo estado.
            $stmt = $this->pdo->prepare(
                "UPDATE projetos
                 SET status = 'em_andamento', atualizado_em = NOW()
                 WHERE id = ? AND status IN ('aberto','em_negociacao')"
            );
            $stmt->execute([$projetoId]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Não foi possível atualizar o projeto contratado.');
            }

            $this->pdo->commit();
            return $contratacaoId;
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $erro;
        }
    }

    public function recusarProposta(int $propostaId, int $clienteUsuarioId): bool
    {
        $sql = "
            UPDATE propostas AS prop
            INNER JOIN projetos AS proj ON proj.id = prop.projeto_id
            INNER JOIN clientes AS cli ON cli.id = proj.cliente_id
            SET prop.status = 'recusada', prop.atualizado_em = NOW()
            WHERE prop.id = ? AND cli.usuario_id = ? AND prop.status IN ('preliminar','em_negociacao','aguardando_revisao','definitiva')
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$propostaId, $clienteUsuarioId]);
        return $stmt->rowCount() === 1;
    }
}
