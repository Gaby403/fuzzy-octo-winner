<?php
/**
 * Painel de administração: página "Conteúdo do Site" com editor JSON,
 * URL do front-end e botão de reset.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	add_menu_page(
		__( 'Conteúdo do Site', 'studio-tabi-headless' ),
		__( 'Conteúdo do Site', 'studio-tabi-headless' ),
		'manage_options',
		'tabi-content',
		'tabi_render_admin_page',
		'dashicons-edit-page',
		3
	);
} );

/**
 * Trata o POST do formulário e renderiza a página.
 */
function tabi_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$notice = null;

	if ( isset( $_POST['tabi_action'] ) && check_admin_referer( 'tabi_save_content' ) ) {
		$action = sanitize_key( wp_unslash( $_POST['tabi_action'] ) );

		if ( 'reset' === $action ) {
			delete_option( TABI_OPTION_CONTENT );
			$notice = array( 'success', __( 'Conteúdo restaurado para os valores padrão.', 'studio-tabi-headless' ) );
		} elseif ( 'save' === $action ) {
			$frontend_url = isset( $_POST['tabi_frontend_url'] ) ? esc_url_raw( wp_unslash( $_POST['tabi_frontend_url'] ) ) : '';
			update_option( TABI_OPTION_FRONTEND_URL, $frontend_url, false );

			$raw     = isset( $_POST['tabi_content_json'] ) ? wp_unslash( $_POST['tabi_content_json'] ) : '';
			$decoded = json_decode( $raw, true );

			if ( null === $decoded && JSON_ERROR_NONE !== json_last_error() ) {
				$notice = array( 'error', sprintf( __( 'JSON inválido: %s. Nada foi salvo.', 'studio-tabi-headless' ), json_last_error_msg() ) );
			} else {
				$result = tabi_save_content( $decoded );
				$notice = is_wp_error( $result )
					? array( 'error', $result->get_error_message() )
					: array( 'success', __( 'Conteúdo salvo. O site já reflete as alterações.', 'studio-tabi-headless' ) );
			}
		}
	}

	$content      = tabi_get_content();
	$json         = wp_json_encode( $content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	$frontend_url = get_option( TABI_OPTION_FRONTEND_URL, '' );
	$endpoint     = rest_url( 'tabi/v1/content' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Conteúdo do Site — Studio Tabi', 'studio-tabi-headless' ); ?></h1>

		<?php if ( $notice ) : ?>
			<div class="notice notice-<?php echo esc_attr( $notice[0] ); ?> is-dismissible"><p><?php echo esc_html( $notice[1] ); ?></p></div>
		<?php endif; ?>

		<p>
			<?php esc_html_e( 'Este WordPress funciona em modo headless: o conteúdo abaixo é servido para o front-end React via', 'studio-tabi-headless' ); ?>
			<code><?php echo esc_html( $endpoint ); ?></code>
		</p>

		<form method="post">
			<?php wp_nonce_field( 'tabi_save_content' ); ?>
			<input type="hidden" name="tabi_action" value="save" />

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="tabi_frontend_url"><?php esc_html_e( 'URL do front-end', 'studio-tabi-headless' ); ?></label></th>
					<td>
						<input type="url" class="regular-text" id="tabi_frontend_url" name="tabi_frontend_url"
							value="<?php echo esc_attr( $frontend_url ); ?>" placeholder="https://studiotabi.com.br" />
						<p class="description"><?php esc_html_e( 'Usada para liberar o CORS da API e redirecionar visitantes que acessarem o WordPress diretamente.', 'studio-tabi-headless' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tabi_content_json"><?php esc_html_e( 'Conteúdo (JSON)', 'studio-tabi-headless' ); ?></label></th>
					<td>
						<textarea id="tabi_content_json" name="tabi_content_json" rows="30" class="large-text code"
							spellcheck="false" style="font-family:monospace;"><?php echo esc_textarea( $json ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Seções: hero, about, services, projects, faq, footer. O JSON é validado antes de salvar.', 'studio-tabi-headless' ); ?></p>
					</td>
				</tr>
			</table>

			<p class="submit">
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Salvar conteúdo', 'studio-tabi-headless' ); ?></button>
			</p>
		</form>

		<form method="post" onsubmit="return confirm('<?php echo esc_js( __( 'Restaurar todo o conteúdo para os valores padrão?', 'studio-tabi-headless' ) ); ?>');">
			<?php wp_nonce_field( 'tabi_save_content' ); ?>
			<input type="hidden" name="tabi_action" value="reset" />
			<button type="submit" class="button"><?php esc_html_e( 'Restaurar padrão', 'studio-tabi-headless' ); ?></button>
		</form>
	</div>
	<?php
}
