<?php
function ePortfolio(string $valor): string { return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); }
$itemEdicao = is_array($itemEdicao) ? $itemEdicao : [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu portfólio - SiteMatch</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="/css/functional.css">
</head>
<body>
<main class="dashboard-main" style="max-width:1100px;margin:0 auto;">
    <p style="margin-bottom:20px;"><a href="/perfil.php">← Voltar ao perfil</a></p>
    <header class="dashboard-header">
        <div class="dashboard-header-text"><h1>Meu portfólio</h1><p>Cadastre trabalhos que ajudem clientes a conhecer sua experiência.</p></div>
    </header>
    <?php if ($mensagemSucesso !== ''): ?><div class="alerta-sucesso"><?= ePortfolio($mensagemSucesso) ?></div><?php endif; ?>
    <?php if ($mensagemErro !== ''): ?><div class="alerta-erro"><?= ePortfolio($mensagemErro) ?></div><?php endif; ?>

    <section class="dashboard-section">
        <h2><?= !empty($itemEdicao) ? 'Editar trabalho' : 'Adicionar trabalho' ?></h2>
        <form method="POST" class="form-card">
            <?php if (!empty($itemEdicao)): ?><input type="hidden" name="id" value="<?= (int) $itemEdicao['id'] ?>"><?php endif; ?>
            <input type="hidden" name="acao" value="salvar">
            <div class="form-grid">
                <div class="form-campo full"><label for="titulo">Título</label><input id="titulo" name="titulo" maxlength="150" required value="<?= ePortfolio((string) ($itemEdicao['titulo'] ?? '')) ?>"></div>
                <div class="form-campo full"><label for="descricao">Descrição</label><textarea id="descricao" name="descricao" required><?= ePortfolio((string) ($itemEdicao['descricao'] ?? '')) ?></textarea></div>
                <div class="form-campo"><label for="link_projeto">Link do projeto</label><input id="link_projeto" name="link_projeto" type="url" value="<?= ePortfolio((string) ($itemEdicao['link_projeto'] ?? '')) ?>"></div>
                <div class="form-campo"><label for="link_repositorio">Link do repositório</label><input id="link_repositorio" name="link_repositorio" type="url" value="<?= ePortfolio((string) ($itemEdicao['link_repositorio'] ?? '')) ?>"></div>
                <div class="form-campo full"><label for="imagem">URL de imagem (opcional)</label><input id="imagem" name="imagem" type="url" value="<?= ePortfolio((string) ($itemEdicao['imagem'] ?? '')) ?>"></div>
            </div>
            <div class="form-acoes"><button class="botao-principal" type="submit"><?= !empty($itemEdicao) ? 'Salvar alterações' : 'Adicionar ao portfólio' ?></button><?php if (!empty($itemEdicao)): ?><a class="botao-secundario" href="portfolio.php">Cancelar edição</a><?php endif; ?></div>
        </form>
    </section>

    <section class="dashboard-section">
        <h2>Trabalhos cadastrados</h2>
        <?php if (empty($itens)): ?><div class="projetos-vazio">Nenhum trabalho cadastrado ainda.</div><?php else: ?>
            <div class="lista-simples">
            <?php foreach ($itens as $item): ?>
                <article class="projeto-card">
                    <h3><?= ePortfolio((string) $item['titulo']) ?></h3>
                    <p style="margin-top:10px;line-height:1.5;"><?= nl2br(ePortfolio((string) $item['descricao'])) ?></p>
                    <div class="acoes-inline">
                        <?php if (!empty($item['link_projeto'])): ?><a class="botao-secundario" target="_blank" rel="noopener" href="<?= ePortfolio((string) $item['link_projeto']) ?>">Ver projeto</a><?php endif; ?>
                        <?php if (!empty($item['link_repositorio'])): ?><a class="botao-secundario" target="_blank" rel="noopener" href="<?= ePortfolio((string) $item['link_repositorio']) ?>">Repositório</a><?php endif; ?>
                        <a class="botao-secundario" href="portfolio.php?editar=<?= (int) $item['id'] ?>">Editar</a>
                        <form method="POST" onsubmit="return confirm('Remover este trabalho do portfólio?');">
                            <input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                            <button class="botao-perigo" type="submit">Excluir</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
