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
		add_action( 'admin_init', array( __CLASS__, 'maybe_create_process_pages' ) );
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
		<div class="wrap stcms-admin">

			<div class="stcms-hero">
				<div class="stcms-hero-mark" aria-hidden="true">◆</div>
				<div class="stcms-hero-text">
					<h1>Studio Tabi <span>CMS</span></h1>
					<p>Edite o conteúdo do site abaixo. As mudanças aparecem no site ao recarregar a página.</p>
				</div>
				<div class="stcms-hero-links">
					<a class="stcms-chip" href="<?php echo esc_url( admin_url( 'edit.php?post_type=st_service' ) ); ?>"><span class="dashicons dashicons-screenoptions"></span> Serviços</a>
					<a class="stcms-chip" href="<?php echo esc_url( admin_url( 'edit.php?post_type=st_project' ) ); ?>"><span class="dashicons dashicons-portfolio"></span> Projetos</a>
					<a class="stcms-chip" href="<?php echo esc_url( admin_url( 'edit.php?post_type=st_faq' ) ); ?>"><span class="dashicons dashicons-editor-help"></span> FAQ</a>
					<a class="stcms-chip" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>"><span class="dashicons dashicons-admin-page"></span> Páginas</a>
					<a class="stcms-chip" href="<?php echo esc_url( admin_url( 'upload.php' ) ); ?>"><span class="dashicons dashicons-format-image"></span> Mídia</a>
				</div>
			</div>

			<form method="post" action="options.php" class="stcms-form">
				<?php settings_fields( 'stcms_group' ); ?>

				<?php self::card_open( 'site', 'dashicons-admin-site-alt3', 'Site', 'Título, descrição para o Google, logo e favicon' ); ?>
					<table class="form-table stcms-fields" role="presentation">
						<?php
						self::row_text( 'Título do site', 'site][title', $o['site']['title'] );
						self::row_textarea( 'Meta descrição (SEO)', 'site][meta_description', $o['site']['meta_description'], 'Aparece no Google e ao compartilhar o link. Ideal: 120–160 caracteres.' );
						self::row_text( 'Slogan curto', 'site][tagline', $o['site']['tagline'] );
						self::row_media( 'Logo', 'site][logo_id', (int) $o['site']['logo_id'] );
						self::row_media( 'Favicon', 'site][favicon_id', (int) $o['site']['favicon_id'], 'Ícone da aba do navegador (PNG/ICO quadrado).' );
						?>
					</table>
				<?php self::card_close(); ?>

				<?php self::card_open( 'integrations', 'dashicons-chart-area', 'Integrações & Analytics', 'Google Analytics 4, Tag Manager e reCAPTCHA v3 (anti-spam)' ); ?>
					<table class="form-table stcms-fields" role="presentation">
						<?php
						self::row_text( 'ID do Google Analytics 4', 'site][ga4_id', $o['site']['ga4_id'], 'Ex.: G-XXXXXXX. Deixe vazio para não carregar.' );
						self::row_text( 'ID do Google Tag Manager', 'site][gtm_id', $o['site']['gtm_id'], 'Ex.: GTM-XXXXXX. Se preenchido, o GA4 pode ser gerido pelo GTM.' );
						self::row_text( 'reCAPTCHA v3 — Site Key', 'site][recaptcha_site', $o['site']['recaptcha_site'], 'Chave pública (client). Ativa a proteção anti-spam nos formulários.' );
						self::row_text( 'reCAPTCHA v3 — Secret Key', 'site][recaptcha_secret', $o['site']['recaptcha_secret'], 'Chave secreta (server). Nunca é exposta na API pública.' );
						self::row_text( 'E-mail que RECEBE os formulários', 'site][form_email', $o['site']['form_email'] ?? '', 'Para onde vão contato e newsletter. Se vazio, usa o e-mail do Rodapé; se este também estiver vazio, usa o e-mail do administrador do WordPress.' );
						?>
					</table>
				<?php self::card_close(); ?>

				<?php self::card_open( 'nav', 'dashicons-menu-alt3', 'Menu (cabeçalho)', 'A marca e os links do topo do site' ); ?>
					<table class="form-table stcms-fields" role="presentation">
						<?php
						self::row_text( 'Marca / logo (texto)', 'nav][brand', $o['nav']['brand'], 'Usado só se nenhuma Logo (imagem) for definida na seção Site.' );
						self::row_text( 'Texto do botão "Iniciar projeto"', 'nav][cta_label', $o['nav']['cta_label'] );
						self::row_text( 'Link do botão "Iniciar projeto"', 'nav][cta_url', $o['nav']['cta_url'], 'Ex.: #contato, /p/orcamento ou https://wa.me/55...' );
						?>
						<tr>
							<th scope="row">Links do menu</th>
							<td><?php self::repeater( 'nav][links', $o['nav']['links'], array( 'label' => 'Rótulo', 'url' => 'Link (#, /p/slug ou https://...)' ) ); ?></td>
						</tr>
					</table>
				<?php self::card_close(); ?>

				<?php self::card_open( 'hero', 'dashicons-cover-image', 'Hero (topo)', 'A primeira dobra: título grande, destaque e descrição' ); ?>
					<table class="form-table stcms-fields" role="presentation">
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

					<div class="stcms-subgroup"><span class="stcms-subtitle">Botão principal (ex.: Ver portfólio)</span>
						<table class="form-table stcms-fields" role="presentation">
							<?php
							self::row_text( 'Texto do botão', 'hero][cta_primary_label', $o['hero']['cta_primary_label'] );
							self::row_text( 'Link do botão', 'hero][cta_primary_url', $o['hero']['cta_primary_url'], 'Ex.: /projetos, #trabalhos ou https://...' );
							?>
						</table>
					</div>

					<div class="stcms-subgroup"><span class="stcms-subtitle">Botão secundário (ex.: Falar com a equipe)</span>
						<table class="form-table stcms-fields" role="presentation">
							<?php
							self::row_text( 'Texto do botão', 'hero][cta_secondary_label', $o['hero']['cta_secondary_label'] );
							self::row_text( 'Link do botão', 'hero][cta_secondary_url', $o['hero']['cta_secondary_url'], 'Ex.: #contato ou https://wa.me/55...' );
							?>
						</table>
					</div>
				<?php self::card_close(); ?>

				<?php self::card_open( 'projects_cta', 'dashicons-portfolio', 'Botão "Ver todos os projetos"', 'Texto e link dos botões "Ver todos / Ver portfólio" da seção de projetos' ); ?>
					<table class="form-table stcms-fields" role="presentation">
						<?php
						self::row_text( 'Texto do botão', 'projects_cta][label', $o['projects_cta']['label'] );
						self::row_text( 'Link do botão', 'projects_cta][url', $o['projects_cta']['url'], 'Padrão: /projetos (a página com todos os projetos). Pode ser um link externo.' );
						?>
					</table>
				<?php self::card_close(); ?>

				<?php self::card_open( 'process', 'dashicons-networking', 'Como Trabalhamos (Processo)', 'Etapas do processo. Cada etapa vira uma página /processo/{slug}.' ); ?>
					<table class="form-table stcms-fields" role="presentation">
						<tr>
							<th scope="row">Etapas</th>
							<td>
								<?php self::repeater( 'process', $o['process'], array( 'title' => 'Título', 'slug' => 'slug (url)', 'icon' => 'ícone', 'summary' => 'Resumo curto' ) ); ?>
								<p class="description">Ícones disponíveis: <code>diagnostico</code>, <code>narrativa</code>, <code>design</code>, <code>desenvolvimento</code>. Para o texto completo da página, crie uma <strong>Página</strong> no WordPress com o mesmo <em>slug</em>.</p>
							</td>
						</tr>
					</table>
				<?php self::card_close(); ?>

				<?php self::card_open( 'inner_pages', 'dashicons-media-document', 'Páginas internas', 'Atalhos para editar o texto completo de cada página de processo e de serviço' ); ?>
					<?php self::render_inner_pages(); ?>
				<?php self::card_close(); ?>

				<?php self::card_open( 'contact', 'dashicons-email-alt', 'Página de contato', 'Textos da página /contato (o e-mail/telefone vêm do Rodapé)' ); ?>
					<table class="form-table stcms-fields" role="presentation">
						<?php
						self::row_text( 'Título', 'contact][title', $o['contact']['title'], 'Primeira parte do título (ex.: VAMOS).' );
						self::row_text( 'Destaque (vermelho)', 'contact][highlight', $o['contact']['highlight'], 'Segunda parte, em vermelho (ex.: CONVERSAR.).' );
						self::row_textarea( 'Descrição', 'contact][description', $o['contact']['description'] );
						?>
					</table>
				<?php self::card_close(); ?>

				<?php self::card_open( 'sections', 'dashicons-layout', 'Seções da home (rótulos e botões)', 'Eyebrows, notas e links dos botões de cada seção' ); ?>
					<div class="stcms-subgroup"><span class="stcms-subtitle">Sobre nós</span>
						<table class="form-table stcms-fields" role="presentation">
							<?php
							self::row_text( 'Eyebrow (texto pequeno)', 'sections][about_eyebrow', $o['sections']['about_eyebrow'] );
							self::row_text( 'Rótulo dos pilares', 'sections][about_pillars_label', $o['sections']['about_pillars_label'] );
							self::row_text( 'Texto do botão', 'sections][about_cta_label', $o['sections']['about_cta_label'] );
							self::row_text( 'Link do botão', 'sections][about_cta_url', $o['sections']['about_cta_url'], 'Ex.: #sobre, /p/sobre ou https://...' );
							?>
						</table>
					</div>
					<div class="stcms-subgroup"><span class="stcms-subtitle">Serviços</span>
						<table class="form-table stcms-fields" role="presentation">
							<?php
							self::row_text( 'Eyebrow', 'sections][services_eyebrow', $o['sections']['services_eyebrow'] );
							self::row_text( 'Texto do botão', 'sections][services_cta_label', $o['sections']['services_cta_label'] );
							self::row_text( 'Link do botão', 'sections][services_cta_url', $o['sections']['services_cta_url'], 'Padrão: /servicos (a página de serviços).' );
							?>
						</table>
					</div>
					<div class="stcms-subgroup"><span class="stcms-subtitle">Projetos</span>
						<table class="form-table stcms-fields" role="presentation">
							<?php
							self::row_text( 'Eyebrow', 'sections][projects_eyebrow', $o['sections']['projects_eyebrow'] );
							self::row_text( 'Nota (ex.: 120+ projetos entregues)', 'sections][projects_note', $o['sections']['projects_note'] );
							self::row_textarea( 'Texto do card "ver portfólio"', 'sections][projects_card_text', $o['sections']['projects_card_text'] );
							?>
						</table>
					</div>
					<div class="stcms-subgroup"><span class="stcms-subtitle">Blog (home)</span>
						<table class="form-table stcms-fields" role="presentation">
							<?php
							self::row_text( 'Eyebrow', 'sections][blog_eyebrow', $o['sections']['blog_eyebrow'] ?? '' );
							self::row_text( 'Título (1ª linha)', 'sections][blog_title', $o['sections']['blog_title'] ?? '', 'Ex.: DO NOSSO' );
							self::row_text( 'Título em destaque (2ª linha, vermelho)', 'sections][blog_highlight', $o['sections']['blog_highlight'] ?? '', 'Ex.: DIÁRIO.' );
							self::row_textarea( 'Nota ao lado do título', 'sections][blog_note', $o['sections']['blog_note'] ?? '' );
							self::row_text( 'Texto do botão', 'sections][blog_cta_label', $o['sections']['blog_cta_label'] ?? '', 'Leva para /blog. A seção some da home se não houver artigos publicados.' );
							?>
						</table>
					</div>
					<div class="stcms-subgroup"><span class="stcms-subtitle">FAQ</span>
						<table class="form-table stcms-fields" role="presentation">
							<?php
							self::row_text( 'Eyebrow', 'sections][faq_eyebrow', $o['sections']['faq_eyebrow'] );
							self::row_textarea( 'Nota abaixo do título', 'sections][faq_note', $o['sections']['faq_note'] );
							self::row_text( 'Texto do botão', 'sections][faq_cta_label', $o['sections']['faq_cta_label'] );
							self::row_text( 'Link do botão', 'sections][faq_cta_url', $o['sections']['faq_cta_url'], 'Padrão: /contato.' );
							?>
						</table>
					</div>
				<?php self::card_close(); ?>

				<?php self::card_open( 'thankyou', 'dashicons-heart', 'Página de obrigado', 'Texto da página /obrigado exibida após enviar o formulário de contato' ); ?>
					<table class="form-table stcms-fields" role="presentation">
						<?php
						self::row_text( 'Título', 'thankyou][title', $o['thankyou']['title'], 'Ex.: OBRIGADO. Um ponto final vermelho é adicionado automaticamente.' );
						self::row_textarea( 'Mensagem', 'thankyou][message', $o['thankyou']['message'] );
						?>
					</table>
				<?php self::card_close(); ?>

				<?php self::card_open( 'about', 'dashicons-info-outline', 'Sobre', 'Parágrafos, estatísticas e pilares' ); ?>
					<table class="form-table stcms-fields" role="presentation">
						<?php
						self::row_textarea( 'Parágrafo 1', 'about][paragraph1', $o['about']['paragraph1'] );
						self::row_textarea( 'Parágrafo 2', 'about][paragraph2', $o['about']['paragraph2'] );
						?>
						<tr>
							<th scope="row">Estatísticas</th>
							<td><?php self::repeater( 'about][stats', $o['about']['stats'], array( 'numeric' => 'Número', 'suffix' => 'Sufixo (+ % ×)', 'label' => 'Rótulo' ) ); ?></td>
						</tr>
						<tr>
							<th scope="row">Pilares</th>
							<td><?php self::repeater( 'about][pillars', $o['about']['pillars'], array( 'title' => 'Título', 'body' => 'Texto' ) ); ?></td>
						</tr>
					</table>
				<?php self::card_close(); ?>

				<?php self::card_open( 'footer', 'dashicons-align-full-width', 'Rodapé', 'Colunas de links, contato, redes sociais e créditos' ); ?>
					<table class="form-table stcms-fields" role="presentation">
						<?php
						self::row_text( 'Marca / logo (texto)', 'footer][brand', $o['footer']['brand'] );
						self::row_textarea( 'Tagline', 'footer][tagline', $o['footer']['tagline'] );
						self::row_text( 'Chamada do rodapé', 'footer][cta_title', $o['footer']['cta_title'] ?? '', 'Ex.: Vamos construir a sua' );
						self::row_text( 'Chamada em destaque (vermelho)', 'footer][cta_highlight', $o['footer']['cta_highlight'] ?? '', 'Ex.: presença digital.' );
						self::row_text( 'Texto do botão (CTA)', 'footer][cta_label', $o['footer']['cta_label'] );
						?>
					</table>

					<div class="stcms-subgroup"><span class="stcms-subtitle">Coluna 1 de links</span>
						<table class="form-table stcms-fields" role="presentation">
							<?php self::row_text( 'Título da coluna', 'footer][col1_title', $o['footer']['col1_title'] ); ?>
							<tr><th scope="row">Links</th><td><?php self::repeater( 'footer][col1_links', $o['footer']['col1_links'], array( 'label' => 'Rótulo', 'url' => 'Link (#, /p/slug ou https://...)' ) ); ?></td></tr>
						</table>
					</div>

					<div class="stcms-subgroup"><span class="stcms-subtitle">Coluna 2 de links</span>
						<table class="form-table stcms-fields" role="presentation">
							<?php self::row_text( 'Título da coluna', 'footer][col2_title', $o['footer']['col2_title'] ); ?>
							<tr><th scope="row">Links</th><td><?php self::repeater( 'footer][col2_links', $o['footer']['col2_links'], array( 'label' => 'Rótulo', 'url' => 'Link (#, /p/slug ou https://...)' ) ); ?></td></tr>
						</table>
					</div>

					<div class="stcms-subgroup"><span class="stcms-subtitle">Contato</span>
						<table class="form-table stcms-fields" role="presentation">
							<?php
							self::row_text( 'Título da coluna', 'footer][contact_title', $o['footer']['contact_title'] );
							self::row_text( 'E-mail', 'footer][email', $o['footer']['email'] );
							self::row_text( 'Telefone', 'footer][phone', $o['footer']['phone'] );
							self::row_text( 'Cidade', 'footer][city', $o['footer']['city'] );
							?>
						</table>
					</div>

					<div class="stcms-subgroup"><span class="stcms-subtitle">Redes sociais</span>
						<table class="form-table stcms-fields" role="presentation">
							<?php self::row_text( 'Título da coluna', 'footer][social_title', $o['footer']['social_title'] ); ?>
							<tr><th scope="row">Redes</th><td><?php self::repeater( 'footer][social', $o['footer']['social'], array( 'label' => 'Rede', 'url' => 'Link (https://...)' ) ); ?></td></tr>
						</table>
					</div>

					<div class="stcms-subgroup"><span class="stcms-subtitle">Rodapé inferior</span>
						<table class="form-table stcms-fields" role="presentation">
							<?php
							self::row_text( 'Copyright', 'footer][copyright', $o['footer']['copyright'] );
							self::row_text( 'Texto "feito em"', 'footer][made_in', $o['footer']['made_in'], 'Deixe vazio para ocultar.' );
							?>
							<tr><th scope="row">Links legais</th><td><?php self::repeater( 'footer][legal', $o['footer']['legal'], array( 'label' => 'Rótulo', 'url' => 'Link (#, /p/slug ou https://...)' ) ); ?></td></tr>
						</table>
					</div>
				<?php self::card_close(); ?>

				<div class="stcms-savebar">
					<span class="stcms-savebar-note"><span class="dashicons dashicons-info-outline"></span> Não esqueça de salvar após editar.</span>
					<?php submit_button( 'Salvar alterações', 'primary large', 'submit', false ); ?>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Abre um "card" de seção com cabeçalho (ícone + título + subtítulo) e um
	 * botão para recolher/expandir. Fechar com card_close().
	 */
	private static function card_open( $id, $icon, $title, $subtitle ) {
		printf(
			'<section class="stcms-card" data-card="%1$s">'
			. '<button type="button" class="stcms-card-head" aria-expanded="true">'
			. '<span class="stcms-card-icon dashicons %2$s"></span>'
			. '<span class="stcms-card-title">%3$s<em>%4$s</em></span>'
			. '<span class="stcms-card-chevron dashicons dashicons-arrow-up-alt2"></span>'
			. '</button>'
			. '<div class="stcms-card-body">',
			esc_attr( $id ),
			esc_attr( $icon ),
			esc_html( $title ),
			esc_html( $subtitle )
		);
	}

	private static function card_close() {
		echo '</div></section>';
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

	private static function row_textarea( $label, $path, $value, $help = '' ) {
		printf(
			'<tr><th scope="row">%s</th><td><textarea name="%s]" rows="3" class="large-text">%s</textarea>%s</td></tr>',
			esc_html( $label ),
			self::name( $path ),
			esc_textarea( $value ),
			$help ? '<p class="description">' . esc_html( $help ) . '</p>' : ''
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

	/**
	 * Lista as páginas internas do site com link direto para o editor certo.
	 *
	 * As etapas do processo moram em Páginas do WordPress (mesmo slug da etapa)
	 * e os serviços no CPT st_service. Reunir tudo aqui evita que o editor
	 * precise adivinhar onde cada texto é alterado.
	 */
	private static function render_inner_pages() {
		$o = self::get();

		echo '<table class="widefat striped" style="margin-bottom:18px"><thead><tr>'
			. '<th>Página</th><th>Endereço</th><th>Situação</th><th style="width:130px">Ação</th>'
			. '</tr></thead><tbody>';

		// --- Etapas do processo (Páginas do WordPress) ---
		$missing = array();
		foreach ( (array) $o['process'] as $step ) {
			$slug = isset( $step['slug'] ) ? $step['slug'] : '';
			if ( '' === $slug ) {
				continue;
			}
			$page = get_page_by_path( $slug );
			$url  = '/processo/' . $slug;
			if ( $page ) {
				printf(
					'<tr><td><strong>%s</strong></td><td><code>%s</code></td><td style="color:#1a7f37">Publicada</td><td><a class="button" href="%s">Editar texto</a></td></tr>',
					esc_html( $step['title'] ),
					esc_html( $url ),
					esc_url( get_edit_post_link( $page->ID ) )
				);
			} else {
				$missing[] = $step;
				printf(
					'<tr><td><strong>%s</strong></td><td><code>%s</code></td><td style="color:#b32d2e">Página ausente</td><td>—</td></tr>',
					esc_html( $step['title'] ),
					esc_html( $url )
				);
			}
		}

		// --- Serviços (CPT) ---
		$services = get_posts(
			array(
				'post_type'   => 'st_service',
				'numberposts' => -1,
				'orderby'     => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
				'order'       => 'ASC',
			)
		);
		foreach ( $services as $s ) {
			$has  = '' !== trim( (string) get_post_meta( $s->ID, 'stcms_page_content', true ) );
			$show = '1' === (string) get_post_meta( $s->ID, 'stcms_show_excerpt', true );
			if ( ! $has ) {
				$cor = '#8a6d00';
				$txt = 'Usando a descrição curta';
			} elseif ( $show ) {
				$cor = '#1a7f37';
				$txt = 'Texto próprio + introdução';
			} else {
				$cor = '#1a7f37';
				$txt = 'Texto próprio';
			}
			printf(
				'<tr><td><strong>%s</strong></td><td><code>%s</code></td><td style="color:%s">%s</td><td><a class="button" href="%s">Editar texto</a></td></tr>',
				esc_html( get_the_title( $s ) ),
				esc_html( '/servicos/' . $s->post_name ),
				esc_attr( $cor ),
				esc_html( $txt ),
				esc_url( get_edit_post_link( $s->ID ) )
			);
		}

		echo '</tbody></table>';

		if ( $missing ) {
			$url = wp_nonce_url( admin_url( 'admin.php?page=studio-tabi&stcms_create_pages=1' ), 'stcms_create_pages' );
			printf(
				'<p><a href="%s" class="button button-secondary">Criar as %d página(s) de processo que faltam</a></p>',
				esc_url( $url ),
				count( $missing )
			);
		}

		echo '<p class="description">As etapas do processo são <strong>Páginas</strong> do WordPress com o mesmo slug da etapa. Os serviços têm um editor próprio dentro de cada serviço, no bloco “Conteúdo da página interna”.</p>';
	}

	/**
	 * Cria as Páginas das etapas de processo que ainda não existem, usando o
	 * resumo da etapa como texto inicial. Disparado pelo botão do card.
	 */
	public static function maybe_create_process_pages() {
		if ( empty( $_GET['stcms_create_pages'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		check_admin_referer( 'stcms_create_pages' );

		$o       = self::get();
		$created = 0;
		foreach ( (array) $o['process'] as $step ) {
			$slug = isset( $step['slug'] ) ? $step['slug'] : '';
			if ( '' === $slug || get_page_by_path( $slug ) ) {
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $step['title'],
					'post_name'    => $slug,
					'post_content' => isset( $step['summary'] ) ? wpautop( $step['summary'] ) : '',
				)
			);
			if ( $id && ! is_wp_error( $id ) ) {
				$created++;
			}
		}
		wp_safe_redirect( admin_url( 'admin.php?page=studio-tabi&stcms_created=' . $created ) );
		exit;
	}

	public static function sanitize( $input ) {
		$out = self::get();

		if ( isset( $input['site'] ) ) {
			$out['site']['title']            = sanitize_text_field( $input['site']['title'] ?? '' );
			$out['site']['meta_description'] = sanitize_textarea_field( $input['site']['meta_description'] ?? '' );
			$out['site']['tagline']    = sanitize_text_field( $input['site']['tagline'] ?? '' );
			$out['site']['logo_id']    = (int) ( $input['site']['logo_id'] ?? 0 );
			$out['site']['favicon_id'] = (int) ( $input['site']['favicon_id'] ?? 0 );
			// Integrações (Analytics / anti-spam). O secret NUNCA é exposto na API.
			$out['site']['ga4_id']          = sanitize_text_field( $input['site']['ga4_id'] ?? '' );
			$out['site']['gtm_id']          = sanitize_text_field( $input['site']['gtm_id'] ?? '' );
			$out['site']['recaptcha_site']  = sanitize_text_field( $input['site']['recaptcha_site'] ?? '' );
			$out['site']['recaptcha_secret'] = sanitize_text_field( $input['site']['recaptcha_secret'] ?? '' );
			$out['site']['form_email']      = sanitize_email( $input['site']['form_email'] ?? '' );
		}

		if ( isset( $input['hero'] ) ) {
			$lines = preg_split( '/\r\n|\r|\n/', (string) ( $input['hero']['title_lines'] ?? '' ) );
			$lines = array_values( array_filter( array_map( 'sanitize_text_field', $lines ), 'strlen' ) );
			$out['hero']['eyebrow']      = sanitize_text_field( $input['hero']['eyebrow'] ?? '' );
			$out['hero']['title_lines']  = $lines ? $lines : stcms_default_options()['hero']['title_lines'];
			$out['hero']['highlight']    = sanitize_text_field( $input['hero']['highlight'] ?? '' );
			$out['hero']['description']  = sanitize_textarea_field( $input['hero']['description'] ?? '' );
			$out['hero']['image_id']     = (int) ( $input['hero']['image_id'] ?? 0 );
			$out['hero']['cta_primary_label']   = sanitize_text_field( $input['hero']['cta_primary_label'] ?? '' );
			$out['hero']['cta_primary_url']     = self::sanitize_link_url( $input['hero']['cta_primary_url'] ?? '' );
			$out['hero']['cta_secondary_label'] = sanitize_text_field( $input['hero']['cta_secondary_label'] ?? '' );
			$out['hero']['cta_secondary_url']   = self::sanitize_link_url( $input['hero']['cta_secondary_url'] ?? '' );
		}

		if ( isset( $input['projects_cta'] ) ) {
			$out['projects_cta']['label'] = sanitize_text_field( $input['projects_cta']['label'] ?? '' );
			$out['projects_cta']['url']   = self::sanitize_link_url( $input['projects_cta']['url'] ?? '' );
		}

		if ( isset( $input['thankyou'] ) ) {
			$out['thankyou']['title']   = sanitize_text_field( $input['thankyou']['title'] ?? '' );
			$out['thankyou']['message'] = sanitize_textarea_field( $input['thankyou']['message'] ?? '' );
		}

		if ( isset( $input['contact'] ) ) {
			$out['contact']['title']       = sanitize_text_field( $input['contact']['title'] ?? '' );
			$out['contact']['highlight']   = sanitize_text_field( $input['contact']['highlight'] ?? '' );
			$out['contact']['description'] = sanitize_textarea_field( $input['contact']['description'] ?? '' );
		}

		if ( isset( $input['process'] ) ) {
			$steps = array();
			foreach ( (array) $input['process'] as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$title = sanitize_text_field( $row['title'] ?? '' );
				if ( '' === $title ) {
					continue;
				}
				$slug = sanitize_title( $row['slug'] ?? '' );
				$steps[] = array(
					'title'   => $title,
					'slug'    => $slug ? $slug : sanitize_title( $title ),
					'icon'    => sanitize_key( $row['icon'] ?? '' ),
					'summary' => sanitize_textarea_field( $row['summary'] ?? '' ),
				);
			}
			$out['process'] = $steps;
		}

		if ( isset( $input['sections'] ) ) {
			$s = $input['sections'];
			$text_keys = array( 'about_eyebrow', 'about_cta_label', 'about_pillars_label', 'services_eyebrow', 'services_cta_label', 'projects_eyebrow', 'projects_note', 'blog_eyebrow', 'blog_title', 'blog_highlight', 'blog_cta_label', 'faq_eyebrow', 'faq_cta_label' );
			foreach ( $text_keys as $k ) {
				$out['sections'][ $k ] = sanitize_text_field( $s[ $k ] ?? '' );
			}
			$out['sections']['projects_card_text'] = sanitize_textarea_field( $s['projects_card_text'] ?? '' );
			$out['sections']['blog_note']          = sanitize_textarea_field( $s['blog_note'] ?? '' );
			$out['sections']['faq_note']           = sanitize_textarea_field( $s['faq_note'] ?? '' );
			foreach ( array( 'about_cta_url', 'services_cta_url', 'faq_cta_url' ) as $k ) {
				$out['sections'][ $k ] = self::sanitize_link_url( $s[ $k ] ?? '' );
			}
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

		if ( isset( $input['nav'] ) ) {
			$out['nav']['brand']     = sanitize_text_field( $input['nav']['brand'] ?? '' );
			$out['nav']['cta_label'] = sanitize_text_field( $input['nav']['cta_label'] ?? '' );
			$out['nav']['cta_url']   = self::sanitize_link_url( $input['nav']['cta_url'] ?? '' );
			$out['nav']['links']     = self::sanitize_links( $input['nav']['links'] ?? array() );
		}

		if ( isset( $input['footer'] ) ) {
			$out['footer']['brand']         = sanitize_text_field( $input['footer']['brand'] ?? '' );
			$out['footer']['tagline']       = sanitize_text_field( $input['footer']['tagline'] ?? '' );
			$out['footer']['cta_title']     = sanitize_text_field( $input['footer']['cta_title'] ?? '' );
			$out['footer']['cta_highlight'] = sanitize_text_field( $input['footer']['cta_highlight'] ?? '' );
			$out['footer']['cta_label']     = sanitize_text_field( $input['footer']['cta_label'] ?? '' );
			$out['footer']['cta_url']       = self::sanitize_link_url( $input['footer']['cta_url'] ?? '' );
			$out['footer']['col1_title']    = sanitize_text_field( $input['footer']['col1_title'] ?? '' );
			$out['footer']['col1_links']    = self::sanitize_links( $input['footer']['col1_links'] ?? array() );
			$out['footer']['col2_title']    = sanitize_text_field( $input['footer']['col2_title'] ?? '' );
			$out['footer']['col2_links']    = self::sanitize_links( $input['footer']['col2_links'] ?? array() );
			$out['footer']['contact_title'] = sanitize_text_field( $input['footer']['contact_title'] ?? '' );
			$out['footer']['email']         = sanitize_email( $input['footer']['email'] ?? '' );
			$out['footer']['phone']         = sanitize_text_field( $input['footer']['phone'] ?? '' );
			$out['footer']['city']          = sanitize_text_field( $input['footer']['city'] ?? '' );
			$out['footer']['social_title']  = sanitize_text_field( $input['footer']['social_title'] ?? '' );
			$out['footer']['social']        = self::sanitize_links( $input['footer']['social'] ?? array() );
			$out['footer']['copyright']     = sanitize_text_field( $input['footer']['copyright'] ?? '' );
			$out['footer']['made_in']       = sanitize_text_field( $input['footer']['made_in'] ?? '' );
			$out['footer']['legal']         = self::sanitize_links( $input['footer']['legal'] ?? array() );
		}

		return $out;
	}

	/**
	 * Sanitize a single link destination: allows "#anchor", relative paths
	 * ("/projetos", "/p/slug") and full URLs (http, mailto, tel, wa.me…).
	 */
	private static function sanitize_link_url( $url ) {
		$url = trim( (string) $url );
		if ( '' === $url ) {
			return '';
		}
		if ( '#' === $url[0] || '/' === $url[0] ) {
			return esc_url_raw( $url, array( 'http', 'https', 'mailto', 'tel' ) ) ?: sanitize_text_field( $url );
		}
		return esc_url_raw( $url, array( 'http', 'https', 'mailto', 'tel' ) );
	}

	/**
	 * Sanitize a repeater of {label, url} link rows, dropping empty ones.
	 */
	private static function sanitize_links( $rows ) {
		$out = array();
		foreach ( (array) $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$label = sanitize_text_field( $row['label'] ?? '' );
			$url   = trim( (string) ( $row['url'] ?? '' ) );
			// Allow "#", relative paths and full URLs.
			if ( '' !== $url && '#' !== $url && ! preg_match( '#^(/|\#)#', $url ) ) {
				$url = esc_url_raw( $url );
			}
			if ( '' === $label && ( '' === $url || '#' === $url ) ) {
				continue;
			}
			$out[] = array( 'label' => $label, 'url' => $url ? $url : '#' );
		}
		return $out;
	}
}
