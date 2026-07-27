<?php
/**
 * Plugin Name:       Studio Tabi CMS (Headless)
 * Plugin URI:        https://studiotabi.com.br
 * Description:        CMS nativo headless para o site Studio Tabi. Adiciona Serviços, Projetos, FAQ, páginas e blocos de conteúdo (Hero, Sobre, Rodapé, favicon) editáveis no WordPress e expostos via API REST para o front-end React.
 * Version:           1.4.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Studio Tabi
 * Text Domain:       studio-tabi-cms
 *
 * @package StudioTabiCMS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STCMS_VERSION', '1.4.0' );
define( 'STCMS_DIR', plugin_dir_path( __FILE__ ) );
define( 'STCMS_URL', plugin_dir_url( __FILE__ ) );

require_once STCMS_DIR . 'includes/defaults.php';
require_once STCMS_DIR . 'includes/class-cpt.php';
require_once STCMS_DIR . 'includes/class-meta.php';
require_once STCMS_DIR . 'includes/class-options.php';
require_once STCMS_DIR . 'includes/class-rest.php';
require_once STCMS_DIR . 'includes/class-seed.php';

/**
 * Boot the plugin.
 */
function stcms_boot() {
	STCMS_CPT::init();
	STCMS_Meta::init();
	STCMS_Options::init();
	STCMS_Rest::init();
}
add_action( 'plugins_loaded', 'stcms_boot' );

/**
 * Enqueue admin assets (repeaters + media picker) on our screens only.
 */
function stcms_admin_assets( $hook ) {
	// Detecta o tipo de post de forma robusta (tela ou global $typenow).
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$post_type = '';
	if ( $screen && ! empty( $screen->post_type ) ) {
		$post_type = $screen->post_type;
	} elseif ( ! empty( $GLOBALS['typenow'] ) ) {
		$post_type = $GLOBALS['typenow'];
	} elseif ( isset( $_GET['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_type = sanitize_key( wp_unslash( $_GET['post_type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	$is_cpt = in_array( $post_type, array( 'st_service', 'st_project' ), true );
	$is_opt = ( false !== strpos( (string) $hook, 'studio-tabi' ) );

	if ( ! $is_cpt && ! $is_opt ) {
		return;
	}

	// Biblioteca de mídia do WordPress (necessária para o seletor de imagens/PDF).
	wp_enqueue_media();

	// Versão baseada no filemtime: garante que o navegador/CDN (LiteSpeed da
	// Hostinger) NUNCA sirvam um admin.js/admin.css antigo em cache — que era o
	// motivo do botão "Adicionar" não abrir nada após atualizar o plugin.
	$js_path  = STCMS_DIR . 'assets/admin.js';
	$css_path = STCMS_DIR . 'assets/admin.css';
	$js_ver   = file_exists( $js_path ) ? (string) filemtime( $js_path ) : STCMS_VERSION;
	$css_ver  = file_exists( $css_path ) ? (string) filemtime( $css_path ) : STCMS_VERSION;

	// Declarar 'media-editor' como dependência garante que wp.media já esteja
	// disponível quando o admin.js rodar (senão o botão "Adicionar" não abre nada).
	wp_enqueue_script( 'stcms-admin', STCMS_URL . 'assets/admin.js', array( 'jquery', 'media-editor' ), $js_ver, true );
	wp_enqueue_style( 'stcms-admin', STCMS_URL . 'assets/admin.css', array( 'dashicons' ), $css_ver );
}
add_action( 'admin_enqueue_scripts', 'stcms_admin_assets' );

/**
 * Activation: register CPTs and seed default content.
 */
function stcms_activate() {
	require_once STCMS_DIR . 'includes/class-cpt.php';
	require_once STCMS_DIR . 'includes/class-seed.php';
	STCMS_Seed::activate();
}
register_activation_hook( __FILE__, 'stcms_activate' );

/**
 * Deactivation: flush rewrite rules.
 */
function stcms_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'stcms_deactivate' );

/**
 * Add a "Ver API" / settings shortcut on the plugins list.
 */
function stcms_action_links( $links ) {
	$custom = array(
		'<a href="' . esc_url( admin_url( 'admin.php?page=studio-tabi' ) ) . '">Conteúdo</a>',
		'<a href="' . esc_url( rest_url( 'studio-tabi/v1/content' ) ) . '" target="_blank" rel="noopener">API</a>',
	);
	return array_merge( $custom, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'stcms_action_links' );
