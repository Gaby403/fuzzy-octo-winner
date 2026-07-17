<?php
/**
 * Modelo de conteúdo: carrega o JSON padrão e mescla com o que foi salvo
 * no painel (option tabi_site_content).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Conteúdo padrão do site, espelho do DEFAULT_CONTENT do front-end React.
 *
 * @return array
 */
function tabi_get_default_content() {
	static $default = null;
	if ( null === $default ) {
		$raw     = file_get_contents( get_template_directory() . '/data/default-content.json' );
		$default = json_decode( $raw, true );
		if ( ! is_array( $default ) ) {
			$default = array();
		}
	}
	return $default;
}

/**
 * Conteúdo atual: o salvo no painel, com fallback para o padrão.
 * A mesclagem é rasa por seção (hero, about, services, projects, faq, footer),
 * igual à do front-end.
 *
 * @return array
 */
function tabi_get_content() {
	$default = tabi_get_default_content();
	$saved   = get_option( TABI_OPTION_CONTENT );

	if ( ! is_array( $saved ) ) {
		return $default;
	}
	return array_merge( $default, $saved );
}

/**
 * Valida e salva o conteúdo. Aceita apenas as seções conhecidas.
 *
 * @param array $content Conteúdo decodificado.
 * @return true|WP_Error
 */
function tabi_save_content( $content ) {
	if ( ! is_array( $content ) ) {
		return new WP_Error( 'tabi_invalid_content', __( 'Conteúdo inválido: esperado um objeto JSON.', 'studio-tabi-headless' ) );
	}

	$allowed = array( 'hero', 'about', 'services', 'projects', 'faq', 'footer' );
	$clean   = array();
	foreach ( $allowed as $section ) {
		if ( isset( $content[ $section ] ) ) {
			$clean[ $section ] = $content[ $section ];
		}
	}

	if ( empty( $clean ) ) {
		return new WP_Error( 'tabi_empty_content', __( 'Nenhuma seção válida encontrada (hero, about, services, projects, faq, footer).', 'studio-tabi-headless' ) );
	}

	update_option( TABI_OPTION_CONTENT, $clean, false );
	return true;
}
