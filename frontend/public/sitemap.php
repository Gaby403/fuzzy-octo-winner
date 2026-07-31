<?php

const VALIDADE = 6 * 3600;
const TIMEOUT  = 8;
const CACHE    = __DIR__ . '/sitemap-cache.xml';
const RESERVA  = __DIR__ . '/sitemap-fallback.xml';

function endereco_valido($url) {
    $url    = rtrim((string) $url, '/');
    $host   = parse_url($url, PHP_URL_HOST);
    $scheme = parse_url($url, PHP_URL_SCHEME);
    if (! $host || ! preg_match('/^[a-z0-9.-]+$/i', $host)) {
        return '';
    }
    $local = in_array($host, array('localhost', '127.0.0.1'), true);
    if ('https' !== $scheme && ! ('http' === $scheme && $local)) {
        return '';
    }
    return $url;
}

function endereco_do_cms() {
    $cfg = @include __DIR__ . '/cms-config.php';
    if (is_array($cfg) && ! empty($cfg['url'])) {
        $url = endereco_valido($cfg['url']);
        if ('' !== $url) {
            return $url;
        }
    }
    $config = @file_get_contents(__DIR__ . '/config.js');
    if (false === $config) {
        return '';
    }
    if (! preg_match_all('/^\s*window\.__STUDIO_TABI_API__\s*=\s*["\']([^"\']+)["\']/m', $config, $m)) {
        return '';
    }
    return endereco_valido(end($m[1]));
}

function buscar_no_cms($base) {
    $url = $base . '/wp-json/studio-tabi/v1/sitemap';

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => TIMEOUT,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_HTTPHEADER     => array('Accept: application/json'),
            CURLOPT_USERAGENT      => 'StudioTabi-Sitemap/1.0',
        ));
        $corpo  = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if (200 !== $status || ! $corpo) {
            return '';
        }
    } else {
        $ctx = stream_context_create(array('http' => array(
            'timeout' => TIMEOUT,
            'header'  => "Accept: application/json\r\n",
        )));
        $corpo = @file_get_contents($url, false, $ctx);
        if (false === $corpo) {
            return '';
        }
    }

    $dados = json_decode($corpo, true);
    if (! is_array($dados) || empty($dados['xml']) || false === strpos($dados['xml'], '<urlset')) {
        return '';
    }
    return $dados['xml'];
}

function responder($xml, $origem) {
    header('Content-Type: application/xml; charset=utf-8');
    header('X-Sitemap-Origem: ' . $origem);
    header('Cache-Control: public, max-age=3600');
    echo $xml;
    exit;
}

if (is_readable(CACHE) && (time() - filemtime(CACHE)) < VALIDADE) {
    $xml = file_get_contents(CACHE);
    if ($xml) {
        responder($xml, 'cache');
    }
}

$base = endereco_do_cms();
if ($base) {
    $xml = buscar_no_cms($base);
    if ($xml) {
        @file_put_contents(CACHE, $xml, LOCK_EX);
        responder($xml, 'cms');
    }
}

if (is_readable(CACHE)) {
    $xml = file_get_contents(CACHE);
    if ($xml) {
        responder($xml, 'cache-vencido');
    }
}

if (is_readable(RESERVA)) {
    $xml = file_get_contents(RESERVA);
    if ($xml) {
        responder($xml, 'reserva');
    }
}

header('Content-Type: application/xml; charset=utf-8');
http_response_code(503);
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
    . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>' . "\n";
