<?php
/**
 * Página inicial: monta o "shell" do app React. O bundle e o conteúdo
 * (window.__TABI_CONTENT__) são injetados pelo functions.php.
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> style="margin:0;background:#111111;">
	<div id="root"></div>
	<?php wp_footer(); ?>
</body>
</html>
