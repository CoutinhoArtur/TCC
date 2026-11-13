<?php
// ==========================================================
// LANDING PAGE - SITEMATCH
// ==========================================================
// Visitantes veem a apresentação da plataforma.
// Usuários autenticados seguem direto para o painel.
// ==========================================================

session_start();

if (isset($_SESSION['usuario'])) {
    header('Location: painel.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiteMatch - Conecte ideias a profissionais</title>
    <link rel="stylesheet" href="/css/global.css">
    <style>
        .landing { min-height:100vh; background:#f3f5ff; }
        .landing-nav { height:72px; display:flex; align-items:center; gap:44px; padding:0 28px; border-bottom:1px solid #dedde4; background:#fff; }
        .landing-nav .site-logo { font-size:21px; }
        .landing-links { display:flex; gap:42px; margin-left:auto; align-items:center; }
        .landing-links a { font-weight:700; }
        .landing-links .cadastro { padding:10px 28px; border-radius:8px; color:#fff; background:#a688f0; }
        .landing-hero { width:min(1200px, calc(100% - 56px)); margin:0 auto; min-height:calc(100vh - 72px); display:grid; grid-template-columns:1fr 1.06fr; gap:40px; align-items:center; padding:62px 0 48px; }
        .landing-kicker { display:inline-flex; padding:9px 20px; margin-bottom:24px; border-radius:14px; color:#6d2cff; background:#e7ddff; font-weight:800; font-size:14px; }
        .landing-copy h1 { max-width:520px; margin-bottom:30px; font-size:clamp(48px,6vw,68px); line-height:.98; letter-spacing:-2px; }
        .landing-copy h1 span { color:#7138ff; }
        .landing-copy p { max-width:570px; margin-bottom:34px; font-size:17px; font-weight:700; line-height:1.35; }
        .landing-actions { display:flex; gap:12px; margin-bottom:34px; }
        .landing-actions a { min-width:210px; }
        .landing-benefits { display:flex; gap:40px; flex-wrap:wrap; }
        .benefit strong { display:block; margin-top:5px; }
        .benefit-icon { color:#7f29ff; font-size:28px; }
        .hero-art { position:relative; min-height:520px; }
        .hero-blob { position:absolute; inset:30px 10px 28px 40px; border-radius:36% 64% 55% 45% / 35% 40% 60% 65%; background:linear-gradient(145deg,#e9d9cd,#c9b7aa); transform:rotate(-4deg); }
        .laptop { position:absolute; left:90px; right:90px; top:110px; height:295px; padding:16px 16px 28px; border:12px solid #1e232b; border-radius:20px 20px 16px 16px; background:#e7e8ec; transform:rotate(7deg); box-shadow:0 28px 35px rgba(16,18,25,.25); }
        .laptop-screen { height:100%; overflow:hidden; border-radius:4px; padding:28px; color:#fff; background:linear-gradient(145deg,#2c477d,#222c68); }
        .screen-brand { font-size:12px; font-weight:800; }
        .screen-title { max-width:220px; margin-top:28px; font-size:31px; line-height:1.05; }
        .screen-pill { display:inline-block; margin-top:18px; padding:8px 14px; border-radius:20px; color:#4a3a91; background:#e7e1ff; font-size:11px; }
        .phone { position:absolute; right:26px; top:220px; width:118px; height:230px; padding:12px 9px; border:8px solid #12151b; border-radius:25px; background:#fff; transform:rotate(8deg); box-shadow:0 20px 28px rgba(20,20,30,.22); }
        .phone-inner { height:100%; padding:24px 10px; border-radius:14px; background:linear-gradient(155deg,#f9fbff,#dce5ff); }
        .phone-inner strong { display:block; color:#4d38a7; font-size:14px; line-height:1.1; }
        .hero-note { position:absolute; right:18px; top:12px; color:#6530ff; font-size:14px; font-style:italic; transform:rotate(8deg); }
        .hero-note::after { content:"↘"; display:block; margin-left:62px; font-size:48px; font-weight:300; }
        @media(max-width:850px){ .landing-nav{gap:16px}.landing-links a:not(.cadastro){display:none}.landing-hero{grid-template-columns:1fr;padding-top:38px}.hero-art{min-height:420px}.landing-copy h1{font-size:50px}.laptop{left:45px;right:90px;top:70px}.phone{top:175px}.landing-benefits{gap:22px} }
        @media(max-width:520px){ .landing-nav{padding-inline:16px}.landing-hero{width:min(100% - 28px,1200px)}.landing-copy h1{font-size:43px}.landing-actions{flex-direction:column}.landing-actions a{width:100%}.hero-art{min-height:340px}.laptop{left:20px;right:60px;height:225px}.phone{width:92px;height:180px;right:0}.hero-note{display:none} }
    </style>
</head>
<body>
<div class="landing">
    <nav class="landing-nav">
        <a class="site-logo" href="/"><span class="brand-mark"></span><span>Site<span>Match</span></span></a>
        <div class="landing-links">
            <a href="#inicio">Início</a>
            <a href="/login.php">Explorar</a>
            <a href="#como-funciona">Como funciona</a>
            <a href="/login.php">Entrar</a>
            <a class="cadastro" href="/cadastro.php">Cadastrar</a>
        </div>
    </nav>

    <main class="landing-hero" id="inicio">
        <section class="landing-copy">
            <span class="landing-kicker">Conecte ideias. Crie sites</span>
            <h1>Seu site,<br>com o <span>profissional certo.</span></h1>
            <p>O SiteMatch facilita o contato entre empreendedores que querem projetos digitais e desenvolvedores prontos para criá-los.</p>
            <div class="landing-actions">
                <a class="botao-principal" href="/cadastro.php?tipo=cliente">Sou empreendedor</a>
                <a class="botao-secundario" href="/cadastro.php?tipo=desenvolvedor">Sou desenvolvedor</a>
            </div>
            <div class="landing-benefits" id="como-funciona">
                <div class="benefit"><span class="benefit-icon">♙</span><strong>Conexão direta</strong></div>
                <div class="benefit"><span class="benefit-icon">♙</span><strong>Projetos reais</strong></div>
                <div class="benefit"><span class="benefit-icon">⬟</span><strong>Seguro e confiável</strong></div>
            </div>
        </section>

        <section class="hero-art" aria-label="Visual da plataforma SiteMatch">
            <div class="hero-blob"></div>
            <div class="hero-note">Dê seu jeito com o<br>profissional ideal</div>
            <div class="laptop">
                <div class="laptop-screen">
                    <div class="screen-brand">SiteMatch</div>
                    <div class="screen-title">Transforme sua ideia em realidade digital</div>
                    <span class="screen-pill">Buscar profissionais</span>
                </div>
            </div>
            <div class="phone"><div class="phone-inner"><strong>Transforme sua ideia em realidade digital.</strong></div></div>
        </section>
    </main>
</div>
</body>
</html>
