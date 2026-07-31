<?php

$config = @include __DIR__ . '/cms-config.php';
$base   = is_array($config) && ! empty($config['url']) ? rtrim($config['url'], '/') : '';
$token  = is_array($config) && ! empty($config['token']) ? (string) $config['token'] : '';

$host   = parse_url($base, PHP_URL_HOST);
$scheme = parse_url($base, PHP_URL_SCHEME);
$local  = in_array($host, array('localhost', '127.0.0.1'), true);
if (! $host || ! preg_match('/^[a-z0-9.-]+$/i', $host) || ('https' !== $scheme && ! ('http' === $scheme && $local))) {
    $base = '';
}

const ROTAS = array(
    '#^content$#'                    => array('GET'),
    '#^pages$#'                      => array('GET'),
    '#^page/[a-zA-Z0-9\-_%]+$#'      => array('GET'),
    '#^posts$#'                      => array('GET'),
    '#^post/[a-zA-Z0-9\-_%]+$#'      => array('GET'),
    '#^categories$#'                 => array('GET'),
    '#^sitemap$#'                    => array('GET'),
    '#^contact$#'                    => array('POST'),
    '#^subscribe$#'                  => array('POST'),
);

const PARAMETROS = array(
    'lang'     => '#^(pt|en)$#',
    'page'     => '#^[0-9]{1,4}$#',
    'per_page' => '#^[0-9]{1,3}$#',
    'category' => '#^[a-zA-Z0-9\-_%]{1,80}$#',
    'search'   => '#^.{0,120}$#u',
);

function recusar($status, $mensagem) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('ok' => false, 'message' => $mensagem));
    exit;
}

if ('' === $base) {
    recusar(503, 'CMS não configurado.');
}

$pedido = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
$rota   = '';
if (preg_match('#/wp-json/studio-tabi/v1/(.+)$#', (string) $pedido, $m)) {
    $rota = trim($m[1], '/');
}
if ('' === $rota) {
    recusar(404, 'Rota não encontrada.');
}

$metodo   = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
$liberada = false;
foreach (ROTAS as $padrao => $metodos) {
    if (preg_match($padrao, $rota)) {
        if (! in_array($metodo, $metodos, true)) {
            recusar(405, 'Método não permitido.');
        }
        $liberada = true;
        break;
    }
}
if (! $liberada) {
    recusar(404, 'Rota não encontrada.');
}

$query = array();
foreach (PARAMETROS as $nome => $formato) {
    if (isset($_GET[$nome]) && is_string($_GET[$nome]) && preg_match($formato, $_GET[$nome])) {
        $query[$nome] = $_GET[$nome];
    }
}

$destino = $base . '/wp-json/studio-tabi/v1/' . $rota;
if ($query) {
    $destino .= '?' . http_build_query($query);
}

$corpo = '';
if ('POST' === $metodo) {
    $corpo = file_get_contents('php://input');
    if (strlen((string) $corpo) > 64 * 1024) {
        recusar(413, 'Conteúdo muito grande.');
    }
}

$ip = isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)
    ? $_SERVER['REMOTE_ADDR']
    : '0.0.0.0';

$cabecalhos = array(
    'Accept: application/json',
    'Origin: ' . (isset($_SERVER['HTTP_HOST']) ? 'https://' . preg_replace('/[^a-z0-9.:-]/i', '', $_SERVER['HTTP_HOST']) : ''),
    'X-STCMS-Client-IP: ' . $ip,
);
if ($token) {
    $cabecalhos[] = 'X-STCMS-Proxy: ' . $token;
}
if ('POST' === $metodo) {
    $cabecalhos[] = 'Content-Type: application/json';
}

$resposta = '';
$status   = 0;
$tipo     = 'application/json; charset=utf-8';

if (function_exists('curl_init')) {
    $ch = curl_init($destino);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_HTTPHEADER     => $cabecalhos,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_USERAGENT      => 'StudioTabi-Proxy/1.0',
    ));
    if ('POST' === $metodo) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $corpo);
    }
    $resposta = curl_exec($ch);
    $status   = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $recebido = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    if ($recebido) {
        $tipo = $recebido;
    }
    curl_close($ch);
} else {
    $ctx = stream_context_create(array('http' => array(
        'method'        => $metodo,
        'header'        => implode("\r\n", $cabecalhos),
        'content'       => $corpo,
        'timeout'       => 15,
        'ignore_errors' => true,
    )));
    $resposta = @file_get_contents($destino, false, $ctx);
    if (isset($http_response_header[0]) && preg_match('#\s(\d{3})\s#', $http_response_header[0], $m)) {
        $status = (int) $m[1];
    }
}

if (false === $resposta || ! $status) {
    recusar(502, 'Não foi possível falar com o CMS.');
}

http_response_code($status);
header('Content-Type: ' . $tipo);
header('Cache-Control: ' . ('GET' === $metodo ? 'public, max-age=120' : 'no-store'));
echo $resposta;
