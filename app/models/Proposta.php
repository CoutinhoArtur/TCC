<?php

// ==========================================================
// MODEL: PROPOSTA
// ==========================================================
//
// Este Model concentra as operações relacionadas
// às propostas enviadas pelos desenvolvedores.
//
// Fluxo principal:
//
// projeto
//    ↓
// proposta preliminar
//    ↓
// negociação pelo chat
//    ↓
// proposta definitiva
//    ↓
// cliente aceita ou recusa
//
// RF10 - Envio de propostas
// RF11 - Gerenciamento de propostas
// ==========================================================

class Proposta
{
    // ------------------------------------------------------
    // CONEXÃO COM O BANCO
    // ------------------------------------------------------

    private PDO $pdo;


    // ------------------------------------------------------
    // CONSTRUTOR
    // ------------------------------------------------------

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    // ======================================================
    // VERIFICAR SE JÁ EXISTE PROPOSTA
    // ======================================================
    //
    // Um desenvolvedor não pode enviar duas propostas
    // diferentes para o mesmo projeto.
    //
    // ======================================================

    public function propostaExiste(
        int $projetoId,
        int $desenvolvedorId
    ): bool {

        $sql = "
            SELECT id

            FROM propostas

            WHERE projeto_id = ?

            AND desenvolvedor_id = ?

            LIMIT 1
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([
            $projetoId,
            $desenvolvedorId
        ]);


        return $stmt->fetch() !== false;
    }


    // ======================================================
    // CRIAR PROPOSTA PRELIMINAR
    // ======================================================
    //
    // Esta é a primeira proposta enviada pelo desenvolvedor.
    //
    // Depois dela:
    //
    // - o chat é liberado;
    // - cliente e desenvolvedor podem negociar;
    // - posteriormente ela poderá virar definitiva.
    //
    // ======================================================

    public function criarPreliminar(
        int $projetoId,
        int $desenvolvedorId,
        int $briefingVersaoId,
        float $valor,
        int $prazoEstimado,
        string $descricao,
        string $entregaveis
    ): int {

        // --------------------------------------------------
        // IMPEDIR PROPOSTA DUPLICADA
        // --------------------------------------------------

        if (
            $this->propostaExiste(
                $projetoId,
                $desenvolvedorId
            )
        ) {

            throw new RuntimeException(
                'Você já enviou uma proposta para este projeto.'
            );
        }


        // --------------------------------------------------
        // INSERT
        // --------------------------------------------------

        $sql = "
            INSERT INTO propostas (
                projeto_id,
                desenvolvedor_id,
                valor,
                prazo_estimado,
                descricao,
                entregaveis,
                briefing_versao_id,
                status
            )
            VALUES (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                'preliminar'
            )
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([

            $projetoId,

            $desenvolvedorId,

            $valor,

            $prazoEstimado,

            $descricao,

            $entregaveis,

            $briefingVersaoId
        ]);


        return (int) $this->pdo->lastInsertId();
    }


    // ======================================================
    // BUSCAR PROPOSTA DO DESENVOLVEDOR PARA UM PROJETO
    // ======================================================

    public function buscarPorProjetoEDesenvolvedor(
        int $projetoId,
        int $desenvolvedorId
    ): array|false {

        $sql = "
            SELECT

                id,

                projeto_id,

                desenvolvedor_id,

                valor,

                prazo_estimado,

                descricao,

                entregaveis,

                briefing_versao_id,

                status,

                criado_em,

                atualizado_em

            FROM propostas

            WHERE projeto_id = ?

            AND desenvolvedor_id = ?

            LIMIT 1
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([
            $projetoId,
            $desenvolvedorId
        ]);


        return $stmt->fetch();
    }


    // ======================================================
    // BUSCAR PROPOSTA POR ID E DESENVOLVEDOR
    // ======================================================
    //
    // Utilizamos também o desenvolvedor_id por segurança.
    //
    // Assim um desenvolvedor não consegue alterar
    // uma proposta pertencente a outro profissional.
    //
    // ======================================================

    public function buscarPorIdEDesenvolvedor(
        int $propostaId,
        int $desenvolvedorId
    ): array|false {

        $sql = "
            SELECT

                id,

                projeto_id,

                desenvolvedor_id,

                valor,

                prazo_estimado,

                descricao,

                entregaveis,

                briefing_versao_id,

                status,

                criado_em,

                atualizado_em

            FROM propostas

            WHERE id = ?

            AND desenvolvedor_id = ?

            LIMIT 1
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([
            $propostaId,
            $desenvolvedorId
        ]);


        return $stmt->fetch();
    }


    // ======================================================
    // TRANSFORMAR EM PROPOSTA DEFINITIVA
    // ======================================================
    //
    // Depois da negociação pelo chat, o desenvolvedor
    // poderá confirmar:
    //
    // - valor final proposto;
    // - prazo final;
    // - descrição final;
    // - entregáveis finais.
    //
    // A mesma proposta é atualizada.
    //
    // NÃO criamos uma segunda linha.
    //
    // Exemplo:
    //
    // antes:
    //
    // status = em_negociacao
    //
    // depois:
    //
    // status = definitiva
    //
    // ======================================================

    public function tornarDefinitiva(
        int $propostaId,
        int $desenvolvedorId,
        float $valor,
        int $prazoEstimado,
        string $descricao,
        string $entregaveis
    ): bool {

        // --------------------------------------------------
        // VALIDAR VALOR
        // --------------------------------------------------

        if ($valor <= 0) {

            throw new InvalidArgumentException(
                'O valor da proposta deve ser maior que zero.'
            );
        }


        // --------------------------------------------------
        // VALIDAR PRAZO
        // --------------------------------------------------

        if ($prazoEstimado <= 0) {

            throw new InvalidArgumentException(
                'O prazo deve ser maior que zero.'
            );
        }


        // --------------------------------------------------
        // VALIDAR DESCRIÇÃO
        // --------------------------------------------------

        $descricao = trim(
            $descricao
        );


        if ($descricao === '') {

            throw new InvalidArgumentException(
                'A descrição da proposta é obrigatória.'
            );
        }


        // --------------------------------------------------
        // VALIDAR ENTREGÁVEIS
        // --------------------------------------------------

        $entregaveis = trim(
            $entregaveis
        );


        if ($entregaveis === '') {

            throw new InvalidArgumentException(
                'Os entregáveis são obrigatórios.'
            );
        }


        // ==================================================
        // ATUALIZAR PROPOSTA
        // ==================================================
        //
        // Permitimos transformar em definitiva apenas
        // propostas que ainda estejam:
        //
        // preliminar
        // OU
        // em_negociacao
        //
        // Uma proposta aceita ou recusada não pode voltar.
        // ==================================================

        $sql = "
            UPDATE propostas

            SET
                valor = ?,
                prazo_estimado = ?,
                descricao = ?,
                entregaveis = ?,
                status = 'definitiva',
                atualizado_em = NOW()

            WHERE id = ?

            AND desenvolvedor_id = ?

            AND status IN (
                'preliminar',
                'em_negociacao',
                'aguardando_revisao'
            )
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([

            $valor,

            $prazoEstimado,

            $descricao,

            $entregaveis,

            $propostaId,

            $desenvolvedorId
        ]);


        // rowCount() informa quantas linhas foram alteradas.
        //
        // 1 = proposta alterada.
        //
        // 0 = proposta inexistente, pertencente a outro
        // desenvolvedor ou com status não permitido.

        return $stmt->rowCount() === 1;
    }
}