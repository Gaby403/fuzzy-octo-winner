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
	return tabi_deep_merge( $default, $saved );
}

/** Uma lista sequencial (0,1,2…) — nesse caso o salvo substitui a lista inteira. */
function tabi_is_list( $a ) {
	if ( array() === $a ) { return true; }
	return array_keys( $a ) === range( 0, count( $a ) - 1 );
}

/**
 * Mescla o conteúdo salvo sobre o padrão. Objetos (hero, footer, ui…) são
 * mesclados por chave — então campos novos caem no padrão em instalações
 * antigas. Listas (projetos, colunas…) são substituídas inteiras pelo salvo.
 */
function tabi_deep_merge( $default, $saved ) {
	if ( ! is_array( $default ) || ! is_array( $saved ) ) {
		return $saved;
	}
	if ( tabi_is_list( $default ) || tabi_is_list( $saved ) ) {
		return $saved;
	}
	$out = $default;
	foreach ( $saved as $k => $v ) {
		$out[ $k ] = array_key_exists( $k, $default ) ? tabi_deep_merge( $default[ $k ], $v ) : $v;
	}
	return $out;
}

/**
 * Valida e salva o conteúdo. Aceita apenas as seções conhecidas.
 *
 * @param array $content Conteúdo decodificado.
 * @return true|WP_Error
 */
function tabi_save_content( $content ) {
	if ( ! is_array( $content ) ) {
		return new WP_Error( 'tabi_invalid_content', __( 'Conteúdo inválido: esperado um objeto JSON.', 'studio-tabi' ) );
	}

	$allowed = array( 'hero', 'about', 'services', 'projects', 'faq', 'footer', 'ui' );
	$clean   = array();
	foreach ( $allowed as $section ) {
		if ( isset( $content[ $section ] ) ) {
			$clean[ $section ] = $content[ $section ];
		}
	}

	if ( empty( $clean ) ) {
		return new WP_Error( 'tabi_empty_content', __( 'Nenhuma seção válida encontrada (hero, about, services, projects, faq, footer).', 'studio-tabi' ) );
	}

	update_option( TABI_OPTION_CONTENT, $clean, false );
	tabi_purge_caches();
	return true;
}

/**
 * Limpa os caches de página mais comuns após salvar, para o site refletir as
 * alterações na hora (LiteSpeed é o padrão na Hostinger).
 */
function tabi_purge_caches() {
	if ( function_exists( 'wp_cache_flush' ) ) { wp_cache_flush(); }
	// LiteSpeed Cache
	do_action( 'litespeed_purge_all' );
	// WP Super Cache
	do_action( 'wpsc_delete_cache' );
	// W3 Total Cache
	if ( function_exists( 'w3tc_flush_all' ) ) { w3tc_flush_all(); }
	// WP Rocket
	if ( function_exists( 'rocket_clean_domain' ) ) { rocket_clean_domain(); }
}
