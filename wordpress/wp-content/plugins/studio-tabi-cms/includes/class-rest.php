<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STCMS_Rest {

	const NS = 'studio-tabi/v1';

	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register' ) );

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
		register_rest_route(
			self::NS,
			'/sitemap',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_sitemap' ),
				'permission_callback' => '__return_true',
			)
		);
	}

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

	public static function req_lang( $req = null ) {
		$l = '';
		if ( $req instanceof WP_REST_Request ) {
			$l = (string) $req->get_param( 'lang' );
		}
		if ( '' === $l && isset( $_GET['lang'] ) ) {
			$l = sanitize_key( wp_unslash( $_GET['lang'] ) );
		}
		return 'en' === $l ? 'en' : 'pt';
	}

	public static function get_content( $req = null ) {
		$lang = self::req_lang( $req );
		$o = STCMS_Options::get( $lang );

		$data = array(
			'locale'   => $lang,
			'site'     => array(
				'title'           => $o['site']['title'],
				'metaDescription' => self::decode( $o['site']['meta_description'] ),
				'tagline'      => $o['site']['tagline'],
				'logoUrl'      => self::img( $o['site']['logo_id'], 'full' ),
				'faviconUrl'   => self::img( $o['site']['favicon_id'], 'full' ),
				'heroImageUrl' => self::img( $o['hero']['image_id'], 'full' ),

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
			'services' => self::services( $lang ),
			'projects' => self::projects( $lang ),
			'faq'      => self::faq( $lang ),
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
				'newsletterTitle'  => self::decode( $o['footer']['newsletter_title'] ?? '' ),
				'newsletterText'   => self::decode( $o['footer']['newsletter_text'] ?? '' ),
				'newsletterButton' => self::decode( $o['footer']['newsletter_button'] ?? '' ),
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
			'pages'    => self::pages_list( $lang ),
		);

		return new WP_REST_Response( $data, 200 );
	}

	public static function submit_contact( WP_REST_Request $req ) {
		$bloqueio = self::bloqueio_de_escrita( 'contact', 5 );
		if ( $bloqueio ) {
			return $bloqueio;
		}

		$p = $req->get_json_params();
		if ( ! is_array( $p ) ) {
			$p = $req->get_params();
		}

		if ( ! empty( $p['website'] ) ) {
			return new WP_REST_Response( array( 'ok' => true ), 200 );
		}

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

	public static function get_posts_list( WP_REST_Request $req ) {
		$page     = max( 1, (int) $req->get_param( 'page' ) );
		$per_page = min( 24, max( 1, (int) ( $req->get_param( 'per_page' ) ?: 9 ) ) );
		$search   = sanitize_text_field( (string) $req->get_param( 'search' ) );
		$category = sanitize_title( (string) $req->get_param( 'category' ) );

		$args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'meta_query'     => self::meta_lang( self::req_lang( $req ) ),
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
		$lang = self::req_lang( $req );
		foreach ( $q->posts as $p ) {
			$items[] = self::post_card( $p, $lang );
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

	private static function post_card( $p, $lang = 'pt' ) {
		$cats = array();
		foreach ( (array) get_the_category( $p->ID ) as $c ) {
			$cats[] = array( 'name' => self::decode( $c->name ), 'slug' => $c->slug );
		}
		$corpo = (string) STCMS_Traducao::texto( $p, 'body', $lang );
		$plain = wp_strip_all_tags( $corpo );
		$words = str_word_count( $plain );
		$resumo = (string) STCMS_Traducao::texto( $p, 'excerpt', $lang );
		if ( '' === trim( $resumo ) ) {
			$resumo = has_excerpt( $p ) ? get_the_excerpt( $p ) : $plain;
		}
		return array(
			'id'          => $p->ID,
			'slug'        => $p->post_name,
			'title'       => self::decode( STCMS_Traducao::texto( $p, 'title', $lang ) ),
			'excerpt'     => self::decode( wp_trim_words( $resumo, 26, '…' ) ),
			'date'        => get_the_date( 'j M Y', $p ),
			'dateISO'     => get_the_date( 'c', $p ),
			'author'      => self::decode( get_the_author_meta( 'display_name', $p->post_author ) ),
			'image'       => get_the_post_thumbnail_url( $p, 'large' ) ? get_the_post_thumbnail_url( $p, 'large' ) : '',
			'categories'  => $cats,
			'readingTime' => max( 1, (int) ceil( $words / 200 ) ),
		);
	}

	public static function get_single_post( WP_REST_Request $req ) {
		$slug = $req->get_param( 'slug' );
		$lang = self::req_lang( $req );
		$post = get_page_by_path( $slug, OBJECT, 'post' );
		if ( ! $post || 'publish' !== $post->post_status ) {
			return new WP_REST_Response( array( 'message' => 'Artigo não encontrado.' ), 404 );
		}

		$card = self::post_card( $post, $lang );

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
				$related[] = self::post_card( $rp, $lang );
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
				'content'      => apply_filters( 'the_content', STCMS_Traducao::texto( $post, 'body', $lang ) ),
				'tags'         => $tags,
				'authorBio'    => self::decode( get_the_author_meta( 'description', $post->post_author ) ),
				'authorAvatar' => get_avatar_url( $post->post_author, array( 'size' => 96 ) ),
				'related'      => $related,
			)
		);
		return new WP_REST_Response( $data, 200 );
	}

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

	public static function subscribe_newsletter( WP_REST_Request $req ) {
		$bloqueio = self::bloqueio_de_escrita( 'subscribe', 8 );
		if ( $bloqueio ) {
			return $bloqueio;
		}

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

	private static function client_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
		return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
	}

	private static function site_origins() {
		$lista = array();
		$opt   = trim( (string) get_option( 'stcms_site_origin', '' ) );
		if ( $opt ) {
			foreach ( explode( ',', $opt ) as $o ) {
				$o = trim( $o );
				if ( $o ) {
					$lista[] = untrailingslashit( $o );
				}
			}
		}
		$home = untrailingslashit( (string) get_home_url() );
		if ( $home ) {
			$lista[] = $home;
		}
		return array_unique( $lista );
	}

	private static function origem_permitida() {
		$origin = isset( $_SERVER['HTTP_ORIGIN'] ) ? untrailingslashit( esc_url_raw( wp_unslash( $_SERVER['HTTP_ORIGIN'] ) ) ) : '';
		if ( '' === $origin ) {
			return true;
		}
		$host = wp_parse_url( $origin, PHP_URL_HOST );
		foreach ( self::site_origins() as $p ) {
			$ph = wp_parse_url( $p, PHP_URL_HOST );
			if ( ! $ph || ! $host ) {
				continue;
			}
			if ( $host === $ph ) {
				return true;
			}
			if ( substr( $host, -strlen( '.' . $ph ) ) === '.' . $ph ) {
				return true;
			}
			if ( substr( $ph, -strlen( '.' . $host ) ) === '.' . $host ) {
				return true;
			}
		}
		return false;
	}

	private static function limite_excedido( $chave, $maximo, $janela ) {
		$id = 'stcms_rl_' . $chave . '_' . md5( self::client_ip() );
		$n  = (int) get_transient( $id );
		if ( $n >= $maximo ) {
			return true;
		}
		set_transient( $id, $n + 1, $janela );
		return false;
	}

	private static function bloqueio_de_escrita( $chave, $maximo ) {
		if ( ! self::origem_permitida() ) {
			return new WP_REST_Response( array( 'ok' => false, 'message' => 'Origem nao autorizada.' ), 403 );
		}
		if ( self::limite_excedido( $chave, $maximo, HOUR_IN_SECONDS ) ) {
			return new WP_REST_Response( array( 'ok' => false, 'message' => 'Muitas tentativas. Aguarde alguns minutos e tente novamente.' ), 429 );
		}
		return null;
	}

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

	private static function verify_recaptcha( $token, $action ) {
		$o      = STCMS_Options::get();
		$secret = trim( (string) ( $o['site']['recaptcha_secret'] ?? '' ) );
		if ( '' === $secret ) {
			return true;
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

		if ( isset( $body['score'] ) && (float) $body['score'] < 0.5 ) {
			return false;
		}
		if ( ! empty( $body['action'] ) && $action && $body['action'] !== $action ) {
			return false;
		}
		return true;
	}

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

	private static function services( $lang = 'pt' ) {
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

			$page = (string) STCMS_Traducao::texto( $p, 'page_content', $lang );
			$body = (string) STCMS_Traducao::texto( $p, 'body', $lang );
			$rich = '' !== trim( $page ) ? $page : $body;
			$out[] = array(
				'num'     => (string) get_post_meta( $p->ID, 'stcms_num', true ),
				'title'   => self::decode( STCMS_Traducao::texto( $p, 'title', $lang ) ),
				'body'    => self::plain( $body ),
				'slug'    => $p->post_name,
				'content' => apply_filters( 'the_content', $rich ),

				'showExcerpt' => '1' === (string) get_post_meta( $p->ID, 'stcms_show_excerpt', true ),
				'image'   => get_the_post_thumbnail_url( $p, 'large' ) ? get_the_post_thumbnail_url( $p, 'large' ) : '',
			);
		}
		return $out;
	}

	private static function faq( $lang = 'pt' ) {
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
				'q' => self::decode( STCMS_Traducao::texto( $p, 'title', $lang ) ),
				'a' => self::plain( STCMS_Traducao::texto( $p, 'body', $lang ) ),
			);
		}
		return $out;
	}

	private static function projects( $lang = 'pt' ) {
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
			$scope   = STCMS_Traducao::lista( $p, 'scope', $lang, self::flatten_rows( get_post_meta( $id, 'stcms_scope', true ), 'item' ) );
			$mockup  = STCMS_Traducao::lista( $p, 'mockup', $lang, self::flatten_rows( get_post_meta( $id, 'stcms_mockup', true ), 'item' ) );
			$results = array();
			$rotulos = array();
			foreach ( (array) get_post_meta( $id, 'stcms_results', true ) as $r ) {
				if ( ! is_array( $r ) ) {
					continue;
				}
				$rotulos[] = isset( $r['label'] ) ? $r['label'] : '';
			}
			$rotulos = STCMS_Traducao::lista( $p, 'results', $lang, $rotulos );
			$i = 0;
			foreach ( (array) get_post_meta( $id, 'stcms_results', true ) as $r ) {
				if ( ! is_array( $r ) ) {
					continue;
				}
				$results[] = array(
					'label' => isset( $rotulos[ $i ] ) ? $rotulos[ $i ] : ( isset( $r['label'] ) ? $r['label'] : '' ),
					'value' => isset( $r['value'] ) ? $r['value'] : '',
				);
				$i++;
			}

			$num = get_post_meta( $id, 'stcms_num', true );

			$out[] = array(
				'id'       => $num ? (string) $num : (string) $id,
				'name'     => self::decode( STCMS_Traducao::texto( $p, 'title', $lang, get_post_meta( $id, 'stcms_name', true ) ? get_post_meta( $id, 'stcms_name', true ) : $p->post_title ) ),
				'category' => (string) STCMS_Traducao::texto( $p, 'category', $lang ),
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
					'duration'    => (string) STCMS_Traducao::texto( $p, 'duration', $lang ),
					'challenge'   => (string) STCMS_Traducao::texto( $p, 'challenge', $lang ),
					'solution'    => (string) STCMS_Traducao::texto( $p, 'solution', $lang ),
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

	private static function title( $post ) {
		return self::decode( get_the_title( $post ) );
	}

	private static function decode( $text ) {
		return html_entity_decode( (string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}

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

	private static function pages_list( $lang = 'pt' ) {
		$pages = get_posts(
			array(
				'post_type'   => 'page',
				'numberposts' => -1,
				'orderby'     => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
				'order'       => 'ASC',
				'post_status' => 'publish',
			)
		);

		$o       = STCMS_Options::get( $lang );
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
				'title' => self::decode( STCMS_Traducao::texto( $p, 'title', $lang ) ),
			);
		}
		return $out;
	}

	public static function get_pages( $req = null ) {
		return new WP_REST_Response( self::pages_list( self::req_lang( $req ) ), 200 );
	}

	public static function get_page( WP_REST_Request $req ) {
		$slug = $req->get_param( 'slug' );
		$lang = self::req_lang( $req );
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( ! $page || 'publish' !== $page->post_status ) {
			return new WP_REST_Response( array( 'message' => 'Página não encontrada.' ), 404 );
		}
		return new WP_REST_Response(
			array(
				'slug'    => $page->post_name,
				'title'   => self::decode( STCMS_Traducao::texto( $page, 'title', $lang ) ),
				'content' => apply_filters( 'the_content', STCMS_Traducao::texto( $page, 'body', $lang ) ),
			),
			200
		);
	}

	private static function rotas_i18n() {
		return array(
			'home'     => array( 'pt' => '', 'en' => '' ),
			'projetos' => array( 'pt' => 'projetos', 'en' => 'work' ),
			'sobre'    => array( 'pt' => 'sobre', 'en' => 'about' ),
			'servicos' => array( 'pt' => 'servicos', 'en' => 'services' ),
			'servico'  => array( 'pt' => 'servicos', 'en' => 'services' ),
			'processo' => array( 'pt' => 'processo', 'en' => 'process' ),
			'blog'     => array( 'pt' => 'blog', 'en' => 'blog' ),
			'artigo'   => array( 'pt' => 'blog', 'en' => 'blog' ),
			'contato'  => array( 'pt' => 'contato', 'en' => 'contact' ),
			'pagina'   => array( 'pt' => 'p', 'en' => 'p' ),
		);
	}

	private static function site_base() {
		$origens = self::site_origins();
		return $origens ? reset( $origens ) : untrailingslashit( (string) get_home_url() );
	}

	private static function rota( $base, $lang, $chave, $param = '' ) {
		$rotas  = self::rotas_i18n();
		$slug   = isset( $rotas[ $chave ][ $lang ] ) ? $rotas[ $chave ][ $lang ] : '';
		$prefixo = 'en' === $lang ? '/en' : '';
		$partes  = array_filter( array( $slug, $param ), 'strlen' );
		if ( ! $partes ) {
			return $base . ( $prefixo ? $prefixo : '/' );
		}
		return $base . $prefixo . '/' . implode( '/', $partes );
	}

	public static function get_sitemap() {
		$base = self::site_base();
		$urls = array();

		$estaticas = array(
			'home'     => array( '1.0', 'weekly' ),
			'projetos' => array( '0.9', 'weekly' ),
			'servicos' => array( '0.9', 'monthly' ),
			'blog'     => array( '0.9', 'weekly' ),
			'sobre'    => array( '0.8', 'monthly' ),
			'contato'  => array( '0.8', 'yearly' ),
		);
		foreach ( $estaticas as $chave => $cfg ) {
			$pt   = self::rota( $base, 'pt', $chave );
			$en   = self::rota( $base, 'en', $chave );
			$alts = array( 'pt-BR' => $pt, 'en' => $en, 'x-default' => $pt );
			foreach ( array( $pt, $en ) as $loc ) {
				$urls[] = array(
					'loc'        => $loc,
					'alternates' => $alts,
					'priority'   => $cfg[0],
					'changefreq' => $cfg[1],
				);
			}
		}

		$servicos_en = array();
		foreach ( get_posts( array( 'post_type' => 'st_service', 'numberposts' => -1, 'post_status' => 'publish' ) ) as $sp ) {
			$servicos_en[ $sp->post_name ] = STCMS_Traducao::tem_traducao( $sp );
		}

		// Um conteúdo serve os dois idiomas com o mesmo slug: quando existe
		// tradução, as duas URLs se apontam; quando não existe, só a portuguesa entra.
		$par = function ( $chave, $slug ) use ( $base ) {
			$pt = self::rota( $base, 'pt', $chave, $slug );
			$en = self::rota( $base, 'en', $chave, $slug );
			return array( 'pt-BR' => $pt, 'en' => $en, 'x-default' => $pt );
		};

		foreach ( self::services( 'pt' ) as $i => $s ) {
			if ( empty( $s['slug'] ) ) {
				continue;
			}
			$traduzido = isset( $servicos_en[ $s['slug'] ] ) ? $servicos_en[ $s['slug'] ] : false;
			$alts      = $traduzido ? $par( 'servico', $s['slug'] ) : array();
			$urls[] = array(
				'loc'        => self::rota( $base, 'pt', 'servico', $s['slug'] ),
				'alternates' => $alts,
				'priority'   => '0.7',
				'changefreq' => 'monthly',
			);
			if ( $traduzido ) {
				$urls[] = array(
					'loc'        => self::rota( $base, 'en', 'servico', $s['slug'] ),
					'alternates' => $alts,
					'priority'   => '0.7',
					'changefreq' => 'monthly',
				);
			}
		}

		foreach ( self::pages_list( 'pt' ) as $pg ) {
			$urls[] = array(
				'loc'        => self::rota( $base, 'pt', 'pagina', $pg['slug'] ),
				'alternates' => array(),
				'priority'   => '0.3',
				'changefreq' => 'yearly',
			);
		}

		$o = STCMS_Options::get();
		foreach ( (array) ( isset( $o['process'] ) ? $o['process'] : array() ) as $etapa ) {
			if ( empty( $etapa['slug'] ) ) {
				continue;
			}
			$pagina = get_page_by_path( $etapa['slug'], OBJECT, 'page' );
			if ( ! $pagina || 'publish' !== $pagina->post_status ) {
				continue;
			}
			$alts = STCMS_Traducao::tem_traducao( $pagina ) ? $par( 'processo', $etapa['slug'] ) : array();
			$urls[] = array(
				'loc'        => self::rota( $base, 'pt', 'processo', $etapa['slug'] ),
				'alternates' => $alts,
				'priority'   => '0.6',
				'changefreq' => 'monthly',
				'lastmod'    => mysql2date( 'Y-m-d', $pagina->post_modified_gmt ),
			);
			if ( $alts ) {
				$urls[] = array(
					'loc'        => self::rota( $base, 'en', 'processo', $etapa['slug'] ),
					'alternates' => $alts,
					'priority'   => '0.6',
					'changefreq' => 'monthly',
					'lastmod'    => mysql2date( 'Y-m-d', $pagina->post_modified_gmt ),
				);
			}
		}

		$posts = get_posts(
			array(
				'post_type'   => 'post',
				'post_status' => 'publish',
				'numberposts' => 500,
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);
		foreach ( $posts as $p ) {
			$alts = STCMS_Traducao::tem_traducao( $p ) ? $par( 'artigo', $p->post_name ) : array();
			$urls[] = array(
				'loc'        => self::rota( $base, 'pt', 'artigo', $p->post_name ),
				'alternates' => $alts,
				'priority'   => '0.6',
				'changefreq' => 'monthly',
				'lastmod'    => mysql2date( 'Y-m-d', $p->post_modified_gmt ),
			);
			if ( $alts ) {
				$urls[] = array(
					'loc'        => self::rota( $base, 'en', 'artigo', $p->post_name ),
					'alternates' => $alts,
					'priority'   => '0.6',
					'changefreq' => 'monthly',
					'lastmod'    => mysql2date( 'Y-m-d', $p->post_modified_gmt ),
				);
			}
		}

		return new WP_REST_Response(
			array(
				'base'      => $base,
				'total'     => count( $urls ),
				'generated' => gmdate( 'c' ),
				'xml'       => self::sitemap_xml( $urls ),
			),
			200
		);
	}

	private static function sitemap_xml( $urls ) {
		$linhas   = array();
		$linhas[] = '<?xml version="1.0" encoding="UTF-8"?>';
		$linhas[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">';
		foreach ( $urls as $u ) {
			$linhas[] = '  <url>';
			$linhas[] = '    <loc>' . self::xml( $u['loc'] ) . '</loc>';
			if ( ! empty( $u['lastmod'] ) ) {
				$linhas[] = '    <lastmod>' . $u['lastmod'] . '</lastmod>';
			}
			if ( ! empty( $u['changefreq'] ) ) {
				$linhas[] = '    <changefreq>' . $u['changefreq'] . '</changefreq>';
			}
			if ( ! empty( $u['priority'] ) ) {
				$linhas[] = '    <priority>' . $u['priority'] . '</priority>';
			}
			foreach ( (array) $u['alternates'] as $hreflang => $href ) {
				$linhas[] = '    <xhtml:link rel="alternate" hreflang="' . $hreflang . '" href="' . self::xml( $href ) . '"/>';
			}
			$linhas[] = '  </url>';
		}
		$linhas[] = '</urlset>';
		return implode( "\n", $linhas ) . "\n";
	}

	private static function xml( $texto ) {
		return htmlspecialchars( (string) $texto, ENT_QUOTES | ENT_XML1, 'UTF-8' );
	}
}
