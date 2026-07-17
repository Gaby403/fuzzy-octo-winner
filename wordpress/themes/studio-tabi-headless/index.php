<?php
/**
 * Front-end desabilitado: em modo headless o site público é o app React.
 * Se a URL do front-end estiver configurada, redireciona o visitante para lá.
 */

defined( 'ABSPATH' ) || exit;

$tabi_frontend = get_option( TABI_OPTION_FRONTEND_URL, '' );

if ( $tabi_frontend && ! is_user_logged_in() ) {
	wp_redirect( esc_url_raw( $tabi_frontend ), 302 );
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="robots" content="noindex" />
	<?php wp_head(); ?>
	<style>
		body { font-family: system-ui, sans-serif; background: #111; color: #efefef; display: grid; place-items: center; min-height: 100vh; margin: 0; }
		main { text-align: center; padding: 24px; }
		a { color: #f20c25; }
		code { background: #1a1a1a; padding: 2px 6px; border-radius: 4px; }
	</style>
</head>
<body>
	<main>
		<h1>Studio Tabi — CMS Headless</h1>
		<p>Este WordPress serve apenas conteúdo via <code><?php echo esc_html( rest_url( 'tabi/v1/content' ) ); ?></code>.</p>
		<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=tabi-content' ) ); ?>">Editar conteúdo do site</a></p>
	</main>
	<?php wp_footer(); ?>
</body>
</html>
