<?php
/**
 * Página não encontrada.
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="tabi-section" style="text-align:center;min-height:50vh">
	<div class="tabi-eyebrow" style="justify-content:center"><span class="dot"></span><?php esc_html_e( 'ERRO 404', 'studio-tabi' ); ?></div>
	<h1 class="tabi-title"><span class="tabi-hl-line"><?php esc_html_e( 'PÁGINA', 'studio-tabi' ); ?></span><span class="tabi-hl-line is-red"><?php esc_html_e( 'NÃO ENCONTRADA.', 'studio-tabi' ); ?></span></h1>
	<p style="color:var(--muted);margin:24px 0 32px"><?php esc_html_e( 'O endereço que você acessou não existe ou foi movido.', 'studio-tabi' ); ?></p>
	<a class="tabi-btn tabi-btn-solid" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'VOLTAR AO INÍCIO', 'studio-tabi' ); ?> <span aria-hidden="true">→</span></a>
</section>
<?php get_footer(); ?>
