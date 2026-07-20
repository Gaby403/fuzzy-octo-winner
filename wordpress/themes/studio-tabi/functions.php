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

require_once __DIR__ . '/inc/content.php';
require_once __DIR__ . '/inc/admin.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'style', 'script' ) );
	remove_action( 'wp_head', 'wp_generator' );
} );

// Site full-bleed: sem a barra de admin empurrando o layout no front-end.
add_filter( 'show_admin_bar', '__return_false' );

// O HTML do site carrega o conteúdo injetado inline, então não pode ser
// cacheado (senão edições no painel não aparecem). Os assets JS/CSS têm hash
// no nome e continuam com cache normal.
add_action( 'template_redirect', function () {
	if ( is_admin() ) { return; }
	nocache_headers();
	header( 'X-LiteSpeed-Cache-Control: no-cache' );
	do_action( 'litespeed_control_set_nocache', 'studio tabi: conteudo dinamico' );
} );

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

/**
 * Rotas do SPA: faz o WordPress servir o app (status 200) para as páginas
 * internas /servicos, /blog, /blog/..., /projeto/... . O React Router assume
 * a renderização a partir do caminho.
 */
function tabi_register_routes() {
	add_rewrite_rule( '^(servicos|blog|projeto)(/.*)?/?$', 'index.php', 'top' );
}
add_action( 'init', 'tabi_register_routes' );

// Regenera as regras ao ativar o tema.
add_action( 'after_switch_theme', function () {
	tabi_register_routes();
	flush_rewrite_rules();
} );

// Aviso: as rotas exigem links permanentes "bonitos" (não "Simples/Plain").
add_action( 'admin_notices', function () {
	if ( '' === get_option( 'permalink_structure' ) ) {
		echo '<div class="notice notice-warning"><p><strong>Studio Tabi:</strong> ';
		echo esc_html__( 'defina os Links Permanentes como “Nome do post” em Configurações → Links Permanentes para as páginas internas (serviços, projetos, blog) funcionarem.', 'studio-tabi' );
		echo '</p></div>';
	}
} );
