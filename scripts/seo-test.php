<?php

$raiz = dirname( __DIR__ );
$dist = $raiz . '/frontend/dist';
$tmp  = sys_get_temp_dir() . '/stcms-seo-test-' . getmypid();
@mkdir( $tmp, 0777, true );

$ok   = 0;
$fail = 0;
function checa( $nome, $cond, $detalhe = '' ) {
	global $ok, $fail;
	if ( $cond ) {
		$ok++;
		echo "  ok  {$nome}\n";
	} else {
		$fail++;
		echo "FAIL  {$nome}" . ( $detalhe ? " — {$detalhe}" : '' ) . "\n";
	}
}

if ( ! is_dir( $dist ) ) {
	echo "dist ausente: rode npm run build antes\n";
	exit( 1 );
}

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

$DESC_PT = 'Texto NOVO do CMS em português para a prévia.';
$DESC_EN = 'BRAND NEW English preview text from the CMS.';

file_put_contents(
	$tmp . '/cms.php',
	'<?php
$lang = isset($_GET["lang"]) && $_GET["lang"] === "en" ? "en" : "pt";
if (getenv("STCMS_FORA") === "1") { http_response_code(500); echo "fora do ar"; exit; }
header("Content-Type: application/json");
echo json_encode(array("site" => array(
    "title" => $lang === "en" ? "Studio Tabi EN" : "Studio Tabi PT",
    "metaDescription" => $lang === "en" ? ' . var_export( $DESC_EN, true ) . ' : ' . var_export( $DESC_PT, true ) . ',
    "logoUrl" => "https://cms.exemplo/logo.png",
)));'
);

// Serve uma cópia do dist com o cms-config apontando para o CMS falso.
@exec( 'cp -r ' . escapeshellarg( $dist ) . ' ' . escapeshellarg( $tmp . '/site' ) );
file_put_contents(
	$tmp . '/site/cms-config.php',
	"<?php return array( 'url' => 'http://127.0.0.1:{$porta_cms}', 'token' => '' );\n"
);
file_put_contents(
	$tmp . '/router.php',
	'<?php
$c = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$f = __DIR__ . "/site" . $c;
if ($c !== "/" && is_file($f)) { return false; }
require __DIR__ . "/site/seo.php";
return true;'
);

$cms  = proc_open( "exec php -S 127.0.0.1:{$porta_cms} " . escapeshellarg( $tmp . '/cms.php' ), array( 1 => array( 'file', '/dev/null', 'w' ), 2 => array( 'file', '/dev/null', 'w' ) ), $p1 );
$site = proc_open( "exec php -S 127.0.0.1:{$porta_site} -t " . escapeshellarg( $tmp . '/site' ) . ' ' . escapeshellarg( $tmp . '/router.php' ), array( 1 => array( 'file', '/dev/null', 'w' ), 2 => array( 'file', '/dev/null', 'w' ) ), $p2 );

if ( ! esperar_http( $porta_cms ) || ! esperar_http( $porta_site ) ) {
	echo "os servidores de teste não subiram\n";
	exit( 1 );
}

function pegar( $caminho ) {
	global $porta_site;
	$ch = curl_init( "http://127.0.0.1:{$porta_site}{$caminho}" );
	curl_setopt_array( $ch, array( CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10 ) );
	$r = curl_exec( $ch );
	$s = (int) curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
	curl_close( $ch );
	return array( 'status' => $s, 'html' => (string) $r );
}

function meta( $html, $attr, $nome ) {
	if ( preg_match( '/<meta\s+[^>]*' . $attr . '=["\']' . preg_quote( $nome, '/' ) . '["\'][^>]*content=["\']([^"\']*)["\']/i', $html, $m ) ) {
		return $m[1];
	}
	return null;
}
function conta_meta( $html, $attr, $nome ) {
	return preg_match_all( '/<meta\s+[^>]*' . $attr . '=["\']' . preg_quote( $nome, '/' ) . '["\']/i', $html );
}

echo "\n== Português ==\n";
$r = pegar( '/' );
checa( 'home responde 200', 200 === $r['status'], 'status ' . $r['status'] );
checa( 'description vem do CMS', meta( $r['html'], 'name', 'description' ) === $DESC_PT, (string) meta( $r['html'], 'name', 'description' ) );
checa( 'og:description vem do CMS', meta( $r['html'], 'property', 'og:description' ) === $DESC_PT );
checa( 'twitter:description vem do CMS', meta( $r['html'], 'name', 'twitter:description' ) === $DESC_PT );
checa( 'title vem do CMS', (bool) preg_match( '#<title>Studio Tabi PT</title>#', $r['html'] ) );
checa( 'og:title vem do CMS', meta( $r['html'], 'property', 'og:title' ) === 'Studio Tabi PT' );

echo "\n== Inglês ==\n";
$r = pegar( '/en' );
checa( '/en responde 200', 200 === $r['status'], 'status ' . $r['status'] );
checa( 'description em inglês', meta( $r['html'], 'name', 'description' ) === $DESC_EN, (string) meta( $r['html'], 'name', 'description' ) );
checa( 'og:description em inglês', meta( $r['html'], 'property', 'og:description' ) === $DESC_EN );
checa( 'title em inglês', (bool) preg_match( '#<title>Studio Tabi EN</title>#', $r['html'] ) );

echo "\n== Sem tags duplicadas ==\n";
$r = pegar( '/' );
foreach ( array( array( 'name', 'description' ), array( 'property', 'og:description' ), array( 'property', 'og:title' ), array( 'name', 'twitter:card' ), array( 'property', 'og:type' ) ) as $p ) {
	checa( "só uma {$p[1]}", 1 === conta_meta( $r['html'], $p[0], $p[1] ), conta_meta( $r['html'], $p[0], $p[1] ) . ' encontradas' );
}

echo "\n== Rotas internas ==\n";
foreach ( array( '/sobre', '/servicos', '/blog', '/en/about', '/en/services' ) as $rota ) {
	$r = pegar( $rota );
	$esperado = 0 === strpos( $rota, '/en' ) ? $DESC_EN : $DESC_PT;
	checa( "{$rota} com a prévia certa", 200 === $r['status'] && meta( $r['html'], 'name', 'description' ) === $esperado );
}

echo "\n== O HTML da rota certa é servido ==\n";
$r = pegar( '/sobre' );
checa( '/sobre traz o conteúdo de /sobre', false !== strpos( $r['html'], 'canonical' ) && (bool) preg_match( '#canonical" href="[^"]*/sobre#', $r['html'] ), 'canonical inesperado' );
$r = pegar( '/en/about' );
checa( '/en/about traz o conteúdo de /en/about', (bool) preg_match( '#canonical" href="[^"]*/en/about#', $r['html'] ) );

echo "\n== Segurança ==\n";
$r = pegar( '/../../etc/passwd' );
checa( 'travessia de caminho não escapa da pasta', false === strpos( $r['html'], 'root:x:' ), 'vazou /etc/passwd' );
$r = pegar( '/rota-que-nao-existe' );
checa( 'rota desconhecida devolve 404 com a página do app', 404 === $r['status'] && false !== strpos( $r['html'], '<div id="root">' ), 'status ' . $r['status'] );


echo "\n== Status 404 em rota que não existe ==\n";
foreach ( array( '/login', '/wp-login.php', '/wp-admin', '/painel', '/admin', '/nao-existe', '/en/login' ) as $rota ) {
	$r = pegar( $rota );
	checa( "{$rota} devolve 404", 404 === $r['status'], 'status ' . $r['status'] );
}

echo "\n== As rotas de verdade continuam 200 ==\n";
$reais = array(
	'/', '/sobre', '/servicos', '/projetos', '/blog', '/contato', '/obrigado',
	'/en', '/en/about', '/en/services', '/en/work', '/en/blog', '/en/contact', '/en/thank-you',
	'/servicos/branding-identidade-visual', '/processo/diagnostico', '/blog/um-artigo', '/p/politica-de-privacidade',
	'/en/services/branding-identidade-visual', '/en/process/diagnosis', '/en/blog/an-article', '/en/p/privacy',
);
foreach ( $reais as $rota ) {
	$r = pegar( $rota );
	checa( "{$rota} responde 200", 200 === $r['status'], 'status ' . $r['status'] );
}

echo "\n== CMS fora do ar ==\n";
// Derruba o CMS e limpa o cache para forçar o caminho de falha.
foreach ( glob( sys_get_temp_dir() . '/stcms_seo_*.json' ) as $f ) {
	@unlink( $f );
}
if ( is_resource( $p1 ) ) {
	$info = proc_get_status( $p1 );
	if ( ! empty( $info['pid'] ) ) {
		@exec( 'pkill -P ' . (int) $info['pid'] . ' 2>/dev/null' );
	}
	proc_terminate( $p1 );
}
usleep( 400000 );
$r = pegar( '/' );
checa( 'site continua no ar sem o CMS', 200 === $r['status'], 'status ' . $r['status'] );
checa( 'serve o HTML do build como reserva', false !== strpos( $r['html'], '<div id="root">' ) );
checa( 'ainda tem uma description', null !== meta( $r['html'], 'name', 'description' ) );

echo "\n== Sem cms-config ==\n";
file_put_contents( $tmp . '/site/cms-config.php', "<?php return array( 'url' => '', 'token' => '' );\n" );
$r = pegar( '/' );
checa( 'sem CMS configurado o site abre igual', 200 === $r['status'] && false !== strpos( $r['html'], '<div id="root">' ) );

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
