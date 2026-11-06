# Migrações

Para um banco que já estava sendo usado antes desta revisão funcional, execute **uma vez**:

`20261106_alinhar_contratacoes.sql`

A migração não apaga registros. Ela apenas alinha contratações antigas criadas como `ativa` para `em_andamento`, conforme a RN14, e altera o valor padrão para novas instalações.
