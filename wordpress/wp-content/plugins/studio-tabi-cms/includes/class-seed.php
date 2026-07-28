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
		self::seed_posts();

		update_option( 'stcms_seeded', 1 );
		update_option( 'stcms_cors_origin', get_option( 'stcms_cors_origin', '*' ) );
		flush_rewrite_rules();
	}

	/**
	 * Semeia alguns artigos de exemplo no blog (posts nativos) + categorias,
	 * para que a listagem já venha preenchida numa instalação nova.
	 */
	private static function seed_posts() {
		// Só semeia se não houver nenhum post publicado além do "Hello World".
		$existing = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 5, 'fields' => 'ids' ) );
		$only_hello = ( 1 === count( $existing ) && 'hello-world' === get_post_field( 'post_name', $existing[0] ) );
		if ( count( $existing ) > 1 || ( 1 === count( $existing ) && ! $only_hello ) ) {
			return;
		}
		if ( $only_hello ) {
			wp_trash_post( $existing[0] );
		}

		$cat_estrategia = self::ensure_category( 'Estratégia' );
		$cat_design     = self::ensure_category( 'Design' );
		$cat_tech       = self::ensure_category( 'Tecnologia' );

		$posts = array(
			array(
				'title'   => 'Presença digital não é custo — é ativo estratégico',
				'excerpt' => 'Por que tratar site e marca como investimento muda o resultado do seu negócio.',
				'cat'     => $cat_estrategia,
				'body'    => "<p>Muita empresa ainda encara o site como uma despesa de marketing. É um erro caro. Presença digital bem construída é um <strong>ativo</strong>: valoriza a marca, reduz o custo de aquisição e sustenta a confiança que fecha negócios.</p><h2>O que muda quando você trata como ativo</h2><p>Você para de perguntar \"quanto custa\" e passa a perguntar \"quanto retorna\". A decisão deixa de ser estética e vira estratégica.</p><h3>Percepção de valor</h3><p>Uma marca coerente comunica solidez antes da primeira conversa. Isso encurta o ciclo de venda.</p><h2>Como começar</h2><p>Comece pelo diagnóstico: onde a marca está, onde precisa chegar e o que trava o caminho. O resto flui a partir daí.</p>",
			),
			array(
				'title'   => 'Design que converte: interface é arquitetura de decisões',
				'excerpt' => 'Cada tela move (ou trava) o usuário. Veja como o design orienta a ação.',
				'cat'     => $cat_design,
				'body'    => "<p>Interface bonita que não converte é decoração cara. Bom design é invisível: conduz o usuário até a ação sem que ele perceba o esforço.</p><h2>Hierarquia visual</h2><p>O olho segue contraste, tamanho e espaço. Use isso para destacar o que importa e silenciar o que distrai.</p><h2>Reduzir atrito</h2><p>Cada campo, cada clique e cada decisão a menos aumenta a conversão. Menos é, quase sempre, mais.</p>",
			),
			array(
				'title'   => 'Performance web: por que velocidade é receita',
				'excerpt' => 'LCP, CLS e INP não são siglas — são dinheiro. Entenda o impacto real.',
				'cat'     => $cat_tech,
				'body'    => "<p>Cada segundo de carregamento a mais derruba conversão e ranking. Performance não é vaidade técnica: é receita e SEO.</p><h2>As métricas que importam</h2><p>LCP (maior elemento visível), CLS (estabilidade visual) e INP (resposta à interação) definem a experiência real do usuário.</p><h2>O básico bem feito</h2><p>Fontes locais, imagens otimizadas, código dividido e cache. O básico, feito com rigor, já coloca você à frente da maioria.</p>",
			),
		);

		$order = 0;
		foreach ( $posts as $p ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'post',
					'post_status'  => 'publish',
					'post_title'   => $p['title'],
					'post_content' => $p['body'],
					'post_excerpt' => $p['excerpt'],
					'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( "-{$order} days" ) ),
				)
			);
			if ( $id && ! is_wp_error( $id ) && $p['cat'] ) {
				wp_set_post_categories( $id, array( $p['cat'] ) );
			}
			$order += 3;
		}
	}

	private static function ensure_category( $name ) {
		$term = term_exists( $name, 'category' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'category' );
		}
		return is_array( $term ) ? (int) $term['term_id'] : 0;
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
					'post_content' => ! empty( $s['content'] ) ? $s['content'] : $s['body'],
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
			update_post_meta( $id, 'stcms_home', ( ! isset( $p['home'] ) || $p['home'] ) ? '1' : '' );
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
