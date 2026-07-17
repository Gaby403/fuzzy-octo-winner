<?php
/**
 * Painel de administração:
 *  - "Conteúdo do Site": editor com campos amigáveis por seção.
 *  - "Editor JSON (avançado)": edição direta do JSON completo.
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
	add_submenu_page(
		'tabi-content',
		__( 'Editor JSON (avançado)', 'studio-tabi-headless' ),
		__( 'Editor JSON (avançado)', 'studio-tabi-headless' ),
		'manage_options',
		'tabi-content-json',
		'tabi_render_json_page'
	);
} );

// ── Helpers ──────────────────────────────────────────────────────────────────

/** Converte textarea "um item por linha" em array de strings. */
function tabi_lines_to_array( $raw ) {
	$lines = array_map( 'trim', explode( "\n", (string) $raw ) );
	return array_values( array_filter( $lines, function ( $l ) { return '' !== $l; } ) );
}

/** Converte textarea "Rótulo | Valor" (um por linha) em array de pares. */
function tabi_pairs_to_array( $raw, $key_a, $key_b ) {
	$out = array();
	foreach ( tabi_lines_to_array( $raw ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		$out[] = array( $key_a => $parts[0], $key_b => isset( $parts[1] ) ? $parts[1] : '' );
	}
	return $out;
}

/** Número: mantém inteiro quando não tem casas decimais. */
function tabi_to_number( $raw ) {
	$n = (float) str_replace( ',', '.', (string) $raw );
	return ( $n === (float) (int) $n ) ? (int) $n : $n;
}

/** Monta o array de conteúdo a partir do formulário estruturado. */
function tabi_content_from_form( $in ) {
	$content = array();

	$content['hero'] = array(
		'titleLines'  => tabi_lines_to_array( sanitize_textarea_field( $in['hero']['titleLines'] ?? '' ) ),
		'description' => sanitize_textarea_field( $in['hero']['description'] ?? '' ),
	);

	$stats = array();
	foreach ( array_values( (array) ( $in['about']['stats'] ?? array() ) ) as $row ) {
		if ( '' === trim( $row['label'] ?? '' ) ) { continue; }
		$stats[] = array(
			'numeric' => tabi_to_number( $row['numeric'] ?? 0 ),
			'suffix'  => sanitize_text_field( $row['suffix'] ?? '' ),
			'label'   => sanitize_text_field( $row['label'] ?? '' ),
		);
	}
	$pillars = array();
	foreach ( array_values( (array) ( $in['about']['pillars'] ?? array() ) ) as $row ) {
		if ( '' === trim( $row['title'] ?? '' ) ) { continue; }
		$pillars[] = array(
			'title' => sanitize_text_field( $row['title'] ?? '' ),
			'body'  => sanitize_textarea_field( $row['body'] ?? '' ),
		);
	}
	$content['about'] = array(
		'paragraph1' => sanitize_textarea_field( $in['about']['paragraph1'] ?? '' ),
		'paragraph2' => sanitize_textarea_field( $in['about']['paragraph2'] ?? '' ),
		'stats'      => $stats,
		'pillars'    => $pillars,
	);

	$services = array();
	foreach ( array_values( (array) ( $in['services'] ?? array() ) ) as $i => $row ) {
		if ( '' === trim( $row['title'] ?? '' ) ) { continue; }
		$services[] = array(
			'num'   => str_pad( (string) ( count( $services ) + 1 ), 2, '0', STR_PAD_LEFT ),
			'title' => sanitize_text_field( $row['title'] ?? '' ),
			'body'  => sanitize_textarea_field( $row['body'] ?? '' ),
		);
	}
	$content['services'] = $services;

	$projects = array();
	foreach ( array_values( (array) ( $in['projects'] ?? array() ) ) as $row ) {
		if ( '' === trim( $row['name'] ?? '' ) ) { continue; }
		$projects[] = array(
			'id'       => str_pad( (string) ( count( $projects ) + 1 ), 2, '0', STR_PAD_LEFT ),
			'name'     => sanitize_text_field( $row['name'] ?? '' ),
			'category' => sanitize_text_field( $row['category'] ?? '' ),
			'year'     => sanitize_text_field( $row['year'] ?? '' ),
			'bg'       => sanitize_text_field( $row['bg'] ?? '' ),
			'accent'   => sanitize_text_field( $row['accent'] ?? '#F20C25' ),
			'featured' => ! empty( $row['featured'] ),
			'imageUrl' => esc_url_raw( $row['imageUrl'] ?? '' ),
			'detail'   => array(
				'client'      => sanitize_text_field( $row['client'] ?? ( $row['name'] ?? '' ) ),
				'scope'       => tabi_lines_to_array( sanitize_textarea_field( $row['scope'] ?? '' ) ),
				'duration'    => sanitize_text_field( $row['duration'] ?? '' ),
				'challenge'   => sanitize_textarea_field( $row['challenge'] ?? '' ),
				'solution'    => sanitize_textarea_field( $row['solution'] ?? '' ),
				'results'     => tabi_pairs_to_array( sanitize_textarea_field( $row['results'] ?? '' ), 'label', 'value' ),
				'mockupLines' => tabi_lines_to_array( sanitize_textarea_field( $row['mockupLines'] ?? '' ) ),
			),
		);
	}
	$content['projects'] = $projects;

	$faq = array();
	foreach ( array_values( (array) ( $in['faq'] ?? array() ) ) as $row ) {
		if ( '' === trim( $row['q'] ?? '' ) ) { continue; }
		$faq[] = array(
			'q' => sanitize_text_field( $row['q'] ?? '' ),
			'a' => sanitize_textarea_field( $row['a'] ?? '' ),
		);
	}
	$content['faq'] = $faq;

	$content['footer'] = array(
		'tagline' => sanitize_text_field( $in['footer']['tagline'] ?? '' ),
		'email'   => sanitize_text_field( $in['footer']['email'] ?? '' ),
		'phone'   => sanitize_text_field( $in['footer']['phone'] ?? '' ),
		'city'    => sanitize_text_field( $in['footer']['city'] ?? '' ),
	);

	return $content;
}

// ── Campos reutilizáveis ─────────────────────────────────────────────────────

function tabi_field_text( $name, $label, $value, $hint = '', $type = 'text' ) {
	?>
	<p class="tabi-field">
		<label><strong><?php echo esc_html( $label ); ?></strong><br/>
		<input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="widefat" /></label>
		<?php if ( $hint ) : ?><span class="description"><?php echo esc_html( $hint ); ?></span><?php endif; ?>
	</p>
	<?php
}

function tabi_field_textarea( $name, $label, $value, $hint = '', $rows = 3 ) {
	?>
	<p class="tabi-field">
		<label><strong><?php echo esc_html( $label ); ?></strong><br/>
		<textarea name="<?php echo esc_attr( $name ); ?>" rows="<?php echo (int) $rows; ?>" class="widefat"><?php echo esc_textarea( $value ); ?></textarea></label>
		<?php if ( $hint ) : ?><span class="description"><?php echo esc_html( $hint ); ?></span><?php endif; ?>
	</p>
	<?php
}

// ── Página principal (campos amigáveis) ──────────────────────────────────────

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

			$result = tabi_save_content( tabi_content_from_form( wp_unslash( (array) ( $_POST['tabi'] ?? array() ) ) ) );
			$notice = is_wp_error( $result )
				? array( 'error', $result->get_error_message() )
				: array( 'success', __( 'Conteúdo salvo. O site já reflete as alterações.', 'studio-tabi-headless' ) );
		}
	}

	$c            = tabi_get_content();
	$frontend_url = get_option( TABI_OPTION_FRONTEND_URL, '' );

	$pairs_to_text = function ( $pairs, $a, $b ) {
		return implode( "\n", array_map( function ( $p ) use ( $a, $b ) {
			return ( $p[ $a ] ?? '' ) . ' | ' . ( $p[ $b ] ?? '' );
		}, (array) $pairs ) );
	};
	?>
	<style>
		.tabi-card { background: #fff; border: 1px solid #dcdcde; border-radius: 6px; margin: 0 0 14px; max-width: 900px; }
		.tabi-card > summary { cursor: pointer; padding: 14px 18px; font-size: 15px; font-weight: 600; }
		.tabi-card > .inside { padding: 4px 18px 16px; border-top: 1px solid #f0f0f1; }
		.tabi-field { margin: 14px 0; }
		.tabi-field .description { display: block; margin-top: 2px; }
		.tabi-row { border: 1px solid #e2e2e5; border-radius: 6px; padding: 4px 14px 10px; margin: 10px 0; background: #fafafa; position: relative; }
		.tabi-row .tabi-remove-row { position: absolute; top: 8px; right: 8px; }
		.tabi-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }
		@media (max-width: 782px) { .tabi-cols { grid-template-columns: 1fr; } }
	</style>

	<div class="wrap">
		<h1><?php esc_html_e( 'Conteúdo do Site — Studio Tabi', 'studio-tabi-headless' ); ?></h1>

		<?php if ( $notice ) : ?>
			<div class="notice notice-<?php echo esc_attr( $notice[0] ); ?> is-dismissible"><p><?php echo esc_html( $notice[1] ); ?></p></div>
		<?php endif; ?>

		<p><?php esc_html_e( 'Edite os textos do site abaixo e clique em "Salvar conteúdo". As alterações aparecem no site imediatamente.', 'studio-tabi-headless' ); ?></p>

		<form method="post">
			<?php wp_nonce_field( 'tabi_save_content' ); ?>
			<input type="hidden" name="tabi_action" value="save" />

			<details class="tabi-card" open>
				<summary><?php esc_html_e( 'Configuração', 'studio-tabi-headless' ); ?></summary>
				<div class="inside">
					<?php tabi_field_text( 'tabi_frontend_url', __( 'URL do site (front-end)', 'studio-tabi-headless' ), $frontend_url, __( 'Endereço público do site React. Necessário para o site conseguir buscar o conteúdo.', 'studio-tabi-headless' ), 'url' ); ?>
				</div>
			</details>

			<details class="tabi-card" open>
				<summary><?php esc_html_e( 'Hero (topo do site)', 'studio-tabi-headless' ); ?></summary>
				<div class="inside">
					<?php tabi_field_textarea( 'tabi[hero][titleLines]', __( 'Título', 'studio-tabi-headless' ), implode( "\n", $c['hero']['titleLines'] ), __( 'Uma linha do título por linha do campo.', 'studio-tabi-headless' ), 3 ); ?>
					<?php tabi_field_textarea( 'tabi[hero][description]', __( 'Descrição', 'studio-tabi-headless' ), $c['hero']['description'], '', 3 ); ?>
				</div>
			</details>

			<details class="tabi-card">
				<summary><?php esc_html_e( 'Sobre', 'studio-tabi-headless' ); ?></summary>
				<div class="inside">
					<?php tabi_field_textarea( 'tabi[about][paragraph1]', __( 'Parágrafo 1', 'studio-tabi-headless' ), $c['about']['paragraph1'], '', 4 ); ?>
					<?php tabi_field_textarea( 'tabi[about][paragraph2]', __( 'Parágrafo 2', 'studio-tabi-headless' ), $c['about']['paragraph2'], '', 4 ); ?>

					<h4><?php esc_html_e( 'Números (estatísticas)', 'studio-tabi-headless' ); ?></h4>
					<div id="tabi-stats">
						<?php foreach ( $c['about']['stats'] as $i => $s ) : ?>
							<div class="tabi-row">
								<button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button>
								<div class="tabi-cols">
									<?php tabi_field_text( "tabi[about][stats][$i][numeric]", __( 'Número', 'studio-tabi-headless' ), $s['numeric'] ); ?>
									<?php tabi_field_text( "tabi[about][stats][$i][suffix]", __( 'Sufixo (+, %, ×…)', 'studio-tabi-headless' ), $s['suffix'] ); ?>
								</div>
								<?php tabi_field_text( "tabi[about][stats][$i][label]", __( 'Rótulo', 'studio-tabi-headless' ), $s['label'] ); ?>
							</div>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button tabi-add-row" data-target="tabi-stats" data-template="tpl-stat"><?php esc_html_e( '+ Adicionar número', 'studio-tabi-headless' ); ?></button>

					<h4><?php esc_html_e( 'Pilares', 'studio-tabi-headless' ); ?></h4>
					<div id="tabi-pillars">
						<?php foreach ( $c['about']['pillars'] as $i => $p ) : ?>
							<div class="tabi-row">
								<button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button>
								<?php tabi_field_text( "tabi[about][pillars][$i][title]", __( 'Título', 'studio-tabi-headless' ), $p['title'] ); ?>
								<?php tabi_field_textarea( "tabi[about][pillars][$i][body]", __( 'Texto', 'studio-tabi-headless' ), $p['body'], '', 3 ); ?>
							</div>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button tabi-add-row" data-target="tabi-pillars" data-template="tpl-pillar"><?php esc_html_e( '+ Adicionar pilar', 'studio-tabi-headless' ); ?></button>
				</div>
			</details>

			<details class="tabi-card">
				<summary><?php esc_html_e( 'Serviços', 'studio-tabi-headless' ); ?></summary>
				<div class="inside">
					<div id="tabi-services">
						<?php foreach ( $c['services'] as $i => $s ) : ?>
							<div class="tabi-row">
								<button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button>
								<?php tabi_field_text( "tabi[services][$i][title]", __( 'Título', 'studio-tabi-headless' ), $s['title'] ); ?>
								<?php tabi_field_textarea( "tabi[services][$i][body]", __( 'Descrição', 'studio-tabi-headless' ), $s['body'], '', 3 ); ?>
							</div>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button tabi-add-row" data-target="tabi-services" data-template="tpl-service"><?php esc_html_e( '+ Adicionar serviço', 'studio-tabi-headless' ); ?></button>
					<p class="description"><?php esc_html_e( 'A numeração (01, 02…) é gerada automaticamente pela ordem.', 'studio-tabi-headless' ); ?></p>
				</div>
			</details>

			<details class="tabi-card">
				<summary><?php esc_html_e( 'Projetos', 'studio-tabi-headless' ); ?></summary>
				<div class="inside">
					<div id="tabi-projects">
						<?php foreach ( $c['projects'] as $i => $p ) : $d = $p['detail']; ?>
							<div class="tabi-row">
								<button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button>
								<div class="tabi-cols">
									<?php tabi_field_text( "tabi[projects][$i][name]", __( 'Nome', 'studio-tabi-headless' ), $p['name'] ); ?>
									<?php tabi_field_text( "tabi[projects][$i][category]", __( 'Categoria', 'studio-tabi-headless' ), $p['category'] ); ?>
									<?php tabi_field_text( "tabi[projects][$i][year]", __( 'Ano', 'studio-tabi-headless' ), $p['year'] ); ?>
									<?php tabi_field_text( "tabi[projects][$i][accent]", __( 'Cor de destaque', 'studio-tabi-headless' ), $p['accent'], __( 'Ex.: #F20C25', 'studio-tabi-headless' ) ); ?>
								</div>
								<p class="tabi-field"><label><input type="checkbox" name="tabi[projects][<?php echo esc_attr( $i ); ?>][featured]" value="1" <?php checked( ! empty( $p['featured'] ) ); ?> /> <?php esc_html_e( 'Projeto em destaque', 'studio-tabi-headless' ); ?></label></p>
								<?php tabi_field_text( "tabi[projects][$i][imageUrl]", __( 'Imagem (URL, opcional)', 'studio-tabi-headless' ), $p['imageUrl'] ?? '', __( 'Cole a URL de uma imagem da Biblioteca de Mídia.', 'studio-tabi-headless' ), 'url' ); ?>
								<?php tabi_field_text( "tabi[projects][$i][bg]", __( 'Fundo (CSS, avançado)', 'studio-tabi-headless' ), $p['bg'] ); ?>
								<div class="tabi-cols">
									<?php tabi_field_text( "tabi[projects][$i][client]", __( 'Cliente', 'studio-tabi-headless' ), $d['client'] ); ?>
									<?php tabi_field_text( "tabi[projects][$i][duration]", __( 'Duração', 'studio-tabi-headless' ), $d['duration'] ); ?>
								</div>
								<?php tabi_field_textarea( "tabi[projects][$i][scope]", __( 'Escopo', 'studio-tabi-headless' ), implode( "\n", $d['scope'] ), __( 'Um item por linha.', 'studio-tabi-headless' ), 3 ); ?>
								<?php tabi_field_textarea( "tabi[projects][$i][challenge]", __( 'Desafio', 'studio-tabi-headless' ), $d['challenge'], '', 4 ); ?>
								<?php tabi_field_textarea( "tabi[projects][$i][solution]", __( 'Solução', 'studio-tabi-headless' ), $d['solution'], '', 4 ); ?>
								<?php tabi_field_textarea( "tabi[projects][$i][results]", __( 'Resultados', 'studio-tabi-headless' ), $pairs_to_text( $d['results'], 'label', 'value' ), __( 'Um por linha, no formato: Rótulo | Valor', 'studio-tabi-headless' ), 4 ); ?>
								<?php tabi_field_textarea( "tabi[projects][$i][mockupLines]", __( 'Menu do mockup', 'studio-tabi-headless' ), implode( "\n", $d['mockupLines'] ), __( 'Um item por linha.', 'studio-tabi-headless' ), 4 ); ?>
							</div>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button tabi-add-row" data-target="tabi-projects" data-template="tpl-project"><?php esc_html_e( '+ Adicionar projeto', 'studio-tabi-headless' ); ?></button>
				</div>
			</details>

			<details class="tabi-card">
				<summary><?php esc_html_e( 'FAQ (perguntas frequentes)', 'studio-tabi-headless' ); ?></summary>
				<div class="inside">
					<div id="tabi-faq">
						<?php foreach ( $c['faq'] as $i => $f ) : ?>
							<div class="tabi-row">
								<button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button>
								<?php tabi_field_text( "tabi[faq][$i][q]", __( 'Pergunta', 'studio-tabi-headless' ), $f['q'] ); ?>
								<?php tabi_field_textarea( "tabi[faq][$i][a]", __( 'Resposta', 'studio-tabi-headless' ), $f['a'], '', 3 ); ?>
							</div>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button tabi-add-row" data-target="tabi-faq" data-template="tpl-faq"><?php esc_html_e( '+ Adicionar pergunta', 'studio-tabi-headless' ); ?></button>
				</div>
			</details>

			<details class="tabi-card">
				<summary><?php esc_html_e( 'Rodapé', 'studio-tabi-headless' ); ?></summary>
				<div class="inside">
					<?php tabi_field_text( 'tabi[footer][tagline]', __( 'Frase', 'studio-tabi-headless' ), $c['footer']['tagline'] ); ?>
					<div class="tabi-cols">
						<?php tabi_field_text( 'tabi[footer][email]', __( 'E-mail', 'studio-tabi-headless' ), $c['footer']['email'] ); ?>
						<?php tabi_field_text( 'tabi[footer][phone]', __( 'Telefone', 'studio-tabi-headless' ), $c['footer']['phone'] ); ?>
					</div>
					<?php tabi_field_text( 'tabi[footer][city]', __( 'Cidade', 'studio-tabi-headless' ), $c['footer']['city'] ); ?>
				</div>
			</details>

			<p class="submit">
				<button type="submit" class="button button-primary button-hero"><?php esc_html_e( 'Salvar conteúdo', 'studio-tabi-headless' ); ?></button>
			</p>
		</form>

		<form method="post" onsubmit="return confirm('<?php echo esc_js( __( 'Restaurar todo o conteúdo para os valores padrão?', 'studio-tabi-headless' ) ); ?>');">
			<?php wp_nonce_field( 'tabi_save_content' ); ?>
			<input type="hidden" name="tabi_action" value="reset" />
			<button type="submit" class="button"><?php esc_html_e( 'Restaurar padrão', 'studio-tabi-headless' ); ?></button>
		</form>
	</div>

	<?php // Modelos de linha para os botões "+ Adicionar" ?>
	<template id="tpl-stat"><div class="tabi-row"><button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button><div class="tabi-cols"><?php tabi_field_text( 'tabi[about][stats][__INDEX__][numeric]', __( 'Número', 'studio-tabi-headless' ), '' ); tabi_field_text( 'tabi[about][stats][__INDEX__][suffix]', __( 'Sufixo (+, %, ×…)', 'studio-tabi-headless' ), '' ); ?></div><?php tabi_field_text( 'tabi[about][stats][__INDEX__][label]', __( 'Rótulo', 'studio-tabi-headless' ), '' ); ?></div></template>
	<template id="tpl-pillar"><div class="tabi-row"><button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button><?php tabi_field_text( 'tabi[about][pillars][__INDEX__][title]', __( 'Título', 'studio-tabi-headless' ), '' ); tabi_field_textarea( 'tabi[about][pillars][__INDEX__][body]', __( 'Texto', 'studio-tabi-headless' ), '', '', 3 ); ?></div></template>
	<template id="tpl-service"><div class="tabi-row"><button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button><?php tabi_field_text( 'tabi[services][__INDEX__][title]', __( 'Título', 'studio-tabi-headless' ), '' ); tabi_field_textarea( 'tabi[services][__INDEX__][body]', __( 'Descrição', 'studio-tabi-headless' ), '', '', 3 ); ?></div></template>
	<template id="tpl-project"><div class="tabi-row"><button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button><div class="tabi-cols"><?php tabi_field_text( 'tabi[projects][__INDEX__][name]', __( 'Nome', 'studio-tabi-headless' ), '' ); tabi_field_text( 'tabi[projects][__INDEX__][category]', __( 'Categoria', 'studio-tabi-headless' ), '' ); tabi_field_text( 'tabi[projects][__INDEX__][year]', __( 'Ano', 'studio-tabi-headless' ), '' ); tabi_field_text( 'tabi[projects][__INDEX__][accent]', __( 'Cor de destaque', 'studio-tabi-headless' ), '#F20C25' ); ?></div><p class="tabi-field"><label><input type="checkbox" name="tabi[projects][__INDEX__][featured]" value="1" /> <?php esc_html_e( 'Projeto em destaque', 'studio-tabi-headless' ); ?></label></p><?php tabi_field_text( 'tabi[projects][__INDEX__][imageUrl]', __( 'Imagem (URL, opcional)', 'studio-tabi-headless' ), '' ); tabi_field_text( 'tabi[projects][__INDEX__][bg]', __( 'Fundo (CSS, avançado)', 'studio-tabi-headless' ), 'linear-gradient(135deg,#1A0505 0%,#2D0A0A 50%,#1A0A14 100%)' ); ?><div class="tabi-cols"><?php tabi_field_text( 'tabi[projects][__INDEX__][client]', __( 'Cliente', 'studio-tabi-headless' ), '' ); tabi_field_text( 'tabi[projects][__INDEX__][duration]', __( 'Duração', 'studio-tabi-headless' ), '' ); ?></div><?php tabi_field_textarea( 'tabi[projects][__INDEX__][scope]', __( 'Escopo', 'studio-tabi-headless' ), '', __( 'Um item por linha.', 'studio-tabi-headless' ), 3 ); tabi_field_textarea( 'tabi[projects][__INDEX__][challenge]', __( 'Desafio', 'studio-tabi-headless' ), '', '', 4 ); tabi_field_textarea( 'tabi[projects][__INDEX__][solution]', __( 'Solução', 'studio-tabi-headless' ), '', '', 4 ); tabi_field_textarea( 'tabi[projects][__INDEX__][results]', __( 'Resultados', 'studio-tabi-headless' ), '', __( 'Um por linha, no formato: Rótulo | Valor', 'studio-tabi-headless' ), 4 ); tabi_field_textarea( 'tabi[projects][__INDEX__][mockupLines]', __( 'Menu do mockup', 'studio-tabi-headless' ), '', __( 'Um item por linha.', 'studio-tabi-headless' ), 4 ); ?></div></template>
	<template id="tpl-faq"><div class="tabi-row"><button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button><?php tabi_field_text( 'tabi[faq][__INDEX__][q]', __( 'Pergunta', 'studio-tabi-headless' ), '' ); tabi_field_textarea( 'tabi[faq][__INDEX__][a]', __( 'Resposta', 'studio-tabi-headless' ), '', '', 3 ); ?></div></template>

	<script>
	(function () {
		let n = 0;
		document.querySelectorAll('.tabi-add-row').forEach(function (btn) {
			btn.addEventListener('click', function () {
				const tpl = document.getElementById(btn.dataset.template);
				const html = tpl.innerHTML.replaceAll('__INDEX__', 'new' + Date.now() + (n++));
				document.getElementById(btn.dataset.target).insertAdjacentHTML('beforeend', html);
			});
		});
		document.addEventListener('click', function (e) {
			if (e.target.classList && e.target.classList.contains('tabi-remove-row')) {
				e.target.closest('.tabi-row').remove();
			}
		});
	})();
	</script>
	<?php
}

// ── Página avançada (editor JSON) ────────────────────────────────────────────

function tabi_render_json_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$notice = null;

	if ( isset( $_POST['tabi_action'] ) && check_admin_referer( 'tabi_save_content_json' ) ) {
		$raw     = isset( $_POST['tabi_content_json'] ) ? wp_unslash( $_POST['tabi_content_json'] ) : '';
		$decoded = json_decode( $raw, true );

		if ( null === $decoded && JSON_ERROR_NONE !== json_last_error() ) {
			$notice = array( 'error', sprintf( __( 'JSON inválido: %s. Nada foi salvo.', 'studio-tabi-headless' ), json_last_error_msg() ) );
		} else {
			$result = tabi_save_content( $decoded );
			$notice = is_wp_error( $result )
				? array( 'error', $result->get_error_message() )
				: array( 'success', __( 'Conteúdo salvo.', 'studio-tabi-headless' ) );
		}
	}

	$json     = wp_json_encode( tabi_get_content(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	$endpoint = rest_url( 'tabi/v1/content' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Editor JSON (avançado)', 'studio-tabi-headless' ); ?></h1>

		<?php if ( $notice ) : ?>
			<div class="notice notice-<?php echo esc_attr( $notice[0] ); ?> is-dismissible"><p><?php echo esc_html( $notice[1] ); ?></p></div>
		<?php endif; ?>

		<p>
			<?php esc_html_e( 'Edição direta do JSON completo servido em', 'studio-tabi-headless' ); ?>
			<code><?php echo esc_html( $endpoint ); ?></code>.
			<?php esc_html_e( 'Para edição comum, prefira a página "Conteúdo do Site".', 'studio-tabi-headless' ); ?>
		</p>

		<form method="post">
			<?php wp_nonce_field( 'tabi_save_content_json' ); ?>
			<input type="hidden" name="tabi_action" value="save" />
			<textarea name="tabi_content_json" rows="30" class="large-text code" spellcheck="false" style="font-family:monospace;"><?php echo esc_textarea( $json ); ?></textarea>
			<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Salvar JSON', 'studio-tabi-headless' ); ?></button></p>
		</form>
	</div>
	<?php
}
