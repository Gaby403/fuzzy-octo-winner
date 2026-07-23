<?php
/**
 * Post do blog.
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) : the_post(); ?>
	<article class="tabi-post">
		<a class="tabi-back" href="<?php echo esc_url( home_url( '/blog' ) ); ?>">← <?php esc_html_e( 'BLOG', 'studio-tabi' ); ?></a>
		<p class="tabi-post-date"><?php echo esc_html( get_the_date() ); ?></p>
		<h1 class="tabi-post-title"><?php the_title(); ?></h1>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="tabi-post-cover"><?php the_post_thumbnail( 'full' ); ?></div>
		<?php endif; ?>
		<div class="tabi-post-body"><?php the_content(); ?></div>
		<?php
		if ( get_the_tag_list() ) {
			echo '<div class="tabi-post-tags">' . get_the_tag_list( '', '' ) . '</div>'; // phpcs:ignore
		}
		?>
	</article>
<?php endwhile;

get_footer();
