<?php
/**
 * Tipos de conteúdo: Serviços e Projetos.
 * Cada um aparece como um menu próprio no painel — o usuário cria itens como
 * cria um post, com imagem destacada e campos próprios.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {

	// ── Serviço ──
	register_post_type( 'servico', array(
		'labels'       => array(
			'name'               => __( 'Serviços', 'studio-tabi' ),
			'singular_name'      => __( 'Serviço', 'studio-tabi' ),
			'add_new'            => __( 'Adicionar serviço', 'studio-tabi' ),
			'add_new_item'       => __( 'Adicionar novo serviço', 'studio-tabi' ),
			'edit_item'          => __( 'Editar serviço', 'studio-tabi' ),
			'new_item'           => __( 'Novo serviço', 'studio-tabi' ),
			'view_item'          => __( 'Ver serviço', 'studio-tabi' ),
			'search_items'       => __( 'Buscar serviços', 'studio-tabi' ),
			'not_found'          => __( 'Nenhum serviço encontrado', 'studio-tabi' ),
			'menu_name'          => __( 'Serviços', 'studio-tabi' ),
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-screenoptions',
		'menu_position'=> 22,
		'supports'     => array( 'title', 'editor', 'page-attributes' ),
		'has_archive'  => false,
	) );

	// ── Projeto ──
	register_post_type( 'projeto', array(
		'labels'       => array(
			'name'               => __( 'Projetos', 'studio-tabi' ),
			'singular_name'      => __( 'Projeto', 'studio-tabi' ),
			'add_new'            => __( 'Adicionar projeto', 'studio-tabi' ),
			'add_new_item'       => __( 'Adicionar novo projeto', 'studio-tabi' ),
			'edit_item'          => __( 'Editar projeto', 'studio-tabi' ),
			'new_item'           => __( 'Novo projeto', 'studio-tabi' ),
			'view_item'          => __( 'Ver projeto', 'studio-tabi' ),
			'search_items'       => __( 'Buscar projetos', 'studio-tabi' ),
			'not_found'          => __( 'Nenhum projeto encontrado', 'studio-tabi' ),
			'menu_name'          => __( 'Projetos', 'studio-tabi' ),
		),
		'public'       => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-portfolio',
		'menu_position'=> 21,
		'rewrite'      => array( 'slug' => 'projeto' ),
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		'has_archive'  => true,
	) );
} );

/**
 * Campos extras do Projeto (caixa de meta simples).
 */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'tabi_projeto', __( 'Detalhes do projeto', 'studio-tabi' ), 'tabi_projeto_metabox', 'projeto', 'normal', 'high' );
} );

function tabi_projeto_fields() {
	return array(
		'categoria'  => __( 'Categoria (ex.: Branding & UI)', 'studio-tabi' ),
		'ano'        => __( 'Ano', 'studio-tabi' ),
		'cliente'    => __( 'Cliente', 'studio-tabi' ),
		'duracao'    => __( 'Duração (ex.: 14 semanas)', 'studio-tabi' ),
		'destaque'   => __( 'Destacar na home? (sim/não)', 'studio-tabi' ),
		'cor'        => __( 'Cor de destaque (ex.: #F20C25)', 'studio-tabi' ),
	);
}

function tabi_projeto_metabox( $post ) {
	wp_nonce_field( 'tabi_projeto_save', 'tabi_projeto_nonce' );
	echo '<style>.tabi-mb label{display:block;font-weight:600;margin:14px 0 4px}.tabi-mb input,.tabi-mb textarea{width:100%}.tabi-mb .desc{color:#777;font-weight:400;font-size:12px}</style>';
	echo '<div class="tabi-mb">';
	foreach ( tabi_projeto_fields() as $key => $label ) {
		$val = get_post_meta( $post->ID, "tabi_$key", true );
		echo '<label>' . esc_html( $label ) . '</label>';
		if ( 'cor' === $key ) {
			echo '<input type="text" name="tabi_' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" placeholder="#F20C25" />';
		} else {
			echo '<input type="text" name="tabi_' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
		}
	}

	$escopo = get_post_meta( $post->ID, 'tabi_escopo', true );
	echo '<label>' . esc_html__( 'Escopo (um item por linha)', 'studio-tabi' ) . '</label>';
	echo '<textarea name="tabi_escopo" rows="4">' . esc_textarea( $escopo ) . '</textarea>';

	$resultados = get_post_meta( $post->ID, 'tabi_resultados', true );
	echo '<label>' . esc_html__( 'Resultados (um por linha, no formato: Rótulo | Valor)', 'studio-tabi' ) . '</label>';
	echo '<textarea name="tabi_resultados" rows="4" placeholder="Aumento em conversão | +38%">' . esc_textarea( $resultados ) . '</textarea>';

	echo '<p class="desc">' . esc_html__( 'A imagem do card é a “Imagem destacada”. A galeria e o texto do case são escritos no editor acima (você pode inserir imagens e blocos livremente).', 'studio-tabi' ) . '</p>';
	echo '</div>';
}

add_action( 'save_post_projeto', function ( $post_id ) {
	if ( ! isset( $_POST['tabi_projeto_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['tabi_projeto_nonce'] ), 'tabi_projeto_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

	foreach ( array_keys( tabi_projeto_fields() ) as $key ) {
		if ( isset( $_POST[ "tabi_$key" ] ) ) {
			update_post_meta( $post_id, "tabi_$key", sanitize_text_field( wp_unslash( $_POST[ "tabi_$key" ] ) ) );
		}
	}
	foreach ( array( 'tabi_escopo', 'tabi_resultados' ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
} );

/** É projeto em destaque? */
function tabi_projeto_is_featured( $post_id ) {
	$v = strtolower( (string) get_post_meta( $post_id, 'tabi_destaque', true ) );
	return in_array( $v, array( 'sim', 's', 'yes', '1', 'true' ), true );
}
