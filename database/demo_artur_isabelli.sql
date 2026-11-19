-- ================================================================
-- SITEMATCH | AMBIENTE DE DEMONSTRACAO DO TCC
-- Cliente: Isabelli | Desenvolvedor: Artur
-- ================================================================
-- COMPATIVEL COM database/schema.sql (sem ALTER TABLE, sem DELETE).
-- Executar no banco local de TESTES, apos criar o schema.
-- Reexecutavel: usa emails e titulos fixos e evita duplicar registros.
-- IMPORTANTE: as contas abaixo sao EXCLUSIVAS para demonstracao.
-- Nao substitui nem altera contas preexistentes com outros emails.
-- Senha das contas demo: SiteMatch@2026! (hash PHP password_hash).
-- Nunca usar estas credenciais em producao.
-- ================================================================
USE projeto_match;
START TRANSACTION;

INSERT INTO usuarios (nome,email,senha_hash,tipo,status)
SELECT 'Artur Coutinho Teixeira','artur.demo@sitematch.test','$2y$12$PXJXWaV6XuW7s9sBB.RGxO8.uEPyjEw0dP3rOuIOevjUVcX5YvyX2','desenvolvedor','ativo'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email='artur.demo@sitematch.test');
INSERT INTO usuarios (nome,email,senha_hash,tipo,status)
SELECT 'Isabelli Queiroz','isabelli.demo@sitematch.test','$2y$12$PXJXWaV6XuW7s9sBB.RGxO8.uEPyjEw0dP3rOuIOevjUVcX5YvyX2','cliente','ativo'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email='isabelli.demo@sitematch.test');
SET @artur_user=(SELECT id FROM usuarios WHERE email='artur.demo@sitematch.test' AND tipo='desenvolvedor');
SET @isa_user=(SELECT id FROM usuarios WHERE email='isabelli.demo@sitematch.test' AND tipo='cliente');
INSERT INTO desenvolvedores (usuario_id,titulo_profissional,bio,experiencia,disponibilidade,faixa_preco_min,faixa_preco_max)
SELECT @artur_user,'Desenvolvedor Web Full Stack',
'Crio sites modernos e sistemas web para pequenos negocios. Trabalho com PHP, MySQL, HTML, CSS e JavaScript.',
'Desenvolvimento de sites institucionais, landing pages, sistemas administrativos, integracao de formulários e bancos relacionais.',
'Disponivel para novos projetos',800.00,6000.00
WHERE @artur_user IS NOT NULL AND NOT EXISTS (SELECT 1 FROM desenvolvedores WHERE usuario_id=@artur_user);
INSERT INTO clientes (usuario_id,nome_empresa,descricao,telefone,cidade)
SELECT @isa_user,'Ideias da Isabelli','Empreendedora buscando transformar negocios locais com boas experiencias digitais.',NULL,'Santo Andre - SP'
WHERE @isa_user IS NOT NULL AND NOT EXISTS (SELECT 1 FROM clientes WHERE usuario_id=@isa_user);
SET @dev=(SELECT id FROM desenvolvedores WHERE usuario_id=@artur_user);
SET @cli=(SELECT id FROM clientes WHERE usuario_id=@isa_user);

-- Habilidades e niveis profissionais (RF03)
INSERT INTO habilidades (nome,status) SELECT 'HTML','ativa' WHERE NOT EXISTS (SELECT 1 FROM habilidades WHERE nome='HTML');
INSERT INTO desenvolvedor_habilidades (desenvolvedor_id,habilidade_id,nivel) SELECT @dev,h.id,'avancado' FROM habilidades h WHERE h.nome='HTML' AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM desenvolvedor_habilidades dh WHERE dh.desenvolvedor_id=@dev AND dh.habilidade_id=h.id);
INSERT INTO habilidades (nome,status) SELECT 'CSS','ativa' WHERE NOT EXISTS (SELECT 1 FROM habilidades WHERE nome='CSS');
INSERT INTO desenvolvedor_habilidades (desenvolvedor_id,habilidade_id,nivel) SELECT @dev,h.id,'avancado' FROM habilidades h WHERE h.nome='CSS' AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM desenvolvedor_habilidades dh WHERE dh.desenvolvedor_id=@dev AND dh.habilidade_id=h.id);
INSERT INTO habilidades (nome,status) SELECT 'JavaScript','ativa' WHERE NOT EXISTS (SELECT 1 FROM habilidades WHERE nome='JavaScript');
INSERT INTO desenvolvedor_habilidades (desenvolvedor_id,habilidade_id,nivel) SELECT @dev,h.id,'intermediario' FROM habilidades h WHERE h.nome='JavaScript' AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM desenvolvedor_habilidades dh WHERE dh.desenvolvedor_id=@dev AND dh.habilidade_id=h.id);
INSERT INTO habilidades (nome,status) SELECT 'PHP','ativa' WHERE NOT EXISTS (SELECT 1 FROM habilidades WHERE nome='PHP');
INSERT INTO desenvolvedor_habilidades (desenvolvedor_id,habilidade_id,nivel) SELECT @dev,h.id,'avancado' FROM habilidades h WHERE h.nome='PHP' AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM desenvolvedor_habilidades dh WHERE dh.desenvolvedor_id=@dev AND dh.habilidade_id=h.id);
INSERT INTO habilidades (nome,status) SELECT 'MySQL','ativa' WHERE NOT EXISTS (SELECT 1 FROM habilidades WHERE nome='MySQL');
INSERT INTO desenvolvedor_habilidades (desenvolvedor_id,habilidade_id,nivel) SELECT @dev,h.id,'avancado' FROM habilidades h WHERE h.nome='MySQL' AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM desenvolvedor_habilidades dh WHERE dh.desenvolvedor_id=@dev AND dh.habilidade_id=h.id);
INSERT INTO habilidades (nome,status) SELECT 'Python','ativa' WHERE NOT EXISTS (SELECT 1 FROM habilidades WHERE nome='Python');
INSERT INTO desenvolvedor_habilidades (desenvolvedor_id,habilidade_id,nivel) SELECT @dev,h.id,'intermediario' FROM habilidades h WHERE h.nome='Python' AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM desenvolvedor_habilidades dh WHERE dh.desenvolvedor_id=@dev AND dh.habilidade_id=h.id);
INSERT INTO habilidades (nome,status) SELECT 'Git','ativa' WHERE NOT EXISTS (SELECT 1 FROM habilidades WHERE nome='Git');
INSERT INTO desenvolvedor_habilidades (desenvolvedor_id,habilidade_id,nivel) SELECT @dev,h.id,'intermediario' FROM habilidades h WHERE h.nome='Git' AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM desenvolvedor_habilidades dh WHERE dh.desenvolvedor_id=@dev AND dh.habilidade_id=h.id);
INSERT INTO habilidades (nome,status) SELECT 'UI/UX','ativa' WHERE NOT EXISTS (SELECT 1 FROM habilidades WHERE nome='UI/UX');
INSERT INTO desenvolvedor_habilidades (desenvolvedor_id,habilidade_id,nivel) SELECT @dev,h.id,'intermediario' FROM habilidades h WHERE h.nome='UI/UX' AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM desenvolvedor_habilidades dh WHERE dh.desenvolvedor_id=@dev AND dh.habilidade_id=h.id);

-- Portfolio do Artur (RF04). Imagem = NULL: capa automatica pela tematica.
INSERT INTO portfolios (desenvolvedor_id,titulo,descricao,link_projeto,link_repositorio,imagem) SELECT @dev,'Site para hamburgueria','Site responsivo com cardapio online, contato pelo WhatsApp e identidade visual quente.',NULL,NULL,NULL WHERE @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM portfolios WHERE desenvolvedor_id=@dev AND titulo='Site para hamburgueria');
INSERT INTO portfolios (desenvolvedor_id,titulo,descricao,link_projeto,link_repositorio,imagem) SELECT @dev,'Loja virtual de roupas','Catalogo de moda com vitrine de produtos, categorias e carrinho demonstrativo.',NULL,NULL,NULL WHERE @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM portfolios WHERE desenvolvedor_id=@dev AND titulo='Loja virtual de roupas');
INSERT INTO portfolios (desenvolvedor_id,titulo,descricao,link_projeto,link_repositorio,imagem) SELECT @dev,'Sistema de agenda','Painel de gerenciamento de horarios, clientes e atendimentos.',NULL,NULL,NULL WHERE @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM portfolios WHERE desenvolvedor_id=@dev AND titulo='Sistema de agenda');
INSERT INTO portfolios (desenvolvedor_id,titulo,descricao,link_projeto,link_repositorio,imagem) SELECT @dev,'Portfolio pessoal','Pagina de apresentacao profissional com trabalhos e habilidades.',NULL,NULL,NULL WHERE @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM portfolios WHERE desenvolvedor_id=@dev AND titulo='Portfolio pessoal');
INSERT INTO portfolios (desenvolvedor_id,titulo,descricao,link_projeto,link_repositorio,imagem) SELECT @dev,'PetShop Digital','Catalogo de servicos, banho e tosa e agendamento de atendimento.',NULL,NULL,NULL WHERE @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM portfolios WHERE desenvolvedor_id=@dev AND titulo='PetShop Digital');

-- Oito projetos mostram os diferentes estados de interface.
INSERT INTO projetos (cliente_id,titulo,categoria,descricao_resumida,orcamento_min,orcamento_max,prazo_desejado,status)
SELECT @cli,'Site para hamburgueria','site','Site responsivo para hamburgueria com cardapio online, fotos de produtos e link para WhatsApp.',1000,1500,DATE_ADD(CURDATE(), INTERVAL 35 DAY),'aberto'
WHERE @cli IS NOT NULL AND NOT EXISTS (SELECT 1 FROM projetos WHERE cliente_id=@cli AND titulo='Site para hamburgueria');
SET @proj_hamburguer=(SELECT id FROM projetos WHERE cliente_id=@cli AND titulo='Site para hamburgueria' ORDER BY id ASC LIMIT 1);
INSERT INTO briefings (projeto_id,status)
SELECT @proj_hamburguer,'aprovado' WHERE @proj_hamburguer IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefings WHERE projeto_id=@proj_hamburguer);
SET @brief_hamburguer=(SELECT id FROM briefings WHERE projeto_id=@proj_hamburguer);
INSERT INTO briefing_versoes (briefing_id,numero_versao,objetivo,publico_alvo,funcionalidades,condicoes_regras,identidade_visual,referencias_visuais,observacoes,criado_por,status)
SELECT @brief_hamburguer,1,'Divulgar cardapio e receber contatos','Clientes que querem pedir hamburguer','Cardapio online; Galeria de lanches; Botao do WhatsApp; Responsividade','Ajustes por mensagem devem ser registrados no sistema.','Visual clean com violeta e branco.','Prototipo SiteMatch.','Dados demonstrativos para apresentacao do TCC.',@isa_user,'aprovada'
WHERE @brief_hamburguer IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefing_versoes WHERE briefing_id=@brief_hamburguer AND numero_versao=1);
SET @bv_hamburguer=(SELECT id FROM briefing_versoes WHERE briefing_id=@brief_hamburguer AND numero_versao=1);
INSERT INTO projetos (cliente_id,titulo,categoria,descricao_resumida,orcamento_min,orcamento_max,prazo_desejado,status)
SELECT @cli,'Loja virtual de roupas','sistema_web','Vitrine online para roupas femininas, filtros por categoria e pagina de produtos.',2000,3500,DATE_ADD(CURDATE(), INTERVAL 50 DAY),'em_negociacao'
WHERE @cli IS NOT NULL AND NOT EXISTS (SELECT 1 FROM projetos WHERE cliente_id=@cli AND titulo='Loja virtual de roupas');
SET @proj_moda=(SELECT id FROM projetos WHERE cliente_id=@cli AND titulo='Loja virtual de roupas' ORDER BY id ASC LIMIT 1);
INSERT INTO briefings (projeto_id,status)
SELECT @proj_moda,'aprovado' WHERE @proj_moda IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefings WHERE projeto_id=@proj_moda);
SET @brief_moda=(SELECT id FROM briefings WHERE projeto_id=@proj_moda);
INSERT INTO briefing_versoes (briefing_id,numero_versao,objetivo,publico_alvo,funcionalidades,condicoes_regras,identidade_visual,referencias_visuais,observacoes,criado_por,status)
SELECT @brief_moda,1,'Criar catalogo com navegacao simples','Mulheres interessadas em moda','Catalogo; Categorias; Busca de produtos; Formulario de contato','Ajustes por mensagem devem ser registrados no sistema.','Visual clean com violeta e branco.','Prototipo SiteMatch.','Dados demonstrativos para apresentacao do TCC.',@isa_user,'aprovada'
WHERE @brief_moda IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefing_versoes WHERE briefing_id=@brief_moda AND numero_versao=1);
SET @bv_moda=(SELECT id FROM briefing_versoes WHERE briefing_id=@brief_moda AND numero_versao=1);
INSERT INTO propostas (projeto_id,desenvolvedor_id,valor,prazo_estimado,descricao,entregaveis,briefing_versao_id,status)
SELECT @proj_moda,@dev,2750.0,45,'Proposta demonstrativa do Artur para Loja virtual de roupas. Inclui desenvolvimento, revisao e entrega.','Layout responsivo; Telas acordadas no briefing; Testes; Entrega final',@bv_moda,'em_negociacao'
WHERE @proj_moda IS NOT NULL AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM propostas WHERE projeto_id=@proj_moda AND desenvolvedor_id=@dev);
SET @proposta_moda=(SELECT id FROM propostas WHERE projeto_id=@proj_moda AND desenvolvedor_id=@dev);
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_moda,@artur_user,'Ola! Analisei o briefing de Loja virtual de roupas e enviei minha proposta.',DATE_SUB(NOW(),INTERVAL 9 DAY),NULL
WHERE @proposta_moda IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_moda AND conteudo='Ola! Analisei o briefing de Loja virtual de roupas e enviei minha proposta.');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_moda,@isa_user,'Obrigado! Podemos conversar sobre prazos e detalhes?',DATE_SUB(NOW(),INTERVAL 8 DAY),NULL
WHERE @proposta_moda IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_moda AND conteudo='Obrigado! Podemos conversar sobre prazos e detalhes?');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_moda,@artur_user,'Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.',DATE_SUB(NOW(),INTERVAL 7 DAY),NULL
WHERE @proposta_moda IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_moda AND conteudo='Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.');
INSERT INTO projetos (cliente_id,titulo,categoria,descricao_resumida,orcamento_min,orcamento_max,prazo_desejado,status)
SELECT @cli,'Portfolio pessoal','site','Portfolio pessoal para apresentar projetos, experiencia e links de contato.',800,1400,DATE_ADD(CURDATE(), INTERVAL 30 DAY),'aberto'
WHERE @cli IS NOT NULL AND NOT EXISTS (SELECT 1 FROM projetos WHERE cliente_id=@cli AND titulo='Portfolio pessoal');
SET @proj_portfolio=(SELECT id FROM projetos WHERE cliente_id=@cli AND titulo='Portfolio pessoal' ORDER BY id ASC LIMIT 1);
INSERT INTO briefings (projeto_id,status)
SELECT @proj_portfolio,'aprovado' WHERE @proj_portfolio IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefings WHERE projeto_id=@proj_portfolio);
SET @brief_portfolio=(SELECT id FROM briefings WHERE projeto_id=@proj_portfolio);
INSERT INTO briefing_versoes (briefing_id,numero_versao,objetivo,publico_alvo,funcionalidades,condicoes_regras,identidade_visual,referencias_visuais,observacoes,criado_por,status)
SELECT @brief_portfolio,1,'Divulgar trabalho profissional','Recrutadores e clientes','Sobre mim; Projetos; Contato; Versao para celular','Ajustes por mensagem devem ser registrados no sistema.','Visual clean com violeta e branco.','Prototipo SiteMatch.','Dados demonstrativos para apresentacao do TCC.',@isa_user,'aprovada'
WHERE @brief_portfolio IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefing_versoes WHERE briefing_id=@brief_portfolio AND numero_versao=1);
SET @bv_portfolio=(SELECT id FROM briefing_versoes WHERE briefing_id=@brief_portfolio AND numero_versao=1);
INSERT INTO propostas (projeto_id,desenvolvedor_id,valor,prazo_estimado,descricao,entregaveis,briefing_versao_id,status)
SELECT @proj_portfolio,@dev,1100.0,30,'Proposta demonstrativa do Artur para Portfolio pessoal. Inclui desenvolvimento, revisao e entrega.','Layout responsivo; Telas acordadas no briefing; Testes; Entrega final',@bv_portfolio,'preliminar'
WHERE @proj_portfolio IS NOT NULL AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM propostas WHERE projeto_id=@proj_portfolio AND desenvolvedor_id=@dev);
SET @proposta_portfolio=(SELECT id FROM propostas WHERE projeto_id=@proj_portfolio AND desenvolvedor_id=@dev);
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_portfolio,@artur_user,'Ola! Analisei o briefing de Portfolio pessoal e enviei minha proposta.',DATE_SUB(NOW(),INTERVAL 9 DAY),NULL
WHERE @proposta_portfolio IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_portfolio AND conteudo='Ola! Analisei o briefing de Portfolio pessoal e enviei minha proposta.');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_portfolio,@isa_user,'Obrigado! Podemos conversar sobre prazos e detalhes?',DATE_SUB(NOW(),INTERVAL 8 DAY),NULL
WHERE @proposta_portfolio IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_portfolio AND conteudo='Obrigado! Podemos conversar sobre prazos e detalhes?');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_portfolio,@artur_user,'Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.',DATE_SUB(NOW(),INTERVAL 7 DAY),NULL
WHERE @proposta_portfolio IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_portfolio AND conteudo='Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.');
INSERT INTO projetos (cliente_id,titulo,categoria,descricao_resumida,orcamento_min,orcamento_max,prazo_desejado,status)
SELECT @cli,'Agendamento para pet shop','sistema_web','Sistema de agendamento de banho, tosa e consultas com painel de clientes.',2500,4900,DATE_ADD(CURDATE(), INTERVAL 65 DAY),'em_andamento'
WHERE @cli IS NOT NULL AND NOT EXISTS (SELECT 1 FROM projetos WHERE cliente_id=@cli AND titulo='Agendamento para pet shop');
SET @proj_pet=(SELECT id FROM projetos WHERE cliente_id=@cli AND titulo='Agendamento para pet shop' ORDER BY id ASC LIMIT 1);
INSERT INTO briefings (projeto_id,status)
SELECT @proj_pet,'aprovado' WHERE @proj_pet IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefings WHERE projeto_id=@proj_pet);
SET @brief_pet=(SELECT id FROM briefings WHERE projeto_id=@proj_pet);
INSERT INTO briefing_versoes (briefing_id,numero_versao,objetivo,publico_alvo,funcionalidades,condicoes_regras,identidade_visual,referencias_visuais,observacoes,criado_por,status)
SELECT @brief_pet,1,'Organizar agendamentos do pet shop','Tutores de caes e gatos','Cadastro de pets; Agenda de horarios; Painel de atendimentos; Historico','Ajustes por mensagem devem ser registrados no sistema.','Visual clean com violeta e branco.','Prototipo SiteMatch.','Dados demonstrativos para apresentacao do TCC.',@isa_user,'aprovada'
WHERE @brief_pet IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefing_versoes WHERE briefing_id=@brief_pet AND numero_versao=1);
SET @bv_pet=(SELECT id FROM briefing_versoes WHERE briefing_id=@brief_pet AND numero_versao=1);
INSERT INTO propostas (projeto_id,desenvolvedor_id,valor,prazo_estimado,descricao,entregaveis,briefing_versao_id,status)
SELECT @proj_pet,@dev,3700.0,45,'Proposta demonstrativa do Artur para Agendamento para pet shop. Inclui desenvolvimento, revisao e entrega.','Layout responsivo; Telas acordadas no briefing; Testes; Entrega final',@bv_pet,'aceita'
WHERE @proj_pet IS NOT NULL AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM propostas WHERE projeto_id=@proj_pet AND desenvolvedor_id=@dev);
SET @proposta_pet=(SELECT id FROM propostas WHERE projeto_id=@proj_pet AND desenvolvedor_id=@dev);
INSERT INTO contratacoes (projeto_id,proposta_id,briefing_versao_id,valor_final,prazo_final_dias,status,data_aceite,data_inicio,data_entrega,data_conclusao)
SELECT @proj_pet,@proposta_pet,@bv_pet,3700.0,45,'em_andamento',DATE_SUB(NOW(),INTERVAL 12 DAY),DATE_SUB(NOW(),INTERVAL 11 DAY),NULL,NULL
WHERE @proposta_pet IS NOT NULL AND NOT EXISTS (SELECT 1 FROM contratacoes WHERE projeto_id=@proj_pet OR proposta_id=@proposta_pet);
SET @contr_pet=(SELECT id FROM contratacoes WHERE projeto_id=@proj_pet);
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_pet,@artur_user,'Ola! Analisei o briefing de Agendamento para pet shop e enviei minha proposta.',DATE_SUB(NOW(),INTERVAL 9 DAY),NULL
WHERE @proposta_pet IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_pet AND conteudo='Ola! Analisei o briefing de Agendamento para pet shop e enviei minha proposta.');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_pet,@isa_user,'Obrigado! Podemos conversar sobre prazos e detalhes?',DATE_SUB(NOW(),INTERVAL 8 DAY),NULL
WHERE @proposta_pet IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_pet AND conteudo='Obrigado! Podemos conversar sobre prazos e detalhes?');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_pet,@artur_user,'Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.',DATE_SUB(NOW(),INTERVAL 7 DAY),NULL
WHERE @proposta_pet IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_pet AND conteudo='Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.');
INSERT INTO projetos (cliente_id,titulo,categoria,descricao_resumida,orcamento_min,orcamento_max,prazo_desejado,status)
SELECT @cli,'Sistema de agenda para salao','sistema_web','Agenda com cadastro de servicos, horarios e confirmacao de atendimento.',1800,3200,DATE_ADD(CURDATE(), INTERVAL 45 DAY),'aguardando_confirmacao'
WHERE @cli IS NOT NULL AND NOT EXISTS (SELECT 1 FROM projetos WHERE cliente_id=@cli AND titulo='Sistema de agenda para salao');
SET @proj_agenda=(SELECT id FROM projetos WHERE cliente_id=@cli AND titulo='Sistema de agenda para salao' ORDER BY id ASC LIMIT 1);
INSERT INTO briefings (projeto_id,status)
SELECT @proj_agenda,'aprovado' WHERE @proj_agenda IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefings WHERE projeto_id=@proj_agenda);
SET @brief_agenda=(SELECT id FROM briefings WHERE projeto_id=@proj_agenda);
INSERT INTO briefing_versoes (briefing_id,numero_versao,objetivo,publico_alvo,funcionalidades,condicoes_regras,identidade_visual,referencias_visuais,observacoes,criado_por,status)
SELECT @brief_agenda,1,'Organizar horarios do salao','Clientes e profissionais','Agenda; Cadastro de servicos; Cadastro de clientes; Confirmacao','Ajustes por mensagem devem ser registrados no sistema.','Visual clean com violeta e branco.','Prototipo SiteMatch.','Dados demonstrativos para apresentacao do TCC.',@isa_user,'aprovada'
WHERE @brief_agenda IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefing_versoes WHERE briefing_id=@brief_agenda AND numero_versao=1);
SET @bv_agenda=(SELECT id FROM briefing_versoes WHERE briefing_id=@brief_agenda AND numero_versao=1);
INSERT INTO propostas (projeto_id,desenvolvedor_id,valor,prazo_estimado,descricao,entregaveis,briefing_versao_id,status)
SELECT @proj_agenda,@dev,2500.0,45,'Proposta demonstrativa do Artur para Sistema de agenda para salao. Inclui desenvolvimento, revisao e entrega.','Layout responsivo; Telas acordadas no briefing; Testes; Entrega final',@bv_agenda,'aceita'
WHERE @proj_agenda IS NOT NULL AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM propostas WHERE projeto_id=@proj_agenda AND desenvolvedor_id=@dev);
SET @proposta_agenda=(SELECT id FROM propostas WHERE projeto_id=@proj_agenda AND desenvolvedor_id=@dev);
INSERT INTO contratacoes (projeto_id,proposta_id,briefing_versao_id,valor_final,prazo_final_dias,status,data_aceite,data_inicio,data_entrega,data_conclusao)
SELECT @proj_agenda,@proposta_agenda,@bv_agenda,2500.0,45,'aguardando_confirmacao',DATE_SUB(NOW(),INTERVAL 20 DAY),DATE_SUB(NOW(),INTERVAL 19 DAY),DATE_SUB(NOW(),INTERVAL 1 DAY),NULL
WHERE @proposta_agenda IS NOT NULL AND NOT EXISTS (SELECT 1 FROM contratacoes WHERE projeto_id=@proj_agenda OR proposta_id=@proposta_agenda);
SET @contr_agenda=(SELECT id FROM contratacoes WHERE projeto_id=@proj_agenda);
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_agenda,@artur_user,'Ola! Analisei o briefing de Sistema de agenda para salao e enviei minha proposta.',DATE_SUB(NOW(),INTERVAL 9 DAY),NULL
WHERE @proposta_agenda IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_agenda AND conteudo='Ola! Analisei o briefing de Sistema de agenda para salao e enviei minha proposta.');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_agenda,@isa_user,'Obrigado! Podemos conversar sobre prazos e detalhes?',DATE_SUB(NOW(),INTERVAL 8 DAY),NULL
WHERE @proposta_agenda IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_agenda AND conteudo='Obrigado! Podemos conversar sobre prazos e detalhes?');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_agenda,@artur_user,'Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.',DATE_SUB(NOW(),INTERVAL 7 DAY),NULL
WHERE @proposta_agenda IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_agenda AND conteudo='Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_agenda,@artur_user,'Entreguei a primeira versao e aguardo sua confirmacao.',DATE_SUB(NOW(),INTERVAL 6 DAY),NULL
WHERE @proposta_agenda IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_agenda AND conteudo='Entreguei a primeira versao e aguardo sua confirmacao.');
INSERT INTO projetos (cliente_id,titulo,categoria,descricao_resumida,orcamento_min,orcamento_max,prazo_desejado,status)
SELECT @cli,'Site institucional para escola','site','Site com paginas de cursos, matriculas e contato institucional.',1200,2500,DATE_ADD(CURDATE(), INTERVAL 50 DAY),'em_andamento'
WHERE @cli IS NOT NULL AND NOT EXISTS (SELECT 1 FROM projetos WHERE cliente_id=@cli AND titulo='Site institucional para escola');
SET @proj_ajustes=(SELECT id FROM projetos WHERE cliente_id=@cli AND titulo='Site institucional para escola' ORDER BY id ASC LIMIT 1);
INSERT INTO briefings (projeto_id,status)
SELECT @proj_ajustes,'aprovado' WHERE @proj_ajustes IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefings WHERE projeto_id=@proj_ajustes);
SET @brief_ajustes=(SELECT id FROM briefings WHERE projeto_id=@proj_ajustes);
INSERT INTO briefing_versoes (briefing_id,numero_versao,objetivo,publico_alvo,funcionalidades,condicoes_regras,identidade_visual,referencias_visuais,observacoes,criado_por,status)
SELECT @brief_ajustes,1,'Apresentar cursos e receber matriculas','Pais e estudantes','Cursos; Calendario; Formulario de interesse; Contato','Ajustes por mensagem devem ser registrados no sistema.','Visual clean com violeta e branco.','Prototipo SiteMatch.','Dados demonstrativos para apresentacao do TCC.',@isa_user,'aprovada'
WHERE @brief_ajustes IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefing_versoes WHERE briefing_id=@brief_ajustes AND numero_versao=1);
SET @bv_ajustes=(SELECT id FROM briefing_versoes WHERE briefing_id=@brief_ajustes AND numero_versao=1);
INSERT INTO propostas (projeto_id,desenvolvedor_id,valor,prazo_estimado,descricao,entregaveis,briefing_versao_id,status)
SELECT @proj_ajustes,@dev,1850.0,45,'Proposta demonstrativa do Artur para Site institucional para escola. Inclui desenvolvimento, revisao e entrega.','Layout responsivo; Telas acordadas no briefing; Testes; Entrega final',@bv_ajustes,'aceita'
WHERE @proj_ajustes IS NOT NULL AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM propostas WHERE projeto_id=@proj_ajustes AND desenvolvedor_id=@dev);
SET @proposta_ajustes=(SELECT id FROM propostas WHERE projeto_id=@proj_ajustes AND desenvolvedor_id=@dev);
INSERT INTO contratacoes (projeto_id,proposta_id,briefing_versao_id,valor_final,prazo_final_dias,status,data_aceite,data_inicio,data_entrega,data_conclusao)
SELECT @proj_ajustes,@proposta_ajustes,@bv_ajustes,1850.0,45,'ajustes_solicitados',DATE_SUB(NOW(),INTERVAL 12 DAY),DATE_SUB(NOW(),INTERVAL 11 DAY),NULL,NULL
WHERE @proposta_ajustes IS NOT NULL AND NOT EXISTS (SELECT 1 FROM contratacoes WHERE projeto_id=@proj_ajustes OR proposta_id=@proposta_ajustes);
SET @contr_ajustes=(SELECT id FROM contratacoes WHERE projeto_id=@proj_ajustes);
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_ajustes,@artur_user,'Ola! Analisei o briefing de Site institucional para escola e enviei minha proposta.',DATE_SUB(NOW(),INTERVAL 9 DAY),NULL
WHERE @proposta_ajustes IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_ajustes AND conteudo='Ola! Analisei o briefing de Site institucional para escola e enviei minha proposta.');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_ajustes,@isa_user,'Obrigado! Podemos conversar sobre prazos e detalhes?',DATE_SUB(NOW(),INTERVAL 8 DAY),NULL
WHERE @proposta_ajustes IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_ajustes AND conteudo='Obrigado! Podemos conversar sobre prazos e detalhes?');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_ajustes,@artur_user,'Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.',DATE_SUB(NOW(),INTERVAL 7 DAY),NULL
WHERE @proposta_ajustes IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_ajustes AND conteudo='Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_ajustes,@isa_user,'Solicitação de ajustes: melhorar o destaque do formulario de matricula e revisar a versao mobile.',DATE_SUB(NOW(),INTERVAL 6 DAY),NULL
WHERE @proposta_ajustes IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_ajustes AND conteudo='Solicitação de ajustes: melhorar o destaque do formulario de matricula e revisar a versao mobile.');
INSERT INTO projetos (cliente_id,titulo,categoria,descricao_resumida,orcamento_min,orcamento_max,prazo_desejado,status)
SELECT @cli,'Landing page de confeitaria','site','Pagina promocional com vitrine de bolos, depoimentos e formulario para encomendas.',900,1800,DATE_ADD(CURDATE(), INTERVAL 25 DAY),'concluido'
WHERE @cli IS NOT NULL AND NOT EXISTS (SELECT 1 FROM projetos WHERE cliente_id=@cli AND titulo='Landing page de confeitaria');
SET @proj_concluido=(SELECT id FROM projetos WHERE cliente_id=@cli AND titulo='Landing page de confeitaria' ORDER BY id ASC LIMIT 1);
INSERT INTO briefings (projeto_id,status)
SELECT @proj_concluido,'aprovado' WHERE @proj_concluido IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefings WHERE projeto_id=@proj_concluido);
SET @brief_concluido=(SELECT id FROM briefings WHERE projeto_id=@proj_concluido);
INSERT INTO briefing_versoes (briefing_id,numero_versao,objetivo,publico_alvo,funcionalidades,condicoes_regras,identidade_visual,referencias_visuais,observacoes,criado_por,status)
SELECT @brief_concluido,1,'Divulgar produtos e encomendas','Clientes da cidade','Galeria; Destaques; Depoimentos; WhatsApp','Ajustes por mensagem devem ser registrados no sistema.','Visual clean com violeta e branco.','Prototipo SiteMatch.','Dados demonstrativos para apresentacao do TCC.',@isa_user,'aprovada'
WHERE @brief_concluido IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefing_versoes WHERE briefing_id=@brief_concluido AND numero_versao=1);
SET @bv_concluido=(SELECT id FROM briefing_versoes WHERE briefing_id=@brief_concluido AND numero_versao=1);
INSERT INTO propostas (projeto_id,desenvolvedor_id,valor,prazo_estimado,descricao,entregaveis,briefing_versao_id,status)
SELECT @proj_concluido,@dev,1350.0,25,'Proposta demonstrativa do Artur para Landing page de confeitaria. Inclui desenvolvimento, revisao e entrega.','Layout responsivo; Telas acordadas no briefing; Testes; Entrega final',@bv_concluido,'aceita'
WHERE @proj_concluido IS NOT NULL AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM propostas WHERE projeto_id=@proj_concluido AND desenvolvedor_id=@dev);
SET @proposta_concluido=(SELECT id FROM propostas WHERE projeto_id=@proj_concluido AND desenvolvedor_id=@dev);
INSERT INTO contratacoes (projeto_id,proposta_id,briefing_versao_id,valor_final,prazo_final_dias,status,data_aceite,data_inicio,data_entrega,data_conclusao)
SELECT @proj_concluido,@proposta_concluido,@bv_concluido,1350.0,25,'concluida',DATE_SUB(NOW(),INTERVAL 40 DAY),DATE_SUB(NOW(),INTERVAL 39 DAY),DATE_SUB(NOW(),INTERVAL 15 DAY),DATE_SUB(NOW(),INTERVAL 14 DAY)
WHERE @proposta_concluido IS NOT NULL AND NOT EXISTS (SELECT 1 FROM contratacoes WHERE projeto_id=@proj_concluido OR proposta_id=@proposta_concluido);
SET @contr_concluido=(SELECT id FROM contratacoes WHERE projeto_id=@proj_concluido);
INSERT INTO avaliacoes (contratacao_id,nota,comentario)
SELECT @contr_concluido,5,'Artur entregou o projeto conforme o briefing, com boa comunicacao e resultado profissional.'
WHERE @contr_concluido IS NOT NULL AND NOT EXISTS (SELECT 1 FROM avaliacoes WHERE contratacao_id=@contr_concluido);
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_concluido,@artur_user,'Ola! Analisei o briefing de Landing page de confeitaria e enviei minha proposta.',DATE_SUB(NOW(),INTERVAL 9 DAY),NULL
WHERE @proposta_concluido IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_concluido AND conteudo='Ola! Analisei o briefing de Landing page de confeitaria e enviei minha proposta.');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_concluido,@isa_user,'Obrigado! Podemos conversar sobre prazos e detalhes?',DATE_SUB(NOW(),INTERVAL 8 DAY),NULL
WHERE @proposta_concluido IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_concluido AND conteudo='Obrigado! Podemos conversar sobre prazos e detalhes?');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_concluido,@artur_user,'Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.',DATE_SUB(NOW(),INTERVAL 7 DAY),NULL
WHERE @proposta_concluido IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_concluido AND conteudo='Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_concluido,@isa_user,'Entrega aprovada! Obrigada pelo trabalho.',DATE_SUB(NOW(),INTERVAL 6 DAY),NULL
WHERE @proposta_concluido IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_concluido AND conteudo='Entrega aprovada! Obrigada pelo trabalho.');
INSERT INTO projetos (cliente_id,titulo,categoria,descricao_resumida,orcamento_min,orcamento_max,prazo_desejado,status)
SELECT @cli,'Loja de artigos esportivos','sistema_web','Loja online com catalogo de artigos esportivos, informacoes e pedidos.',2400,4300,DATE_ADD(CURDATE(), INTERVAL 60 DAY),'em_negociacao'
WHERE @cli IS NOT NULL AND NOT EXISTS (SELECT 1 FROM projetos WHERE cliente_id=@cli AND titulo='Loja de artigos esportivos');
SET @proj_definitiva=(SELECT id FROM projetos WHERE cliente_id=@cli AND titulo='Loja de artigos esportivos' ORDER BY id ASC LIMIT 1);
INSERT INTO briefings (projeto_id,status)
SELECT @proj_definitiva,'aprovado' WHERE @proj_definitiva IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefings WHERE projeto_id=@proj_definitiva);
SET @brief_definitiva=(SELECT id FROM briefings WHERE projeto_id=@proj_definitiva);
INSERT INTO briefing_versoes (briefing_id,numero_versao,objetivo,publico_alvo,funcionalidades,condicoes_regras,identidade_visual,referencias_visuais,observacoes,criado_por,status)
SELECT @brief_definitiva,1,'Criar uma vitrine de produtos esportivos','Esportistas e academias','Catalogo; Filtros; Pagina do produto; Formulario de pedido','Ajustes por mensagem devem ser registrados no sistema.','Visual clean com violeta e branco.','Prototipo SiteMatch.','Dados demonstrativos para apresentacao do TCC.',@isa_user,'aprovada'
WHERE @brief_definitiva IS NOT NULL AND NOT EXISTS (SELECT 1 FROM briefing_versoes WHERE briefing_id=@brief_definitiva AND numero_versao=1);
SET @bv_definitiva=(SELECT id FROM briefing_versoes WHERE briefing_id=@brief_definitiva AND numero_versao=1);
INSERT INTO propostas (projeto_id,desenvolvedor_id,valor,prazo_estimado,descricao,entregaveis,briefing_versao_id,status)
SELECT @proj_definitiva,@dev,3350.0,45,'Proposta demonstrativa do Artur para Loja de artigos esportivos. Inclui desenvolvimento, revisao e entrega.','Layout responsivo; Telas acordadas no briefing; Testes; Entrega final',@bv_definitiva,'definitiva'
WHERE @proj_definitiva IS NOT NULL AND @dev IS NOT NULL AND NOT EXISTS (SELECT 1 FROM propostas WHERE projeto_id=@proj_definitiva AND desenvolvedor_id=@dev);
SET @proposta_definitiva=(SELECT id FROM propostas WHERE projeto_id=@proj_definitiva AND desenvolvedor_id=@dev);
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_definitiva,@artur_user,'Ola! Analisei o briefing de Loja de artigos esportivos e enviei minha proposta.',DATE_SUB(NOW(),INTERVAL 9 DAY),NULL
WHERE @proposta_definitiva IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_definitiva AND conteudo='Ola! Analisei o briefing de Loja de artigos esportivos e enviei minha proposta.');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_definitiva,@isa_user,'Obrigado! Podemos conversar sobre prazos e detalhes?',DATE_SUB(NOW(),INTERVAL 8 DAY),NULL
WHERE @proposta_definitiva IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_definitiva AND conteudo='Obrigado! Podemos conversar sobre prazos e detalhes?');
INSERT INTO mensagens (proposta_id,remetente_id,conteudo,enviada_em,lida_em)
SELECT @proposta_definitiva,@artur_user,'Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.',DATE_SUB(NOW(),INTERVAL 7 DAY),NULL
WHERE @proposta_definitiva IS NOT NULL AND NOT EXISTS (SELECT 1 FROM mensagens WHERE proposta_id=@proposta_definitiva AND conteudo='Claro! Podemos ajustar os detalhes pelo chat antes da aprovacao.');

COMMIT;
-- Conferencia rapida dos registros gerados
SELECT id,nome,email,tipo FROM usuarios WHERE email IN ('artur.demo@sitematch.test','isabelli.demo@sitematch.test');
SELECT p.id,p.titulo,p.status,pr.status AS proposta_status,c.status AS contratacao_status
FROM projetos p
LEFT JOIN propostas pr ON pr.projeto_id=p.id AND pr.desenvolvedor_id=@dev
LEFT JOIN contratacoes c ON c.projeto_id=p.id
WHERE p.cliente_id=@cli ORDER BY p.id DESC;
-- AVISO: cenarios iniciam em varios estados para demonstracao;
-- para validar o FLUXO COMPLETO, crie novo projeto pela interface.
