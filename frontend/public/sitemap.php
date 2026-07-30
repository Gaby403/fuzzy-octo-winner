<?php
/**
 * sitemap.xml do Studio Tabi, montado no próprio servidor.
 *
 * Busca a lista de URLs no WordPress (studio-tabi/v1/sitemap) e guarda o
 * resultado em cache num arquivo ao lado. Assim o sitemap acompanha os
 * artigos, serviços e traduções publicados no CMS sem precisar de build:
 * o Hostinger roda PHP, e é só isso que este arquivo usa.
 *
 * Ordem de preferência ao responder:
 *   1. cache recente;
 *   2. resposta nova do WordPress;
 *   3. cache vencido (melhor um sitemap velho que nenhum);
 *   4. sitemap-fallback.xml, que vem junto com o site.
 */

const VALIDADE = 6 * 3600;          // 6 horas de cache
const TIMEOUT  = 8;                 // segundos esperando o WordPress
const CACHE    = __DIR__ . '/sitemap-cache.xml';
const RESERVA  = __DIR__ . '/sitemap-fallback.xml';

/**
 * Endereço do WordPress: lido do mesmo config.js que o site usa, para existir
 * um lugar só para editar. Só aceita https e um host bem formado.
 */
function endereco_do_cms() {
    $config = @file_get_contents(__DIR__ . '/config.js');
    if (false === $config) {
        return '';
    }
    // A última atribuição vence — as anteriores costumam ser exemplos no comentário.
    if (! preg_match_all('/^\s*window\.__STUDIO_TABI_API__\s*=\s*["\']([^"\']+)["\']/m', $config, $m)) {
        return '';
    }
    $url    = rtrim(end($m[1]), '/');
    $host   = parse_url($url, PHP_URL_HOST);
    $scheme = parse_url($url, PHP_URL_SCHEME);
    if (! $host || ! preg_match('/^[a-z0-9.-]+$/i', $host)) {
        return '';
    }
    // Em produção só https. Http fica liberado apenas para o WordPress local.
    $local = in_array($host, array('localhost', '127.0.0.1'), true);
    if ('https' !== $scheme && ! ('http' === $scheme && $local)) {
        return '';
    }
    return $url;
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

// 1. Cache ainda válido.
if (is_readable(CACHE) && (time() - filemtime(CACHE)) < VALIDADE) {
    $xml = file_get_contents(CACHE);
    if ($xml) {
        responder($xml, 'cache');
    }
}

// 2. Busca no WordPress e renova o cache.
$base = endereco_do_cms();
if ($base) {
    $xml = buscar_no_cms($base);
    if ($xml) {
        // @ porque um diretório sem permissão de escrita não pode derrubar o sitemap.
        @file_put_contents(CACHE, $xml, LOCK_EX);
        responder($xml, 'cms');
    }
}

// 3. Cache vencido serve melhor que nada.
if (is_readable(CACHE)) {
    $xml = file_get_contents(CACHE);
    if ($xml) {
        responder($xml, 'cache-vencido');
    }
}

// 4. Cópia estática publicada junto com o site.
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
