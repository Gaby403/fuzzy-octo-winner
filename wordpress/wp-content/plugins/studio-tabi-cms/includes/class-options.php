<?php
/**
 * Studio Tabi admin options page for the singleton content blocks
 * (Site, Hero, Sobre, Rodapé). Everything is stored in a single option
 * `stcms_options` and validated against the defaults.
 *
 * @package StudioTabiCMS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STCMS_Options {

	const OPTION = 'stcms_options';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
	}

	/**
	 * Merged current options (stored values on top of defaults).
	 */
	public static function get() {
		$stored = get_option( self::OPTION, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}
		return self::deep_merge( stcms_default_options(), $stored );
	}

	private static function deep_merge( $defaults, $values ) {
		foreach ( $values as $key => $value ) {
			if ( is_array( $value ) && isset( $defaults[ $key ] ) && is_array( $defaults[ $key ] ) && self::is_assoc( $defaults[ $key ] ) ) {
				$defaults[ $key ] = self::deep_merge( $defaults[ $key ], $value );
			} else {
				$defaults[ $key ] = $value;
			}
		}
		return $defaults;
	}

	private static function is_assoc( $arr ) {
		return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
	}

	/* --------------------------------------------------------------- menu/reg */

	public static function menu() {
		add_menu_page(
			'Studio Tabi',
			'Studio Tabi',
			'manage_options',
			'studio-tabi',
			array( __CLASS__, 'render_page' ),
			'dashicons-admin-customizer',
			3
		);
		add_submenu_page( 'studio-tabi', 'Conteúdo do site', 'Conteúdo', 'manage_options', 'studio-tabi', array( __CLASS__, 'render_page' ) );
	}

	public static function register() {
		register_setting(
			'stcms_group',
			self::OPTION,
			array( 'sanitize_callback' => array( __CLASS__, 'sanitize' ) )
		);
	}

	/* ------------------------------------------------------------- rendering  */

	public static function render_page() {
		$o = self::get();
		?>
		<div class="wrap">
			<h1>Conteúdo do site — Studio Tabi</h1>
			<p>Edite os textos e imagens fixos do site. Serviços, Projetos, FAQ e Páginas têm menus próprios.</p>
			<form method="post" action="options.php">
				<?php settings_fields( 'stcms_group' ); ?>

				<h2 class="title">Site</h2>
				<table class="form-table" role="presentation">
					<?php
					self::row_text( 'Título do site', 'site][title', $o['site']['title'] );
					self::row_text( 'Slogan curto', 'site][tagline', $o['site']['tagline'] );
					self::row_media( 'Logo', 'site][logo_id', (int) $o['site']['logo_id'] );
					self::row_media( 'Favicon', 'site][favicon_id', (int) $o['site']['favicon_id'], 'Ícone exibido na aba do navegador (PNG/ICO quadrado).' );
					?>
				</table>

				<h2 class="title">Hero</h2>
				<table class="form-table" role="presentation">
					<?php self::row_text( 'Olho (texto acima do título)', 'hero][eyebrow', $o['hero']['eyebrow'] ); ?>
					<tr>
						<th scope="row">Linhas do título</th>
						<td>
							<textarea name="<?php echo esc_attr( self::OPTION ); ?>[hero][title_lines]" rows="3" class="large-text" placeholder="Uma linha por linha"><?php echo esc_textarea( implode( "\n", (array) $o['hero']['title_lines'] ) ); ?></textarea>
							<p class="description">Uma linha do título por linha de texto.</p>
						</td>
					</tr>
					<?php
					self::row_text( 'Palavra em destaque (vermelho)', 'hero][highlight', $o['hero']['highlight'], 'É a última linha do título, em vermelho (ex.: DIGITAL.). Deixe vazio para remover.' );
					self::row_textarea( 'Descrição', 'hero][description', $o['hero']['description'] );
					self::row_media( 'Imagem de fundo (opcional)', 'hero][image_id', (int) $o['hero']['image_id'], 'Se vazio, o hero usa a arte de montanha animada padrão.' );
					?>
				</table>

				<h2 class="title">Sobre</h2>
				<table class="form-table" role="presentation">
					<?php
					self::row_textarea( 'Parágrafo 1', 'about][paragraph1', $o['about']['paragraph1'] );
					self::row_textarea( 'Parágrafo 2', 'about][paragraph2', $o['about']['paragraph2'] );
					?>
					<tr>
						<th scope="row">Estatísticas</th>
						<td>
							<?php self::repeater( 'about][stats', $o['about']['stats'], array( 'numeric' => 'Número', 'suffix' => 'Sufixo (+ % ×)', 'label' => 'Rótulo' ) ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">Pilares</th>
						<td>
							<?php self::repeater( 'about][pillars', $o['about']['pillars'], array( 'title' => 'Título', 'body' => 'Texto' ) ); ?>
						</td>
					</tr>
				</table>

				<h2 class="title">Rodapé</h2>
				<table class="form-table" role="presentation">
					<?php
					self::row_text( 'Tagline', 'footer][tagline', $o['footer']['tagline'] );
					self::row_text( 'E-mail', 'footer][email', $o['footer']['email'] );
					self::row_text( 'Telefone', 'footer][phone', $o['footer']['phone'] );
					self::row_text( 'Cidade', 'footer][city', $o['footer']['city'] );
					?>
				</table>

				<?php submit_button( 'Salvar alterações' ); ?>
			</form>
		</div>
		<?php
	}

	private static function name( $path ) {
		return esc_attr( self::OPTION . '[' . $path );
	}

	private static function row_text( $label, $path, $value, $help = '' ) {
		printf(
			'<tr><th scope="row">%s</th><td><input type="text" name="%s]" value="%s" class="regular-text" style="width:100%%;max-width:640px" />%s</td></tr>',
			esc_html( $label ),
			self::name( $path ),
			esc_attr( $value ),
			$help ? '<p class="description">' . esc_html( $help ) . '</p>' : ''
		);
	}

	private static function row_textarea( $label, $path, $value ) {
		printf(
			'<tr><th scope="row">%s</th><td><textarea name="%s]" rows="3" class="large-text">%s</textarea></td></tr>',
			esc_html( $label ),
			self::name( $path ),
			esc_textarea( $value )
		);
	}

	private static function row_media( $label, $path, $id, $help = '' ) {
		$url = $id ? wp_get_attachment_image_url( $id, 'thumbnail' ) : '';
		echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td>';
		echo '<div class="stcms-media">';
		printf( '<input type="hidden" class="stcms-media-id" name="%s]" value="%s" />', self::name( $path ), esc_attr( $id ) );
		printf( '<img class="stcms-media-preview" src="%s" style="max-width:120px;display:%s;margin-bottom:8px;border:1px solid #dcdcde" />', esc_url( $url ), $url ? 'block' : 'none' );
		echo '<br /><button type="button" class="button stcms-media-pick">Selecionar imagem</button> ';
		printf( '<button type="button" class="button-link stcms-media-clear" style="color:#b32d2e;display:%s">Remover</button>', $url ? 'inline-block' : 'none' );
		if ( $help ) {
			echo '<p class="description">' . esc_html( $help ) . '</p>';
		}
		echo '</div></td></tr>';
	}

	private static function repeater( $path, $rows, $cols ) {
		$base = self::OPTION . '[' . $path . ']';
		$rows = array_values( array_filter( (array) $rows, 'is_array' ) );
		if ( empty( $rows ) ) {
			$rows = array( array() );
		}
		echo '<div class="stcms-repeater" data-base="' . esc_attr( $base ) . '">';
		echo '<script type="application/json" class="stcms-cols">' . wp_json_encode( $cols ) . '</script>';
		echo '<div class="stcms-rows">';
		foreach ( $rows as $i => $row ) {
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
		echo '</div>';
		echo '<button type="button" class="button stcms-add">+ Adicionar</button>';
		echo '</div>';
	}

	/* ------------------------------------------------------------- sanitize   */

	public static function sanitize( $input ) {
		$out = self::get();

		if ( isset( $input['site'] ) ) {
			$out['site']['title']      = sanitize_text_field( $input['site']['title'] ?? '' );
			$out['site']['tagline']    = sanitize_text_field( $input['site']['tagline'] ?? '' );
			$out['site']['logo_id']    = (int) ( $input['site']['logo_id'] ?? 0 );
			$out['site']['favicon_id'] = (int) ( $input['site']['favicon_id'] ?? 0 );
		}

		if ( isset( $input['hero'] ) ) {
			$lines = preg_split( '/\r\n|\r|\n/', (string) ( $input['hero']['title_lines'] ?? '' ) );
			$lines = array_values( array_filter( array_map( 'sanitize_text_field', $lines ), 'strlen' ) );
			$out['hero']['eyebrow']      = sanitize_text_field( $input['hero']['eyebrow'] ?? '' );
			$out['hero']['title_lines']  = $lines ? $lines : stcms_default_options()['hero']['title_lines'];
			$out['hero']['highlight']    = sanitize_text_field( $input['hero']['highlight'] ?? '' );
			$out['hero']['description']  = sanitize_textarea_field( $input['hero']['description'] ?? '' );
			$out['hero']['image_id']     = (int) ( $input['hero']['image_id'] ?? 0 );
		}

		if ( isset( $input['about'] ) ) {
			$out['about']['paragraph1'] = sanitize_textarea_field( $input['about']['paragraph1'] ?? '' );
			$out['about']['paragraph2'] = sanitize_textarea_field( $input['about']['paragraph2'] ?? '' );

			$stats = array();
			foreach ( (array) ( $input['about']['stats'] ?? array() ) as $row ) {
				$label   = sanitize_text_field( $row['label'] ?? '' );
				$numeric = sanitize_text_field( $row['numeric'] ?? '' );
				if ( '' === $label && '' === $numeric ) {
					continue;
				}
				$stats[] = array(
					'numeric' => is_numeric( $numeric ) ? 0 + $numeric : $numeric,
					'suffix'  => sanitize_text_field( $row['suffix'] ?? '' ),
					'label'   => $label,
				);
			}
			$out['about']['stats'] = $stats;

			$pillars = array();
			foreach ( (array) ( $input['about']['pillars'] ?? array() ) as $row ) {
				$title = sanitize_text_field( $row['title'] ?? '' );
				$body  = sanitize_textarea_field( $row['body'] ?? '' );
				if ( '' === $title && '' === $body ) {
					continue;
				}
				$pillars[] = array( 'title' => $title, 'body' => $body );
			}
			$out['about']['pillars'] = $pillars;
		}

		if ( isset( $input['footer'] ) ) {
			$out['footer']['tagline'] = sanitize_text_field( $input['footer']['tagline'] ?? '' );
			$out['footer']['email']   = sanitize_email( $input['footer']['email'] ?? '' );
			$out['footer']['phone']   = sanitize_text_field( $input['footer']['phone'] ?? '' );
			$out['footer']['city']    = sanitize_text_field( $input['footer']['city'] ?? '' );
		}

		return $out;
	}
}
