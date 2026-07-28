<?php
/**
 * REST API that exposes the whole site content as a single JSON document,
 * shaped exactly like the React front-end's SiteContent object, plus the
 * dynamic pages. This is the contract the headless front-end consumes.
 *
 * Namespace: studio-tabi/v1
 *   GET /content        Full site content.
 *   GET /pages          List of published pages (slug + title) for navigation.
 *   GET /page/{slug}    Single page (title + rendered HTML).
 *
 * @package StudioTabiCMS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STCMS_Rest {

	const NS = 'studio-tabi/v1';

	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register' ) );
		// Allow cross-origin reads from the decoupled front-end.
		add_action( 'rest_api_init', array( __CLASS__, 'cors' ), 15 );
	}

	public static function register() {
		register_rest_route(
			self::NS,
			'/content',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_content' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			self::NS,
			'/pages',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_pages' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			self::NS,
			'/contact',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'submit_contact' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			self::NS,
			'/page/(?P<slug>[a-zA-Z0-9\-_%]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_page' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'slug' => array( 'sanitize_callback' => 'sanitize_title' ),
				),
			)
		);
		// Blog (posts nativos do WordPress).
		register_rest_route(
			self::NS,
			'/posts',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_posts_list' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			self::NS,
			'/post/(?P<slug>[a-zA-Z0-9\-_%]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_single_post' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'slug' => array( 'sanitize_callback' => 'sanitize_title' ),
				),
			)
		);
		register_rest_route(
			self::NS,
			'/categories',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_categories_list' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			self::NS,
			'/subscribe',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'subscribe_newsletter' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Send permissive CORS headers so a front-end hosted on another domain
	 * (or a Vite dev server) can read the API. The allowed origin is
	 * configurable via the STCMS_CORS_ORIGIN constant or the
	 * `stcms_cors_origin` option; defaults to "*".
	 */
	public static function cors() {
		remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
		add_filter(
			'rest_pre_serve_request',
			function ( $served ) {
				$origin = defined( 'STCMS_CORS_ORIGIN' ) ? STCMS_CORS_ORIGIN : get_option( 'stcms_cors_origin', '*' );
				header( 'Access-Control-Allow-Origin: ' . $origin );
				header( 'Access-Control-Allow-Methods: GET, POST, OPTIONS' );
				header( 'Access-Control-Allow-Headers: Authorization, Content-Type' );
				header( 'Vary: Origin' );
				return $served;
			}
		);
	}

	/* ------------------------------------------------------------- content    */

	public static function get_content() {
		$o = STCMS_Options::get();

		$data = array(
			'site'     => array(
				'title'           => $o['site']['title'],
				'metaDescription' => self::decode( $o['site']['meta_description'] ),
				'tagline'      => $o['site']['tagline'],
				'logoUrl'      => self::img( $o['site']['logo_id'], 'full' ),
				'faviconUrl'   => self::img( $o['site']['favicon_id'], 'full' ),
				'heroImageUrl' => self::img( $o['hero']['image_id'], 'full' ),
				// Integrações públicas (client-side). O secret do reCAPTCHA jamais é exposto.
				'ga4Id'         => (string) ( $o['site']['ga4_id'] ?? '' ),
				'gtmId'         => (string) ( $o['site']['gtm_id'] ?? '' ),
				'recaptchaSite' => (string) ( $o['site']['recaptcha_site'] ?? '' ),
			),
			'nav'      => array(
				'brand'    => self::decode( $o['nav']['brand'] ),
				'links'    => self::links( $o['nav']['links'] ),
				'ctaLabel' => self::decode( $o['nav']['cta_label'] ),
				'ctaUrl'   => (string) $o['nav']['cta_url'],
			),
			'hero'     => array(
				'eyebrow'     => $o['hero']['eyebrow'],
				'titleLines'  => array_values( (array) $o['hero']['title_lines'] ),
				'highlight'   => $o['hero']['highlight'],
				'description' => $o['hero']['description'],
				'ctaPrimary'  => array(
					'label' => self::decode( $o['hero']['cta_primary_label'] ),
					'url'   => (string) $o['hero']['cta_primary_url'],
				),
				'ctaSecondary' => array(
					'label' => self::decode( $o['hero']['cta_secondary_label'] ),
					'url'   => (string) $o['hero']['cta_secondary_url'],
				),
			),
			'projectsCta' => array(
				'label' => self::decode( $o['projects_cta']['label'] ),
				'url'   => (string) $o['projects_cta']['url'],
			),
			'thankYou' => array(
				'title'   => self::decode( $o['thankyou']['title'] ),
				'message' => self::decode( $o['thankyou']['message'] ),
			),
			'contact' => array(
				'title'       => self::decode( $o['contact']['title'] ),
				'highlight'   => self::decode( $o['contact']['highlight'] ),
				'description' => self::decode( $o['contact']['description'] ),
			),
			'process' => array_map(
				function ( $s ) {
					return array(
						'title'   => self::decode( $s['title'] ?? '' ),
						'slug'    => (string) ( $s['slug'] ?? '' ),
						'icon'    => (string) ( $s['icon'] ?? '' ),
						'summary' => self::decode( $s['summary'] ?? '' ),
					);
				},
				array_values( (array) ( $o['process'] ?? array() ) )
			),
			'sections' => array(
				'about' => array(
					'eyebrow'      => self::decode( $o['sections']['about_eyebrow'] ),
					'pillarsLabel' => self::decode( $o['sections']['about_pillars_label'] ),
					'ctaLabel'     => self::decode( $o['sections']['about_cta_label'] ),
					'ctaUrl'       => (string) $o['sections']['about_cta_url'],
				),
				'services' => array(
					'eyebrow'  => self::decode( $o['sections']['services_eyebrow'] ),
					'ctaLabel' => self::decode( $o['sections']['services_cta_label'] ),
					'ctaUrl'   => (string) $o['sections']['services_cta_url'],
				),
				'projects' => array(
					'eyebrow'  => self::decode( $o['sections']['projects_eyebrow'] ),
					'note'     => self::decode( $o['sections']['projects_note'] ),
					'cardText' => self::decode( $o['sections']['projects_card_text'] ),
				),
				'blog' => array(
					'eyebrow'   => self::decode( $o['sections']['blog_eyebrow'] ?? '' ),
					'title'     => self::decode( $o['sections']['blog_title'] ?? '' ),
					'highlight' => self::decode( $o['sections']['blog_highlight'] ?? '' ),
					'note'      => self::decode( $o['sections']['blog_note'] ?? '' ),
					'ctaLabel'  => self::decode( $o['sections']['blog_cta_label'] ?? '' ),
				),
				'faq' => array(
					'eyebrow'  => self::decode( $o['sections']['faq_eyebrow'] ),
					'note'     => self::decode( $o['sections']['faq_note'] ),
					'ctaLabel' => self::decode( $o['sections']['faq_cta_label'] ),
					'ctaUrl'   => (string) $o['sections']['faq_cta_url'],
				),
			),
			'about'    => array(
				'paragraph1' => $o['about']['paragraph1'],
				'paragraph2' => $o['about']['paragraph2'],
				'stats'      => self::stats( $o['about']['stats'] ),
				'pillars'    => array_values( (array) $o['about']['pillars'] ),
			),
			'services' => self::services(),
			'projects' => self::projects(),
			'faq'      => self::faq(),
			'footer'   => array(
				'brand'        => self::decode( $o['footer']['brand'] ),
				'tagline'      => $o['footer']['tagline'],
				'ctaTitle'     => self::decode( $o['footer']['cta_title'] ?? '' ),
				'ctaHighlight' => self::decode( $o['footer']['cta_highlight'] ?? '' ),
				'ctaLabel'     => self::decode( $o['footer']['cta_label'] ),
				'ctaUrl'       => (string) $o['footer']['cta_url'],
				'columns'      => array(
					array( 'title' => self::decode( $o['footer']['col1_title'] ), 'links' => self::links( $o['footer']['col1_links'] ) ),
					array( 'title' => self::decode( $o['footer']['col2_title'] ), 'links' => self::links( $o['footer']['col2_links'] ) ),
				),
				'contactTitle' => self::decode( $o['footer']['contact_title'] ),
				'email'        => $o['footer']['email'],
				'phone'        => $o['footer']['phone'],
				'city'         => $o['footer']['city'],
				'socialTitle'  => self::decode( $o['footer']['social_title'] ),
				'social'       => self::links( $o['footer']['social'] ),
				'copyright'    => self::decode( $o['footer']['copyright'] ),
				'madeIn'       => self::decode( $o['footer']['made_in'] ),
				'legal'        => self::links( $o['footer']['legal'] ),
			),
			'pages'    => self::pages_list(),
		);

		return new WP_REST_Response( $data, 200 );
	}

	/* ------------------------------------------------------------- contact    */

	/**
	 * Receive a contact-form submission from the headless front-end and email
	 * it to the site owner. Recipient = footer e-mail (editável no CMS),
	 * caindo para o e-mail do admin do WordPress se estiver vazio.
	 */
	public static function submit_contact( WP_REST_Request $req ) {
		$p = $req->get_json_params();
		if ( ! is_array( $p ) ) {
			$p = $req->get_params();
		}

		// Honeypot anti-spam: bots preenchem o campo oculto "website".
		if ( ! empty( $p['website'] ) ) {
			return new WP_REST_Response( array( 'ok' => true ), 200 );
		}

		// reCAPTCHA v3 (só bloqueia se o secret estiver configurado no CMS).
		if ( ! self::verify_recaptcha( $p['recaptchaToken'] ?? '', 'contact' ) ) {
			return new WP_REST_Response(
				array( 'ok' => false, 'message' => 'Falha na verificação anti-spam. Recarregue a página e tente novamente.' ),
				422
			);
		}

		$name    = sanitize_text_field( $p['name'] ?? '' );
		$email   = sanitize_email( $p['email'] ?? '' );
		$subject = sanitize_text_field( $p['subject'] ?? '' );
		$message = sanitize_textarea_field( $p['message'] ?? '' );

		if ( '' === $name || '' === $message || ! is_email( $email ) ) {
			return new WP_REST_Response(
				array( 'ok' => false, 'message' => 'Preencha nome, um e-mail válido e a mensagem.' ),
				422
			);
		}

		$recipient = self::form_recipient();

		$site_name   = get_bloginfo( 'name' );
		$mail_title  = $subject ? $subject : 'Nova mensagem pelo site';
		$mail_body   = "Nome: {$name}\n";
		$mail_body  .= "E-mail: {$email}\n";
		if ( $subject ) {
			$mail_body .= "Assunto: {$subject}\n";
		}
		$mail_body  .= "\nMensagem:\n{$message}\n";

		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			'Reply-To: ' . $name . ' <' . $email . '>',
		);

		$sent = wp_mail( $recipient, '[' . $site_name . '] ' . $mail_title, $mail_body, $headers );

		if ( ! $sent ) {
			return new WP_REST_Response(
				array( 'ok' => false, 'message' => 'Não foi possível enviar agora. Tente novamente ou escreva direto para o nosso e-mail.' ),
				500
			);
		}

		return new WP_REST_Response( array( 'ok' => true, 'message' => 'Mensagem enviada! Em breve entraremos em contato.' ), 200 );
	}

	/* ------------------------------------------------------------- blog       */

	/** Lista paginada de posts do blog (posts nativos do WordPress). */
	public static function get_posts_list( WP_REST_Request $req ) {
		$page     = max( 1, (int) $req->get_param( 'page' ) );
		$per_page = min( 24, max( 1, (int) ( $req->get_param( 'per_page' ) ?: 9 ) ) );
		$search   = sanitize_text_field( (string) $req->get_param( 'search' ) );
		$category = sanitize_title( (string) $req->get_param( 'category' ) );

		$args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		if ( '' !== $search ) {
			$args['s'] = $search;
		}
		if ( '' !== $category ) {
			$args['category_name'] = $category;
		}

		$q     = new WP_Query( $args );
		$items = array();
		foreach ( $q->posts as $p ) {
			$items[] = self::post_card( $p );
		}
		wp_reset_postdata();

		return new WP_REST_Response(
			array(
				'items'      => $items,
				'total'      => (int) $q->found_posts,
				'totalPages' => (int) $q->max_num_pages,
				'page'       => $page,
				'perPage'    => $per_page,
			),
			200
		);
	}

	/** Resumo de um post para listagens (card). */
	private static function post_card( $p ) {
		$cats = array();
		foreach ( (array) get_the_category( $p->ID ) as $c ) {
			$cats[] = array( 'name' => self::decode( $c->name ), 'slug' => $c->slug );
		}
		$plain = wp_strip_all_tags( $p->post_content );
		$words = str_word_count( $plain );
		return array(
			'id'          => $p->ID,
			'slug'        => $p->post_name,
			'title'       => self::title( $p ),
			'excerpt'     => self::decode( wp_trim_words( has_excerpt( $p ) ? get_the_excerpt( $p ) : $plain, 26, '…' ) ),
			'date'        => get_the_date( 'j M Y', $p ),
			'dateISO'     => get_the_date( 'c', $p ),
			'author'      => self::decode( get_the_author_meta( 'display_name', $p->post_author ) ),
			'image'       => get_the_post_thumbnail_url( $p, 'large' ) ? get_the_post_thumbnail_url( $p, 'large' ) : '',
			'categories'  => $cats,
			'readingTime' => max( 1, (int) ceil( $words / 200 ) ),
		);
	}

	/** Post único com conteúdo completo, autor e relacionados. */
	public static function get_single_post( WP_REST_Request $req ) {
		$slug = $req->get_param( 'slug' );
		$post = get_page_by_path( $slug, OBJECT, 'post' );
		if ( ! $post || 'publish' !== $post->post_status ) {
			return new WP_REST_Response( array( 'message' => 'Artigo não encontrado.' ), 404 );
		}

		$card = self::post_card( $post );

		// Relacionados: mesma categoria, exceto o próprio.
		$cat_ids = wp_get_post_categories( $post->ID );
		$related = array();
		if ( $cat_ids ) {
			$rq = new WP_Query(
				array(
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => 3,
					'post__not_in'   => array( $post->ID ),
					'category__in'   => $cat_ids,
					'orderby'        => 'date',
					'order'          => 'DESC',
				)
			);
			foreach ( $rq->posts as $rp ) {
				$related[] = self::post_card( $rp );
			}
			wp_reset_postdata();
		}

		$tags = array();
		foreach ( (array) get_the_tags( $post->ID ) as $t ) {
			$tags[] = array( 'name' => self::decode( $t->name ), 'slug' => $t->slug );
		}

		$data = array_merge(
			$card,
			array(
				'content'      => apply_filters( 'the_content', $post->post_content ),
				'tags'         => $tags,
				'authorBio'    => self::decode( get_the_author_meta( 'description', $post->post_author ) ),
				'authorAvatar' => get_avatar_url( $post->post_author, array( 'size' => 96 ) ),
				'related'      => $related,
			)
		);
		return new WP_REST_Response( $data, 200 );
	}

	/** Categorias do blog (com contagem). */
	public static function get_categories_list() {
		$cats = get_categories( array( 'hide_empty' => true ) );
		$out  = array();
		foreach ( $cats as $c ) {
			$out[] = array(
				'name'  => self::decode( $c->name ),
				'slug'  => $c->slug,
				'count' => (int) $c->count,
			);
		}
		return new WP_REST_Response( $out, 200 );
	}

	/** Inscrição na newsletter: guarda o e-mail e avisa o dono do site. */
	public static function subscribe_newsletter( WP_REST_Request $req ) {
		$p     = $req->get_json_params();
		$email = sanitize_email( is_array( $p ) ? ( $p['email'] ?? '' ) : '' );
		if ( ! is_email( $email ) ) {
			return new WP_REST_Response( array( 'ok' => false, 'message' => 'Informe um e-mail válido.' ), 422 );
		}
		if ( ! self::verify_recaptcha( is_array( $p ) ? ( $p['recaptchaToken'] ?? '' ) : '', 'newsletter' ) ) {
			return new WP_REST_Response( array( 'ok' => false, 'message' => 'Falha na verificação anti-spam. Tente novamente.' ), 422 );
		}
		$list = get_option( 'stcms_newsletter', array() );
		if ( ! is_array( $list ) ) {
			$list = array();
		}
		if ( ! in_array( $email, $list, true ) ) {
			$list[] = $email;
			update_option( 'stcms_newsletter', $list );
			wp_mail( self::form_recipient(), '[' . get_bloginfo( 'name' ) . '] Nova inscrição na newsletter', "Novo e-mail inscrito: {$email}" );
		}
		return new WP_REST_Response( array( 'ok' => true, 'message' => 'Inscrição confirmada! Obrigado.' ), 200 );
	}

	/**
	 * Para onde vão os e-mails dos formulários (contato e newsletter).
	 *
	 * Ordem: "E-mail que recebe os formulários" (Integrações) → e-mail do
	 * Rodapé → e-mail do administrador do WordPress. Assim o endereço de
	 * exibição no site pode ser diferente do que recebe as mensagens.
	 */
	private static function form_recipient() {
		$o = STCMS_Options::get();
		$form = trim( (string) ( $o['site']['form_email'] ?? '' ) );
		if ( $form && is_email( $form ) ) {
			return $form;
		}
		$footer = trim( (string) ( $o['footer']['email'] ?? '' ) );
		if ( $footer && is_email( $footer ) ) {
			return $footer;
		}
		return get_option( 'admin_email' );
	}

	/**
	 * Verifica um token do reCAPTCHA v3 contra o Google. Só valida quando o
	 * secret está configurado no CMS — assim o site continua funcionando sem
	 * reCAPTCHA. Retorna true se aprovado (ou se a proteção está desligada).
	 *
	 * @param string $token  Token gerado pelo grecaptcha.execute() no cliente.
	 * @param string $action Ação esperada (ex.: "contact", "newsletter").
	 */
	private static function verify_recaptcha( $token, $action ) {
		$o      = STCMS_Options::get();
		$secret = trim( (string) ( $o['site']['recaptcha_secret'] ?? '' ) );
		if ( '' === $secret ) {
			return true; // Proteção desligada: não bloqueia envios.
		}
		$token = trim( (string) $token );
		if ( '' === $token ) {
			return false;
		}
		$resp = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'timeout' => 8,
				'body'    => array(
					'secret'   => $secret,
					'response' => $token,
				),
			)
		);
		if ( is_wp_error( $resp ) ) {
			return false;
		}
		$body = json_decode( wp_remote_retrieve_body( $resp ), true );
		if ( ! is_array( $body ) || empty( $body['success'] ) ) {
			return false;
		}
		// v3 devolve um score (0..1); ações abaixo de 0.5 são tratadas como bot.
		if ( isset( $body['score'] ) && (float) $body['score'] < 0.5 ) {
			return false;
		}
		if ( ! empty( $body['action'] ) && $action && $body['action'] !== $action ) {
			return false;
		}
		return true;
	}

	/**
	 * Cover/thumbnail URL for a project: the dedicated "Foto de capa"
	 * (stcms_cover) if set, otherwise the WordPress featured image.
	 */
	private static function project_cover( $id, $post ) {
		$cover = (int) get_post_meta( $id, 'stcms_cover', true );
		if ( $cover ) {
			$url = self::img( $cover, 'large' );
			if ( ! $url ) {
				$url = wp_get_attachment_url( $cover );
			}
			if ( $url ) {
				return $url;
			}
		}
		$thumb = get_the_post_thumbnail_url( $post, 'large' );
		return $thumb ? $thumb : '';
	}

	private static function img( $id, $size = 'full' ) {
		$id = (int) $id;
		if ( ! $id ) {
			return '';
		}
		$url = wp_get_attachment_image_url( $id, $size );
		return $url ? $url : '';
	}

	/**
	 * Resolve a comma-separated list of attachment IDs into full image URLs.
	 * Uses the full size (always exists) + falls back to wp_get_attachment_url
	 * so nenhuma imagem some por falta de um tamanho intermediário.
	 */
	private static function gallery_urls( $raw ) {
		if ( ! $raw ) {
			return array();
		}
		$out = array();
		foreach ( explode( ',', (string) $raw ) as $id ) {
			$id  = (int) $id;
			$url = self::img( $id, 'full' );
			if ( ! $url ) {
				$url = wp_get_attachment_url( $id );
			}
			if ( $url ) {
				$out[] = $url;
			}
		}
		return $out;
	}

	/**
	 * Resolve a comma-separated list of attachment IDs into documents
	 * (PDFs etc.), returning { url, title } for each.
	 */
	private static function document_list( $raw ) {
		if ( ! $raw ) {
			return array();
		}
		$out = array();
		foreach ( explode( ',', (string) $raw ) as $id ) {
			$id  = (int) $id;
			$url = wp_get_attachment_url( $id );
			if ( ! $url ) {
				continue;
			}
			$title = get_the_title( $id );
			if ( '' === $title ) {
				$title = basename( wp_parse_url( $url, PHP_URL_PATH ) );
			}
			$out[] = array(
				'url'   => $url,
				'title' => self::decode( $title ),
			);
		}
		return $out;
	}

	private static function stats( $stats ) {
		$out = array();
		foreach ( (array) $stats as $s ) {
			$numeric = isset( $s['numeric'] ) ? $s['numeric'] : 0;
			$out[]   = array(
				'numeric' => is_numeric( $numeric ) ? 0 + $numeric : $numeric,
				'suffix'  => isset( $s['suffix'] ) ? $s['suffix'] : '',
				'label'   => isset( $s['label'] ) ? $s['label'] : '',
			);
		}
		return $out;
	}

	private static function services() {
		$posts = get_posts(
			array(
				'post_type'   => 'st_service',
				'numberposts' => -1,
				'orderby'     => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
				'order'       => 'ASC',
			)
		);
		$out = array();
		foreach ( $posts as $p ) {
			$out[] = array(
				'num'     => (string) get_post_meta( $p->ID, 'stcms_num', true ),
				'title'   => self::title( $p ),
				'body'    => self::plain( $p->post_content ),
				'slug'    => $p->post_name,
				'content' => apply_filters( 'the_content', $p->post_content ),
				'image'   => get_the_post_thumbnail_url( $p, 'large' ) ? get_the_post_thumbnail_url( $p, 'large' ) : '',
			);
		}
		return $out;
	}

	private static function faq() {
		$posts = get_posts(
			array(
				'post_type'   => 'st_faq',
				'numberposts' => -1,
				'orderby'     => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
				'order'       => 'ASC',
			)
		);
		$out = array();
		foreach ( $posts as $p ) {
			$out[] = array(
				'q' => self::title( $p ),
				'a' => self::plain( $p->post_content ),
			);
		}
		return $out;
	}

	private static function projects() {
		$posts = get_posts(
			array(
				'post_type'   => 'st_project',
				'numberposts' => -1,
				'orderby'     => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
				'order'       => 'ASC',
			)
		);
		$out = array();
		foreach ( $posts as $p ) {
			$id      = $p->ID;
			$scope   = self::flatten_rows( get_post_meta( $id, 'stcms_scope', true ), 'item' );
			$mockup  = self::flatten_rows( get_post_meta( $id, 'stcms_mockup', true ), 'item' );
			$results = array();
			foreach ( (array) get_post_meta( $id, 'stcms_results', true ) as $r ) {
				if ( ! is_array( $r ) ) {
					continue;
				}
				$results[] = array(
					'label' => isset( $r['label'] ) ? $r['label'] : '',
					'value' => isset( $r['value'] ) ? $r['value'] : '',
				);
			}

			$num = get_post_meta( $id, 'stcms_num', true );

			$out[] = array(
				'id'       => $num ? (string) $num : (string) $id,
				'name'     => get_post_meta( $id, 'stcms_name', true ) ? get_post_meta( $id, 'stcms_name', true ) : self::title( $p ),
				'category' => (string) get_post_meta( $id, 'stcms_category', true ),
				'year'     => (string) get_post_meta( $id, 'stcms_year', true ),
				'bg'       => (string) get_post_meta( $id, 'stcms_bg', true ),
				'accent'   => (string) get_post_meta( $id, 'stcms_accent', true ),
				'featured' => '1' === get_post_meta( $id, 'stcms_featured', true ),
				'home'     => '1' === get_post_meta( $id, 'stcms_home', true ),
				'imageUrl' => self::project_cover( $id, $p ),
				'url'      => (string) get_post_meta( $id, 'stcms_url', true ),
				'gallery'  => self::gallery_urls( get_post_meta( $id, 'stcms_gallery', true ) ),
				'documents' => self::document_list( get_post_meta( $id, 'stcms_documents', true ) ),
				'detail'   => array(
					'client'      => (string) get_post_meta( $id, 'stcms_client', true ),
					'scope'       => $scope,
					'duration'    => (string) get_post_meta( $id, 'stcms_duration', true ),
					'challenge'   => (string) get_post_meta( $id, 'stcms_challenge', true ),
					'solution'    => (string) get_post_meta( $id, 'stcms_solution', true ),
					'results'     => $results,
					'mockupLines' => $mockup,
				),
			);
		}
		return $out;
	}

	private static function flatten_rows( $rows, $key ) {
		$out = array();
		foreach ( (array) $rows as $row ) {
			if ( is_array( $row ) && isset( $row[ $key ] ) && '' !== $row[ $key ] ) {
				$out[] = $row[ $key ];
			}
		}
		return $out;
	}

	private static function plain( $content ) {
		return self::decode( trim( wp_strip_all_tags( $content ) ) );
	}

	/**
	 * Title as plain UTF-8 text. get_the_title() runs the `the_title` filter,
	 * which HTML-encodes characters like & into &#038;; a headless JSON API
	 * must return the decoded text so the front-end renders it verbatim.
	 */
	private static function title( $post ) {
		return self::decode( get_the_title( $post ) );
	}

	private static function decode( $text ) {
		return html_entity_decode( (string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}

	/**
	 * Normalize a repeater of {label, url} into clean link objects.
	 */
	private static function links( $rows ) {
		$out = array();
		foreach ( (array) $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$label = isset( $row['label'] ) ? self::decode( $row['label'] ) : '';
			$url   = isset( $row['url'] ) ? $row['url'] : '#';
			if ( '' === $label && ( '' === $url || '#' === $url ) ) {
				continue;
			}
			$out[] = array( 'label' => $label, 'url' => $url ? $url : '#' );
		}
		return $out;
	}

	/* --------------------------------------------------------------- pages    */

	private static function pages_list() {
		$pages = get_posts(
			array(
				'post_type'   => 'page',
				'numberposts' => -1,
				'orderby'     => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
				'order'       => 'ASC',
				'post_status' => 'publish',
			)
		);
		// Não listar as páginas de etapa do processo (têm rota própria /processo/{slug}).
		$o       = STCMS_Options::get();
		$exclude = array();
		foreach ( (array) ( $o['process'] ?? array() ) as $s ) {
			if ( ! empty( $s['slug'] ) ) {
				$exclude[] = $s['slug'];
			}
		}
		$out = array();
		foreach ( $pages as $p ) {
			if ( in_array( $p->post_name, $exclude, true ) ) {
				continue;
			}
			$out[] = array(
				'slug'  => $p->post_name,
				'title' => self::title( $p ),
			);
		}
		return $out;
	}

	public static function get_pages() {
		return new WP_REST_Response( self::pages_list(), 200 );
	}

	public static function get_page( WP_REST_Request $req ) {
		$slug = $req->get_param( 'slug' );
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( ! $page || 'publish' !== $page->post_status ) {
			return new WP_REST_Response( array( 'message' => 'Página não encontrada.' ), 404 );
		}
		return new WP_REST_Response(
			array(
				'slug'    => $page->post_name,
				'title'   => self::title( $page ),
				'content' => apply_filters( 'the_content', $page->post_content ),
			),
			200
		);
	}
}
