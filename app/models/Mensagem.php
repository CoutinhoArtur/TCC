<?php

// ==========================================================
// MODEL: MENSAGEM / CHAT
// ==========================================================
//
// Este Model concentra as operações relacionadas ao chat.
//
// O chat do SiteMatch NÃO existe solto.
//
// Toda conversa está vinculada a uma proposta:
//
// projeto
//    ↓
// proposta preliminar
//    ↓
// chat / negociação
//
// Isso implementa a decisão definida no projeto:
//
// "O chat é liberado após o envio da proposta,
// antes da contratação."
//
// RF13 - Comunicação entre cliente e desenvolvedor
// RN15 - Chat liberado após proposta
// ==========================================================

class Mensagem
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
    // BUSCAR PARTICIPANTES DA PROPOSTA
    // ======================================================
    //
    // Precisamos descobrir:
    //
    // - qual usuário é o cliente;
    // - qual usuário é o desenvolvedor.
    //
    // Isso será utilizado para impedir que uma terceira
    // pessoa acesse uma conversa que não pertence a ela.
    //
    // ======================================================

    public function buscarParticipantes(
        int $propostaId
    ): array|false {

        $sql = "
            SELECT
                proposta.id AS proposta_id,
                proposta.status AS proposta_status,

                projeto.id AS projeto_id,
                projeto.titulo AS projeto_titulo,

                cliente.usuario_id AS cliente_usuario_id,

                desenvolvedor.usuario_id
                    AS desenvolvedor_usuario_id

            FROM propostas AS proposta

            INNER JOIN projetos AS projeto
                ON projeto.id = proposta.projeto_id

            INNER JOIN clientes AS cliente
                ON cliente.id = projeto.cliente_id

            INNER JOIN desenvolvedores AS desenvolvedor
                ON desenvolvedor.id =
                    proposta.desenvolvedor_id

            WHERE proposta.id = ?

            LIMIT 1
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([
            $propostaId
        ]);


        return $stmt->fetch();
    }


    // ======================================================
    // VERIFICAR SE USUÁRIO PARTICIPA DA CONVERSA
    // ======================================================
    //
    // Retorna true apenas quando o usuário logado é:
    //
    // - o cliente dono do projeto;
    // OU
    // - o desenvolvedor que enviou a proposta.
    //
    // ======================================================

    public function usuarioParticipa(
        int $propostaId,
        int $usuarioId
    ): bool {

        $participantes =
            $this->buscarParticipantes(
                $propostaId
            );


        if ($participantes === false) {

            return false;
        }


        $clienteUsuarioId = (int) (
            $participantes['cliente_usuario_id']
            ?? 0
        );


        $desenvolvedorUsuarioId = (int) (
            $participantes[
                'desenvolvedor_usuario_id'
            ]
            ?? 0
        );


        return (
            $usuarioId === $clienteUsuarioId ||
            $usuarioId === $desenvolvedorUsuarioId
        );
    }


    // ======================================================
    // ENVIAR MENSAGEM
    // ======================================================
    //
    // Recebe:
    //
    // proposta_id
    // usuário remetente
    // conteúdo da mensagem
    //
    // Também transforma automaticamente uma proposta
    // "preliminar" em "em_negociacao" quando a conversa
    // efetivamente começa.
    //
    // ======================================================

    public function enviar(
        int $propostaId,
        int $remetenteId,
        string $conteudo
    ): int {

        // --------------------------------------------------
        // REMOVER ESPAÇOS EXTRAS
        // --------------------------------------------------

        $conteudo = trim(
            $conteudo
        );


        // --------------------------------------------------
        // IMPEDIR MENSAGEM VAZIA
        // --------------------------------------------------

        if ($conteudo === '') {

            throw new InvalidArgumentException(
                'A mensagem não pode estar vazia.'
            );
        }


        // --------------------------------------------------
        // VALIDAR PARTICIPANTE
        // --------------------------------------------------

        if (
            !$this->usuarioParticipa(
                $propostaId,
                $remetenteId
            )
        ) {

            throw new RuntimeException(
                'Usuário não autorizado para esta conversa.'
            );
        }


        try {

            // ==================================================
            // INICIAR TRANSAÇÃO
            // ==================================================
            //
            // Faremos duas operações:
            //
            // 1. inserir mensagem;
            // 2. alterar proposta para em_negociacao,
            //    caso ainda esteja preliminar.
            //
            // ==================================================

            $this->pdo->beginTransaction();


            // ==================================================
            // INSERIR MENSAGEM
            // ==================================================

            $sqlMensagem = "
                INSERT INTO mensagens (
                    proposta_id,
                    remetente_id,
                    conteudo
                )
                VALUES (
                    ?,
                    ?,
                    ?
                )
            ";


            $stmtMensagem =
                $this->pdo->prepare(
                    $sqlMensagem
                );


            $stmtMensagem->execute([
                $propostaId,
                $remetenteId,
                $conteudo
            ]);


            // Recuperamos o ID da mensagem criada.
            $mensagemId =
                (int) $this->pdo->lastInsertId();


            // ==================================================
            // ATUALIZAR STATUS DA PROPOSTA
            // ==================================================
            //
            // Se ela ainda estiver:
            //
            // preliminar
            //
            // passa para:
            //
            // em_negociacao
            //
            // porque houve início efetivo da conversa.
            //
            // ==================================================

            $sqlProposta = "
                UPDATE propostas

                SET
                    status = 'em_negociacao',
                    atualizado_em = NOW()

                WHERE id = ?

                AND status = 'preliminar'
            ";


            $stmtProposta =
                $this->pdo->prepare(
                    $sqlProposta
                );


            $stmtProposta->execute([
                $propostaId
            ]);


            // ==================================================
            // CONFIRMAR TRANSAÇÃO
            // ==================================================

            $this->pdo->commit();


            return $mensagemId;


        } catch (Throwable $erro) {


            // --------------------------------------------------
            // DESFAZER EM CASO DE ERRO
            // --------------------------------------------------

            if ($this->pdo->inTransaction()) {

                $this->pdo->rollBack();
            }


            throw $erro;
        }
    }


    // ======================================================
    // BUSCAR HISTÓRICO DA CONVERSA
    // ======================================================
    //
    // Retorna todas as mensagens da proposta
    // da mais antiga para a mais recente.
    //
    // Também retorna o nome e tipo do remetente.
    //
    // ======================================================

    public function listarPorProposta(
        int $propostaId
    ): array {

        $sql = "
            SELECT
                mensagem.id,
                mensagem.proposta_id,
                mensagem.remetente_id,
                mensagem.conteudo,
                mensagem.enviada_em,
                mensagem.lida_em,

                usuario.nome AS remetente_nome,

                usuario.tipo AS remetente_tipo

            FROM mensagens AS mensagem

            INNER JOIN usuarios AS usuario
                ON usuario.id =
                    mensagem.remetente_id

            WHERE mensagem.proposta_id = ?

            ORDER BY
                mensagem.enviada_em ASC,
                mensagem.id ASC
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([
            $propostaId
        ]);


        return $stmt->fetchAll();
    }


    // ======================================================
    // MARCAR MENSAGENS COMO LIDAS
    // ======================================================
    //
    // Quando o usuário abre o chat, marcamos como lidas
    // apenas as mensagens enviadas pela outra pessoa.
    //
    // ======================================================

    public function marcarComoLidas(
        int $propostaId,
        int $usuarioAtualId
    ): void {

        $sql = "
            UPDATE mensagens

            SET lida_em = NOW()

            WHERE proposta_id = ?

            AND remetente_id <> ?

            AND lida_em IS NULL
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([
            $propostaId,
            $usuarioAtualId
        ]);
    }
}