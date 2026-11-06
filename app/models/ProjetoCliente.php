<?php

// ==========================================================
// MODEL: PROJETOS DO CLIENTE
// ==========================================================
// RF07 / RN06 - Visualização e edição dos próprios projetos.
// ==========================================================

class ProjetoCliente
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscarClienteIdPorUsuario(int $usuarioId): ?int
    {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM clientes WHERE usuario_id = ? LIMIT 1"
        );
        $stmt->execute([$usuarioId]);
        $registro = $stmt->fetch();
        return $registro === false ? null : (int) $registro['id'];
    }

    public function listarTodos(int $clienteId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                p.id, p.titulo, p.categoria, p.descricao_resumida,
                p.orcamento_min, p.orcamento_max, p.prazo_desejado,
                p.status, p.criado_em, p.atualizado_em,
                (SELECT COUNT(*) FROM propostas AS prop WHERE prop.projeto_id = p.id) AS total_propostas,
                (SELECT c.id FROM contratacoes AS c WHERE c.projeto_id = p.id LIMIT 1) AS contratacao_id,
                (SELECT c.status FROM contratacoes AS c WHERE c.projeto_id = p.id LIMIT 1) AS contratacao_status
             FROM projetos AS p
             WHERE p.cliente_id = ?
             ORDER BY p.criado_em DESC, p.id DESC"
        );
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $projetoId, int $clienteId): array|false
    {
        $sql = "
            SELECT
                p.id, p.cliente_id, p.titulo, p.categoria, p.descricao_resumida,
                p.orcamento_min, p.orcamento_max, p.prazo_desejado,
                p.status, p.criado_em, p.atualizado_em,
                b.id AS briefing_id,
                bv.id AS briefing_versao_id,
                bv.numero_versao,
                bv.publico_alvo,
                bv.funcionalidades,
                bv.condicoes_regras,
                bv.identidade_visual,
                bv.referencias_visuais,
                bv.observacoes,
                (SELECT COUNT(*) FROM propostas AS prop WHERE prop.projeto_id = p.id) AS total_propostas,
                (SELECT c.id FROM contratacoes AS c WHERE c.projeto_id = p.id LIMIT 1) AS contratacao_id
            FROM projetos AS p
            LEFT JOIN briefings AS b ON b.projeto_id = p.id
            LEFT JOIN briefing_versoes AS bv
                ON bv.briefing_id = b.id
                AND bv.numero_versao = (
                    SELECT MAX(bv2.numero_versao)
                    FROM briefing_versoes AS bv2
                    WHERE bv2.briefing_id = b.id
                )
            WHERE p.id = ? AND p.cliente_id = ?
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$projetoId, $clienteId]);
        return $stmt->fetch();
    }

    /** @param array<string,mixed> $dados */
    public function atualizarSemPropostas(
        int $projetoId,
        int $clienteId,
        int $usuarioId,
        array $dados
    ): void {
        $categorias = ['site', 'sistema_web', 'aplicativo_mobile', 'sistema_desktop', 'outro'];
        $titulo = trim((string) ($dados['titulo'] ?? ''));
        $categoria = trim((string) ($dados['categoria'] ?? ''));
        $descricao = trim((string) ($dados['descricao_resumida'] ?? ''));
        $publico = trim((string) ($dados['publico_alvo'] ?? ''));
        $funcionalidades = trim((string) ($dados['funcionalidades'] ?? ''));
        $prazo = trim((string) ($dados['prazo_desejado'] ?? ''));

        if ($titulo === '' || mb_strlen($titulo) > 150) {
            throw new InvalidArgumentException('Informe um título válido.');
        }
        if (!in_array($categoria, $categorias, true)) {
            throw new InvalidArgumentException('Categoria inválida.');
        }
        if ($descricao === '' || $publico === '' || $funcionalidades === '') {
            throw new InvalidArgumentException('Preencha descrição, público e funcionalidades.');
        }
        if (!is_numeric($dados['orcamento_min'] ?? null) || !is_numeric($dados['orcamento_max'] ?? null)) {
            throw new InvalidArgumentException('Informe um orçamento válido.');
        }
        $orcamentoMin = (float) $dados['orcamento_min'];
        $orcamentoMax = (float) $dados['orcamento_max'];
        if ($orcamentoMin < 0 || $orcamentoMax < $orcamentoMin) {
            throw new InvalidArgumentException('A faixa de orçamento é inválida.');
        }
        $data = DateTime::createFromFormat('Y-m-d', $prazo);
        if ($data === false || $data->format('Y-m-d') !== $prazo) {
            throw new InvalidArgumentException('Informe uma data de prazo válida.');
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "SELECT p.id, p.status,
                        (SELECT COUNT(*) FROM propostas AS prop WHERE prop.projeto_id = p.id) AS total_propostas,
                        b.id AS briefing_id
                 FROM projetos AS p
                 LEFT JOIN briefings AS b ON b.projeto_id = p.id
                 WHERE p.id = ? AND p.cliente_id = ?
                 LIMIT 1 FOR UPDATE"
            );
            $stmt->execute([$projetoId, $clienteId]);
            $projeto = $stmt->fetch();
            if ($projeto === false) {
                throw new RuntimeException('Projeto não encontrado.');
            }
            if (!in_array((string) $projeto['status'], ['rascunho', 'aberto'], true)) {
                throw new RuntimeException('Este projeto não pode mais ser editado.');
            }
            if ((int) $projeto['total_propostas'] > 0) {
                throw new RuntimeException('Projetos que já receberam propostas não podem ter o briefing alterado por esta tela.');
            }

            $stmt = $this->pdo->prepare(
                "UPDATE projetos
                 SET titulo = ?, categoria = ?, descricao_resumida = ?,
                     orcamento_min = ?, orcamento_max = ?, prazo_desejado = ?, atualizado_em = NOW()
                 WHERE id = ? AND cliente_id = ?"
            );
            $stmt->execute([
                $titulo, $categoria, $descricao, $orcamentoMin, $orcamentoMax,
                $prazo, $projetoId, $clienteId
            ]);

            $briefingId = (int) ($projeto['briefing_id'] ?? 0);
            if ($briefingId <= 0) {
                throw new RuntimeException('Briefing do projeto não encontrado.');
            }

            $stmt = $this->pdo->prepare(
                "SELECT COALESCE(MAX(numero_versao), 0) AS ultima
                 FROM briefing_versoes WHERE briefing_id = ?"
            );
            $stmt->execute([$briefingId]);
            $ultima = (int) (($stmt->fetch()['ultima'] ?? 0));

            $stmt = $this->pdo->prepare(
                "UPDATE briefing_versoes SET status = 'arquivada'
                 WHERE briefing_id = ? AND status <> 'arquivada'"
            );
            $stmt->execute([$briefingId]);

            $stmt = $this->pdo->prepare(
                "INSERT INTO briefing_versoes
                 (briefing_id, numero_versao, objetivo, publico_alvo, funcionalidades,
                  condicoes_regras, identidade_visual, referencias_visuais, observacoes,
                  criado_por, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'aprovada')"
            );
            $stmt->execute([
                $briefingId,
                $ultima + 1,
                $descricao,
                $publico,
                $funcionalidades,
                $this->nuloSeVazio($dados['condicoes_regras'] ?? ''),
                $this->nuloSeVazio($dados['identidade_visual'] ?? ''),
                $this->nuloSeVazio($dados['referencias_visuais'] ?? ''),
                $this->nuloSeVazio($dados['observacoes'] ?? ''),
                $usuarioId
            ]);

            $stmt = $this->pdo->prepare(
                "UPDATE briefings SET status = 'publicado', atualizado_em = NOW() WHERE id = ?"
            );
            $stmt->execute([$briefingId]);

            $this->pdo->commit();
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $erro;
        }
    }

    private function nuloSeVazio(mixed $valor): ?string
    {
        $texto = trim((string) $valor);
        return $texto === '' ? null : $texto;
    }
}
