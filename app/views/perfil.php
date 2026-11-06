<?php
/** @var array<string,mixed> $usuario */
/** @var array<string,mixed> $perfil */
/** @var array<int,array<string,mixed>> $habilidades */

function ePerfil(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

$tipoUsuario = (string) ($usuario['tipo'] ?? '');
$voltar = $tipoUsuario === 'cliente' ? '/cliente/dashboard.php' : '/desenvolvedor/dashboard.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu perfil - SiteMatch</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="/css/functional.css">
</head>
<body>
<main class="dashboard-main" style="max-width: 1000px; margin: 0 auto;">
    <p style="margin-bottom:20px;"><a href="<?= ePerfil($voltar) ?>">← Voltar ao painel</a></p>
    <header class="dashboard-header">
        <div class="dashboard-header-text">
            <h1>Meu perfil</h1>
            <p>Atualize suas informações cadastrais<?= $tipoUsuario === 'desenvolvedor' ? ' e profissionais' : '' ?>.</p>
        </div>
        <?php if ($tipoUsuario === 'desenvolvedor'): ?>
            <a class="botao-principal" href="/desenvolvedor/portfolio.php">Meu portfólio</a>
        <?php endif; ?>
    </header>

    <?php if ($mensagemSucesso !== ''): ?><div class="alerta-sucesso"><?= ePerfil($mensagemSucesso) ?></div><?php endif; ?>
    <?php if ($mensagemErro !== ''): ?><div class="alerta-erro"><?= ePerfil($mensagemErro) ?></div><?php endif; ?>

    <form method="POST" class="form-card">
        <div class="form-grid">
            <div class="form-campo">
                <label for="nome">Nome</label>
                <input id="nome" name="nome" maxlength="150" required value="<?= ePerfil((string) ($perfil['nome'] ?? '')) ?>">
            </div>
            <div class="form-campo">
                <label for="email">E-mail</label>
                <input id="email" name="email" type="email" maxlength="150" required value="<?= ePerfil((string) ($perfil['email'] ?? '')) ?>">
            </div>

            <?php if ($tipoUsuario === 'cliente'): ?>
                <div class="form-campo">
                    <label for="nome_empresa">Empresa (opcional)</label>
                    <input id="nome_empresa" name="nome_empresa" maxlength="150" value="<?= ePerfil((string) ($perfil['nome_empresa'] ?? '')) ?>">
                </div>
                <div class="form-campo">
                    <label for="telefone">Telefone</label>
                    <input id="telefone" name="telefone" maxlength="20" value="<?= ePerfil((string) ($perfil['telefone'] ?? '')) ?>">
                </div>
                <div class="form-campo">
                    <label for="cidade">Cidade</label>
                    <input id="cidade" name="cidade" maxlength="100" value="<?= ePerfil((string) ($perfil['cidade'] ?? '')) ?>">
                </div>
                <div class="form-campo full">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao"><?= ePerfil((string) ($perfil['cliente_descricao'] ?? '')) ?></textarea>
                </div>
            <?php else: ?>
                <div class="form-campo full">
                    <label for="titulo_profissional">Título profissional</label>
                    <input id="titulo_profissional" name="titulo_profissional" maxlength="120" value="<?= ePerfil((string) ($perfil['titulo_profissional'] ?? '')) ?>" placeholder="Ex.: Desenvolvedor Full Stack">
                </div>
                <div class="form-campo full">
                    <label for="bio">Descrição profissional</label>
                    <textarea id="bio" name="bio"><?= ePerfil((string) ($perfil['bio'] ?? '')) ?></textarea>
                </div>
                <div class="form-campo full">
                    <label for="experiencia">Experiência</label>
                    <textarea id="experiencia" name="experiencia"><?= ePerfil((string) ($perfil['experiencia'] ?? '')) ?></textarea>
                </div>
                <div class="form-campo">
                    <label for="disponibilidade">Disponibilidade</label>
                    <input id="disponibilidade" name="disponibilidade" maxlength="100" value="<?= ePerfil((string) ($perfil['disponibilidade'] ?? '')) ?>">
                </div>
                <div class="form-campo">
                    <label>Faixa de preço</label>
                    <div style="display:flex;gap:8px;">
                        <input name="faixa_preco_min" type="number" min="0" step="0.01" placeholder="Mínimo" value="<?= ePerfil((string) ($perfil['faixa_preco_min'] ?? '')) ?>">
                        <input name="faixa_preco_max" type="number" min="0" step="0.01" placeholder="Máximo" value="<?= ePerfil((string) ($perfil['faixa_preco_max'] ?? '')) ?>">
                    </div>
                </div>
                <div class="form-campo full">
                    <label>Habilidades e tecnologias</label>
                    <div id="habilidades-container">
                        <?php foreach ($habilidades as $habilidade): ?>
                            <div class="habilidade-linha">
                                <input name="habilidade_nome[]" value="<?= ePerfil((string) ($habilidade['nome'] ?? '')) ?>" placeholder="Ex.: PHP">
                                <select name="habilidade_nivel[]">
                                    <?php foreach (['basico'=>'Básico','intermediario'=>'Intermediário','avancado'=>'Avançado'] as $valor=>$rotulo): ?>
                                        <option value="<?= $valor ?>" <?= (($habilidade['nivel'] ?? '') === $valor) ? 'selected' : '' ?>><?= $rotulo ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="botao-secundario remover-habilidade">Remover</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="adicionar-habilidade" class="botao-secundario" style="margin-top:8px;">+ Adicionar habilidade</button>
                </div>
            <?php endif; ?>
        </div>
        <div class="form-acoes"><button class="botao-principal" type="submit">Salvar perfil</button></div>
    </form>
</main>
<?php if ($tipoUsuario === 'desenvolvedor'): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('#habilidades-container');
    const adicionar = document.querySelector('#adicionar-habilidade');
    const criarLinha = () => {
        const linha = document.createElement('div');
        linha.className = 'habilidade-linha';
        linha.innerHTML = '<input name="habilidade_nome[]" placeholder="Ex.: JavaScript">' +
            '<select name="habilidade_nivel[]"><option value="basico">Básico</option><option value="intermediario" selected>Intermediário</option><option value="avancado">Avançado</option></select>' +
            '<button type="button" class="botao-secundario remover-habilidade">Remover</button>';
        container.appendChild(linha);
    };
    if (container && container.children.length === 0) criarLinha();
    adicionar?.addEventListener('click', criarLinha);
    container?.addEventListener('click', (evento) => {
        const alvo = evento.target;
        if (alvo instanceof HTMLElement && alvo.classList.contains('remover-habilidade')) {
            alvo.closest('.habilidade-linha')?.remove();
        }
    });
});
</script>
<?php endif; ?>
</body>
</html>
