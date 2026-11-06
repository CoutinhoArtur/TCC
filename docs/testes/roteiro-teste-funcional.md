# Roteiro de teste funcional — SiteMatch

Este roteiro cobre o fluxo principal descrito nos requisitos RF01–RF16 e nas regras RN01–RN21.

## Preparação

1. Banco `projeto_match` criado e atualizado.
2. `app/config/database.local.php` configurado.
3. Servidor iniciado dentro de `public/` com `php -S localhost:8000`.
4. Ter uma conta cliente e uma conta desenvolvedor válidas.

## Fluxo 1 — Perfil e portfólio

### Cliente
1. Entrar como cliente.
2. Abrir **Meu perfil**.
3. Alterar um dado cadastral e salvar.
4. Confirmar que o dado continua atualizado após recarregar.

### Desenvolvedor
1. Entrar como desenvolvedor.
2. Abrir **Meu perfil**.
3. Preencher título profissional, bio, experiência, disponibilidade e faixa de preço.
4. Adicionar pelo menos duas habilidades com níveis diferentes.
5. Salvar e recarregar.
6. Abrir **Meu portfólio**.
7. Criar um trabalho, editar e confirmar a alteração.

## Fluxo 2 — Projeto e pesquisa

1. Como cliente, publicar um novo projeto com briefing completo.
2. Conferir o projeto em **Meus projetos**.
3. Antes de receber propostas, testar a edição do projeto.
4. Como desenvolvedor, abrir **Explorar projetos**.
5. Testar filtros por categoria, orçamento e palavra/tecnologia.
6. Abrir os detalhes do projeto.

## Fluxo 3 — Proposta e negociação

1. Como desenvolvedor, enviar uma proposta preliminar.
2. Confirmar que não é possível enviar uma segunda proposta para o mesmo projeto.
3. Abrir o chat e enviar uma mensagem.
4. Como cliente, abrir a conversa e responder.
5. Como desenvolvedor, transformar a proposta em definitiva.
6. Conferir valor, prazo e descrição finais.

## Fluxo 4 — Aceite e contratação

1. Como cliente, abrir as propostas recebidas.
2. Conferir a proposta definitiva.
3. Testar **Recusar** em um cenário de teste separado ou aceitar a proposta escolhida.
4. Ao aceitar, confirmar que:
   - proposta fica `aceita`;
   - outras propostas pendentes do mesmo projeto ficam `encerrada`;
   - contratação é criada;
   - contratação fica `em_andamento`;
   - projeto deixa de aceitar novas propostas.
5. Abrir **Contratações** / **Acompanhar contratação**.

## Fluxo 5 — Entrega, ajustes e conclusão

1. Como desenvolvedor, abrir a contratação e clicar em **Sinalizar entrega**.
2. Confirmar status `Aguardando confirmação`.
3. Como cliente, testar **Solicitar ajustes** informando uma descrição.
4. Confirmar que a contratação volta para `Em andamento` e que a solicitação aparece no chat.
5. Como desenvolvedor, sinalizar a entrega novamente.
6. Como cliente, clicar em **Confirmar conclusão**.
7. Confirmar contratação `Concluída` e projeto `Concluído`.

## Fluxo 6 — Avaliação

1. Com contratação concluída, abrir a contratação como cliente.
2. Registrar nota de 1 a 5 e comentário.
3. Confirmar que a avaliação aparece na contratação e no perfil público do desenvolvedor.
4. Confirmar que o sistema não oferece uma segunda avaliação para a mesma contratação.

## Fluxo 7 — Pesquisa de desenvolvedores e histórico

1. Como cliente, abrir **Desenvolvedores**.
2. Pesquisar por nome/habilidade e abrir um perfil.
3. Conferir habilidades, portfólio e avaliações.
4. Abrir **Histórico** como cliente.
5. Abrir **Histórico** como desenvolvedor.
6. Confirmar que registros concluídos/encerrados permanecem consultáveis.

## Controle de acesso

Testar pelo menos:

- cliente tentando abrir URL exclusiva de desenvolvedor;
- desenvolvedor tentando abrir URL exclusiva de cliente;
- usuário tentando acessar contratação da qual não participa;
- desenvolvedor tentando alterar proposta de outro desenvolvedor;
- cliente tentando gerenciar projeto de outro cliente.

O sistema deve bloquear essas operações mesmo quando a URL é digitada diretamente.
