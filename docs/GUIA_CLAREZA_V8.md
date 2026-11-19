# SiteMatch V8 — Orientações de uso e fluxo

## O que esta versão resolve

Esta revisão prioriza **clareza**. Não acrescenta novas regras de negócio e não exige novas tabelas nem importação de SQL.

- Na tela **Propostas recebidas**, a Isabelli vê a diferença entre proposta preliminar, negociação e proposta definitiva. A aceitação só aparece quando já era permitida pelo back-end.
- Em **Minhas propostas**, o Artur vê as próximas ações e entende quando deve enviar uma proposta definitiva ou aguardar o cliente.
- Na página **Acompanhamento**, uma linha do tempo explica as etapas: aceite → desenvolvimento → entrega/revisão → conclusão. O texto muda conforme o perfil e status já existentes.
- Em **Contratações** e **Meus projetos**, pequenas mensagens apresentam o próximo passo.
- No **Histórico**, os status aparecem em linguagem legível em vez dos códigos técnicos.

As imagens da V7 e todas as funcionalidades existentes foram mantidas. Os formulários, nomes de campos, métodos GET/POST, rotas, Models, controllers e SQL não foram modificados.

## Como usar, já que o SQL de demonstração foi importado

1. **Não execute novamente nenhum SQL**. Mantenha o mesmo MySQL que já funcionou.
2. Extraia a V8 numa pasta separada. O conteúdo do projeto fica na pasta `TCC`.
3. Copie seu `app/config/database.local.php` da sua instalação atual para `TCC/app/config/database.local.php`, se necessário. O arquivo não vem no ZIP por conter credenciais locais.
4. Inicie a aplicação com o comando que você já utilizava (pelo servidor PHP local ou Apache) e entre com as contas demo de Artur e Isabelli.
5. Faça os testes abaixo e observe as orientações nas telas.

## Teste recomendado para a banca

**Artur (desenvolvedor):**

1. Entre em Explorar projetos e abra o projeto de hamburgueria.
2. Envie uma proposta preliminar, ou consulte uma das propostas de demonstração já existentes.
3. Use Minhas propostas para entender quando finalizar a proposta.
4. Em uma contratação ativa, clique em Iniciar projeto; em andamento, clique em Sinalizar entrega.
5. Se o cliente pedir ajustes, leia a solicitação na tela de acompanhamento e sinalize uma nova entrega.

**Isabelli (cliente):**

1. Entre em Meus projetos e veja as propostas.
2. Confira a orientação que explica por que só propostas definitivas podem ser aceitas.
3. Abra Contratações e acompanhe a linha do tempo de um projeto.
4. Diante de uma entrega, escolha entre Confirmar conclusão ou Solicitar ajustes.
5. Quando concluir, avalie o desenvolvedor e consulte o Histórico.

## Limites da validação técnica

A sintaxe dos arquivos PHP e JavaScript foi verificada; as alterações foram comparadas com a V7 para garantir que back-end e SQL permaneceram idênticos. **O acesso ao seu MySQL local e a homologação real dos dois usuários não podem ser simulados neste ambiente.** Portanto, execute os testes acima antes da banca.

## Possíveis evoluções futuras (não implementadas)

- Foto de perfil com upload persistente.
- Notificações internas ao mudar uma etapa.
- Indicador real de novos eventos no painel.

Essas funções exigiriam uma implementação de back-end específica; não são necessárias para a clareza básica da demonstração atual.
