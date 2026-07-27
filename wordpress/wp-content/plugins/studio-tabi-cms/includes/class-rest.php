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

		$o         = STCMS_Options::get();
		$recipient = ( ! empty( $o['footer']['email'] ) && is_email( $o['footer']['email'] ) )
			? $o['footer']['email']
			: get_option( 'admin_email' );

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
				'num'   => (string) get_post_meta( $p->ID, 'stcms_num', true ),
				'title' => self::title( $p ),
				'body'  => self::plain( $p->post_content ),
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
		$out = array();
		foreach ( $pages as $p ) {
			// Skip the WP front page / privacy stub if present.
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
