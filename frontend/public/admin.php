<?php
$cfg  = @include __DIR__ . '/cms-config.php';
$base = is_array($cfg) && ! empty($cfg['url']) ? rtrim($cfg['url'], '/') : '';

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
