<?php
/**
 * Registers the custom post types that power the site content:
 * Serviços, Projetos and FAQ.
 *
 * @package StudioTabiCMS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STCMS_CPT {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
	}

	public static function register() {

		register_post_type(
			'st_service',
			array(
				'labels'       => self::labels( 'Serviço', 'Serviços' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-screenoptions',
				'supports'     => array( 'title', 'editor', 'page-attributes' ),
				'has_archive'  => false,
				'rewrite'      => false,
			)
		);

		register_post_type(
			'st_project',
			array(
				'labels'       => self::labels( 'Projeto', 'Projetos' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-portfolio',
				'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
				'has_archive'  => false,
				'rewrite'      => false,
			)
		);

		register_post_type(
			'st_faq',
			array(
				'labels'       => self::labels( 'Pergunta (FAQ)', 'FAQ' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-editor-help',
				'supports'     => array( 'title', 'editor', 'page-attributes' ),
				'has_archive'  => false,
				'rewrite'      => false,
			)
		);
	}

	/**
	 * Build a full label array from a singular / plural pair (pt-BR).
	 */
	private static function labels( $singular, $plural ) {
		return array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'menu_name'             => $plural,
			'add_new'               => 'Adicionar novo',
			'add_new_item'          => 'Adicionar ' . $singular,
			'edit_item'             => 'Editar ' . $singular,
			'new_item'              => 'Novo ' . $singular,
			'view_item'             => 'Ver ' . $singular,
			'search_items'          => 'Buscar ' . $plural,
			'not_found'             => 'Nenhum item encontrado',
			'not_found_in_trash'    => 'Nenhum item na lixeira',
			'all_items'             => $plural,
		);
	}
}
