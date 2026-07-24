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
	}

	public static function add_boxes() {
		add_meta_box( 'stcms_service', 'Dados do serviço', array( __CLASS__, 'render_service' ), 'st_service', 'side', 'high' );
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
		echo '<p style="color:#787c82;font-size:12px">O <strong>título</strong> do post é o nome do serviço e o <strong>conteúdo</strong> é a descrição.</p>';
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
		echo '</div><div>';
		self::text_row( 'Cor de destaque (hex)', 'stcms_accent', self::field( $post->ID, 'stcms_accent', '#F20C25' ), 'Ex: #F20C25' );
		self::text_row( 'Fundo (CSS gradient)', 'stcms_bg', self::field( $post->ID, 'stcms_bg', 'linear-gradient(135deg,#1A0505 0%,#2D0A0A 50%,#1A0A14 100%)' ) );
		$featured = self::field( $post->ID, 'stcms_featured', '' );
		printf(
			'<p><label style="font-weight:600"><input type="checkbox" name="stcms_featured" value="1" %s /> Projeto em destaque</label></p>',
			checked( $featured, '1', false )
		);
		echo '<p style="color:#787c82;font-size:12px">Use a <strong>Imagem destacada</strong> (coluna lateral) como capa do projeto.</p>';
		echo '</div></div>';

		self::textarea_row( 'Desafio', 'stcms_challenge', self::field( $post->ID, 'stcms_challenge' ), 4 );
		self::textarea_row( 'Solução', 'stcms_solution', self::field( $post->ID, 'stcms_solution' ), 4 );

		self::repeater( 'Escopo', 'stcms_scope', (array) get_post_meta( $post->ID, 'stcms_scope', true ), array( 'item' => 'Item do escopo' ) );
		self::repeater( 'Resultados', 'stcms_results', (array) get_post_meta( $post->ID, 'stcms_results', true ), array( 'label' => 'Label', 'value' => 'Valor' ) );
		self::repeater( 'Linhas do mockup', 'stcms_mockup', (array) get_post_meta( $post->ID, 'stcms_mockup', true ), array( 'item' => 'Linha' ) );
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
		}

		if ( 'st_project' === $post->post_type ) {
			foreach ( array( 'stcms_name', 'stcms_category', 'stcms_year', 'stcms_client', 'stcms_duration', 'stcms_accent', 'stcms_bg' ) as $key ) {
				self::save_text( $post_id, $key );
			}
			self::save_textarea( $post_id, 'stcms_challenge' );
			self::save_textarea( $post_id, 'stcms_solution' );
			update_post_meta( $post_id, 'stcms_featured', isset( $_POST['stcms_featured'] ) ? '1' : '' );
			self::save_repeater( $post_id, 'stcms_scope' );
			self::save_repeater( $post_id, 'stcms_results' );
			self::save_repeater( $post_id, 'stcms_mockup' );
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
