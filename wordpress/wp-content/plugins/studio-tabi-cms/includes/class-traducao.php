<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gera as versões em inglês dos serviços, FAQs, projetos e artigos.
 *
 * Cada item em português ganha um gêmeo marcado com stcms_lang=en e ligado à
 * origem por stcms_traducao_de — é esse vínculo que permite ao sitemap declarar
 * hreflang recíproco entre as duas URLs. Quando o texto em português é um dos
 * que o plugin instala por padrão, usa a tradução pronta; quando é conteúdo do
 * autor, duplica como está para ele traduzir, preservando imagem, ordem,
 * categorias e todas as configurações.
 */
class STCMS_Traducao {

	const META_ORIGEM = 'stcms_traducao_de';

	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'maybe_criar' ) );
	}

	public static function tipos() {
		return array(
			'st_service' => 'Serviços',
			'st_faq'     => 'Perguntas frequentes',
			'st_project' => 'Projetos',
			'post'       => 'Artigos do blog',
		);
	}

	/**
	 * Conta, por tipo, quantos itens em português ainda não têm versão em inglês.
	 */
	public static function pendentes() {
		$out = array();
		foreach ( array_keys( self::tipos() ) as $tipo ) {
			$out[ $tipo ] = count( self::sem_traducao( $tipo ) );
		}
		return $out;
	}

	private static function originais( $tipo ) {
		return get_posts(
			array(
				'post_type'   => $tipo,
				'post_status' => 'publish',
				'numberposts' => -1,
				'orderby'     => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
				'order'       => 'ASC',
				'meta_query'  => array(
					'relation' => 'OR',
					array( 'key' => 'stcms_lang', 'value' => 'en', 'compare' => '!=' ),
					array( 'key' => 'stcms_lang', 'compare' => 'NOT EXISTS' ),
				),
			)
		);
	}

	private static function traducao_de( $id ) {
		$achados = get_posts(
			array(
				'post_type'   => 'any',
				'post_status' => 'any',
				'numberposts' => 1,
				'fields'      => 'ids',
				'meta_query'  => array(
					array( 'key' => self::META_ORIGEM, 'value' => (string) $id ),
				),
			)
		);
		return $achados ? (int) $achados[0] : 0;
	}

	private static function sem_traducao( $tipo ) {
		$out = array();
		foreach ( self::originais( $tipo ) as $p ) {
			if ( ! self::traducao_de( $p->ID ) ) {
				$out[] = $p;
			}
		}
		return $out;
	}

	public static function maybe_criar() {
		if ( empty( $_GET['stcms_traduzir'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		check_admin_referer( 'stcms_traduzir' );

		$total = 0;
		foreach ( array_keys( self::tipos() ) as $tipo ) {
			$total += self::traduzir_tipo( $tipo );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=studio-tabi&stcms_lang=en&stcms_traduzidos=' . $total ) );
		exit;
	}

	public static function traduzir_tipo( $tipo ) {
		$dicionario = function_exists( 'stcms_traducoes_en' ) ? stcms_traducoes_en() : array();
		$chave      = array(
			'st_service' => 'servicos',
			'st_faq'     => 'faq',
			'st_project' => 'projetos',
			'post'       => 'posts',
		);
		$tabela = isset( $chave[ $tipo ], $dicionario[ $chave[ $tipo ] ] ) ? $dicionario[ $chave[ $tipo ] ] : array();

		$criados = 0;
		foreach ( self::sem_traducao( $tipo ) as $origem ) {
			if ( self::criar_gemeo( $origem, $tipo, $tabela ) ) {
				$criados++;
			}
		}
		return $criados;
	}

	private static function criar_gemeo( $origem, $tipo, $tabela ) {
		$titulo_pt = $origem->post_title;
		$pronto    = isset( $tabela[ $titulo_pt ] ) ? $tabela[ $titulo_pt ] : array();

		$novo = wp_insert_post(
			array(
				'post_type'    => $tipo,
				'post_status'  => 'publish',
				'post_title'   => isset( $pronto['title'] ) ? $pronto['title'] : $titulo_pt,
				'post_content' => isset( $pronto['content'] ) ? $pronto['content'] : $origem->post_content,
				'post_excerpt' => isset( $pronto['excerpt'] ) ? $pronto['excerpt'] : $origem->post_excerpt,
				'post_name'    => $origem->post_name . '-en',
				'menu_order'   => $origem->menu_order,
				'post_date'    => $origem->post_date,
			)
		);
		if ( ! $novo || is_wp_error( $novo ) ) {
			return false;
		}

		update_post_meta( $novo, 'stcms_lang', 'en' );
		update_post_meta( $novo, self::META_ORIGEM, (string) $origem->ID );

		self::copiar_metas( $origem->ID, $novo, $pronto );

		$capa = get_post_thumbnail_id( $origem->ID );
		if ( $capa ) {
			set_post_thumbnail( $novo, $capa );
		}

		if ( 'post' === $tipo ) {
			$cats = wp_get_post_categories( $origem->ID );
			if ( $cats ) {
				wp_set_post_categories( $novo, $cats );
			}
			$tags = wp_get_post_tags( $origem->ID, array( 'fields' => 'ids' ) );
			if ( $tags ) {
				wp_set_post_tags( $novo, $tags );
			}
		}

		return true;
	}

	/**
	 * Copia as metas do original e aplica por cima o que houver de tradução pronta.
	 * As metas de lista (escopo, mockup, resultados) voltam ao formato de linhas
	 * que o painel usa.
	 */
	private static function copiar_metas( $de, $para, $pronto ) {
		$ignorar = array( 'stcms_lang', self::META_ORIGEM, '_thumbnail_id', '_edit_lock', '_edit_last' );

		foreach ( get_post_meta( $de ) as $chave => $valores ) {
			if ( in_array( $chave, $ignorar, true ) || ! isset( $valores[0] ) ) {
				continue;
			}
			update_post_meta( $para, $chave, maybe_unserialize( $valores[0] ) );
		}

		$linhas = array( 'stcms_scope', 'stcms_mockup' );
		foreach ( $pronto as $chave => $valor ) {
			if ( in_array( $chave, array( 'title', 'content', 'excerpt' ), true ) ) {
				continue;
			}
			if ( in_array( $chave, $linhas, true ) && is_array( $valor ) ) {
				$valor = array_map(
					function ( $v ) {
						return array( 'item' => $v );
					},
					$valor
				);
			}
			update_post_meta( $para, $chave, $valor );
		}

		// O texto longo da página interna do serviço acompanha a tradução.
		if ( isset( $pronto['content'] ) && '' !== (string) get_post_meta( $de, 'stcms_page_content', true ) ) {
			update_post_meta( $para, 'stcms_page_content', $pronto['content'] );
		}
	}

	/**
	 * Devolve o par [id_pt, id_en] de cada item traduzido de um tipo,
	 * para o sitemap declarar hreflang recíproco.
	 */
	public static function pares( $tipo ) {
		$out = array();
		foreach ( self::originais( $tipo ) as $p ) {
			$en = self::traducao_de( $p->ID );
			if ( $en ) {
				$out[ $p->ID ] = $en;
			}
		}
		return $out;
	}
}
