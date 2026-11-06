<?php

// ==========================================================
// MODEL: USUÁRIO
// ==========================================================
// Este arquivo concentra operações relacionadas aos usuários.
//
// Responsabilidades iniciais:
//
// - verificar se um e-mail já está cadastrado;
// - criar um novo usuário;
// - criar automaticamente seu perfil de cliente
//   ou desenvolvedor.
//
// RF01 - Cadastro de usuários
// RN01 - Cada e-mail pode pertencer a apenas uma conta
// RN02 - Usuário deve escolher cliente ou desenvolvedor
// RNF02 - Senhas devem ser armazenadas com hash
// RNF05 - Consultas devem utilizar prepared statements
// ==========================================================

class Usuario
{
    // Guarda a conexão PDO recebida no construtor.
    private PDO $pdo;


    // ------------------------------------------------------
    // CONSTRUTOR
    // ------------------------------------------------------
    // Sempre que criarmos:
    //
    // $usuarioModel = new Usuario($pdo);
    //
    // a conexão com o banco ficará disponível
    // dentro desta classe.
    // ------------------------------------------------------

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    // ------------------------------------------------------
    // VERIFICAR SE O E-MAIL JÁ EXISTE
    // ------------------------------------------------------
    // RN01:
    // um mesmo e-mail não pode possuir duas contas.
    // ------------------------------------------------------

    public function emailExiste(string $email): bool
    {
        // Utilizamos prepared statement para não inserir
        // diretamente o valor recebido dentro do SQL.
        $sql = "
            SELECT id
            FROM usuarios
            WHERE email = ?
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([$email]);

        // Se fetch() encontrar algum registro,
        // significa que o e-mail já existe.
        return $stmt->fetch() !== false;
    }


    // ------------------------------------------------------
    // CADASTRAR NOVO USUÁRIO
    // ------------------------------------------------------
    // Esta função cria:
    //
    // 1. registro em usuarios;
    // 2. perfil em clientes OU desenvolvedores.
    //
    // As duas operações fazem parte de uma transação.
    //
    // Se uma delas falhar, nenhuma será mantida.
    // ------------------------------------------------------

    public function cadastrar(
        string $nome,
        string $email,
        string $senha,
        string $tipo
    ): int {

        try {

            // Começamos uma transação.
            //
            // Isso garante que o cadastro principal
            // e o perfil específico sejam criados juntos.
            $this->pdo->beginTransaction();


            // --------------------------------------------------
            // PROTEGER A SENHA
            // --------------------------------------------------
            // Nunca salvamos a senha original no banco.
            //
            // password_hash() gera uma representação segura
            // da senha para armazenamento.
            // --------------------------------------------------

            $senhaHash = password_hash(
                $senha,
                PASSWORD_DEFAULT
            );


            // --------------------------------------------------
            // CRIAR CONTA PRINCIPAL
            // --------------------------------------------------

            $sql = "
                INSERT INTO usuarios
                    (nome, email, senha_hash, tipo)
                VALUES
                    (?, ?, ?, ?)
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                $nome,
                $email,
                $senhaHash,
                $tipo
            ]);


            // Recuperamos o ID gerado automaticamente
            // pelo AUTO_INCREMENT.
            $usuarioId = (int) $this->pdo->lastInsertId();


            // --------------------------------------------------
            // CRIAR PERFIL ESPECÍFICO
            // --------------------------------------------------
            // RN02:
            // cada usuário possui inicialmente apenas
            // um tipo de perfil.
            // --------------------------------------------------

            if ($tipo === 'cliente') {

                $sqlPerfil = "
                    INSERT INTO clientes (usuario_id)
                    VALUES (?)
                ";

            } else {

                $sqlPerfil = "
                    INSERT INTO desenvolvedores (usuario_id)
                    VALUES (?)
                ";
            }


            $stmtPerfil = $this->pdo->prepare($sqlPerfil);

            $stmtPerfil->execute([
                $usuarioId
            ]);


            // Se chegamos até aqui sem erro,
            // confirmamos definitivamente as alterações.
            $this->pdo->commit();


            // Retornamos o ID do novo usuário.
            return $usuarioId;


        } catch (Throwable $erro) {

            // Se alguma operação falhar,
            // desfazemos todas as alterações realizadas
            // dentro da transação.
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }


            // Relançamos o erro para que o controller
            // possa decidir como tratar a situação.
            throw $erro;
        }
    }


    // ==========================================================
// BUSCAR USUÁRIO PELO E-MAIL
// ==========================================================
// Utilizado principalmente durante o login.
//
// RF02 - Autenticação de usuários
//
// Retorna:
// - os dados do usuário, caso seja encontrado;
// - false, caso não exista.
// ==========================================================

public function buscarPorEmail(string $email): array|false
{
    // ------------------------------------------------------
    // CONSULTA SEGURA COM PREPARED STATEMENT
    // ------------------------------------------------------

    $sql = "
        SELECT
            id,
            nome,
            email,
            senha_hash,
            tipo,
            status
        FROM usuarios
        WHERE email = ?
        LIMIT 1
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        $email
    ]);


    // fetch() retornará:
    //
    // array → se encontrou o usuário
    // false → se não encontrou
    return $stmt->fetch();
}

}