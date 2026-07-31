<?php
/**
 * Atalho para o painel do WordPress.
 *
 * Existe para o endereço do CMS não precisar aparecer no código do site: o
 * redirecionamento acontece no servidor. Quem clica vê a URL só depois, na
 * barra do navegador — o que é inevitável e não expõe nada a mais.
 */
$cfg  = @include __DIR__ . '/cms-config.php';
$base = is_array($cfg) && ! empty($cfg['url']) ? rtrim($cfg['url'], '/') : '';

// Só redireciona para um endereço bem formado — nada de mandar o navegador
// para um destino que veio torto do arquivo de configuração.
$host   = parse_url($base, PHP_URL_HOST);
$scheme = parse_url($base, PHP_URL_SCHEME);
$local  = in_array($host, array('localhost', '127.0.0.1'), true);
if (! $host || ! preg_match('/^[a-z0-9.:-]+$/i', $host) || ('https' !== $scheme && ! ('http' === $scheme && $local))) {
    $base = '';
}

if ('' === $base) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Painel não configurado: defina a URL do WordPress no cms-config.php.';
    exit;
}

header('Cache-Control: no-store');
header('Location: ' . $base . '/wp-admin/', true, 302);
