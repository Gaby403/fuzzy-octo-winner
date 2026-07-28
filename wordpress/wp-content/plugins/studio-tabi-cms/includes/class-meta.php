<?php
/**
 * Custom meta boxes for the st_service and st_project post types.
 * Uses only native WordPress APIs (no ACF dependency) so the plugin is
 * fully self-contained and portable to any host, including Hostinger.
 *
 * @package StudioTabiCMS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STCMS_Meta {

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_boxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'edit_form_after_title', array( __CLASS__, 'title_hint' ) );
		// Placeholder amigável no campo de título de cada tipo.
		add_filter( 'enter_title_here', array( __CLASS__, 'title_placeholder' ), 10, 2 );
	}

	/**
	 * Dica logo abaixo do campo de título, explicando o que é título x conteúdo.
	 */
	public static function title_hint( $post ) {
		$hints = array(
			'st_service' => 'Título = <strong>nome do serviço</strong>. O texto grande abaixo (conteúdo) é a <strong>descrição</strong>.',
			'st_faq'     => 'Título = <strong>a pergunta</strong>. O texto grande abaixo (conteúdo) é a <strong>resposta</strong>.',
			'st_project' => 'Título = <strong>nome do projeto</strong>. Os demais campos (fotos, link, resultados…) ficam no box <em>“Dados do projeto”</em> abaixo.',
		);
		if ( isset( $hints[ $post->post_type ] ) ) {
			echo '<p style="margin:8px 0 0;color:#50575e;font-size:13px">' . wp_kses_post( $hints[ $post->post_type ] ) . '</p>';
		}
	}

	/**
	 * Texto de exemplo dentro do campo de título.
	 */
	public static function title_placeholder( $text, $post ) {
		$map = array(
			'st_service' => 'Nome do serviço',
			'st_faq'     => 'Digite a pergunta',
			'st_project' => 'Nome do projeto',
		);
		return isset( $map[ $post->post_type ] ) ? $map[ $post->post_type ] : $text;
	}

	public static function add_boxes() {
		add_meta_box( 'stcms_service', 'Dados do serviço', array( __CLASS__, 'render_service' ), 'st_service', 'side', 'high' );
		add_meta_box( 'stcms_service_page', 'Conteúdo da página interna (/servicos/…)', array( __CLASS__, 'render_service_page' ), 'st_service', 'normal', 'high' );
		add_meta_box( 'stcms_project', 'Dados do projeto', array( __CLASS__, 'render_project' ), 'st_project', 'normal', 'high' );
	}

	/* ---------------------------------------------------------------- helpers */

	private static function field( $post_id, $key, $default = '' ) {
		$v = get_post_meta( $post_id, $key, true );
		return ( '' === $v || null === $v ) ? $default : $v;
	}

	private static function text_row( $label, $name, $value, $help = '' ) {
		printf(
			'<p><label style="display:block;font-weight:600;margin-bottom:4px">%s</label>'
			. '<input type="text" name="%s" value="%s" style="width:100%%" />%s</p>',
			esc_html( $label ),
			esc_attr( $name ),
			esc_attr( $value ),
			$help ? '<span style="color:#787c82;font-size:12px">' . esc_html( $help ) . '</span>' : ''
		);
	}

	private static function textarea_row( $label, $name, $value, $rows = 4 ) {
		printf(
			'<p><label style="display:block;font-weight:600;margin-bottom:4px">%s</label>'
			. '<textarea name="%s" rows="%d" style="width:100%%">%s</textarea></p>',
			esc_html( $label ),
			esc_attr( $name ),
			(int) $rows,
			esc_textarea( $value )
		);
	}

	/* ------------------------------------------------------------- service ui */

	public static function render_service( $post ) {
		wp_nonce_field( 'stcms_meta', 'stcms_meta_nonce' );
		self::text_row( 'Número (ex: 01)', 'stcms_num', self::field( $post->ID, 'stcms_num' ) );
		echo '<p style="color:#787c82;font-size:12px">O <strong>título</strong> do post é o nome do serviço e o <strong>conteúdo</strong> é a descrição curta (card da home).</p>';
	}

	/**
	 * Editor rico do texto que aparece na página interna do serviço
	 * (/servicos/slug). É separado do conteúdo do post, que fica sendo a
	 * descrição curta usada nos cards — assim a página pode ser bem mais
	 * extensa sem inchar a home.
	 */
	public static function render_service_page( $post ) {
		wp_nonce_field( 'stcms_meta', 'stcms_meta_nonce' );
		$value = get_post_meta( $post->ID, 'stcms_page_content', true );
		$show  = '1' === (string) get_post_meta( $post->ID, 'stcms_show_excerpt', true );

		echo '<p style="color:#787c82;font-size:12px;margin-top:0">Texto completo exibido em <strong>/servicos/' . esc_html( $post->post_name ) . '</strong>. Se ficar vazio, a página usa a descrição curta.</p>';

		echo '<p style="margin:0 0 12px"><label><input type="checkbox" name="stcms_show_excerpt" value="1" ' . checked( $show, true, false ) . '> '
			. 'Exibir também a <strong>descrição curta</strong> como introdução, acima do texto completo</label><br>'
			. '<span style="color:#787c82;font-size:12px">Desmarcado, a página mostra apenas o texto abaixo — útil quando a descrição curta serve só para o card da home.</span></p>';
		wp_editor(
			$value,
			'stcms_page_content',
			array(
				'textarea_name' => 'stcms_page_content',
				'textarea_rows' => 12,
				'media_buttons' => true,
			)
		);
	}

	/* ------------------------------------------------------------- project ui */

	public static function render_project( $post ) {
		wp_nonce_field( 'stcms_meta', 'stcms_meta_nonce' );

		echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:0 24px">';
		echo '<div>';
		self::text_row( 'Nome', 'stcms_name', self::field( $post->ID, 'stcms_name', $post->post_title ) );
		self::text_row( 'Categoria', 'stcms_category', self::field( $post->ID, 'stcms_category' ) );
		self::text_row( 'Ano', 'stcms_year', self::field( $post->ID, 'stcms_year' ) );
		self::text_row( 'Cliente', 'stcms_client', self::field( $post->ID, 'stcms_client' ) );
		self::text_row( 'Duração', 'stcms_duration', self::field( $post->ID, 'stcms_duration' ) );
		self::text_row( 'Link do projeto/site (URL)', 'stcms_url', self::field( $post->ID, 'stcms_url' ), 'Opcional. Ex.: https://exemplo.com — vira o botão "Ver projeto completo" (abre em nova aba).' );
		echo '</div><div>';
		self::text_row( 'Cor de destaque (hex)', 'stcms_accent', self::field( $post->ID, 'stcms_accent', '#F20C25' ), 'Ex: #F20C25' );
		self::text_row( 'Fundo (CSS gradient)', 'stcms_bg', self::field( $post->ID, 'stcms_bg', 'linear-gradient(135deg,#1A0505 0%,#2D0A0A 50%,#1A0A14 100%)' ) );
		$home = self::field( $post->ID, 'stcms_home', '' );
		printf(
			'<p><label style="font-weight:600"><input type="checkbox" name="stcms_home" value="1" %s /> Aparecer na página inicial</label></p>',
			checked( $home, '1', false )
		);
		echo '<p style="color:#787c82;font-size:12px">A home mostra no máximo <strong>4</strong> projetos marcados aqui. Todos aparecem sempre na página <code>/projetos</code>.</p>';
		$featured = self::field( $post->ID, 'stcms_featured', '' );
		printf(
			'<p><label style="font-weight:600"><input type="checkbox" name="stcms_featured" value="1" %s /> Projeto em destaque (card maior)</label></p>',
			checked( $featured, '1', false )
		);
		echo '</div></div>';

		self::cover_field( $post->ID );

		self::textarea_row( 'Desafio', 'stcms_challenge', self::field( $post->ID, 'stcms_challenge' ), 4 );
		self::textarea_row( 'Solução', 'stcms_solution', self::field( $post->ID, 'stcms_solution' ), 4 );

		self::repeater( 'Escopo', 'stcms_scope', (array) get_post_meta( $post->ID, 'stcms_scope', true ), array( 'item' => 'Item do escopo' ) );
		self::repeater( 'Resultados', 'stcms_results', (array) get_post_meta( $post->ID, 'stcms_results', true ), array( 'label' => 'Label', 'value' => 'Valor' ) );
		self::repeater( 'Linhas do mockup', 'stcms_mockup', (array) get_post_meta( $post->ID, 'stcms_mockup', true ), array( 'item' => 'Linha' ) );

		self::gallery_field( $post->ID );
		self::documents_field( $post->ID );
	}

	/**
	 * Cover picker: a single image used as the project's thumbnail on the home
	 * grid and on the /projetos page. Falls back to the WordPress "Imagem
	 * destacada" (featured image) when empty. Uses the same markup that
	 * admin.js already wires up (.stcms-media / .stcms-media-pick).
	 */
	private static function cover_field( $post_id ) {
		$id  = (int) self::field( $post_id, 'stcms_cover', 0 );
		$url = $id ? wp_get_attachment_image_url( $id, 'medium' ) : '';

		echo '<div class="stcms-cover" style="margin:14px 0;border-top:1px solid #dcdcde;padding-top:10px">';
		echo '<strong style="display:block;margin-bottom:6px">Foto de capa (miniatura na home)</strong>';
		echo '<p style="color:#787c82;font-size:12px;margin:0 0 8px">Imagem que aparece no card do projeto na página inicial e em /projetos. Se ficar vazia, usa a <strong>Imagem destacada</strong> (coluna lateral).</p>';
		echo '<div class="stcms-media">';
		printf( '<input type="hidden" class="stcms-media-id" name="stcms_cover" value="%s" />', esc_attr( $id ) );
		printf( '<img class="stcms-media-preview" src="%s" style="max-width:220px;display:%s;margin-bottom:8px;border:1px solid #dcdcde;border-radius:6px" />', esc_url( $url ), $url ? 'block' : 'none' );
		echo '<br /><button type="button" class="button stcms-media-pick">Selecionar imagem</button> ';
		printf( '<button type="button" class="button-link stcms-media-clear" style="color:#b32d2e;display:%s">Remover</button>', $url ? 'inline-block' : 'none' );
		echo '</div></div>';
	}

	/**
	 * PDF/documents picker: stores a comma-separated list of attachment IDs and
	 * shows the file names with a remove button.
	 */
	private static function documents_field( $post_id ) {
		$raw = get_post_meta( $post_id, 'stcms_documents', true );
		$ids = $raw ? array_values( array_filter( array_map( 'intval', explode( ',', $raw ) ) ) ) : array();

		echo '<div class="stcms-docs" style="margin:14px 0;border-top:1px solid #dcdcde;padding-top:10px">';
		echo '<strong style="display:block;margin-bottom:6px">PDFs / Documentos</strong>';
		echo '<p style="color:#787c82;font-size:12px;margin:0 0 8px">Arquivos PDF que aparecem na seção "Documentos" da página do projeto, com pré-visualização.</p>';
		printf( '<input type="hidden" class="stcms-docs-ids" name="stcms_documents" value="%s" />', esc_attr( implode( ',', $ids ) ) );
		echo '<div class="stcms-docs-list" style="display:flex;flex-direction:column;gap:6px;margin-bottom:10px">';
		foreach ( $ids as $id ) {
			$url = wp_get_attachment_url( $id );
			if ( ! $url ) {
				continue;
			}
			$title = get_the_title( $id );
			if ( '' === $title ) {
				$title = basename( wp_parse_url( $url, PHP_URL_PATH ) );
			}
			printf(
				'<div class="stcms-doc-item" data-id="%d" style="display:flex;align-items:center;gap:8px;background:#f7f8fa;border:1px solid #e6e8ec;border-radius:8px;padding:6px 10px">'
				. '<span class="dashicons dashicons-media-document" style="color:#b32d2e"></span>'
				. '<span style="flex:1;font-size:13px">%s</span>'
				. '<button type="button" class="stcms-doc-remove" title="Remover" style="background:#fff;border:1px solid #e6e8ec;color:#b32d2e;border-radius:50%%;width:22px;height:22px;cursor:pointer;line-height:1">×</button>'
				. '</div>',
				$id,
				esc_html( $title )
			);
		}
		echo '</div>';
		echo '<button type="button" class="button stcms-docs-add">+ Adicionar PDF</button>';
		echo '</div>';
	}

	/**
	 * Gallery picker: stores a comma-separated list of attachment IDs and shows
	 * thumbnails with a remove button. Uses the WordPress media library.
	 */
	private static function gallery_field( $post_id ) {
		$raw = get_post_meta( $post_id, 'stcms_gallery', true );
		$ids = $raw ? array_values( array_filter( array_map( 'intval', explode( ',', $raw ) ) ) ) : array();

		echo '<div class="stcms-gallery" style="margin:14px 0;border-top:1px solid #dcdcde;padding-top:10px">';
		echo '<strong style="display:block;margin-bottom:6px">Galeria de imagens</strong>';
		echo '<p style="color:#787c82;font-size:12px;margin:0 0 8px">Fotos extras que aparecem na seção "Galeria" da página do projeto.</p>';
		printf( '<input type="hidden" class="stcms-gallery-ids" name="stcms_gallery" value="%s" />', esc_attr( implode( ',', $ids ) ) );
		echo '<div class="stcms-gallery-preview" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px">';
		foreach ( $ids as $id ) {
			$url = wp_get_attachment_image_url( $id, 'thumbnail' );
			if ( ! $url ) {
				continue;
			}
			printf(
				'<div class="stcms-gallery-item" data-id="%d" style="position:relative;width:84px;height:84px">'
				. '<img src="%s" style="width:100%%;height:100%%;object-fit:cover;border:1px solid #dcdcde;border-radius:4px" />'
				. '<button type="button" class="stcms-gallery-remove" title="Remover" style="position:absolute;top:-7px;right:-7px;background:#b32d2e;color:#fff;border:none;border-radius:50%%;width:20px;height:20px;cursor:pointer;line-height:18px;padding:0">×</button>'
				. '</div>',
				$id,
				esc_url( $url )
			);
		}
		echo '</div>';
		echo '<button type="button" class="button stcms-gallery-add">+ Adicionar imagens</button>';
		echo '</div>';
	}

	/**
	 * Render a simple JS-driven repeater. Rows are stored as nested arrays
	 * under the given base name, e.g. name="stcms_results[0][label]".
	 *
	 * @param string $label  Section label.
	 * @param string $base   Base field name.
	 * @param array  $rows   Existing rows.
	 * @param array  $cols   column_key => label map.
	 */
	private static function repeater( $label, $base, $rows, $cols ) {
		$rows = array_values( array_filter( (array) $rows, 'is_array' ) );
		echo '<div class="stcms-repeater" data-base="' . esc_attr( $base ) . '" style="margin:14px 0;border-top:1px solid #dcdcde;padding-top:10px">';
		echo '<strong style="display:block;margin-bottom:6px">' . esc_html( $label ) . '</strong>';
		echo '<div class="stcms-rows">';

		$cols_json = wp_json_encode( $cols );
		echo '<script type="application/json" class="stcms-cols">' . $cols_json . '</script>';

		if ( empty( $rows ) ) {
			$rows = array( array() );
		}
		foreach ( $rows as $i => $row ) {
			self::repeater_row( $base, $i, $cols, $row );
		}
		echo '</div>';
		echo '<button type="button" class="button stcms-add" style="margin-top:6px">+ Adicionar</button>';
		echo '</div>';
	}

	private static function repeater_row( $base, $i, $cols, $row ) {
		echo '<div class="stcms-row" style="display:flex;gap:6px;margin-bottom:6px;align-items:center">';
		foreach ( $cols as $key => $col_label ) {
			$val = isset( $row[ $key ] ) ? $row[ $key ] : '';
			printf(
				'<input type="text" placeholder="%s" name="%s[%d][%s]" value="%s" style="flex:1" />',
				esc_attr( $col_label ),
				esc_attr( $base ),
				(int) $i,
				esc_attr( $key ),
				esc_attr( $val )
			);
		}
		echo '<button type="button" class="button-link stcms-remove" style="color:#b32d2e">×</button>';
		echo '</div>';
	}

	/* ----------------------------------------------------------------- saving */

	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST['stcms_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['stcms_meta_nonce'] ) ), 'stcms_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( 'st_service' === $post->post_type ) {
			self::save_text( $post_id, 'stcms_num' );
			// Conteúdo rico da página interna: aceita o HTML permitido a posts.
			if ( isset( $_POST['stcms_page_content'] ) ) {
				update_post_meta( $post_id, 'stcms_page_content', wp_kses_post( wp_unslash( $_POST['stcms_page_content'] ) ) );
				// A checkbox só chega quando marcada; o isset acima garante que
				// estamos no formulário certo antes de gravar o valor vazio.
				update_post_meta( $post_id, 'stcms_show_excerpt', empty( $_POST['stcms_show_excerpt'] ) ? '' : '1' );
			}
		}

		if ( 'st_project' === $post->post_type ) {
			foreach ( array( 'stcms_name', 'stcms_category', 'stcms_year', 'stcms_client', 'stcms_duration', 'stcms_accent', 'stcms_bg' ) as $key ) {
				self::save_text( $post_id, $key );
			}
			self::save_textarea( $post_id, 'stcms_challenge' );
			self::save_textarea( $post_id, 'stcms_solution' );
			update_post_meta( $post_id, 'stcms_featured', isset( $_POST['stcms_featured'] ) ? '1' : '' );
		update_post_meta( $post_id, 'stcms_home', isset( $_POST['stcms_home'] ) ? '1' : '' );
			self::save_repeater( $post_id, 'stcms_scope' );
			self::save_repeater( $post_id, 'stcms_results' );
			self::save_repeater( $post_id, 'stcms_mockup' );

			// URL do projeto (link externo)
			if ( isset( $_POST['stcms_url'] ) ) {
				update_post_meta( $post_id, 'stcms_url', esc_url_raw( wp_unslash( $_POST['stcms_url'] ) ) );
			}
			// Foto de capa (miniatura na home): ID do anexo
			update_post_meta( $post_id, 'stcms_cover', (int) ( $_POST['stcms_cover'] ?? 0 ) );
			// Galeria: lista de IDs de anexos separada por vírgula
			if ( isset( $_POST['stcms_gallery'] ) ) {
				$ids   = array_filter( array_map( 'intval', explode( ',', sanitize_text_field( wp_unslash( $_POST['stcms_gallery'] ) ) ) ) );
				update_post_meta( $post_id, 'stcms_gallery', implode( ',', $ids ) );
			}
			// Documentos (PDFs): lista de IDs de anexos separada por vírgula
			if ( isset( $_POST['stcms_documents'] ) ) {
				$docs = array_filter( array_map( 'intval', explode( ',', sanitize_text_field( wp_unslash( $_POST['stcms_documents'] ) ) ) ) );
				update_post_meta( $post_id, 'stcms_documents', implode( ',', $docs ) );
			}
		}
	}

	private static function save_text( $post_id, $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}

	private static function save_textarea( $post_id, $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}

	private static function save_repeater( $post_id, $key ) {
		if ( ! isset( $_POST[ $key ] ) || ! is_array( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, array() );
			return;
		}
		$clean = array();
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( wp_unslash( $_POST[ $key ] ) as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$clean_row = array();
			$has_value = false;
			foreach ( $row as $col => $val ) {
				$val = sanitize_text_field( $val );
				$clean_row[ sanitize_key( $col ) ] = $val;
				if ( '' !== $val ) {
					$has_value = true;
				}
			}
			if ( $has_value ) {
				$clean[] = $clean_row;
			}
		}
		update_post_meta( $post_id, $key, $clean );
	}
}
