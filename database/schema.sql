CREATE DATABASE IF NOT EXISTS projeto_match
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE projeto_match;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'desenvolvedor') NOT NULL,
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL DEFAULT NULL
);

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    nome_empresa VARCHAR(150) NULL,
    descricao TEXT NULL,
    telefone VARCHAR(20) NULL,
    cidade VARCHAR(100) NULL,

    CONSTRAINT fk_clientes_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);

CREATE TABLE desenvolvedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    titulo_profissional VARCHAR(120) NULL,
    bio TEXT NULL,
    experiencia TEXT NULL,
    disponibilidade VARCHAR(100) NULL,
    faixa_preco_min DECIMAL(10,2) NULL,
    faixa_preco_max DECIMAL(10,2) NULL,

    CONSTRAINT fk_desenvolvedores_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);

CREATE TABLE projetos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    categoria ENUM(
        'site',
        'sistema_web',
        'aplicativo_mobile',
        'sistema_desktop',
        'outro'
    ) NOT NULL,
    descricao_resumida TEXT NOT NULL,
    orcamento_min DECIMAL(10,2) NULL,
    orcamento_max DECIMAL(10,2) NULL,
    prazo_desejado DATE NULL,
    status ENUM(
        'rascunho',
        'aberto',
        'em_negociacao',
        'contratado',
        'em_andamento',
        'aguardando_confirmacao',
        'concluido',
        'cancelado'
    ) NOT NULL DEFAULT 'rascunho',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL DEFAULT NULL,

    CONSTRAINT fk_projetos_cliente
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
        ON DELETE CASCADE
);

CREATE TABLE briefings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT NOT NULL UNIQUE,
    status ENUM(
        'rascunho',
        'publicado',
        'em_revisao',
        'aguardando_validacao',
        'aprovado'
    ) NOT NULL DEFAULT 'rascunho',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL DEFAULT NULL,

    CONSTRAINT fk_briefings_projeto
        FOREIGN KEY (projeto_id)
        REFERENCES projetos(id)
        ON DELETE CASCADE
);

CREATE TABLE briefing_versoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    briefing_id INT NOT NULL,
    numero_versao INT NOT NULL,
    objetivo TEXT NOT NULL,
    publico_alvo TEXT NULL,
    funcionalidades TEXT NOT NULL,
    condicoes_regras TEXT NULL,
    identidade_visual TEXT NULL,
    referencias_visuais TEXT NULL,
    observacoes TEXT NULL,
    criado_por INT NOT NULL,
    status ENUM(
        'rascunho',
        'aguardando_validacao',
        'aprovada',
        'arquivada'
    ) NOT NULL DEFAULT 'rascunho',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_briefing_versoes_briefing
        FOREIGN KEY (briefing_id)
        REFERENCES briefings(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_briefing_versoes_usuario
        FOREIGN KEY (criado_por)
        REFERENCES usuarios(id)
        ON DELETE RESTRICT,

    CONSTRAINT uq_briefing_numero_versao
        UNIQUE (briefing_id, numero_versao)
);

CREATE TABLE habilidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('ativa', 'inativa') NOT NULL DEFAULT 'ativa',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE desenvolvedor_habilidades (
    desenvolvedor_id INT NOT NULL,
    habilidade_id INT NOT NULL,
    nivel ENUM(
        'basico',
        'intermediario',
        'avancado'
    ) NOT NULL,

    PRIMARY KEY (desenvolvedor_id, habilidade_id),

    CONSTRAINT fk_dev_habilidades_desenvolvedor
        FOREIGN KEY (desenvolvedor_id)
        REFERENCES desenvolvedores(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_dev_habilidades_habilidade
        FOREIGN KEY (habilidade_id)
        REFERENCES habilidades(id)
        ON DELETE CASCADE
);

CREATE TABLE portfolios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    desenvolvedor_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    link_projeto VARCHAR(255) NULL,
    link_repositorio VARCHAR(255) NULL,
    imagem VARCHAR(255) NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL DEFAULT NULL,

    CONSTRAINT fk_portfolios_desenvolvedor
        FOREIGN KEY (desenvolvedor_id)
        REFERENCES desenvolvedores(id)
        ON DELETE CASCADE
);

CREATE TABLE propostas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT NOT NULL,
    desenvolvedor_id INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    prazo_estimado INT NOT NULL,
    descricao TEXT NOT NULL,
    entregaveis TEXT NULL,
    briefing_versao_id INT NULL,
    status ENUM(
        'preliminar',
        'em_negociacao',
        'aguardando_revisao',
        'definitiva',
        'aceita',
        'recusada',
        'encerrada',
        'cancelada'
    ) NOT NULL DEFAULT 'preliminar',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL DEFAULT NULL,

    CONSTRAINT fk_propostas_projeto
        FOREIGN KEY (projeto_id)
        REFERENCES projetos(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_propostas_desenvolvedor
        FOREIGN KEY (desenvolvedor_id)
        REFERENCES desenvolvedores(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_propostas_briefing_versao
        FOREIGN KEY (briefing_versao_id)
        REFERENCES briefing_versoes(id)
        ON DELETE SET NULL,

    CONSTRAINT uq_proposta_desenvolvedor_projeto
        UNIQUE (projeto_id, desenvolvedor_id)
);

CREATE TABLE mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proposta_id INT NOT NULL,
    remetente_id INT NOT NULL,
    conteudo TEXT NOT NULL,
    enviada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    lida_em DATETIME NULL DEFAULT NULL,

    CONSTRAINT fk_mensagens_proposta
        FOREIGN KEY (proposta_id)
        REFERENCES propostas(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_mensagens_remetente
        FOREIGN KEY (remetente_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);

CREATE TABLE contratacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT NOT NULL UNIQUE,
    proposta_id INT NOT NULL UNIQUE,
    briefing_versao_id INT NOT NULL,
    valor_final DECIMAL(10,2) NOT NULL,
    prazo_final_dias INT NOT NULL,
    status ENUM(
        'ativa',
        'em_andamento',
        'aguardando_confirmacao',
        'ajustes_solicitados',
        'concluida',
        'cancelada'
    ) NOT NULL DEFAULT 'em_andamento',
    data_aceite DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_inicio DATETIME NULL DEFAULT NULL,
    data_entrega DATETIME NULL DEFAULT NULL,
    data_conclusao DATETIME NULL DEFAULT NULL,
    data_cancelamento DATETIME NULL DEFAULT NULL,

    CONSTRAINT fk_contratacoes_projeto
        FOREIGN KEY (projeto_id)
        REFERENCES projetos(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_contratacoes_proposta
        FOREIGN KEY (proposta_id)
        REFERENCES propostas(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_contratacoes_briefing_versao
        FOREIGN KEY (briefing_versao_id)
        REFERENCES briefing_versoes(id)
        ON DELETE RESTRICT
);

CREATE TABLE avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contratacao_id INT NOT NULL UNIQUE,
    nota TINYINT NOT NULL,
    comentario TEXT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_avaliacoes_contratacao
        FOREIGN KEY (contratacao_id)
        REFERENCES contratacoes(id)
        ON DELETE CASCADE,

    CONSTRAINT chk_avaliacoes_nota
        CHECK (nota BETWEEN 1 AND 5)
);