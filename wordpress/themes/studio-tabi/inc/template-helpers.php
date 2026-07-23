<?php
/**
 * Funções auxiliares de template.
 */

defined( 'ABSPATH' ) || exit;

/** Atalho para get_theme_mod. */
function tabi_mod( $id, $default = '' ) {
	return get_theme_mod( $id, $default );
}

/**
 * Renderiza um título multilinha. Cada linha vira um bloco; linhas entre
 * *asteriscos* saem em vermelho. Escapa o conteúdo.
 */
function tabi_headline( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	$out   = '';
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' === $line ) { continue; }
		$red = ( strlen( $line ) > 1 && '*' === $line[0] && '*' === substr( $line, -1 ) );
		$clean = $red ? substr( $line, 1, -1 ) : $line;
		$out  .= '<span class="tabi-hl-line' . ( $red ? ' is-red' : '' ) . '">' . esc_html( $clean ) . '</span>';
	}
	return $out;
}

/** Imprime um título multilinha. */
function tabi_the_headline( $text ) {
	echo tabi_headline( $text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapado em tabi_headline.
}

/**
 * Converte "Rótulo | Valor" (uma linha por par) em array.
 */
function tabi_parse_pairs( $raw ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) { continue; }
		$parts   = array_map( 'trim', explode( '|', $line, 2 ) );
		$out[]   = array( 'label' => $parts[0], 'value' => isset( $parts[1] ) ? $parts[1] : '' );
	}
	return $out;
}

/** Lista de linhas não vazias. */
function tabi_lines( $raw ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
		$line = trim( $line );
		if ( '' !== $line ) { $out[] = $line; }
	}
	return $out;
}

/** URL para uma âncora na home (funciona de qualquer página). */
function tabi_home_anchor( $url ) {
	if ( '' === $url ) { return home_url( '/' ); }
	if ( 0 === strpos( $url, '#' ) ) { return home_url( '/' ) . $url; }
	return $url;
}
