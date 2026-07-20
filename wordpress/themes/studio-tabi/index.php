<?php
/**
 * Fallback: mesmo shell da página inicial para qualquer requisição servida
 * pelo WordPress. O app React assume a renderização a partir do #root.
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
