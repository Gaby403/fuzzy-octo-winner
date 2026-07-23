<?php
/**
 * Rodapé do site.
 */

defined( 'ABSPATH' ) || exit;

$email = tabi_mod( 'tabi_footer_email', 'oi@studiotabi.com.br' );
?>
<footer class="tabi-footer" id="contato">
	<div class="tabi-footer-top">
		<div class="tabi-footer-brand">
			<span class="tabi-footer-logo">STUDIO TABI</span>
			<span class="tabi-footer-rule"></span>
			<p><?php echo esc_html( tabi_mod( 'tabi_footer_tagline', '' ) ); ?></p>
			<a class="tabi-btn tabi-btn-outline" href="mailto:<?php echo esc_attr( $email ); ?>">
				<?php echo esc_html( tabi_mod( 'tabi_footer_cta', 'INICIAR PROJETO' ) ); ?> <span aria-hidden="true">→</span>
			</a>
		</div>

		<div class="tabi-footer-col">
			<span class="tabi-footer-label"><?php esc_html_e( 'Navegação', 'studio-tabi' ); ?></span>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/#trabalhos' ) ); ?>"><?php esc_html_e( 'Trabalhos', 'studio-tabi' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/#servicos' ) ); ?>"><?php esc_html_e( 'Serviços', 'studio-tabi' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/#sobre' ) ); ?>"><?php esc_html_e( 'Sobre', 'studio-tabi' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/blog' ) ); ?>"><?php esc_html_e( 'Blog', 'studio-tabi' ); ?></a></li>
			</ul>
		</div>

		<div class="tabi-footer-col">
			<span class="tabi-footer-label"><?php esc_html_e( 'Contato', 'studio-tabi' ); ?></span>
			<ul>
				<li><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
				<li><?php echo esc_html( tabi_mod( 'tabi_footer_phone', '' ) ); ?></li>
				<li><?php echo esc_html( tabi_mod( 'tabi_footer_city', '' ) ); ?></li>
			</ul>
		</div>

		<div class="tabi-footer-col">
			<span class="tabi-footer-label"><?php esc_html_e( 'Social', 'studio-tabi' ); ?></span>
			<ul>
				<?php foreach ( tabi_lines( tabi_mod( 'tabi_footer_social', '' ) ) as $social ) : ?>
					<li><a href="#"><?php echo esc_html( $social ); ?> <span aria-hidden="true">↗</span></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="tabi-footer-bottom">
		<span><?php echo esc_html( tabi_mod( 'tabi_footer_copyright', '' ) ); ?></span>
		<span class="tabi-footer-sign"><?php echo esc_html( tabi_mod( 'tabi_footer_signature', '' ) ); ?></span>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
