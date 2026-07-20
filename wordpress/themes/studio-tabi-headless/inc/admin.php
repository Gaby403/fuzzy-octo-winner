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

// Carrega a Biblioteca de Mídia só na nossa página de edição.
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( 'toplevel_page_tabi-content' === $hook ) {
		wp_enqueue_media();
	}
} );

// ── Helpers ──────────────────────────────────────────────────────────────────

/** Converte textarea "um item por linha" em array de strings. */
function tabi_lines_to_array( $raw ) {
	$lines = array_map( 'trim', explode( "\n", (string) $raw ) );
	return array_values( array_filter( $lines, function ( $l ) { return '' !== $l; } ) );
}

/** Converte textarea "Rótulo | Valor" (um por linha) em array de pares. Compat. */
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

/** Lê os resultados de um projeto vindos como pares (label/value) ou texto legado. */
function tabi_results_from_input( $rin ) {
	$out = array();
	if ( is_array( $rin ) ) {
		foreach ( array_values( $rin ) as $r ) {
			$label = sanitize_text_field( $r['label'] ?? '' );
			$value = sanitize_text_field( $r['value'] ?? '' );
			if ( '' === $label && '' === $value ) { continue; }
			$out[] = array( 'label' => $label, 'value' => $value );
		}
		return $out;
	}
	return tabi_pairs_to_array( sanitize_textarea_field( (string) $rin ), 'label', 'value' );
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
	foreach ( array_values( (array) ( $in['services'] ?? array() ) ) as $row ) {
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
		$accent = sanitize_text_field( $row['accent'] ?? '#F20C25' );
		$projects[] = array(
			'id'       => str_pad( (string) ( count( $projects ) + 1 ), 2, '0', STR_PAD_LEFT ),
			'name'     => sanitize_text_field( $row['name'] ?? '' ),
			'category' => sanitize_text_field( $row['category'] ?? '' ),
			'year'     => sanitize_text_field( $row['year'] ?? '' ),
			'bg'       => '' !== trim( $row['bg'] ?? '' ) ? sanitize_text_field( $row['bg'] ) : tabi_gradient_from_accent( $accent ),
			'accent'   => $accent,
			'featured' => ! empty( $row['featured'] ),
			'imageUrl' => esc_url_raw( $row['imageUrl'] ?? '' ),
			'detail'   => array(
				'client'      => sanitize_text_field( $row['client'] ?? ( $row['name'] ?? '' ) ),
				'scope'       => tabi_lines_to_array( sanitize_textarea_field( $row['scope'] ?? '' ) ),
				'duration'    => sanitize_text_field( $row['duration'] ?? '' ),
				'challenge'   => sanitize_textarea_field( $row['challenge'] ?? '' ),
				'solution'    => sanitize_textarea_field( $row['solution'] ?? '' ),
				'results'     => tabi_results_from_input( $row['results'] ?? array() ),
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

/** Gera um gradiente escuro a partir da cor de destaque (para o card do projeto). */
function tabi_gradient_from_accent( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) ) {
		return 'linear-gradient(135deg,#1A0505 0%,#2D0A0A 50%,#1A0A14 100%)';
	}
	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );
	// Mistura ~12% da cor com preto para um fundo escuro tonalizado.
	$mix = function ( $c ) { return (int) round( $c * 0.12 ); };
	$c1  = sprintf( '#%02X%02X%02X', $mix( $r ), $mix( $g ), $mix( $b ) );
	$c2  = sprintf( '#%02X%02X%02X', (int) round( $r * 0.20 ), (int) round( $g * 0.20 ), (int) round( $b * 0.20 ) );
	return "linear-gradient(135deg,{$c1} 0%,{$c2} 55%,#0D0A0A 100%)";
}

// ── Campos reutilizáveis ─────────────────────────────────────────────────────

function tabi_field_text( $name, $label, $value, $hint = '', $type = 'text', $placeholder = '' ) {
	?>
	<p class="tabi-field">
		<label><span class="tabi-label"><?php echo esc_html( $label ); ?></span>
		<input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" class="widefat" /></label>
		<?php if ( $hint ) : ?><span class="description"><?php echo esc_html( $hint ); ?></span><?php endif; ?>
	</p>
	<?php
}

function tabi_field_textarea( $name, $label, $value, $hint = '', $rows = 3, $placeholder = '' ) {
	?>
	<p class="tabi-field">
		<label><span class="tabi-label"><?php echo esc_html( $label ); ?></span>
		<textarea name="<?php echo esc_attr( $name ); ?>" rows="<?php echo (int) $rows; ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" class="widefat"><?php echo esc_textarea( $value ); ?></textarea></label>
		<?php if ( $hint ) : ?><span class="description"><?php echo esc_html( $hint ); ?></span><?php endif; ?>
	</p>
	<?php
}

/** Seletor de cor (nativo) + campo de texto com o código, sincronizados. */
function tabi_field_color( $name, $label, $value ) {
	$value = $value ? $value : '#F20C25';
	?>
	<p class="tabi-field">
		<label><span class="tabi-label"><?php echo esc_html( $label ); ?></span></label>
		<span class="tabi-color">
			<input type="color" class="tabi-color-picker" value="<?php echo esc_attr( $value ); ?>" />
			<input type="text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="tabi-color-text" />
		</span>
	</p>
	<?php
}

/** Botão de escolher imagem na Biblioteca de Mídia, com prévia. */
function tabi_field_image( $name, $label, $value ) {
	?>
	<div class="tabi-field tabi-media">
		<span class="tabi-label"><?php echo esc_html( $label ); ?></span>
		<div class="tabi-media-row">
			<img class="tabi-media-preview" src="<?php echo esc_url( $value ); ?>" style="<?php echo $value ? '' : 'display:none;'; ?>" alt="" />
			<input type="hidden" class="tabi-media-url" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
			<button type="button" class="button tabi-media-btn"><?php esc_html_e( 'Escolher imagem', 'studio-tabi-headless' ); ?></button>
			<button type="button" class="button-link tabi-media-remove" style="<?php echo $value ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button>
		</div>
	</div>
	<?php
}

/** Uma linha de resultado (rótulo + valor). */
function tabi_result_row( $proj, $res, $label = '', $value = '' ) {
	?>
	<div class="tabi-result-row">
		<input type="text" name="tabi[projects][<?php echo esc_attr( $proj ); ?>][results][<?php echo esc_attr( $res ); ?>][label]" value="<?php echo esc_attr( $label ); ?>" placeholder="<?php esc_attr_e( 'Ex.: Aumento em conversão', 'studio-tabi-headless' ); ?>" />
		<input type="text" name="tabi[projects][<?php echo esc_attr( $proj ); ?>][results][<?php echo esc_attr( $res ); ?>][value]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'Ex.: +38%', 'studio-tabi-headless' ); ?>" class="tabi-result-value" />
		<button type="button" class="button-link-delete tabi-remove-inline" title="<?php esc_attr_e( 'Remover', 'studio-tabi-headless' ); ?>">✕</button>
	</div>
	<?php
}

/** Bloco completo de um projeto (usado na listagem e no template de novo item). */
function tabi_project_fields( $i, $p ) {
	$d = isset( $p['detail'] ) ? $p['detail'] : array();
	?>
	<div class="tabi-row tabi-project" data-proj="<?php echo esc_attr( $i ); ?>">
		<button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover projeto', 'studio-tabi-headless' ); ?></button>

		<div class="tabi-cols">
			<?php tabi_field_text( "tabi[projects][$i][name]", __( 'Nome do projeto', 'studio-tabi-headless' ), $p['name'] ?? '' ); ?>
			<?php tabi_field_text( "tabi[projects][$i][category]", __( 'Categoria', 'studio-tabi-headless' ), $p['category'] ?? '', '', 'text', 'Branding & UI' ); ?>
			<?php tabi_field_text( "tabi[projects][$i][year]", __( 'Ano', 'studio-tabi-headless' ), $p['year'] ?? '', '', 'text', '2025' ); ?>
			<?php tabi_field_color( "tabi[projects][$i][accent]", __( 'Cor de destaque', 'studio-tabi-headless' ), $p['accent'] ?? '#F20C25' ); ?>
		</div>

		<?php tabi_field_image( "tabi[projects][$i][imageUrl]", __( 'Imagem do projeto', 'studio-tabi-headless' ), $p['imageUrl'] ?? '' ); ?>

		<p class="tabi-field tabi-check">
			<label><input type="checkbox" name="tabi[projects][<?php echo esc_attr( $i ); ?>][featured]" value="1" <?php checked( ! empty( $p['featured'] ) ); ?> /> <?php esc_html_e( 'Destacar este projeto', 'studio-tabi-headless' ); ?></label>
		</p>

		<details class="tabi-sub">
			<summary><?php esc_html_e( 'Detalhes do case (página do projeto)', 'studio-tabi-headless' ); ?></summary>
			<div class="tabi-sub-inside">
				<div class="tabi-cols">
					<?php tabi_field_text( "tabi[projects][$i][client]", __( 'Cliente', 'studio-tabi-headless' ), $d['client'] ?? '' ); ?>
					<?php tabi_field_text( "tabi[projects][$i][duration]", __( 'Duração', 'studio-tabi-headless' ), $d['duration'] ?? '', '', 'text', '14 semanas' ); ?>
				</div>
				<?php tabi_field_textarea( "tabi[projects][$i][scope]", __( 'Escopo', 'studio-tabi-headless' ), implode( "\n", (array) ( $d['scope'] ?? array() ) ), __( 'Um item por linha.', 'studio-tabi-headless' ), 3, "Identidade Visual\nUI/UX Design" ); ?>
				<?php tabi_field_textarea( "tabi[projects][$i][challenge]", __( 'Desafio', 'studio-tabi-headless' ), $d['challenge'] ?? '', '', 4 ); ?>
				<?php tabi_field_textarea( "tabi[projects][$i][solution]", __( 'Solução', 'studio-tabi-headless' ), $d['solution'] ?? '', '', 4 ); ?>

				<div class="tabi-results">
					<span class="tabi-label"><?php esc_html_e( 'Resultados', 'studio-tabi-headless' ); ?></span>
					<div class="tabi-results-list">
						<?php foreach ( (array) ( $d['results'] ?? array() ) as $j => $r ) : ?>
							<?php tabi_result_row( $i, $j, $r['label'] ?? '', $r['value'] ?? '' ); ?>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button tabi-add-result"><?php esc_html_e( '+ Adicionar resultado', 'studio-tabi-headless' ); ?></button>
				</div>

				<?php tabi_field_textarea( "tabi[projects][$i][mockupLines]", __( 'Itens do menu (mockup)', 'studio-tabi-headless' ), implode( "\n", (array) ( $d['mockupLines'] ?? array() ) ), __( 'Um item por linha.', 'studio-tabi-headless' ), 4, "DASHBOARD\nRELATÓRIOS" ); ?>
			</div>
		</details>

		<details class="tabi-sub">
			<summary><?php esc_html_e( 'Aparência avançada', 'studio-tabi-headless' ); ?></summary>
			<div class="tabi-sub-inside">
				<?php tabi_field_text( "tabi[projects][$i][bg]", __( 'Fundo do card (CSS)', 'studio-tabi-headless' ), $p['bg'] ?? '', __( 'Deixe em branco para gerar automaticamente a partir da cor de destaque.', 'studio-tabi-headless' ) ); ?>
			</div>
		</details>
	</div>
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
			$result = tabi_save_content( tabi_content_from_form( wp_unslash( (array) ( $_POST['tabi'] ?? array() ) ) ) );
			$notice = is_wp_error( $result )
				? array( 'error', $result->get_error_message() )
				: array( 'success', __( 'Conteúdo salvo! O site já está atualizado.', 'studio-tabi-headless' ) );
		}
	}

	$c = tabi_get_content();
	?>
	<style>
		.tabi-wrap { max-width: 920px; }
		.tabi-intro { background:#fff; border:1px solid #dcdcde; border-left:4px solid #2271b1; border-radius:6px; padding:12px 18px; margin:12px 0 18px; }
		.tabi-card { background:#fff; border:1px solid #dcdcde; border-radius:8px; margin:0 0 14px; box-shadow:0 1px 1px rgba(0,0,0,.03); }
		.tabi-card > summary { cursor:pointer; padding:16px 20px; font-size:15px; font-weight:600; list-style:none; display:flex; align-items:center; gap:10px; }
		.tabi-card > summary::-webkit-details-marker { display:none; }
		.tabi-card > summary::after { content:"⌄"; margin-left:auto; font-size:20px; color:#888; line-height:1; }
		.tabi-card[open] > summary::after { content:"⌃"; }
		.tabi-card > summary .tabi-emoji { font-size:18px; }
		.tabi-card > .inside { padding:6px 20px 18px; border-top:1px solid #f0f0f1; }
		.tabi-field { margin:14px 0; }
		.tabi-label { display:block; font-weight:600; margin-bottom:4px; }
		.tabi-field .description { display:block; margin-top:3px; color:#666; }
		.tabi-check label { font-weight:600; }
		.tabi-row { border:1px solid #e2e2e5; border-radius:8px; padding:12px 16px 14px; margin:12px 0; background:#fafafa; position:relative; }
		.tabi-row .tabi-remove-row { position:absolute; top:10px; right:12px; font-size:12px; }
		.tabi-cols { display:grid; grid-template-columns:1fr 1fr; gap:0 18px; }
		@media (max-width:782px){ .tabi-cols { grid-template-columns:1fr; } }
		.tabi-add-row { margin-top:6px; }
		.tabi-sub { margin:12px 0 0; border:1px dashed #d0d0d4; border-radius:6px; }
		.tabi-sub > summary { cursor:pointer; padding:10px 14px; font-weight:600; color:#2271b1; }
		.tabi-sub-inside { padding:4px 14px 12px; }
		.tabi-color { display:flex; align-items:center; gap:8px; }
		.tabi-color-picker { width:44px; height:32px; padding:0; border:1px solid #ccc; border-radius:4px; cursor:pointer; background:none; }
		.tabi-color-text { width:110px; font-family:monospace; }
		.tabi-media-row { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
		.tabi-media-preview { width:64px; height:64px; object-fit:cover; border-radius:6px; border:1px solid #ddd; }
		.tabi-result-row { display:flex; align-items:center; gap:8px; margin:6px 0; }
		.tabi-result-row input { flex:1; }
		.tabi-result-row .tabi-result-value { max-width:140px; flex:0 0 140px; }
		.tabi-remove-inline { color:#b32d2e; text-decoration:none; font-weight:700; padding:0 6px; }
		.tabi-savebar { position:sticky; bottom:0; background:rgba(255,255,255,.96); border-top:1px solid #dcdcde; padding:12px 0; margin-top:10px; z-index:10; }
	</style>

	<div class="wrap tabi-wrap">
		<h1><?php esc_html_e( 'Conteúdo do Site', 'studio-tabi-headless' ); ?></h1>

		<?php if ( $notice ) : ?>
			<div class="notice notice-<?php echo esc_attr( $notice[0] ); ?> is-dismissible"><p><?php echo esc_html( $notice[1] ); ?></p></div>
		<?php endif; ?>

		<div class="tabi-intro">
			<?php esc_html_e( 'Edite os textos e imagens do site nas seções abaixo. Clique em uma seção para abrir. Ao terminar, clique em "Salvar alterações" — o site é atualizado na hora.', 'studio-tabi-headless' ); ?>
		</div>

		<form method="post">
			<?php wp_nonce_field( 'tabi_save_content' ); ?>
			<input type="hidden" name="tabi_action" value="save" />

			<details class="tabi-card" open>
				<summary><span class="tabi-emoji">🏔️</span><?php esc_html_e( 'Topo do site (Hero)', 'studio-tabi-headless' ); ?></summary>
				<div class="inside">
					<?php tabi_field_textarea( 'tabi[hero][titleLines]', __( 'Título principal', 'studio-tabi-headless' ), implode( "\n", $c['hero']['titleLines'] ), __( 'Uma linha do título por linha do campo.', 'studio-tabi-headless' ), 3 ); ?>
					<?php tabi_field_textarea( 'tabi[hero][description]', __( 'Descrição', 'studio-tabi-headless' ), $c['hero']['description'], '', 3 ); ?>
				</div>
			</details>

			<details class="tabi-card">
				<summary><span class="tabi-emoji">💬</span><?php esc_html_e( 'Sobre', 'studio-tabi-headless' ); ?></summary>
				<div class="inside">
					<?php tabi_field_textarea( 'tabi[about][paragraph1]', __( 'Parágrafo 1', 'studio-tabi-headless' ), $c['about']['paragraph1'], '', 4 ); ?>
					<?php tabi_field_textarea( 'tabi[about][paragraph2]', __( 'Parágrafo 2', 'studio-tabi-headless' ), $c['about']['paragraph2'], '', 4 ); ?>

					<h3><?php esc_html_e( 'Números', 'studio-tabi-headless' ); ?></h3>
					<div id="tabi-stats">
						<?php foreach ( $c['about']['stats'] as $i => $s ) : ?>
							<div class="tabi-row">
								<button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button>
								<div class="tabi-cols">
									<?php tabi_field_text( "tabi[about][stats][$i][numeric]", __( 'Número', 'studio-tabi-headless' ), $s['numeric'], '', 'text', '120' ); ?>
									<?php tabi_field_text( "tabi[about][stats][$i][suffix]", __( 'Símbolo', 'studio-tabi-headless' ), $s['suffix'], __( 'Ex.: +, %, ×', 'studio-tabi-headless' ), 'text', '+' ); ?>
								</div>
								<?php tabi_field_text( "tabi[about][stats][$i][label]", __( 'Legenda', 'studio-tabi-headless' ), $s['label'], '', 'text', 'PROJETOS ENTREGUES' ); ?>
							</div>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button tabi-add-row" data-target="tabi-stats" data-template="tpl-stat"><?php esc_html_e( '+ Adicionar número', 'studio-tabi-headless' ); ?></button>

					<h3><?php esc_html_e( 'Pilares', 'studio-tabi-headless' ); ?></h3>
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
				<summary><span class="tabi-emoji">🛠️</span><?php esc_html_e( 'Serviços', 'studio-tabi-headless' ); ?></summary>
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
					<p class="description"><?php esc_html_e( 'A numeração (01, 02…) é automática, conforme a ordem.', 'studio-tabi-headless' ); ?></p>
				</div>
			</details>

			<details class="tabi-card">
				<summary><span class="tabi-emoji">📁</span><?php esc_html_e( 'Projetos', 'studio-tabi-headless' ); ?></summary>
				<div class="inside">
					<div id="tabi-projects">
						<?php foreach ( $c['projects'] as $i => $p ) : ?>
							<?php tabi_project_fields( $i, $p ); ?>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button tabi-add-row" data-target="tabi-projects" data-template="tpl-project"><?php esc_html_e( '+ Adicionar projeto', 'studio-tabi-headless' ); ?></button>
				</div>
			</details>

			<details class="tabi-card">
				<summary><span class="tabi-emoji">❓</span><?php esc_html_e( 'Perguntas frequentes (FAQ)', 'studio-tabi-headless' ); ?></summary>
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
				<summary><span class="tabi-emoji">📮</span><?php esc_html_e( 'Rodapé e contato', 'studio-tabi-headless' ); ?></summary>
				<div class="inside">
					<?php tabi_field_text( 'tabi[footer][tagline]', __( 'Frase do rodapé', 'studio-tabi-headless' ), $c['footer']['tagline'] ); ?>
					<div class="tabi-cols">
						<?php tabi_field_text( 'tabi[footer][email]', __( 'E-mail', 'studio-tabi-headless' ), $c['footer']['email'] ); ?>
						<?php tabi_field_text( 'tabi[footer][phone]', __( 'Telefone', 'studio-tabi-headless' ), $c['footer']['phone'] ); ?>
					</div>
					<?php tabi_field_text( 'tabi[footer][city]', __( 'Cidade', 'studio-tabi-headless' ), $c['footer']['city'] ); ?>
				</div>
			</details>

			<div class="tabi-savebar">
				<button type="submit" class="button button-primary button-hero"><?php esc_html_e( 'Salvar alterações', 'studio-tabi-headless' ); ?></button>
			</div>
		</form>

		<form method="post" onsubmit="return confirm('<?php echo esc_js( __( 'Restaurar todo o conteúdo para os valores padrão? Isso apaga suas edições.', 'studio-tabi-headless' ) ); ?>');" style="margin-top:16px;">
			<?php wp_nonce_field( 'tabi_save_content' ); ?>
			<input type="hidden" name="tabi_action" value="reset" />
			<button type="submit" class="button"><?php esc_html_e( 'Restaurar padrão', 'studio-tabi-headless' ); ?></button>
		</form>
	</div>

	<?php // ── Modelos (templates) para os botões "+ Adicionar" ── ?>
	<template id="tpl-stat"><div class="tabi-row"><button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button><div class="tabi-cols"><?php tabi_field_text( 'tabi[about][stats][__INDEX__][numeric]', __( 'Número', 'studio-tabi-headless' ), '', '', 'text', '120' ); tabi_field_text( 'tabi[about][stats][__INDEX__][suffix]', __( 'Símbolo', 'studio-tabi-headless' ), '', __( 'Ex.: +, %, ×', 'studio-tabi-headless' ), 'text', '+' ); ?></div><?php tabi_field_text( 'tabi[about][stats][__INDEX__][label]', __( 'Legenda', 'studio-tabi-headless' ), '' ); ?></div></template>
	<template id="tpl-pillar"><div class="tabi-row"><button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button><?php tabi_field_text( 'tabi[about][pillars][__INDEX__][title]', __( 'Título', 'studio-tabi-headless' ), '' ); tabi_field_textarea( 'tabi[about][pillars][__INDEX__][body]', __( 'Texto', 'studio-tabi-headless' ), '', '', 3 ); ?></div></template>
	<template id="tpl-service"><div class="tabi-row"><button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button><?php tabi_field_text( 'tabi[services][__INDEX__][title]', __( 'Título', 'studio-tabi-headless' ), '' ); tabi_field_textarea( 'tabi[services][__INDEX__][body]', __( 'Descrição', 'studio-tabi-headless' ), '', '', 3 ); ?></div></template>
	<template id="tpl-project"><?php tabi_project_fields( '__INDEX__', array( 'accent' => '#F20C25' ) ); ?></template>
	<template id="tpl-faq"><div class="tabi-row"><button type="button" class="button-link-delete tabi-remove-row"><?php esc_html_e( 'Remover', 'studio-tabi-headless' ); ?></button><?php tabi_field_text( 'tabi[faq][__INDEX__][q]', __( 'Pergunta', 'studio-tabi-headless' ), '' ); tabi_field_textarea( 'tabi[faq][__INDEX__][a]', __( 'Resposta', 'studio-tabi-headless' ), '', '', 3 ); ?></div></template>
	<template id="tpl-result"><?php tabi_result_row( '__PROJ__', '__RES__' ); ?></template>

	<script>
	(function () {
		function uid() { return 'x' + Date.now().toString(36) + Math.floor(Math.random() * 1e4); }

		// Sincroniza o seletor de cor com o campo de texto (nos dois sentidos).
		function bindColor(scope) {
			(scope || document).querySelectorAll('.tabi-color').forEach(function (wrap) {
				if (wrap.dataset.bound) return;
				wrap.dataset.bound = '1';
				var picker = wrap.querySelector('.tabi-color-picker');
				var text = wrap.querySelector('.tabi-color-text');
				if (!picker || !text) return;
				picker.addEventListener('input', function () { text.value = picker.value.toUpperCase(); });
				text.addEventListener('input', function () { if (/^#[0-9a-fA-F]{6}$/.test(text.value)) picker.value = text.value; });
			});
		}
		bindColor(document);

		// Botões "+ Adicionar" (seções de nível superior).
		document.querySelectorAll('.tabi-add-row').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var tpl = document.getElementById(btn.dataset.template);
				var html = tpl.innerHTML.replaceAll('__INDEX__', uid());
				var container = document.getElementById(btn.dataset.target);
				container.insertAdjacentHTML('beforeend', html);
				bindColor(container.lastElementChild);
			});
		});

		document.addEventListener('click', function (e) {
			// Remover item (linha).
			var rm = e.target.closest('.tabi-remove-row');
			if (rm) { e.preventDefault(); rm.closest('.tabi-row').remove(); return; }

			// Adicionar resultado dentro de um projeto.
			var ar = e.target.closest('.tabi-add-result');
			if (ar) {
				e.preventDefault();
				var proj = ar.closest('.tabi-project').dataset.proj;
				var tpl = document.getElementById('tpl-result');
				var html = tpl.innerHTML.replaceAll('__PROJ__', proj).replaceAll('__RES__', uid());
				ar.closest('.tabi-results').querySelector('.tabi-results-list').insertAdjacentHTML('beforeend', html);
				return;
			}

			// Remover resultado.
			var ri = e.target.closest('.tabi-remove-inline');
			if (ri) { e.preventDefault(); ri.closest('.tabi-result-row').remove(); return; }

			// Escolher imagem (Biblioteca de Mídia).
			var mb = e.target.closest('.tabi-media-btn');
			if (mb && window.wp && wp.media) {
				e.preventDefault();
				var wrap = mb.closest('.tabi-media');
				var frame = wp.media({ title: 'Selecionar imagem', multiple: false, library: { type: 'image' }, button: { text: 'Usar esta imagem' } });
				frame.on('select', function () {
					var att = frame.state().get('selection').first().toJSON();
					wrap.querySelector('.tabi-media-url').value = att.url;
					var img = wrap.querySelector('.tabi-media-preview');
					img.src = att.url; img.style.display = '';
					wrap.querySelector('.tabi-media-remove').style.display = '';
				});
				frame.open();
				return;
			}

			// Remover imagem.
			var mr = e.target.closest('.tabi-media-remove');
			if (mr) {
				e.preventDefault();
				var w = mr.closest('.tabi-media');
				w.querySelector('.tabi-media-url').value = '';
				w.querySelector('.tabi-media-preview').style.display = 'none';
				mr.style.display = 'none';
				return;
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
			<?php esc_html_e( 'Edição direta do JSON completo. Para edição comum, prefira a página "Conteúdo do Site".', 'studio-tabi-headless' ); ?>
			<code><?php echo esc_html( $endpoint ); ?></code>
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
