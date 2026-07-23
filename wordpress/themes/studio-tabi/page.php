<?php
/**
 * Página (conteúdo criado em Páginas do WordPress).
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) : the_post(); ?>
	<article class="tabi-page">
		<h1 class="tabi-page-title"><span class="tabi-hl-line"><?php the_title(); ?></span></h1>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="tabi-post-cover"><?php the_post_thumbnail( 'full' ); ?></div>
		<?php endif; ?>
		<div class="tabi-post-body"><?php the_content(); ?></div>
		<?php wp_link_pages(); ?>
	</article>
<?php endwhile;

get_footer();
