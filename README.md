# SiteMatch — TCC

Plataforma web em PHP e MySQL que conecta clientes que precisam de projetos de software a desenvolvedores. O fluxo contempla publicação de projetos, perfis profissionais, portfólio, propostas, negociação por chat, contratação, acompanhamento, entrega, conclusão, avaliação e histórico.

## Tecnologias

- PHP 8+
- MySQL 8+
- HTML5
- CSS3
- JavaScript
- PDO com prepared statements

## Estrutura principal

```text
app/
  config/        conexão com banco
  controllers/   controllers da aplicação
  models/        regras e consultas
  views/         telas PHP
public/           raiz pública da aplicação
database/         schema, seeds e migrações
docs/             requisitos e regras de negócio
```

## 1. Banco de dados

O banco utilizado é:

```text
projeto_match
```

Para uma instalação nova, execute:

```text
database/schema.sql
database/seeds.sql
```

Se estiver usando o banco já existente do desenvolvimento anterior, execute também uma vez:

```text
database/migrations/20261106_alinhar_contratacoes.sql
```

Essa migração preserva os dados e alinha contratações antigas que ainda estavam com status `ativa` ao fluxo documentado de `em_andamento`.

## 2. Configuração local do banco

O sistema lê usuário e senha do MySQL em:

```text
app/config/database.local.php
```

Existe um modelo seguro em:

```text
app/config/database.local.example.php
```

O arquivo `database.local.php` está no `.gitignore` e não deve ser commitado.

## 3. Executar o sistema

No terminal, entre na pasta `public`:

```bash
cd public
php -S localhost:8000
```

Depois acesse:

```text
http://localhost:8000
```

A rota inicial redireciona automaticamente para o login ou para o painel quando já existe uma sessão.

Também é possível usar Apache, desde que `public/` seja servido como raiz pública ou as rotas sejam ajustadas de acordo com o ambiente.

## Funcionalidades implementadas

- RF01 — Cadastro de usuários
- RF02 — Login e logout
- RF03 — Visualização e edição de perfil
- RF04 — Perfil profissional do desenvolvedor e habilidades
- RF05 — CRUD de portfólio
- RF06 — Publicação de projetos e briefing
- RF07 — Visualização, edição controlada e acompanhamento de projetos
- RF08 — Pesquisa de projetos por filtros
- RF09 — Pesquisa e consulta de desenvolvedores
- RF10 — Envio de proposta
- RF11 — Aceite e recusa de propostas
- RF12 — Registro da contratação
- RF13 — Chat e histórico de mensagens
- RF14 — Acompanhamento da contratação
- RF15 — Avaliação após conclusão
- RF16 — Histórico

## Fluxo principal

```text
Cliente publica projeto
        ↓
Desenvolvedor pesquisa e envia proposta
        ↓
Cliente e desenvolvedor negociam pelo chat
        ↓
Desenvolvedor formaliza proposta definitiva
        ↓
Cliente aceita ou recusa
        ↓
Contratação em andamento
        ↓
Desenvolvedor sinaliza entrega
        ↓
Cliente confirma conclusão ou solicita ajustes
        ↓
Projeto concluído
        ↓
Cliente avalia o desenvolvedor
        ↓
Histórico permanece disponível
```

## Segurança e integridade

- senhas armazenadas com `password_hash()`;
- autenticação com `password_verify()`;
- controle de acesso por perfil;
- verificação de propriedade de projetos, propostas e contratações;
- consultas parametrizadas com PDO;
- transações para operações críticas;
- mensagens de erro sem exposição de credenciais do banco.

## Testes

Há um roteiro funcional em:

```text
docs/testes/roteiro-teste-funcional.md
```

## Interface visual

A interface utiliza um design system próprio em tons de roxo e branco, com:

- sidebar responsiva para cliente e desenvolvedor;
- cards, filtros, formulários e alertas padronizados;
- login e cadastro responsivos;
- chat adaptado para desktop e celular;
- estados visuais consistentes para projetos, propostas e contratações.

Os principais estilos ficam em `public/css/` e possuem comentários por seção para facilitar manutenção e apresentação do código.
