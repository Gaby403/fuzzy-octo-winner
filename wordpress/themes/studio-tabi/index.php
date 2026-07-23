<?php
/**
 * Listagem (blog e fallback geral).
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="tabi-section tabi-blog">
	<div class="tabi-eyebrow"><span class="dot"></span><?php esc_html_e( 'STUDIO TABI — BLOG', 'studio-tabi' ); ?></div>
	<h1 class="tabi-title tabi-page-title">
		<?php
		if ( is_search() ) {
			/* translators: %s: search terms. */
			printf( '<span class="tabi-hl-line">%s</span>', esc_html( sprintf( __( 'Busca: %s', 'studio-tabi' ), get_search_query() ) ) );
		} else {
			tabi_the_headline( "IDEIAS &\n*ARTIGOS.*" );
		}
		?>
	</h1>

	<?php if ( have_posts() ) : ?>
		<div class="tabi-blog-grid">
			<?php while ( have_posts() ) : the_post(); ?>
				<a class="tabi-blog-card" href="<?php the_permalink(); ?>">
					<div class="tabi-blog-img">
						<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'tabi-card' ); } ?>
					</div>
					<div class="tabi-blog-info">
						<span class="tabi-blog-date"><?php echo esc_html( get_the_date() ); ?></span>
						<h2><?php the_title(); ?></h2>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
					</div>
				</a>
			<?php endwhile; ?>
		</div>
		<div class="tabi-pagination"><?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?></div>
	<?php else : ?>
		<div class="tabi-empty-box">
			<p class="tabi-empty-title"><?php esc_html_e( 'Nenhum post publicado ainda', 'studio-tabi' ); ?></p>
			<p><?php esc_html_e( 'Publique posts no menu “Posts” do WordPress e eles aparecem aqui.', 'studio-tabi' ); ?></p>
		</div>
	<?php endif; ?>
</section>
<?php get_footer(); ?>
