<?php
/**
 * Studio Tabi — tema WordPress nativo.
 *
 * Textos do site: Aparência → Personalizar.
 * Serviços e Projetos: menus próprios no painel (custom post types).
 * Blog: Posts. Páginas: Páginas.
 */

defined( 'ABSPATH' ) || exit;

define( 'TABI_VERSION', '2.1.0' );

require_once get_template_directory() . '/inc/defaults.php';
require_once get_template_directory() . '/inc/template-helpers.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/post-types.php';

/**
 * Recursos do tema.
 */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 40, 'width' => 160, 'flex-height' => true, 'flex-width' => true ) );

	register_nav_menus( array(
		'primary' => __( 'Menu principal', 'studio-tabi' ),
	) );

	// Tamanho de imagem para os cards.
	add_image_size( 'tabi-card', 900, 675, true );
} );

/**
 * CSS e JS do tema.
 */
add_action( 'wp_enqueue_scripts', function () {
	// Fontes Google.
	wp_enqueue_style(
		'tabi-fonts',
		'https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@700;900&family=Be+Vietnam+Pro:wght@400;500;600&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'tabi-theme', get_template_directory_uri() . '/assets/css/theme.css', array( 'tabi-fonts' ), TABI_VERSION );
	wp_enqueue_script( 'tabi-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), TABI_VERSION, true );
} );

// Marca que o JS está ativo (para as animações de revelação). Cedo, no <head>,
// para não haver "flash". Sem JS, nada é escondido.
add_action( 'wp_head', function () {
	echo "<script>document.documentElement.className+=' tabi-js';</script>\n";
}, 1 );

/**
 * Menu de fallback quando nenhum menu foi definido: links para as seções.
 */
function tabi_default_menu() {
	$items = array(
		home_url( '/#trabalhos' ) => __( 'Trabalhos', 'studio-tabi' ),
		home_url( '/#servicos' )  => __( 'Serviços', 'studio-tabi' ),
		home_url( '/#sobre' )     => __( 'Sobre', 'studio-tabi' ),
		home_url( '/blog' )       => __( 'Blog', 'studio-tabi' ),
		home_url( '/#contato' )   => __( 'Contato', 'studio-tabi' ),
	);
	echo '<ul id="tabi-menu" class="tabi-nav-list">';
	foreach ( $items as $url => $label ) {
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}
