<?php
/**
 * Página de um projeto.
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) : the_post();
	$id   = get_the_ID();
	$cor  = get_post_meta( $id, 'tabi_cor', true ) ?: '#F20C25';
	$cat  = get_post_meta( $id, 'tabi_categoria', true );
	$ano  = get_post_meta( $id, 'tabi_ano', true );
	$cli  = get_post_meta( $id, 'tabi_cliente', true );
	$dur  = get_post_meta( $id, 'tabi_duracao', true );
	$escopo     = tabi_lines( get_post_meta( $id, 'tabi_escopo', true ) );
	$resultados = tabi_parse_pairs( get_post_meta( $id, 'tabi_resultados', true ) );
	?>
	<article class="tabi-project-single" style="--accent:<?php echo esc_attr( $cor ); ?>">

		<div class="tabi-project-hero" <?php if ( has_post_thumbnail() ) : ?>style="background-image:linear-gradient(to top, #080808 6%, rgba(8,8,8,.35) 60%, rgba(8,8,8,.55)), url('<?php echo esc_url( get_the_post_thumbnail_url( $id, 'full' ) ); ?>')"<?php endif; ?>>
			<div class="tabi-project-hero-inner">
				<a class="tabi-back" href="<?php echo esc_url( get_post_type_archive_link( 'projeto' ) ); ?>">← <?php esc_html_e( 'PROJETOS', 'studio-tabi' ); ?></a>
				<p class="tabi-project-cat"><?php echo esc_html( trim( $cat . ( $ano ? " — $ano" : '' ), ' —' ) ); ?></p>
				<h1><?php the_title(); ?></h1>
			</div>
		</div>

		<div class="tabi-project-body">

			<?php if ( $cli || $dur || $ano || $escopo ) : ?>
				<div class="tabi-project-meta">
					<?php if ( $cli ) : ?><div><span><?php esc_html_e( 'Cliente', 'studio-tabi' ); ?></span><strong><?php echo esc_html( $cli ); ?></strong></div><?php endif; ?>
					<?php if ( $dur ) : ?><div><span><?php esc_html_e( 'Duração', 'studio-tabi' ); ?></span><strong><?php echo esc_html( $dur ); ?></strong></div><?php endif; ?>
					<?php if ( $escopo ) : ?><div><span><?php esc_html_e( 'Áreas', 'studio-tabi' ); ?></span><strong><?php echo esc_html( count( $escopo ) ); ?></strong></div><?php endif; ?>
					<?php if ( $ano ) : ?><div><span><?php esc_html_e( 'Ano', 'studio-tabi' ); ?></span><strong><?php echo esc_html( $ano ); ?></strong></div><?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $escopo ) : ?>
				<div class="tabi-tags">
					<?php foreach ( $escopo as $tag ) : ?><span class="tabi-tag"><?php echo esc_html( $tag ); ?></span><?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="tabi-project-content"><?php the_content(); ?></div>

			<?php if ( $resultados ) : ?>
				<div class="tabi-results">
					<p class="tabi-label"><?php echo esc_html( tabi_mod( 'tabi_project_results_label', 'RESULTADOS' ) ); ?></p>
					<div class="tabi-results-grid">
						<?php foreach ( $resultados as $r ) : ?>
							<div class="tabi-result">
								<span class="tabi-result-val"><?php echo esc_html( $r['value'] ); ?></span>
								<span class="tabi-result-lab"><?php echo esc_html( $r['label'] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="tabi-project-nav">
				<?php
				$prev = get_previous_post();
				$next = get_next_post();
				?>
				<?php if ( $prev ) : ?><a href="<?php echo esc_url( get_permalink( $prev ) ); ?>">← <?php esc_html_e( 'ANTERIOR', 'studio-tabi' ); ?></a><?php else : ?><span></span><?php endif; ?>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'projeto' ) ); ?>"><?php esc_html_e( 'TODOS OS PROJETOS', 'studio-tabi' ); ?></a>
				<?php if ( $next ) : ?><a href="<?php echo esc_url( get_permalink( $next ) ); ?>"><?php esc_html_e( 'PRÓXIMO', 'studio-tabi' ); ?> →</a><?php else : ?><span></span><?php endif; ?>
			</div>
		</div>
	</article>
<?php endwhile;

get_footer();
