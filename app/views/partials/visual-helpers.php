<?php
/* ==========================================================
   HELPERS VISUAIS - SITEMATCH
   ==========================================================
   Centraliza decisões puramente visuais para placeholders,
   capas de projetos e avatares padrão.
   Não executa regras de negócio nem altera contratos do PHP.
   ========================================================== */

if (!function_exists('smVisualSlug')) {
    function smVisualSlug(string $texto): string
    {
        $texto = strtolower(trim($texto));
        $mapa = [
            'á'=>'a','à'=>'a','ã'=>'a','â'=>'a','ä'=>'a',
            'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
            'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
            'ó'=>'o','ò'=>'o','õ'=>'o','ô'=>'o','ö'=>'o',
            'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
            'ç'=>'c'
        ];
        $texto = strtr($texto, $mapa);
        $texto = preg_replace('/[^a-z0-9]+/u', ' ', $texto) ?? $texto;
        return trim($texto);
    }
}

if (!function_exists('smInitials')) {
    function smInitials(string $nome): string
    {
        $partes = preg_split('/\s+/u', trim($nome)) ?: [];
        $iniciais = '';
        foreach ($partes as $parte) {
            if ($parte === '') continue;
            $inicial = preg_match('/^./u', $parte, $m) ? $m[0] : '';
            $iniciais .= strtoupper($inicial);
            if (preg_match_all('/./u', $iniciais) >= 2) break;
        }
        return $iniciais !== '' ? $iniciais : 'SM';
    }
}

if (!function_exists('smAvatarPath')) {
    function smAvatarPath(string $tipo = 'user'): string
    {
        return match ($tipo) {
            'cliente' => '/img/placeholders/avatar-cliente.svg',
            'desenvolvedor' => '/img/placeholders/avatar-desenvolvedor.svg',
            default => '/img/placeholders/avatar-user.svg',
        };
    }
}

if (!function_exists('smAvatarFromRecord')) {
    function smAvatarFromRecord(array $dados, string $tipoPadrao = 'user'): string
    {
        foreach (['foto', 'foto_perfil', 'avatar', 'imagem', 'url_foto'] as $campo) {
            $valor = trim((string)($dados[$campo] ?? ''));
            if ($valor !== '') {
                return $valor;
            }
        }
        return smAvatarPath($tipoPadrao);
    }
}

if (!function_exists('smProjectVisual')) {
    function smProjectVisual(string $titulo, string $categoria = '', string $descricao = ''): array
    {
        $texto = smVisualSlug($titulo . ' ' . $categoria . ' ' . $descricao);
        $mapa = [
            'pet' => ['petshop', 'pet shop', 'banho e tosa', 'veterinario', 'cachorro', 'gato', 'pet'],
            'school' => ['escola', 'educacao', 'curso', 'colegio', 'matricula'],
            'burger' => ['hamburguer', 'hamburgueria', 'burger', 'lanche', 'lanchonete', 'cardapio', 'restaurante', 'food'],
            'fashion' => ['roupa', 'roupas', 'moda', 'vestuario', 'camiseta', 'loja de roupas', 'fashion'],
            'portfolio' => ['portfolio', 'portifolio', 'curriculo', 'pessoal', 'apresentacao'],
            'mobile' => ['mobile', 'aplicativo', 'app', 'ios', 'android'],
            'webapp' => ['sistema', 'dashboard', 'painel', 'gestao', 'web', 'software', 'erp'],
            'business' => ['site institucional', 'institucional', 'empresa', 'negocio', 'servico', 'site'],
            'ecommerce' => ['ecommerce', 'e commerce', 'loja virtual', 'catalogo', 'vendas', 'checkout', 'produto'],
        ];

        $tema = 'business';
        foreach ($mapa as $candidato => $palavras) {
            foreach ($palavras as $palavra) {
                if (str_contains($texto, smVisualSlug($palavra))) {
                    $tema = $candidato;
                    break 2;
                }
            }
        }

        if ($categoria === 'aplicativo_mobile' && $tema === 'business') {
            $tema = 'mobile';
        } elseif (($categoria === 'sistema_web' || $categoria === 'sistema_desktop') && $tema === 'business') {
            $tema = 'webapp';
        } elseif ($categoria === 'site' && $tema === 'business') {
            $tema = str_contains($texto, 'loja') ? 'ecommerce' : 'business';
        }

        $rotulos = [
            'pet' => 'Capa ilustrativa de projeto para pet shop',
            'school' => 'Capa ilustrativa de projeto educacional',
            'burger' => 'Capa ilustrativa de projeto para hamburgueria',
            'fashion' => 'Capa ilustrativa de projeto para loja de roupas',
            'portfolio' => 'Capa ilustrativa de projeto de portfólio pessoal',
            'mobile' => 'Capa ilustrativa de aplicativo mobile',
            'webapp' => 'Capa ilustrativa de sistema web',
            'business' => 'Capa ilustrativa de site institucional',
            'ecommerce' => 'Capa ilustrativa de loja virtual',
        ];

        return [
            'theme' => $tema,
            'src' => '/img/placeholders/project-' . $tema . '.svg',
            'alt' => $rotulos[$tema] ?? 'Imagem ilustrativa do projeto',
        ];
    }
}
