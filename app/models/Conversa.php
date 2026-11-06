<?php

// ==========================================================
// MODEL: CONVERSA
// ==========================================================
//
// Responsável pela caixa de mensagens.
//
// Mensagem.php:
// → trabalha com UMA conversa.
//
// Conversa.php:
// → lista TODAS as conversas do usuário.
//
// Cada conversa está ligada a:
//
// projeto
//    ↓
// proposta
//    ↓
// mensagens
//
// RF13 - Comunicação entre usuários
// ==========================================================

class Conversa
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
    // LISTAR CONVERSAS DO USUÁRIO
    // ======================================================

    public function listarPorUsuario(
        int $usuarioId
    ): array {

        // --------------------------------------------------
        // BUSCAR PROPOSTAS EM QUE O USUÁRIO PARTICIPA
        // --------------------------------------------------
        //
        // Pode participar como:
        //
        // - cliente dono do projeto;
        // - desenvolvedor autor da proposta.
        //
        // Também exigimos que a proposta já tenha
        // pelo menos uma mensagem.
        // --------------------------------------------------

        $sql = "
            SELECT

                proposta.id AS proposta_id,

                proposta.status AS proposta_status,


                projeto.id AS projeto_id,

                projeto.titulo AS projeto_titulo,


                cliente.usuario_id
                    AS cliente_usuario_id,


                desenvolvedor.usuario_id
                    AS desenvolvedor_usuario_id,


                usuario_cliente.nome
                    AS cliente_nome,


                usuario_desenvolvedor.nome
                    AS desenvolvedor_nome


            FROM propostas AS proposta


            INNER JOIN projetos AS projeto

                ON projeto.id =
                    proposta.projeto_id


            INNER JOIN clientes AS cliente

                ON cliente.id =
                    projeto.cliente_id


            INNER JOIN usuarios AS usuario_cliente

                ON usuario_cliente.id =
                    cliente.usuario_id


            INNER JOIN desenvolvedores AS desenvolvedor

                ON desenvolvedor.id =
                    proposta.desenvolvedor_id


            INNER JOIN usuarios AS usuario_desenvolvedor

                ON usuario_desenvolvedor.id =
                    desenvolvedor.usuario_id


            WHERE (

                cliente.usuario_id = ?

                OR

                desenvolvedor.usuario_id = ?
            )


            AND EXISTS (

                SELECT 1

                FROM mensagens

                WHERE mensagens.proposta_id =
                    proposta.id
            )
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([
            $usuarioId,
            $usuarioId
        ]);


        $conversas =
            $stmt->fetchAll();


        // ==================================================
        // COMPLETAR INFORMAÇÕES DE CADA CONVERSA
        // ==================================================
        //
        // Fazemos isso separadamente para evitar uma consulta
        // SQL muito grande e difícil de manter.
        // ==================================================

        foreach ($conversas as $indice => $conversa) {


            // ----------------------------------------------
            // ID DA PROPOSTA
            // ----------------------------------------------

            $propostaId = (int) (
                $conversa['proposta_id'] ?? 0
            );


            // ----------------------------------------------
            // DESCOBRIR A OUTRA PESSOA
            // ----------------------------------------------

            $clienteUsuarioId = (int) (
                $conversa['cliente_usuario_id'] ?? 0
            );


            // Se o usuário logado é o cliente,
            // mostramos o desenvolvedor.
            if ($usuarioId === $clienteUsuarioId) {

                $conversas[$indice]['outro_usuario_nome'] =
                    (string) (
                        $conversa['desenvolvedor_nome']
                        ?? ''
                    );

            } else {

                // Se o usuário logado é o desenvolvedor,
                // mostramos o cliente.
                $conversas[$indice]['outro_usuario_nome'] =
                    (string) (
                        $conversa['cliente_nome']
                        ?? ''
                    );
            }


            // ----------------------------------------------
            // ÚLTIMA MENSAGEM
            // ----------------------------------------------

            $ultimaMensagem =
                $this->buscarUltimaMensagem(
                    $propostaId
                );


            if ($ultimaMensagem !== false) {

                $conversas[$indice]['ultima_mensagem'] =
                    (string) (
                        $ultimaMensagem['conteudo']
                        ?? ''
                    );


                $conversas[$indice]['ultima_mensagem_em'] =
                    (string) (
                        $ultimaMensagem['enviada_em']
                        ?? ''
                    );

            } else {

                $conversas[$indice]['ultima_mensagem'] =
                    '';


                $conversas[$indice]['ultima_mensagem_em'] =
                    '';
            }


            // ----------------------------------------------
            // QUANTIDADE DE NÃO LIDAS
            // ----------------------------------------------

            $conversas[$indice]['nao_lidas'] =
                $this->contarNaoLidasDaProposta(
                    $propostaId,
                    $usuarioId
                );
        }


        // ==================================================
        // ORDENAR PELA MENSAGEM MAIS RECENTE
        // ==================================================
        //
        // Como completamos a data depois da consulta,
        // fazemos a ordenação pelo PHP.
        // ==================================================

        usort(
            $conversas,

            function (
                array $a,
                array $b
            ): int {

                $dataA = (string) (
                    $a['ultima_mensagem_em'] ?? ''
                );


                $dataB = (string) (
                    $b['ultima_mensagem_em'] ?? ''
                );


                return strcmp(
                    $dataB,
                    $dataA
                );
            }
        );


        return $conversas;
    }


    // ======================================================
    // BUSCAR ÚLTIMA MENSAGEM DA PROPOSTA
    // ======================================================

    private function buscarUltimaMensagem(
        int $propostaId
    ): array|false {

        $sql = "
            SELECT
                id,
                conteudo,
                enviada_em

            FROM mensagens

            WHERE proposta_id = ?

            ORDER BY
                enviada_em DESC,
                id DESC

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
    // NÃO LIDAS DE UMA CONVERSA
    // ======================================================
    //
    // Conta somente mensagens:
    //
    // - enviadas pela outra pessoa;
    // - ainda não lidas.
    // ======================================================

    private function contarNaoLidasDaProposta(
        int $propostaId,
        int $usuarioId
    ): int {

        $sql = "
            SELECT
                COUNT(*) AS total

            FROM mensagens

            WHERE proposta_id = ?

            AND remetente_id <> ?

            AND lida_em IS NULL
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([
            $propostaId,
            $usuarioId
        ]);


        $resultado =
            $stmt->fetch();


        return (int) (
            $resultado['total'] ?? 0
        );
    }


    // ======================================================
    // TOTAL DE MENSAGENS NÃO LIDAS DO USUÁRIO
    // ======================================================
    //
    // Futuramente permitirá mostrar no menu:
    //
    // Mensagens (2)
    //
    // ======================================================

    public function contarNaoLidas(
        int $usuarioId
    ): int {

        $sql = "
            SELECT
                COUNT(*) AS total

            FROM mensagens AS mensagem


            INNER JOIN propostas AS proposta

                ON proposta.id =
                    mensagem.proposta_id


            INNER JOIN projetos AS projeto

                ON projeto.id =
                    proposta.projeto_id


            INNER JOIN clientes AS cliente

                ON cliente.id =
                    projeto.cliente_id


            INNER JOIN desenvolvedores AS desenvolvedor

                ON desenvolvedor.id =
                    proposta.desenvolvedor_id


            WHERE mensagem.remetente_id <> ?

            AND mensagem.lida_em IS NULL


            AND (

                cliente.usuario_id = ?

                OR

                desenvolvedor.usuario_id = ?
            )
        ";


        $stmt = $this->pdo->prepare(
            $sql
        );


        $stmt->execute([
            $usuarioId,
            $usuarioId,
            $usuarioId
        ]);


        $resultado =
            $stmt->fetch();


        return (int) (
            $resultado['total'] ?? 0
        );
    }
}