<?php
/**
 * Plugin Name:       Studio Tabi CMS (Headless)
 * Plugin URI:        https://studiotabi.com.br
 * Description:        CMS nativo headless para o site Studio Tabi. Adiciona Serviços, Projetos, FAQ, páginas e blocos de conteúdo (Hero, Sobre, Rodapé, favicon) editáveis no WordPress e expostos via API REST para o front-end React.
 * Version:           1.0.0
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

define( 'STCMS_VERSION', '1.0.0' );
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
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$is_cpt = $screen && in_array( $screen->post_type, array( 'st_service', 'st_project' ), true );
	$is_opt = ( 'toplevel_page_studio-tabi' === $hook );

	if ( ! $is_cpt && ! $is_opt ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'stcms-admin', STCMS_URL . 'assets/admin.js', array(), STCMS_VERSION, true );
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
