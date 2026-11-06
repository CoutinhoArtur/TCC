<?php

// ==========================================================
// MODEL: PROJETO
// ==========================================================
//
// Este Model concentra operações relacionadas aos projetos.
//
// Nesta primeira implementação ele será responsável por:
//
// 1. criar o projeto;
// 2. criar o briefing principal;
// 3. criar a primeira versão do briefing.
//
// As três operações acontecem dentro de uma TRANSAÇÃO.
//
// Isso significa:
//
// deu tudo certo
//      ↓
// COMMIT
//      ↓
// dados ficam salvos
//
// ocorreu algum erro
//      ↓
// ROLLBACK
//      ↓
// nenhuma inserção é mantida
//
// RF06  - Publicação de projetos
// RF17  - Briefing guiado
// RNF05 - Consultas com prepared statements
// ==========================================================

class Projeto
{
    // ------------------------------------------------------
    // CONEXÃO COM O BANCO
    // ------------------------------------------------------

    private PDO $pdo;


    // ------------------------------------------------------
    // CONSTRUTOR
    // ------------------------------------------------------
    //
    // Recebe a conexão criada em database.php.
    // ------------------------------------------------------

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    // ======================================================
    // PUBLICAR PROJETO COMPLETO
    // ======================================================
    //
    // Recebe:
    //
    // $clienteId
    //     → clientes.id
    //
    // $usuarioId
    //     → usuarios.id
    //
    // $dados
    //     → informações coletadas nas quatro etapas
    //       do Briefing Guiado.
    //
    // Retorna:
    //
    // o ID do projeto criado.
    // ======================================================

    public function publicarCompleto(
        int $clienteId,
        int $usuarioId,
        array $dados
    ): int {

        try {

            // --------------------------------------------------
            // INICIAR TRANSAÇÃO
            // --------------------------------------------------

            $this->pdo->beginTransaction();


            // ==================================================
            // 1. CRIAR PROJETO
            // ==================================================
            //
            // O projeto nasce com status "aberto".
            //
            // Isso significa que ele já poderá futuramente
            // aparecer para os desenvolvedores.
            // ==================================================

            $sqlProjeto = "
                INSERT INTO projetos (
                    cliente_id,
                    titulo,
                    categoria,
                    descricao_resumida,
                    orcamento_min,
                    orcamento_max,
                    prazo_desejado,
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
                    'aberto'
                )
            ";


            $stmtProjeto = $this->pdo->prepare(
                $sqlProjeto
            );


            $stmtProjeto->execute([

                $clienteId,

                $dados['titulo'],

                $dados['categoria'],

                $dados['descricao_resumida'],

                $dados['orcamento_min'],

                $dados['orcamento_max'],

                $dados['prazo_desejado']
            ]);


            // --------------------------------------------------
            // RECUPERAR ID DO PROJETO
            // --------------------------------------------------
            //
            // Exemplo:
            //
            // projetos.id = 2
            // --------------------------------------------------

            $projetoId = (int) $this->pdo->lastInsertId();


            // ==================================================
            // 2. CRIAR BRIEFING PRINCIPAL
            // ==================================================
            //
            // Cada projeto possui um briefing principal.
            //
            // Esse briefing poderá possuir várias versões
            // durante futuras negociações.
            // ==================================================

            $sqlBriefing = "
                INSERT INTO briefings (
                    projeto_id,
                    status
                )
                VALUES (
                    ?,
                    'publicado'
                )
            ";


            $stmtBriefing = $this->pdo->prepare(
                $sqlBriefing
            );


            $stmtBriefing->execute([
                $projetoId
            ]);


            // --------------------------------------------------
            // RECUPERAR ID DO BRIEFING
            // --------------------------------------------------

            $briefingId = (int) $this->pdo->lastInsertId();


            // ==================================================
            // 3. CRIAR PRIMEIRA VERSÃO DO BRIEFING
            // ==================================================
            //
            // Essa é a versão criada pelo próprio cliente
            // durante o Briefing Guiado.
            //
            // numero_versao = 1
            //
            // criado_por = usuarios.id do cliente
            //
            // status = aprovada
            //
            // Ela poderá futuramente ser substituída por
            // versões revisadas durante a negociação.
            // ==================================================


            // --------------------------------------------------
            // CAMPOS OPCIONAIS
            // --------------------------------------------------
            //
            // Se o cliente deixou algum campo vazio,
            // armazenamos NULL em vez de string vazia.
            // --------------------------------------------------

            $condicoesRegras =
                trim((string) (
                    $dados['condicoes_regras'] ?? ''
                ));

            $identidadeVisual =
                trim((string) (
                    $dados['identidade_visual'] ?? ''
                ));

            $referenciasVisuais =
                trim((string) (
                    $dados['referencias_visuais'] ?? ''
                ));

            $observacoes =
                trim((string) (
                    $dados['observacoes'] ?? ''
                ));


            $sqlVersao = "
                INSERT INTO briefing_versoes (
                    briefing_id,
                    numero_versao,
                    objetivo,
                    publico_alvo,
                    funcionalidades,
                    condicoes_regras,
                    identidade_visual,
                    referencias_visuais,
                    observacoes,
                    criado_por,
                    status
                )
                VALUES (
                    ?,
                    1,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    'aprovada'
                )
            ";


            $stmtVersao = $this->pdo->prepare(
                $sqlVersao
            );


            $stmtVersao->execute([

                // Qual briefing recebe esta versão.
                $briefingId,


                // ------------------------------------------------
                // OBJETIVO
                // ------------------------------------------------
                //
                // Por enquanto usamos a descrição inicial como
                // objetivo do projeto.
                //
                // Futuramente podemos criar uma pergunta própria
                // para objetivo, se acharmos necessário.
                // ------------------------------------------------

                $dados['descricao_resumida'],


                // Público que utilizará o projeto.
                $dados['publico_alvo'],


                // O que essas pessoas precisam conseguir fazer.
                $dados['funcionalidades'],


                // Regras de negócio informadas pelo cliente.
                $condicoesRegras !== ''
                    ? $condicoesRegras
                    : null,


                // Preferências de aparência.
                $identidadeVisual !== ''
                    ? $identidadeVisual
                    : null,


                // Referências visuais.
                $referenciasVisuais !== ''
                    ? $referenciasVisuais
                    : null,


                // Observações adicionais.
                $observacoes !== ''
                    ? $observacoes
                    : null,


                // Usuário que criou esta versão.
                $usuarioId
            ]);


            // ==================================================
            // CONFIRMAR TRANSAÇÃO
            // ==================================================
            //
            // Se chegamos aqui, todas as inserções funcionaram.
            // ==================================================

            $this->pdo->commit();


            // Retornamos o ID do projeto criado.
            return $projetoId;


        } catch (Throwable $erro) {

            // ==================================================
            // DESFAZER TRANSAÇÃO
            // ==================================================
            //
            // Se qualquer INSERT falhar,
            // desfazemos as operações anteriores.
            // ==================================================

            if ($this->pdo->inTransaction()) {

                $this->pdo->rollBack();
            }


            // O erro continua sendo lançado para que
            // a página que chamou o Model possa tratá-lo.
            throw $erro;
        }
    }
}