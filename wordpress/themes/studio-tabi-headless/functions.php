<?php
/**
 * Studio Tabi — functions.php
 *
 * Tema WordPress unificado: o próprio WordPress serve o site (app React
 * embutido) e o conteúdo é editado pelos campos do painel. Sem API externa,
 * sem CORS. O conteúdo é injetado inline na página (window.__TABI_CONTENT__).
 */

defined( 'ABSPATH' ) || exit;

define( 'TABI_OPTION_CONTENT', 'tabi_site_content' );
define( 'TABI_OPTION_FRONTEND_URL', 'tabi_frontend_url' );

require_once __DIR__ . '/inc/content.php';
require_once __DIR__ . '/inc/rest.php';
require_once __DIR__ . '/inc/admin.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'style', 'script' ) );
	remove_action( 'wp_head', 'wp_generator' );
} );

// Site full-bleed: sem a barra de admin empurrando o layout no front-end.
add_filter( 'show_admin_bar', '__return_false' );

/**
 * Enfileira o bundle do app (JS + CSS gerados pelo Vite em /assets) e injeta
 * o conteúdo do site inline, antes do script, para o React consumir sem rede.
 */
add_action( 'wp_enqueue_scripts', function () {
	$dir = get_template_directory() . '/assets';
	$uri = get_template_directory_uri() . '/assets';

	$js  = glob( $dir . '/*.js' );
	$css = glob( $dir . '/*.css' );

	if ( ! empty( $css ) ) {
		wp_enqueue_style( 'tabi-app', $uri . '/' . basename( $css[0] ), array(), null );
	}
	if ( ! empty( $js ) ) {
		wp_enqueue_script( 'tabi-app', $uri . '/' . basename( $js[0] ), array(), null, true );

		$content = wp_json_encode(
			tabi_get_content(),
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);
		wp_add_inline_script( 'tabi-app', 'window.__TABI_CONTENT__ = ' . $content . ';', 'before' );
	}
} );

// O bundle do Vite é um ES module: marca a tag <script> como type="module".
add_filter( 'script_loader_tag', function ( $tag, $handle, $src ) {
	if ( 'tabi-app' === $handle ) {
		return '<script type="module" src="' . esc_url( $src ) . '" id="tabi-app-js"></script>' . "\n";
	}
	return $tag;
}, 10, 3 );
