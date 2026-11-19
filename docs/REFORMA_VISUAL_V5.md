# SiteMatch — Reforma visual V5 (TCC SENAI)

## Objetivo
Melhorar a apresentação visual da versão funcional V4, sem alterar banco, SQL, PHP de processamento, Models, Controllers, rotas ou contratos de formulários.

## Alterações visuais
- `public/css/polish.css`: acabamento compartilhado, tipografia, hierarquia, cores, bordas, botões, dashboard, formulários, filtros, cards, perfil público, projetos e chat.
- Todas as views passam a carregar `polish.css` após seus estilos originais; a V4 permanece como base.
- Landing: imagem ilustrativa derivada de **recorte da própria imagem de protótipo enviada** (`public/img/site-match-hero-referencia.webp`).
- Capturas originais do Figma recebidas do usuário em `docs/prototipos-figma/`, somente como referência; **não** representam projetos reais cadastrados no MySQL.
- Chat mobile: navegação de retorno à lista de conversas.
- Perfil público: remoção do botão “Salvar contato” que não executava uma função real.

## JavaScript (`public/js/ui.js`)
- Menu mobile com sobreposição, fechamento fora/ESC e estado aria-expanded.
- Filtros instantâneos somente nos elementos já carregados, sem interferir nas pesquisas do back-end.
- Abas com ARIA e navegação por teclado.
- Textarea autoajustável e rolagem automática da conversa.
- Confirmações acessíveis e feedback de submissão sem bloquear botões antes de confirmar.

## Restrições respeitadas
**Nenhuma alteração** em `app/models`, `app/controllers`, `app/config`, `database`, `sql.sql`, `public/cliente`, `public/desenvolvedor` ou processamento PHP. Nenhum framework acrescentado. Nenhuma credencial distribuída.

## Validação disponível no ambiente
- Verificação de sintaxe `php -l` em todos os arquivos PHP.
- `node --check` em todos os arquivos JS.
- Comparação byte a byte entre todos os arquivos protegidos e os do ZIP original.
- Resposta HTTP 200 da página pública em servidor PHP local.
- Conferência de existência dos novos recursos estáticos.

## Limites de homologação
Sem a instância MySQL e as contas de teste locais, não foi possível executar login, persistência e o fluxo de contratação ponta a ponta. A tentativa de captura automática com navegador headless não concluiu nesta sessão. Faça o teste final em navegador nas larguras 1440, 768 e 375 px, com contas de cliente e desenvolvedor, antes da apresentação.

## Uso
Extraia a pasta `TCC` em outro diretório e compare antes de substituir sua versão atual. Preserve sua configuração local `app/config/database.local.php` na própria máquina. Não foi incluída no ZIP.
