<?php
/**
 * Cabeçalho do site.
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="tabi-header">
	<div class="tabi-header-inner">
		<a class="tabi-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				echo 'STUDIO <span>TABI</span>';
			}
			?>
		</a>

		<nav class="tabi-nav" aria-label="<?php esc_attr_e( 'Menu principal', 'studio-tabi' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'tabi-nav-list',
					'menu_id'        => 'tabi-menu',
					'depth'          => 1,
					'fallback_cb'    => 'tabi_default_menu',
				) );
			} else {
				tabi_default_menu();
			}
			?>
		</nav>

		<button class="tabi-burger" aria-label="<?php esc_attr_e( 'Abrir menu', 'studio-tabi' ); ?>" aria-expanded="false" aria-controls="tabi-menu">
			<span></span><span></span><span></span>
		</button>
	</div>
</header>
