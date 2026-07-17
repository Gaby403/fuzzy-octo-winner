<?php
/**
 * Studio Tabi Headless — functions.php
 *
 * O WordPress funciona apenas como CMS. O front-end React (pasta /frontend
 * do repositório) consome o conteúdo via REST API.
 */

defined( 'ABSPATH' ) || exit;

define( 'TABI_OPTION_CONTENT', 'tabi_site_content' );
define( 'TABI_OPTION_FRONTEND_URL', 'tabi_frontend_url' );

require_once __DIR__ . '/inc/content.php';
require_once __DIR__ . '/inc/rest.php';
require_once __DIR__ . '/inc/admin.php';

// Remove partes do WP que não fazem sentido em modo headless.
add_action( 'after_setup_theme', function () {
	remove_action( 'wp_head', 'wp_generator' );
	add_theme_support( 'title-tag' );
} );
