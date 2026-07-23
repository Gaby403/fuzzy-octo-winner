<?php
/**
 * Funções auxiliares de template.
 */

defined( 'ABSPATH' ) || exit;

/** Atalho para get_theme_mod, com fallback no padrão central do tema. */
function tabi_mod( $id, $default = null ) {
	if ( null === $default || '' === $default ) {
		$default = tabi_default( $id );
	}
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

/** Marca "Tabi" como SVG (usada no sol e na lua do hero). */
function tabi_mark( $color = '#EFEFEF', $class = '' ) {
	$d = 'M170.261 204.844L103.042 219.81L91.5114 188.161L105.741 185.217V92.4837L109.421 91.5023C101.08 85.1235 90.7799 80.4621 80.4757 77.0273L87.8359 68.6858L58.3952 68.1951L58.1499 85.3688H94.9462L94.4555 129.28L92.2474 169.02C91.2661 185.458 88.0767 204.349 71.8843 213.181C65.9962 216.371 59.6173 217.597 52.9932 218.333C49.3131 206.807 43.9157 196.257 39.4996 189.633C34.8381 199.692 29.9313 209.26 23.5525 218.579C15.4563 208.034 7.11483 197.48 0 191.105C19.3818 160.438 26.7374 135.904 27.4734 94.9371L27.9641 67.9498H2.70781V36.5464H38.5227C36.0694 27.4689 32.1439 19.1273 26.9918 9.80901L55.9418 0C61.3393 9.56822 66.0007 21.0991 70.4168 32.1394C66.0007 33.3661 61.5846 34.5928 57.4138 36.0648L94.7008 36.5555V59.8627C106.722 42.4436 116.781 21.8352 122.669 0.495221C132.969 3.68463 143.769 6.62869 154.314 10.0634C152.351 17.9143 149.898 24.0478 147.444 31.1626L219.815 31.4079V63.5473L189.393 63.302C196.262 69.4354 203.868 75.0782 211.468 80.721C198.715 88.8172 185.222 95.6867 171.728 102.066C172.709 108.935 174.182 116.05 176.635 122.183C183.75 115.559 190.619 108.69 196.017 100.839L221.041 119.975C210.492 131.752 200.433 140.338 189.147 148.925C197.243 162.419 210.492 174.195 223.495 182.532C214.908 192.346 208.529 203.141 202.395 213.445C188.661 205.103 178.357 194.063 170.751 182.041L170.261 204.853V204.844ZM57.1685 117.263C54.7151 138.853 50.0537 164.609 41.9575 184.972C46.1283 184.727 50.5443 184.727 54.4698 183.745C57.6592 182.764 59.8672 179.82 61.0939 176.876C63.7927 158.234 65.0193 137.135 65.2647 117.263H57.1685ZM131.747 63.5382C126.35 72.8611 120.461 81.6933 113.351 89.5441C135.427 83.656 157.508 75.5598 177.135 64.0289L131.747 63.5382ZM138.126 179.579C147.203 177.862 155.295 176.635 165.354 174.427C155.295 155.29 148.185 135.418 144.995 113.583C142.542 114.319 140.334 115.3 138.126 116.282V179.574V179.579Z';
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 224 220" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="' . $d . '" fill="' . esc_attr( $color ) . '"/></svg>';
}

/** URL para uma âncora na home (funciona de qualquer página). */
function tabi_home_anchor( $url ) {
	if ( '' === $url ) { return home_url( '/' ); }
	if ( 0 === strpos( $url, '#' ) ) { return home_url( '/' ) . $url; }
	return $url;
}
