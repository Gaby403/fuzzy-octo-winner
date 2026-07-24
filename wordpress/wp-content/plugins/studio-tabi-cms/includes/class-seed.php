<?php
/**
 * Seeds the default content on plugin activation so a fresh install already
 * looks like the shipped design. Runs only once (guarded by an option) and
 * never overwrites content the user has created.
 *
 * @package StudioTabiCMS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STCMS_Seed {

	public static function activate() {
		// Make sure the CPTs exist for this request before inserting posts.
		STCMS_CPT::register();

		if ( get_option( 'stcms_seeded' ) ) {
			flush_rewrite_rules();
			return;
		}

		self::seed_options();
		self::seed_services();
		self::seed_faq();
		self::seed_projects();
		self::seed_sample_page();

		update_option( 'stcms_seeded', 1 );
		update_option( 'stcms_cors_origin', get_option( 'stcms_cors_origin', '*' ) );
		flush_rewrite_rules();
	}

	private static function seed_options() {
		if ( ! get_option( STCMS_Options::OPTION ) ) {
			update_option( STCMS_Options::OPTION, stcms_default_options() );
		}
	}

	private static function seed_services() {
		if ( self::count( 'st_service' ) > 0 ) {
			return;
		}
		$order = 0;
		foreach ( stcms_default_services() as $s ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'st_service',
					'post_status'  => 'publish',
					'post_title'   => $s['title'],
					'post_content' => $s['body'],
					'menu_order'   => $order++,
				)
			);
			if ( $id && ! is_wp_error( $id ) ) {
				update_post_meta( $id, 'stcms_num', $s['num'] );
			}
		}
	}

	private static function seed_faq() {
		if ( self::count( 'st_faq' ) > 0 ) {
			return;
		}
		$order = 0;
		foreach ( stcms_default_faq() as $f ) {
			wp_insert_post(
				array(
					'post_type'    => 'st_faq',
					'post_status'  => 'publish',
					'post_title'   => $f['q'],
					'post_content' => $f['a'],
					'menu_order'   => $order++,
				)
			);
		}
	}

	private static function seed_projects() {
		if ( self::count( 'st_project' ) > 0 ) {
			return;
		}
		$order = 0;
		foreach ( stcms_default_projects() as $p ) {
			$id = wp_insert_post(
				array(
					'post_type'   => 'st_project',
					'post_status' => 'publish',
					'post_title'  => $p['name'],
					'menu_order'  => $order++,
				)
			);
			if ( ! $id || is_wp_error( $id ) ) {
				continue;
			}
			update_post_meta( $id, 'stcms_num', $p['id'] );
			update_post_meta( $id, 'stcms_name', $p['name'] );
			update_post_meta( $id, 'stcms_category', $p['category'] );
			update_post_meta( $id, 'stcms_year', $p['year'] );
			update_post_meta( $id, 'stcms_bg', $p['bg'] );
			update_post_meta( $id, 'stcms_accent', $p['accent'] );
			update_post_meta( $id, 'stcms_featured', $p['featured'] ? '1' : '' );
			update_post_meta( $id, 'stcms_client', $p['client'] );
			update_post_meta( $id, 'stcms_duration', $p['duration'] );
			update_post_meta( $id, 'stcms_challenge', $p['challenge'] );
			update_post_meta( $id, 'stcms_solution', $p['solution'] );

			update_post_meta( $id, 'stcms_scope', array_map( function ( $v ) { return array( 'item' => $v ); }, $p['scope'] ) );
			update_post_meta( $id, 'stcms_mockup', array_map( function ( $v ) { return array( 'item' => $v ); }, $p['mockup_lines'] ) );
			update_post_meta( $id, 'stcms_results', $p['results'] );
		}
	}

	private static function seed_sample_page() {
		if ( get_page_by_path( 'contato' ) ) {
			return;
		}
		wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Contato',
				'post_name'    => 'contato',
				'post_content' => "<p>Vamos conversar sobre o seu próximo projeto. Envie um e-mail para <strong>oi@studiotabi.com.br</strong> ou preencha o formulário e retornamos em até um dia útil.</p>\n<p>Esta página foi criada no WordPress e é renderizada automaticamente pelo front-end headless — edite ou crie novas páginas em <em>Páginas</em>.</p>",
			)
		);
	}

	private static function count( $type ) {
		$q = new WP_Query(
			array(
				'post_type'      => $type,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		return $q->found_posts;
	}
}
