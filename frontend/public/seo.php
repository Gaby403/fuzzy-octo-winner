<?php

$raiz = __DIR__;

function stcms_rota() {
	$pedido = isset( $_SERVER['REQUEST_URI'] ) ? parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '/';
	$pedido = '/' . trim( (string) $pedido, '/' );
	if ( preg_match( '#\.\.|\0#', $pedido ) ) {
		return '/';
	}
	return $pedido;
}

function stcms_arquivo( $raiz, $rota ) {
	$candidatos = array(
		$raiz . rtrim( $rota, '/' ) . '/index.html',
		$raiz . '/index.html',
	);
	foreach ( $candidatos as $c ) {
		$real = realpath( $c );
		if ( $real && 0 === strpos( $real, realpath( $raiz ) ) && is_readable( $real ) ) {
			return $real;
		}
	}
	return null;
}

$rota    = stcms_rota();
$arquivo = stcms_arquivo( $raiz, $rota );
$html    = $arquivo ? file_get_contents( $arquivo ) : false;

if ( false === $html ) {
	http_response_code( 404 );
	header( 'Content-Type: text/html; charset=UTF-8' );
	echo '<!doctype html><meta charset="utf-8"><title>404</title>';
	exit;
}

// Se algo abaixo estourar, o HTML estático ainda sai — a página nunca fica em
// branco por causa da injeção.
$GLOBALS['stcms_html_base'] = $html;
$GLOBALS['stcms_entregue']  = false;
register_shutdown_function(
	function () {
		if ( $GLOBALS['stcms_entregue'] ) {
			return;
		}
		if ( ! headers_sent() ) {
			header( 'Content-Type: text/html; charset=UTF-8' );
		}
		echo $GLOBALS['stcms_html_base'];
	}
);

$lang = ( '/en' === $rota || 0 === strpos( $rota, '/en/' ) ) ? 'en' : 'pt';

try {
	$meta = stcms_seo_meta( $raiz, $lang );
	if ( $meta ) {
		$html = stcms_injetar( $html, $meta );
	}
} catch ( Throwable $e ) {
	$html = $GLOBALS['stcms_html_base'];
}

$GLOBALS['stcms_entregue'] = true;
header( 'Content-Type: text/html; charset=UTF-8' );
header( 'Cache-Control: no-cache, no-store, must-revalidate' );
echo $html;
exit;

function stcms_base_do_cms( $raiz ) {
	$cfg = @include $raiz . '/cms-config.php';
	$url = is_array( $cfg ) && ! empty( $cfg['url'] ) ? rtrim( $cfg['url'], '/' ) : '';
	$host   = parse_url( $url, PHP_URL_HOST );
	$scheme = parse_url( $url, PHP_URL_SCHEME );
	$local  = in_array( $host, array( 'localhost', '127.0.0.1' ), true );
	if ( ! $host || ! preg_match( '/^[a-z0-9.-]+$/i', $host ) || ( 'https' !== $scheme && ! ( 'http' === $scheme && $local ) ) ) {
		return '';
	}
	return $url;
}

function stcms_seo_meta( $raiz, $lang ) {
	$api = stcms_base_do_cms( $raiz );
	if ( ! $api ) {
		return null;
	}
	$cache = sys_get_temp_dir() . '/stcms_seo_' . $lang . '_' . md5( $api ) . '.json';
	$ttl   = 300;

	if ( is_readable( $cache ) && ( time() - filemtime( $cache ) ) < $ttl ) {
		$c = json_decode( (string) file_get_contents( $cache ), true );
		if ( $c ) {
			return $c;
		}
	}

	$json = stcms_http_get( $api . '/wp-json/studio-tabi/v1/content?lang=' . $lang );
	if ( ! $json ) {
		// CMS fora do ar: vale a última cópia boa, mesmo vencida.
		if ( is_readable( $cache ) ) {
			$c = json_decode( (string) file_get_contents( $cache ), true );
			if ( $c ) {
				return $c;
			}
		}
		return null;
	}

	$data = json_decode( $json, true );
	if ( ! isset( $data['site'] ) ) {
		return null;
	}
	$s    = $data['site'];
	$meta = array(
		'title'       => isset( $s['title'] ) ? (string) $s['title'] : '',
		'description' => isset( $s['metaDescription'] ) ? (string) $s['metaDescription'] : '',
		'image'       => isset( $s['logoUrl'] ) ? (string) $s['logoUrl'] : '',
	);
	@file_put_contents( $cache, json_encode( $meta ) );
	return $meta;
}

function stcms_http_get( $url ) {
	if ( function_exists( 'curl_init' ) ) {
		$ch = curl_init( $url );
		curl_setopt_array(
			$ch,
			array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT        => 4,
				CURLOPT_CONNECTTIMEOUT => 3,
				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_HTTPHEADER     => array( 'Accept: application/json' ),
			)
		);
		$r    = curl_exec( $ch );
		$code = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );
		return ( $r && $code >= 200 && $code < 300 ) ? $r : false;
	}
	$ctx = stream_context_create( array( 'http' => array( 'timeout' => 4, 'header' => 'Accept: application/json' ) ) );
	return @file_get_contents( $url, false, $ctx );
}

/**
 * Troca as tags que já vieram do build em vez de acrescentar outras — duas
 * og:description no mesmo HTML deixam a prévia a critério de quem lê.
 */
function stcms_injetar( $html, $meta ) {
	$title = '' !== $meta['title'] ? $meta['title'] : null;
	$desc  = '' !== $meta['description'] ? $meta['description'] : null;
	$img   = '' !== $meta['image'] ? $meta['image'] : null;

	if ( null !== $title ) {
		$html = preg_replace( '/<title>.*?<\/title>/is', '<title>' . stcms_esc( $title ) . '</title>', $html, 1 );
	}

	$remover = array( 'name' => array( 'description', 'twitter:title', 'twitter:description', 'twitter:image', 'twitter:card' ), 'property' => array( 'og:title', 'og:description', 'og:image', 'og:type' ) );
	foreach ( $remover as $attr => $nomes ) {
		foreach ( $nomes as $nome ) {
			$html = preg_replace( '/\s*<meta\s+[^>]*' . $attr . '=["\']' . preg_quote( $nome, '/' ) . '["\'][^>]*>/i', '', $html );
		}
	}

	$tags = array();
	if ( null !== $desc ) {
		$tags[] = '<meta name="description" content="' . stcms_esc( $desc ) . '"/>';
		$tags[] = '<meta property="og:description" content="' . stcms_esc( $desc ) . '"/>';
		$tags[] = '<meta name="twitter:description" content="' . stcms_esc( $desc ) . '"/>';
	}
	if ( null !== $title ) {
		$tags[] = '<meta property="og:title" content="' . stcms_esc( $title ) . '"/>';
		$tags[] = '<meta name="twitter:title" content="' . stcms_esc( $title ) . '"/>';
	}
	$tags[] = '<meta property="og:type" content="website"/>';
	if ( null !== $img ) {
		$tags[] = '<meta property="og:image" content="' . stcms_esc( $img ) . '"/>';
		$tags[] = '<meta name="twitter:image" content="' . stcms_esc( $img ) . '"/>';
		$tags[] = '<meta name="twitter:card" content="summary_large_image"/>';
	} else {
		$tags[] = '<meta name="twitter:card" content="summary"/>';
	}

	$bloco = "\n  " . implode( "\n  ", $tags ) . "\n";
	return preg_replace( '/<\/head>/i', $bloco . '</head>', $html, 1 );
}

function stcms_esc( $s ) {
	return htmlspecialchars( $s, ENT_QUOTES, 'UTF-8' );
}
