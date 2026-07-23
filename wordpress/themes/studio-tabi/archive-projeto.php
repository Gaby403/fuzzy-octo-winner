<?php
/**
 * Lista de todos os projetos.
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="tabi-section tabi-archive">
	<div class="tabi-eyebrow"><span class="dot"></span><?php echo esc_html( tabi_mod( 'tabi_projects_eyebrow', 'STUDIO TABI — PROJETOS' ) ); ?></div>
	<h1 class="tabi-title tabi-page-title"><?php tabi_the_headline( tabi_mod( 'tabi_projects_title', "TRABALHOS\n*SELECIONADOS.*" ) ); ?></h1>

	<div class="tabi-projects-grid">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
			$cor = get_post_meta( get_the_ID(), 'tabi_cor', true ) ?: '#F20C25';
			$cat = get_post_meta( get_the_ID(), 'tabi_categoria', true );
			$ano = get_post_meta( get_the_ID(), 'tabi_ano', true );
			?>
			<a class="tabi-project-card" href="<?php the_permalink(); ?>" style="--accent:<?php echo esc_attr( $cor ); ?>">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="tabi-project-img"><?php the_post_thumbnail( 'tabi-card' ); ?></div>
				<?php else : ?>
					<div class="tabi-project-img tabi-project-img-ph"></div>
				<?php endif; ?>
				<div class="tabi-project-info">
					<span class="tabi-project-cat"><?php echo esc_html( trim( $cat . ( $ano ? " — $ano" : '' ), ' —' ) ); ?></span>
					<h3><?php the_title(); ?></h3>
				</div>
			</a>
		<?php endwhile; else : ?>
			<p class="tabi-empty"><?php esc_html_e( 'Nenhum projeto publicado ainda.', 'studio-tabi' ); ?></p>
		<?php endif; ?>
	</div>

	<div class="tabi-pagination"><?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?></div>
</section>
<?php get_footer(); ?>
