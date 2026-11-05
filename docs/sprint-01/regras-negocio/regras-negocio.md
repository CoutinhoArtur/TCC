# REGRAS DE NEGÓCIO — SPRINT 1

**Projeto:** SiteMatch (nome provisório)  
**Versão:** 1.0 — Proposta para validação  
**Curso:** Técnico em Desenvolvimento de Sistemas — SENAI

## 1. INTRODUÇÃO

As regras de negócio estabelecem as condições, restrições e critérios que deverão ser respeitados durante a utilização da plataforma.

Essas regras têm como finalidade garantir a organização dos processos, a integridade das informações e o funcionamento adequado das operações realizadas pelos clientes e desenvolvedores.

As regras serão consideradas durante a modelagem do banco de dados, implementação das funcionalidades e execução dos testes do sistema.

## 2. REGRAS DE NEGÓCIO

### 2.1 Cadastro e autenticação

**RN01 — Unicidade do e-mail**

Cada endereço de e-mail poderá estar associado a apenas uma conta cadastrada na plataforma.

**RN02 — Definição do perfil**

Durante o cadastro, o usuário deverá selecionar um dos dois perfis disponíveis: cliente ou desenvolvedor.

Na primeira versão da plataforma, cada conta estará vinculada a apenas um tipo de perfil.

**RN03 — Acesso às funcionalidades**

As funcionalidades restritas deverão ser disponibilizadas conforme o perfil do usuário autenticado.

Clientes e desenvolvedores possuirão permissões diferentes, de acordo com suas atividades dentro da plataforma.

### 2.2 Publicação e gerenciamento de projetos

**RN04 — Permissão para publicação**

Somente usuários cadastrados como clientes poderão publicar projetos na plataforma.

**RN05 — Informações obrigatórias do projeto**

Para publicar um projeto, o cliente deverá informar, obrigatoriamente:

- Título;
- Descrição;
- Categoria do software;
- Funcionalidades desejadas;
- Orçamento disponível;
- Prazo desejado.

**RN06 — Propriedade do projeto**

Somente o cliente responsável pela publicação poderá editar ou gerenciar seu projeto.

**RN07 — Categorias de projetos**

Cada projeto deverá estar vinculado a uma categoria, entre as opções inicialmente disponibilizadas:

- Desenvolvimento de sites;
- Sistemas web;
- Aplicativos mobile;
- Sistemas desktop;
- Outros projetos de desenvolvimento de software.

### 2.3 Propostas

**RN08 — Permissão para envio de propostas**

Somente usuários cadastrados como desenvolvedores poderão enviar propostas para os projetos publicados.

**RN09 — Limite de propostas**

Um desenvolvedor poderá manter apenas uma proposta ativa por projeto.

**RN10 — Informações obrigatórias da proposta**

Toda proposta deverá conter:

- Valor oferecido;
- Prazo estimado;
- Descrição do serviço proposto.

**RN11 — Gerenciamento das propostas**

Somente o cliente responsável pelo projeto poderá aceitar ou recusar as propostas recebidas.

**RN12 — Aceitação de propostas**

Cada projeto poderá possuir apenas uma proposta aceita.

Ao aceitar uma proposta, o sistema deverá registrar a contratação, atualizar o status do projeto e encerrar as demais propostas pendentes.

Após a contratação, o projeto não poderá receber novas propostas.

### 2.4 Contratação e acompanhamento

**RN13 — Registro da contratação**

Uma contratação somente poderá ser registrada após a aceitação de uma proposta válida pelo cliente responsável pelo projeto.

**RN14 — Status da contratação**

As contratações deverão apresentar um dos seguintes status:

- Em andamento;
- Concluída;
- Cancelada.

O status inicial após a aceitação da proposta será "Em andamento".


## 2.5 Comunicação e negociação

### RN15 — Disponibilidade do chat

O chat deverá ser disponibilizado após o envio de uma proposta válida pelo desenvolvedor, independentemente de sua aceitação pelo cliente.

A comunicação será restrita ao cliente responsável pelo projeto e ao desenvolvedor que enviou a proposta.

O objetivo será permitir que os envolvidos esclareçam dúvidas, discutam funcionalidades, negociem valores, alinhem prazos e estabeleçam as condições para uma possível contratação.

### RN16 — Histórico de mensagens

Cada conversa deverá estar vinculada à proposta que deu origem ao contato.

As mensagens deverão permanecer registradas, possibilitando que os participantes consultem o histórico da negociação e, caso a proposta seja aceita, continuem utilizando a mesma conversa durante a execução do projeto.

As conversas não poderão ser visualizadas por usuários que não participem da respectiva negociação.

### RN17 — Formalização da negociação

Antes da aceitação de uma proposta, os envolvidos poderão discutir suas condições por meio do chat.

Caso sejam negociados novos valores, prazos ou alterações no serviço inicialmente apresentado, o desenvolvedor deverá atualizar formalmente a proposta.

O cliente deverá visualizar as condições atualizadas antes de realizar a aceitação.

A contratação será registrada somente após a confirmação explícita do cliente, considerando a versão da proposta aceita.

## 2.6 Entrega e conclusão do projeto

### RN18 — Sinalização de entrega

Após realizar o serviço contratado, o desenvolvedor poderá sinalizar que o projeto foi entregue.

Essa ação deverá alterar o status da contratação para "Aguardando confirmação".

A sinalização de entrega não representará a conclusão automática da contratação.

### RN19 — Confirmação da conclusão

Somente o cliente responsável pela contratação poderá confirmar a conclusão do projeto.

Após essa confirmação, a contratação deverá receber o status "Concluída".

Caso identifique pendências, o cliente poderá registrar sua solicitação de ajustes, retornando a contratação ao status "Em andamento".

## 2.7 Avaliações

### RN20 — Condição para avaliação

O cliente somente poderá avaliar o desenvolvedor após a confirmação da conclusão da contratação.

A avaliação não deverá ser disponibilizada enquanto a contratação estiver em andamento, aguardando confirmação ou cancelada.

### RN21 — Limite de avaliações

Cada contratação poderá receber apenas uma avaliação do cliente, contendo uma nota de 1 a 5 e um comentário.

A avaliação deverá permanecer vinculada ao desenvolvedor e à contratação correspondente.

## 3. CONSIDERAÇÕES

O fluxo principal da plataforma deverá valorizar a negociação entre cliente e desenvolvedor, permitindo a comunicação antes e depois da formalização da contratação.

A aceitação de uma proposta representará a confirmação das condições acordadas entre os envolvidos, incluindo o valor, o prazo e a descrição do serviço.

A conclusão do projeto dependerá da sinalização de entrega pelo desenvolvedor e da posterior confirmação pelo cliente.

Essas condições deverão ser consideradas na implementação das regras de negócio, na estruturação do banco de dados e nos testes funcionais do sistema.