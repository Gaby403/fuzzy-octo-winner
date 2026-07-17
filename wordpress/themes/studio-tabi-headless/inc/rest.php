<?php
/**
 * REST API headless:
 *   GET  /wp-json/tabi/v1/content  → público, retorna o conteúdo do site
 *   POST /wp-json/tabi/v1/content  → requer manage_options (Application Password)
 *
 * CORS é liberado apenas para a origem do front-end configurada no painel.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
	register_rest_route( 'tabi/v1', '/content', array(
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => function () {
				return rest_ensure_response( tabi_get_content() );
			},
			'permission_callback' => '__return_true',
		),
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => function ( WP_REST_Request $request ) {
				$result = tabi_save_content( $request->get_json_params() );
				if ( is_wp_error( $result ) ) {
					$result->add_data( array( 'status' => 400 ) );
					return $result;
				}
				return rest_ensure_response( array(
					'saved'   => true,
					'content' => tabi_get_content(),
				) );
			},
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
		),
	) );
} );

/**
 * Origem permitida para CORS: derivada da URL do front-end configurada.
 *
 * @return string Origem (scheme://host[:porta]) ou '' se não configurada.
 */
function tabi_get_frontend_origin() {
	$url = get_option( TABI_OPTION_FRONTEND_URL, '' );
	if ( ! $url ) {
		return '';
	}
	$parts = wp_parse_url( $url );
	if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) ) {
		return '';
	}
	$origin = $parts['scheme'] . '://' . $parts['host'];
	if ( ! empty( $parts['port'] ) ) {
		$origin .= ':' . $parts['port'];
	}
	return $origin;
}

// Substitui os headers CORS padrão do WP pelos do front-end configurado.
add_action( 'rest_api_init', function () {
	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
	add_filter( 'rest_pre_serve_request', function ( $value ) {
		$allowed = tabi_get_frontend_origin();
		$origin  = get_http_origin();

		if ( $allowed && $origin === $allowed ) {
			header( 'Access-Control-Allow-Origin: ' . $allowed );
			header( 'Access-Control-Allow-Methods: GET, POST, OPTIONS' );
			header( 'Access-Control-Allow-Headers: Authorization, Content-Type' );
			header( 'Access-Control-Allow-Credentials: true' );
			header( 'Vary: Origin', false );
		} else {
			rest_send_cors_headers( $value );
		}
		return $value;
	} );
}, 15 );
