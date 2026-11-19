# SiteMatch V7 — Guia de demonstração para o TCC

## O que mudou visualmente

- Capas de projetos selecionadas por palavras-chave do título, categoria e descrição (hamburgueria, moda, pet shop, escola, portfólio, sistema web, app, loja virtual e site institucional).
- Capas nas páginas **Explorar projetos**, **Meus projetos**, **Minhas propostas**, nos dois dashboards, no **Detalhe do projeto** e nos cartões do portfólio (privado e público).
- Avatares ilustrativos na sidebar, na pesquisa de profissionais, na página de perfil, no perfil público e no chat.
- As imagens ficam no próprio projeto: `public/img/placeholders/`. Não dependem de serviços externos ou de um banco de imagens remoto.
- As imagens são ilustrações genéricas, **não são fotografias enviadas pelos usuários**. O código não cria nem armazena fotos de perfil reais.

## Preparação dos dados

1. Faça backup do seu MySQL antes de importar qualquer script.
2. Em banco de testes já criado com `database/schema.sql`, execute `database/demo_artur_isabelli.sql`.
3. **Não execute `database/schema.sql` por cima do seu banco atual sem revisar!** Para um banco novo, crie primeiro o schema e depois o script demo.
4. O script usa a base `projeto_match`, duas contas demo e títulos fixos; ele tenta evitar duplicações. Não remove registros, não altera tabelas e não troca senhas de contas existentes.
5. Faça login e navegue pelas contas demonstrativas. Se sua aplicação usa outro nome de banco, ajuste apenas a linha `USE projeto_match;` em uma cópia do arquivo demo.

### Contas de demonstração

| Perfil | E-mail | Senha |
|---|---|---|
| Desenvolvedor — Artur | `artur.demo@sitematch.test` | `SiteMatch@2026!` |
| Cliente — Isabelli | `isabelli.demo@sitematch.test` | `SiteMatch@2026!` |

**Atenção:** credenciais de laboratório, nunca para produção ou sistemas expostos à internet. O SQL já armazena um hash BCrypt compatível com `password_verify()` do PHP, não a senha em texto claro.

### Cenários cadastrados

| Projeto | Estado do projeto | Estado da proposta / contratação | Demonstração |
|---|---|---|---|
| Site para hamburgueria | Aberto | Sem proposta | Explorar / enviar nova proposta |
| Loja virtual de roupas | Em negociação | Proposta em negociação | Chat / negociação |
| Portfolio pessoal | Aberto | Proposta preliminar | Revisar proposta / chat |
| Agendamento para pet shop | Em andamento | Contratação em andamento | Acompanhamento da execução |
| Sistema de agenda para salao | Aguardando confirmação | Contratação aguardando confirmação | Cliente analisa entrega |
| Site institucional para escola | Em andamento | Ajustes solicitados | Fluxo de solicitação de ajustes |
| Landing page de confeitaria | Concluído | Concluída + avaliação 5/5 | Histórico / avaliação |
| Loja de artigos esportivos | Em negociação | Proposta definitiva | Cliente decide sobre a proposta |

O script também inclui **habilidades**, **portfólio com cinco projetos**, **briefings aprovados** e **mensagens simuladas**.

> Esses cenários representam estados de demonstração persistidos em MySQL; eles **não substituem os testes manuais dos fluxos completos**. Para testar de ponta a ponta, publique um projeto novo por meio da interface com as duas contas.

## O que pode parecer pouco claro na interface atual

1. **Status técnicos**: alguns estados como `em_negociacao`, `aguardando_revisao` e `ajustes_solicitados` merecem descrições de linguagem simples nas telas relevantes.
2. **Proposta preliminar vs. definitiva**: explicitar que a primeira abre a negociação e a segunda permite a decisão do cliente.
3. **Botões dependentes de etapa**: incluir textos curtos de orientação, por exemplo "Próximo passo: aguardar aprovação do cliente" (somente quando aplicável).
4. **Etapas da contratação**: uma linha do tempo visual (aceite → execução → entrega → ajustes → conclusão) tornaria o processo mais legível.
5. **Avisos e recuperação de senha**: não apresentar ícones ou links como funções disponíveis quando a integração correspondente não existir.
6. **Imagem do projeto e foto real do usuário**: hoje o sistema usa imagens automáticas, sem upload. Upload persistente exigiria aprovação de mudanças no servidor e possivelmente no schema.

## Verificação recomendada antes da banca

- Navegar nas duas contas demo e revisar os status exibidos em cada tela.
- Abrir projeto e conferir proposta, chat e contratação nos casos aplicáveis.
- Conferir visual em 1440 px, 768 px e 375 px.
- Testar um projeto novo do começo ao fim com banco local.
- Confirmar que `app/config/database.local.php` não acompanha o ZIP compartilhado.
