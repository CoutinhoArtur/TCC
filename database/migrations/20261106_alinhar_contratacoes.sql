USE projeto_match;

-- ==========================================================
-- MIGRAÇÃO: ALINHAR CONTRATAÇÕES À RN14
-- ==========================================================
-- A documentação do projeto define que a contratação começa
-- com status "Em andamento" após o aceite da proposta.
-- Registros criados pela versão anterior usavam "ativa".
-- ==========================================================

ALTER TABLE contratacoes
MODIFY COLUMN status ENUM(
    'ativa',
    'em_andamento',
    'aguardando_confirmacao',
    'ajustes_solicitados',
    'concluida',
    'cancelada'
) NOT NULL DEFAULT 'em_andamento';

-- Normaliza contratações antigas que ainda estão em "ativa".
UPDATE contratacoes AS c
INNER JOIN projetos AS p ON p.id = c.projeto_id
SET
    c.status = 'em_andamento',
    c.data_inicio = COALESCE(c.data_inicio, c.data_aceite),
    p.status = 'em_andamento',
    p.atualizado_em = NOW()
WHERE c.status = 'ativa'
AND p.status = 'contratado';
