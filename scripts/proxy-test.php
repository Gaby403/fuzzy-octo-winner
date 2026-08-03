<?php

$raiz    = dirname( __DIR__ );
$publico = $raiz . '/frontend/public';
$tmp     = sys_get_temp_dir() . '/stcms-proxy-test-' . getmypid();
@mkdir( $tmp, 0777, true );

$ok = 0;
$fail = 0;
function checa( $nome, $condicao, $detalhe = '' ) {
	global $ok, $fail;
	if ( $condicao ) {
		$ok++;
		echo "  ok  {$nome}\n";
	} else {
		$fail++;
		echo "FAIL  {$nome}" . ( $detalhe ? " — {$detalhe}" : '' ) . "\n";
	}
}

file_put_contents( $tmp . '/cms.php', <<<'PHP'
<?php
$corpo = file_get_contents('php://input');
$cabecalhos = array();
foreach ($_SERVER as $k => $v) {
    if (strpos($k, 'HTTP_') === 0) { $cabecalhos[$k] = $v; }
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode(array(
    'ok'          => true,
    'path'        => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    'query'       => $_GET,
    'method'      => $_SERVER['REQUEST_METHOD'],
    'body_len'    => strlen($corpo),
    'origin'      => $cabecalhos['HTTP_ORIGIN'] ?? '',
    'client_ip'   => $cabecalhos['HTTP_X_STCMS_CLIENT_IP'] ?? '',
    'proxy_token' => $cabecalhos['HTTP_X_STCMS_PROXY'] ?? '',
));
PHP
);

file_put_contents( $tmp . '/router.php', <<<'PHP'
<?php
$caminho = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (preg_match('#^/wp-json/studio-tabi/v1/#', $caminho)) {
    require __DIR__ . '/public/api.php';
    return true;
}
return false;
PHP
);

@mkdir( $tmp . '/public', 0777, true );
copy( $publico . '/api.php', $tmp . '/public/api.php' );

function porta_livre() {
	$s = stream_socket_server( 'tcp://127.0.0.1:0', $errno, $errstr );
	$nome = stream_socket_get_name( $s, false );
	fclose( $s );
	return (int) substr( $nome, strrpos( $nome, ':' ) + 1 );
}

function esperar_http( $porta, $caminho = '/' ) {
	for ( $i = 0; $i < 100; $i++ ) {
		$ch = curl_init( "http://127.0.0.1:{$porta}{$caminho}" );
		curl_setopt_array( $ch, array( CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 2, CURLOPT_NOBODY => true ) );
		curl_exec( $ch );
		$code = (int) curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
		curl_close( $ch );
		if ( $code > 0 ) {
			return true;
		}
		usleep( 100000 );
	}
	return false;
}

$porta_cms  = porta_livre();
$porta_site = porta_livre();

file_put_contents(
	$tmp . '/public/cms-config.php',
	"<?php return array( 'url' => 'http://127.0.0.1:{$porta_cms}', 'token' => 'chave-de-teste' );\n"
);

$cms  = proc_open( "exec php -S 127.0.0.1:{$porta_cms} " . escapeshellarg( $tmp . '/cms.php' ), array( 1 => array( 'file', '/dev/null', 'w' ), 2 => array( 'file', '/dev/null', 'w' ) ), $p1 );
$site = proc_open( "exec php -S 127.0.0.1:{$porta_site} -t " . escapeshellarg( $tmp ) . ' ' . escapeshellarg( $tmp . '/router.php' ), array( 1 => array( 'file', '/dev/null', 'w' ), 2 => array( 'file', '/dev/null', 'w' ) ), $p2 );

if ( ! esperar_http( $porta_cms ) || ! esperar_http( $porta_site, '/wp-json/studio-tabi/v1/content' ) ) {
	echo "os servidores de teste não subiram\n";
	exit( 1 );
}

function chamar( $caminho, $metodo = 'GET', $corpo = null, $seguir = false ) {
	global $porta_site;
	$ch = curl_init( "http://127.0.0.1:{$porta_site}{$caminho}" );
	curl_setopt_array( $ch, array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER         => true,
		CURLOPT_TIMEOUT        => 10,
		CURLOPT_FOLLOWLOCATION => $seguir,
		CURLOPT_CUSTOMREQUEST  => $metodo,
	) );
	if ( null !== $corpo ) {
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $corpo );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
	}
	$bruto  = curl_exec( $ch );
	$status = (int) curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
	$tam    = (int) curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
	curl_close( $ch );
	return array(
		'status'     => $status,
		'cabecalhos' => substr( (string) $bruto, 0, $tam ),
		'corpo'      => substr( (string) $bruto, $tam ),
	);
}

echo "\n== Rotas liberadas ==\n";
foreach ( array( 'content', 'pages', 'posts', 'categories', 'sitemap', 'page/sobre', 'post/meu-artigo' ) as $rota ) {
	$r = chamar( "/wp-json/studio-tabi/v1/{$rota}" );
	$j = json_decode( $r['corpo'], true );
	checa( "GET {$rota} chega ao CMS", 200 === $r['status'] && isset( $j['path'] ) && $j['path'] === "/wp-json/studio-tabi/v1/{$rota}", $r['corpo'] );
}

echo "\n== Rotas bloqueadas ==\n";
foreach ( array( 'users', 'settings', 'newsletter', '../../wp-admin/index.php', 'page/../../secret' ) as $rota ) {
	$r = chamar( '/wp-json/studio-tabi/v1/' . $rota );
	checa( "recusa {$rota}", 404 === $r['status'] || 400 === $r['status'], 'status ' . $r['status'] );
}

echo "\n== Métodos ==\n";
$r = chamar( '/wp-json/studio-tabi/v1/content', 'POST', '{}' );
checa( 'POST em rota só de leitura devolve 405', 405 === $r['status'], 'status ' . $r['status'] );
$r = chamar( '/wp-json/studio-tabi/v1/contact' );
checa( 'GET em rota só de escrita devolve 405', 405 === $r['status'], 'status ' . $r['status'] );
$r = chamar( '/wp-json/studio-tabi/v1/contact', 'POST', '{"name":"a"}' );
$j = json_decode( $r['corpo'], true );
checa( 'POST /contact é repassado', 200 === $r['status'] && 'POST' === ( $j['method'] ?? '' ) );
checa( 'corpo do POST chega inteiro', 12 === ( $j['body_len'] ?? 0 ), 'len ' . ( $j['body_len'] ?? '?' ) );

echo "\n== Parâmetros ==\n";
$r = chamar( '/wp-json/studio-tabi/v1/posts?lang=en&page=2&per_page=9&category=design&search=marca' );
$j = json_decode( $r['corpo'], true );
checa( 'lang passa', 'en' === ( $j['query']['lang'] ?? '' ) );
checa( 'page passa', '2' === ( $j['query']['page'] ?? '' ) );
checa( 'per_page passa', '9' === ( $j['query']['per_page'] ?? '' ) );
checa( 'category passa', 'design' === ( $j['query']['category'] ?? '' ) );
checa( 'search passa', 'marca' === ( $j['query']['search'] ?? '' ) );

$r = chamar( '/wp-json/studio-tabi/v1/posts?lang=de&page=abc&_fields=id&rest_route=/wp/v2/users' );
$j = json_decode( $r['corpo'], true );
checa( 'lang inválido é descartado', ! isset( $j['query']['lang'] ) );
checa( 'page inválido é descartado', ! isset( $j['query']['page'] ) );
checa( 'parâmetro desconhecido não passa', ! isset( $j['query']['_fields'] ) && ! isset( $j['query']['rest_route'] ) );

echo "\n== Cabeçalhos de identificação ==\n";
$r = chamar( '/wp-json/studio-tabi/v1/content' );
$j = json_decode( $r['corpo'], true );
checa( 'envia a chave do proxy', 'chave-de-teste' === ( $j['proxy_token'] ?? '' ) );
checa( 'envia o IP do visitante', filter_var( $j['client_ip'] ?? '', FILTER_VALIDATE_IP ) !== false, (string) ( $j['client_ip'] ?? '' ) );
checa( 'envia a origem do próprio site', 0 === strpos( (string) ( $j['origin'] ?? '' ), 'https://127.0.0.1' ), (string) ( $j['origin'] ?? '' ) );

echo "\n== Corpo grande ==\n";
$r = chamar( '/wp-json/studio-tabi/v1/contact', 'POST', str_repeat( 'x', 70 * 1024 ) );
checa( 'POST acima de 64 KiB é recusado', 413 === $r['status'], 'status ' . $r['status'] );

echo "\n== Cache ==\n";
$r = chamar( '/wp-json/studio-tabi/v1/content' );
checa( 'leitura é cacheável', (bool) preg_match( '/Cache-Control:\s*public/i', $r['cabecalhos'] ) );
$r = chamar( '/wp-json/studio-tabi/v1/subscribe', 'POST', '{"email":"a@b.co"}' );
checa( 'escrita não é cacheável', (bool) preg_match( '/Cache-Control:\s*no-store/i', $r['cabecalhos'] ) );


echo "\n== Configuração inválida ==\n";
file_put_contents( $tmp . '/public/cms-config.php', "<?php return array( 'url' => '', 'token' => '' );\n" );
clearstatcache();
$r = chamar( '/wp-json/studio-tabi/v1/content' );
checa( 'sem URL do CMS devolve 503', 503 === $r['status'], 'status ' . $r['status'] );

file_put_contents( $tmp . '/public/cms-config.php', "<?php return array( 'url' => 'http://cms.exemplo.com.br', 'token' => '' );\n" );
clearstatcache();
$r = chamar( '/wp-json/studio-tabi/v1/content' );
checa( 'http em domínio público é recusado', 503 === $r['status'], 'status ' . $r['status'] );

file_put_contents( $tmp . '/public/cms-config.php', "<?php return array( 'url' => 'javascript:alert(1)', 'token' => '' );\n" );
clearstatcache();

foreach ( array( $p1, $p2 ) as $p ) {
	if ( is_resource( $p ) ) {
		$info = proc_get_status( $p );
		if ( ! empty( $info['pid'] ) ) {
			@exec( 'pkill -P ' . (int) $info['pid'] . ' 2>/dev/null' );
		}
		proc_terminate( $p );
		proc_close( $p );
	}
}
@exec( 'rm -rf ' . escapeshellarg( $tmp ) );

echo "\n{$ok} passaram, {$fail} falharam\n";
exit( $fail ? 1 : 0 );
