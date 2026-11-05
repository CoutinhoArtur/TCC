# LEVANTAMENTO DE REQUISITOS — SPRINT 1

## 1. REQUISITOS FUNCIONAIS

Os requisitos funcionais descrevem as funcionalidades que deverão ser disponibilizadas pelo sistema, considerando as necessidades dos usuários e os objetivos estabelecidos para o projeto.

A plataforma contará com dois tipos principais de usuários: cliente e desenvolvedor. Cada perfil terá acesso a funcionalidades específicas, de acordo com sua participação no processo de contratação.

### RF01 — Cadastro de usuários
O sistema deverá permitir que novos usuários realizem seu cadastro, informando nome, e-mail, senha e tipo de perfil.

**Perfis disponíveis:** cliente e desenvolvedor.

### RF02 — Autenticação de usuários
O sistema deverá permitir que usuários cadastrados realizem login e logout, acessando as funcionalidades correspondentes ao seu perfil.

### RF03 — Gerenciamento de perfil
O sistema deverá permitir que os usuários visualizem e editem suas informações cadastrais.

### RF04 — Cadastro de perfil profissional
O sistema deverá permitir que desenvolvedores cadastrem suas informações profissionais, incluindo descrição, habilidades, tecnologias utilizadas e experiência.

### RF05 — Gerenciamento de portfólio
O sistema deverá permitir que desenvolvedores cadastrem, editem e removam projetos de seus portfólios, apresentando trabalhos realizados anteriormente.

### RF06 — Publicação de projetos
O sistema deverá permitir que clientes publiquem demandas de desenvolvimento de software, informando título, descrição, categoria, funcionalidades desejadas, orçamento e prazo.

### RF07 — Gerenciamento de projetos
O sistema deverá permitir que clientes visualizem, editem e acompanhem os projetos publicados em suas contas.

### RF08 — Pesquisa de projetos
O sistema deverá permitir que desenvolvedores pesquisem projetos disponíveis na plataforma, utilizando filtros como categoria, orçamento e tecnologias desejadas.

### RF09 — Pesquisa de desenvolvedores
O sistema deverá permitir que clientes pesquisem desenvolvedores cadastrados e consultem seus perfis profissionais.

### RF10 — Envio de propostas
O sistema deverá permitir que desenvolvedores enviem propostas para projetos disponíveis, informando valor, prazo estimado e descrição do serviço oferecido.

### RF11 — Gerenciamento de propostas
O sistema deverá permitir que clientes visualizem as propostas recebidas e realizem sua aceitação ou recusa.

### RF12 — Registro de contratação
O sistema deverá registrar uma contratação quando o cliente aceitar uma proposta enviada por um desenvolvedor.

### RF13 — Comunicação entre usuários
O sistema deverá disponibilizar um chat para comunicação entre cliente e desenvolvedor envolvidos em uma contratação.

### RF14 — Acompanhamento de projetos
O sistema deverá permitir que os usuários envolvidos acompanhem o andamento de uma contratação, por meio da visualização de seu status.

### RF15 — Avaliação do desenvolvedor
O sistema deverá permitir que o cliente avalie o desenvolvedor após a conclusão de um projeto, atribuindo uma nota e registrando um comentário.

### RF16 — Visualização de histórico
O sistema deverá permitir que os usuários consultem seus históricos de projetos e contratações realizados na plataforma.


# LEVANTAMENTO DE REQUISITOS — SPRINT 1

## 2. REQUISITOS NÃO FUNCIONAIS

Os requisitos não funcionais estabelecem características técnicas e critérios de qualidade que deverão ser considerados durante o desenvolvimento da plataforma.

Esses requisitos estão relacionados à segurança, usabilidade, desempenho, compatibilidade, manutenção e integridade dos dados.

### RNF01 — Responsividade

A plataforma deverá apresentar uma interface responsiva, adaptando-se a computadores, tablets e smartphones.

**Critério de verificação:** as principais telas deverão funcionar nas larguras de referência de 375 px, 768 px e 1440 px, sem perda de funcionalidades ou sobreposição indevida de elementos.

### RNF02 — Segurança das senhas

As senhas dos usuários deverão ser armazenadas utilizando mecanismos seguros de hash, disponibilizados pela linguagem PHP.

**Critério de verificação:** nenhuma senha deverá ser armazenada em texto simples no banco de dados.

### RNF03 — Controle de acesso

O sistema deverá restringir o acesso às funcionalidades de acordo com o tipo de usuário autenticado.

**Critério de verificação:** usuários não autenticados ou sem a permissão correspondente não poderão executar operações restritas, inclusive por acesso direto às URLs.

### RNF04 — Validação de dados

Os dados recebidos por formulários deverão ser validados no servidor antes de serem processados ou armazenados.

**Critério de verificação:** informações obrigatórias ausentes, inválidas ou incompatíveis com os formatos definidos deverão ser rejeitadas.

### RNF05 — Proteção das consultas ao banco de dados

As consultas que utilizarem dados fornecidos pelos usuários deverão empregar instruções preparadas, evitando a concatenação direta desses valores nas instruções SQL.

**Critério de verificação:** as operações de acesso ao banco deverão utilizar consultas parametrizadas por meio do PDO.

### RNF06 — Compatibilidade com navegadores

A plataforma deverá funcionar nas versões estáveis atuais dos navegadores Google Chrome, Microsoft Edge e Mozilla Firefox.

**Critério de verificação:** as funcionalidades principais deverão ser testadas nesses três navegadores.

### RNF07 — Usabilidade

A interface deverá manter consistência visual, nomenclaturas padronizadas e navegação compreensível.

**Critério de verificação:** os usuários deverão conseguir localizar as funcionalidades principais por meio dos menus e elementos de navegação previstos no protótipo.

### RNF08 — Desempenho

O sistema deverá apresentar tempo de resposta adequado durante a utilização de suas funcionalidades principais.

**Critério de verificação:** em ambiente local de testes, com até 100 registros por listagem e cinco usuários simultâneos simulados, as páginas principais deverão apresentar tempo de resposta do servidor de até três segundos em pelo menos 95% das requisições, desconsiderando o carregamento de recursos externos.

### RNF09 — Integridade dos dados

O banco de dados deverá utilizar chaves primárias, chaves estrangeiras e restrições para preservar a consistência das informações armazenadas.

**Critério de verificação:** o sistema não deverá permitir registros que violem os relacionamentos e as restrições estabelecidas na modelagem.

### RNF10 — Manutenibilidade

O código deverá ser organizado em módulos, mantendo separação entre configurações, regras de negócio, acesso aos dados e apresentação visual.

**Critério de verificação:** os arquivos deverão seguir a estrutura de diretórios e os padrões de nomenclatura definidos pela equipe.

### RNF11 — Versionamento

O código-fonte deverá ser mantido em um repositório GitHub compartilhado entre os integrantes.

**Critério de verificação:** as alterações deverão possuir histórico de commits, permitindo identificar modificações realizadas durante o desenvolvimento.

### RNF12 — Acessibilidade

A interface deverá considerar boas práticas de acessibilidade digital, incluindo identificação dos campos de formulário, navegação por teclado, contraste adequado e apresentação textual de informações relevantes.

**Critério de verificação:** as principais telas deverão ser verificadas por navegação utilizando teclado e inspeção dos elementos de formulário e contraste.

### RNF13 — Tratamento de erros

O sistema deverá apresentar mensagens compreensíveis quando ocorrerem falhas durante a execução das funcionalidades, evitando a exposição de detalhes técnicos sensíveis.

**Critério de verificação:** erros de conexão, validação e operações não autorizadas deverão apresentar respostas adequadas ao usuário, sem divulgar credenciais ou informações internas do servidor.

### RNF14 — Privacidade dos dados

A plataforma deverá limitar a exposição das informações pessoais cadastradas, disponibilizando os dados conforme a finalidade de cada funcionalidade.

**Critério de verificação:** informações privadas, como senhas e dados de autenticação, não deverão ser exibidas em perfis públicos, respostas indevidas ou mensagens de erro.

### RNF15 — Tecnologias e ambiente de execução

O sistema deverá ser desenvolvido utilizando HTML5, CSS3, JavaScript e PHP, com armazenamento de dados em MySQL.

**Critério de verificação:** a aplicação deverá executar em ambiente local configurado para PHP e MySQL, sem dependência obrigatória do pacote XAMPP.