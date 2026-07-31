<?php

$dir  = __DIR__;
$html = @file_get_contents( $dir . '/index.html' );
if ( false === $html ) {
	http_response_code( 500 );
	echo 'index.html não encontrado.';
	exit;
}

$api = '';
$cfg = @file_get_contents( $dir . '/config.js' );
if ( $cfg && preg_match( '/__STUDIO_TABI_API__\s*=\s*["\']([^"\']+)["\']/', $cfg, $m ) ) {
	$api = rtrim( trim( $m[1] ), '/' );
}

$meta = stcms_seo_meta( $api );
if ( $meta ) {
	$html = stcms_inject_head( $html, $meta );
}

header( 'Content-Type: text/html; charset=UTF-8' );
echo $html;
exit;

function stcms_seo_meta( $api ) {
	if ( ! $api ) {
		return null;
	}
	$cache = sys_get_temp_dir() . '/stcms_seo_' . md5( $api ) . '.json';
	$ttl   = 300;

	if ( is_readable( $cache ) && ( time() - filemtime( $cache ) ) < $ttl ) {
		$c = json_decode( file_get_contents( $cache ), true );
		if ( $c ) {
			return $c;
		}
	}

	$json = stcms_http_get( $api . '/wp-json/studio-tabi/v1/content' );
	if ( ! $json ) {
		if ( is_readable( $cache ) ) {
			$c = json_decode( file_get_contents( $cache ), true );
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
		'title'       => isset( $s['title'] ) ? $s['title'] : '',
		'description' => isset( $s['metaDescription'] ) ? $s['metaDescription'] : '',
		'image'       => isset( $s['logoUrl'] ) ? $s['logoUrl'] : '',
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
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTPHEADER     => array( 'Accept: application/json' ),
			)
		);
		$r    = curl_exec( $ch );
		$code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );
		return ( $r && $code >= 200 && $code < 300 ) ? $r : false;
	}
	$ctx = stream_context_create( array( 'http' => array( 'timeout' => 4, 'header' => 'Accept: application/json' ) ) );
	return @file_get_contents( $url, false, $ctx );
}

function stcms_inject_head( $html, $meta ) {
	$title = '' !== $meta['title'] ? $meta['title'] : null;
	$desc  = '' !== $meta['description'] ? $meta['description'] : null;
	$img   = '' !== $meta['image'] ? $meta['image'] : null;

	if ( null !== $title ) {
		$html = preg_replace( '/<title>.*?<\/title>/is', '<title>' . stcms_esc( $title ) . '</title>', $html, 1 );
	}
	if ( null !== $desc ) {
		$html = preg_replace( '/\s*<meta\s+name=["\']description["\'][^>]*>/i', '', $html, 1 );
	}

	$tags = array();
	if ( null !== $desc ) {
		$tags[] = '<meta name="description" content="' . stcms_esc( $desc ) . '"/>';
	}
	if ( null !== $title ) {
		$tags[] = '<meta property="og:title" content="' . stcms_esc( $title ) . '"/>';
		$tags[] = '<meta name="twitter:title" content="' . stcms_esc( $title ) . '"/>';
	}
	if ( null !== $desc ) {
		$tags[] = '<meta property="og:description" content="' . stcms_esc( $desc ) . '"/>';
		$tags[] = '<meta name="twitter:description" content="' . stcms_esc( $desc ) . '"/>';
	}
	$tags[] = '<meta property="og:type" content="website"/>';
	if ( null !== $img ) {
		$tags[] = '<meta property="og:image" content="' . stcms_esc( $img ) . '"/>';
		$tags[] = '<meta name="twitter:image" content="' . stcms_esc( $img ) . '"/>';
		$tags[] = '<meta name="twitter:card" content="summary_large_image"/>';
	} else {
		$tags[] = '<meta name="twitter:card" content="summary"/>';
	}

	$block = "\n  " . implode( "\n  ", $tags ) . "\n";
	return preg_replace( '/<\/head>/i', $block . '</head>', $html, 1 );
}

function stcms_esc( $s ) {
	return htmlspecialchars( $s, ENT_QUOTES, 'UTF-8' );
}
