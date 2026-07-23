<?php
/**
 * Página inicial.
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>

<!-- ── HERO ── -->
<section class="tabi-hero">
	<div class="tabi-hero-scene" aria-hidden="true">
		<div class="tabi-hero-sun"></div>
		<svg class="tabi-mtn tabi-mtn-3" viewBox="0 0 1600 520" preserveAspectRatio="none"><path d="M0 520 L0 300 C200 250 380 340 560 300 C760 255 900 200 1040 150 C1160 110 1320 180 1460 240 C1520 265 1570 285 1600 300 L1600 520 Z"/></svg>
		<svg class="tabi-mtn tabi-mtn-2" viewBox="0 0 1600 520" preserveAspectRatio="none"><path d="M0 520 L0 340 C180 300 360 360 540 330 C740 296 900 250 1060 210 C1200 175 1340 230 1470 280 C1530 302 1575 318 1600 330 L1600 520 Z"/></svg>
		<svg class="tabi-mtn tabi-mtn-1" viewBox="0 0 1600 520" preserveAspectRatio="none"><path d="M0 520 L0 390 C160 360 340 400 520 380 C720 356 900 320 1080 300 C1240 282 1360 320 1480 350 C1540 366 1578 378 1600 386 L1600 520 Z"/></svg>
	</div>

	<div class="tabi-hero-content">
		<div class="tabi-eyebrow"><span class="dot"></span><?php echo esc_html( tabi_mod( 'tabi_hero_eyebrow', '' ) ); ?></div>
		<h1 class="tabi-hero-title"><?php tabi_the_headline( tabi_mod( 'tabi_hero_title', '' ) ); ?></h1>
		<p class="tabi-hero-desc"><?php echo esc_html( tabi_mod( 'tabi_hero_desc', '' ) ); ?></p>
		<div class="tabi-hero-cta">
			<a class="tabi-btn tabi-btn-solid" href="<?php echo esc_url( tabi_home_anchor( tabi_mod( 'tabi_hero_cta1_url', '#trabalhos' ) ) ); ?>">
				<?php echo esc_html( tabi_mod( 'tabi_hero_cta1', 'VER PORTFÓLIO' ) ); ?> <span aria-hidden="true">→</span>
			</a>
			<a class="tabi-btn tabi-btn-text" href="<?php echo esc_url( tabi_home_anchor( tabi_mod( 'tabi_hero_cta2_url', '#contato' ) ) ); ?>">
				<?php echo esc_html( tabi_mod( 'tabi_hero_cta2', '' ) ); ?>
			</a>
		</div>
	</div>
</section>

<!-- ── SOBRE ── -->
<section class="tabi-section tabi-about" id="sobre">
	<div class="tabi-eyebrow"><span class="dot"></span><?php echo esc_html( tabi_mod( 'tabi_about_eyebrow', '' ) ); ?></div>
	<div class="tabi-about-grid">
		<h2 class="tabi-title"><?php tabi_the_headline( tabi_mod( 'tabi_about_title', '' ) ); ?></h2>
		<div class="tabi-about-text">
			<p><?php echo esc_html( tabi_mod( 'tabi_about_p1', '' ) ); ?></p>
			<p><?php echo esc_html( tabi_mod( 'tabi_about_p2', '' ) ); ?></p>
		</div>
	</div>

	<div class="tabi-stats">
		<?php for ( $i = 1; $i <= 4; $i++ ) :
			$num = tabi_mod( "tabi_stat{$i}_num", '' );
			$lab = tabi_mod( "tabi_stat{$i}_label", '' );
			if ( '' === $num && '' === $lab ) { continue; } ?>
			<div class="tabi-stat">
				<span class="tabi-stat-num"><?php echo esc_html( $num ); ?></span>
				<span class="tabi-stat-label"><?php echo esc_html( $lab ); ?></span>
			</div>
		<?php endfor; ?>
	</div>

	<div class="tabi-pillars">
		<?php for ( $i = 1; $i <= 4; $i++ ) :
			$t = tabi_mod( "tabi_pillar{$i}_title", '' );
			$b = tabi_mod( "tabi_pillar{$i}_body", '' );
			if ( '' === $t ) { continue; } ?>
			<div class="tabi-pillar">
				<span class="tabi-pillar-num"><?php echo esc_html( str_pad( $i, 2, '0', STR_PAD_LEFT ) ); ?></span>
				<h3><?php echo esc_html( $t ); ?></h3>
				<p><?php echo esc_html( $b ); ?></p>
			</div>
		<?php endfor; ?>
	</div>
</section>

<!-- ── SERVIÇOS ── -->
<section class="tabi-section tabi-services" id="servicos">
	<div class="tabi-eyebrow"><span class="dot"></span><?php echo esc_html( tabi_mod( 'tabi_services_eyebrow', '' ) ); ?></div>
	<h2 class="tabi-title"><?php tabi_the_headline( tabi_mod( 'tabi_services_title', '' ) ); ?></h2>
	<p class="tabi-section-intro"><?php echo esc_html( tabi_mod( 'tabi_services_intro', '' ) ); ?></p>

	<div class="tabi-services-grid">
		<?php
		$servicos = new WP_Query( array( 'post_type' => 'servico', 'posts_per_page' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
		if ( $servicos->have_posts() ) :
			$n = 0;
			while ( $servicos->have_posts() ) : $servicos->the_post(); $n++; ?>
				<div class="tabi-service">
					<span class="tabi-service-num"><?php echo esc_html( str_pad( $n, 2, '0', STR_PAD_LEFT ) ); ?></span>
					<h3><?php the_title(); ?></h3>
					<div class="tabi-service-body"><?php the_excerpt(); ?></div>
				</div>
			<?php endwhile;
			wp_reset_postdata();
		else : ?>
			<p class="tabi-empty"><?php esc_html_e( 'Adicione serviços no menu “Serviços” do painel.', 'studio-tabi' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<!-- ── PROJETOS ── -->
<section class="tabi-section tabi-projects" id="trabalhos">
	<div class="tabi-projects-head">
		<div>
			<div class="tabi-eyebrow"><span class="dot"></span><?php echo esc_html( tabi_mod( 'tabi_projects_eyebrow', '' ) ); ?></div>
			<h2 class="tabi-title"><?php tabi_the_headline( tabi_mod( 'tabi_projects_title', '' ) ); ?></h2>
		</div>
		<span class="tabi-projects-meta"><?php echo esc_html( tabi_mod( 'tabi_projects_meta', '' ) ); ?></span>
	</div>

	<div class="tabi-projects-grid">
		<?php
		$projetos = new WP_Query( array( 'post_type' => 'projeto', 'posts_per_page' => 6, 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
		if ( $projetos->have_posts() ) :
			while ( $projetos->have_posts() ) : $projetos->the_post();
				$cor  = get_post_meta( get_the_ID(), 'tabi_cor', true ) ?: '#F20C25';
				$cat  = get_post_meta( get_the_ID(), 'tabi_categoria', true );
				$ano  = get_post_meta( get_the_ID(), 'tabi_ano', true );
				$feat = tabi_projeto_is_featured( get_the_ID() );
				?>
				<a class="tabi-project-card <?php echo $feat ? 'is-featured' : ''; ?>" href="<?php the_permalink(); ?>" style="--accent:<?php echo esc_attr( $cor ); ?>">
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
			<?php endwhile;
			wp_reset_postdata();
		else : ?>
			<p class="tabi-empty"><?php esc_html_e( 'Adicione projetos no menu “Projetos” do painel.', 'studio-tabi' ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( post_type_exists( 'projeto' ) ) : ?>
		<a class="tabi-more" href="<?php echo esc_url( get_post_type_archive_link( 'projeto' ) ); ?>"><?php esc_html_e( 'VER TODOS OS PROJETOS', 'studio-tabi' ); ?> <span aria-hidden="true">→</span></a>
	<?php endif; ?>
</section>

<!-- ── FAQ ── -->
<section class="tabi-section tabi-faq" id="faq">
	<div class="tabi-faq-grid">
		<div class="tabi-faq-intro">
			<div class="tabi-eyebrow"><span class="dot"></span><?php echo esc_html( tabi_mod( 'tabi_faq_eyebrow', '' ) ); ?></div>
			<h2 class="tabi-title"><?php tabi_the_headline( tabi_mod( 'tabi_faq_title', '' ) ); ?></h2>
			<p><?php echo esc_html( tabi_mod( 'tabi_faq_intro', '' ) ); ?></p>
		</div>
		<div class="tabi-faq-list">
			<?php for ( $i = 1; $i <= 6; $i++ ) :
				$q = tabi_mod( "tabi_faq{$i}_q", '' );
				$a = tabi_mod( "tabi_faq{$i}_a", '' );
				if ( '' === $q ) { continue; } ?>
				<div class="tabi-faq-item">
					<button class="tabi-faq-q" aria-expanded="false">
						<span class="tabi-faq-n"><?php echo esc_html( str_pad( $i, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span class="tabi-faq-text"><?php echo esc_html( $q ); ?></span>
						<span class="tabi-faq-plus" aria-hidden="true">+</span>
					</button>
					<div class="tabi-faq-a"><p><?php echo esc_html( $a ); ?></p></div>
				</div>
			<?php endfor; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
