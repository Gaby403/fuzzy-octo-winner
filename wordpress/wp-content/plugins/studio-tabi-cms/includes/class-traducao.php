<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Versão em inglês de cada conteúdo, guardada no próprio post.
 *
 * Não existe post gêmeo: o serviço, a pergunta, o projeto, o artigo e a página
 * continuam sendo um só registro no WordPress. A caixa "Versão em inglês"
 * aparece dentro do próprio item e guarda os textos em metas stcms_en_*.
 * Quando a API é chamada com lang=en, cada campo devolve a versão em inglês se
 * estiver preenchida e o texto em português quando não estiver.
 */
class STCMS_Traducao {

	const PREFIXO = 'stcms_en_';

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_box' ) );
		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'admin_init', array( __CLASS__, 'maybe_preencher' ) );
		add_action( 'admin_init', array( __CLASS__, 'maybe_limpar_duplicatas' ) );
	}

	/**
	 * Campos traduzíveis por tipo de conteúdo.
	 * origem: de onde vem o texto em português (post_title, post_content,
	 * post_excerpt ou o nome de uma meta).
	 */
	public static function campos( $tipo ) {
		$mapa = array(
			'st_service' => array(
				'title'        => array( 'rotulo' => 'Nome do serviço', 'origem' => 'post_title', 'formato' => 'texto' ),
				'body'         => array( 'rotulo' => 'Descrição curta', 'origem' => 'post_content', 'formato' => 'area' ),
				'page_content' => array( 'rotulo' => 'Texto da página interna', 'origem' => 'stcms_page_content', 'formato' => 'rico' ),
			),
			'st_faq' => array(
				'title' => array( 'rotulo' => 'Pergunta', 'origem' => 'post_title', 'formato' => 'texto' ),
				'body'  => array( 'rotulo' => 'Resposta', 'origem' => 'post_content', 'formato' => 'area' ),
			),
			'st_project' => array(
				'title'     => array( 'rotulo' => 'Nome do projeto', 'origem' => 'post_title', 'formato' => 'texto' ),
				'category'  => array( 'rotulo' => 'Categoria', 'origem' => 'stcms_category', 'formato' => 'texto' ),
				'duration'  => array( 'rotulo' => 'Duração', 'origem' => 'stcms_duration', 'formato' => 'texto' ),
				'challenge' => array( 'rotulo' => 'O desafio', 'origem' => 'stcms_challenge', 'formato' => 'area' ),
				'solution'  => array( 'rotulo' => 'A solução', 'origem' => 'stcms_solution', 'formato' => 'area' ),
				'scope'     => array( 'rotulo' => 'Escopo (um por linha)', 'origem' => 'stcms_scope', 'formato' => 'linhas' ),
				'mockup'    => array( 'rotulo' => 'Linhas do mockup (uma por linha)', 'origem' => 'stcms_mockup', 'formato' => 'linhas' ),
				'results'   => array( 'rotulo' => 'Rótulos dos resultados (um por linha, na mesma ordem)', 'origem' => 'stcms_results', 'formato' => 'linhas' ),
			),
			'post' => array(
				'title'   => array( 'rotulo' => 'Título', 'origem' => 'post_title', 'formato' => 'texto' ),
				'excerpt' => array( 'rotulo' => 'Resumo', 'origem' => 'post_excerpt', 'formato' => 'area' ),
				'body'    => array( 'rotulo' => 'Texto do artigo', 'origem' => 'post_content', 'formato' => 'rico' ),
			),
			'page' => array(
				'title' => array( 'rotulo' => 'Título', 'origem' => 'post_title', 'formato' => 'texto' ),
				'body'  => array( 'rotulo' => 'Conteúdo', 'origem' => 'post_content', 'formato' => 'rico' ),
			),
		);
		return isset( $mapa[ $tipo ] ) ? $mapa[ $tipo ] : array();
	}

	public static function tipos() {
		return array( 'st_service', 'st_faq', 'st_project', 'post', 'page' );
	}

	/**
	 * Valor de um campo no idioma pedido, caindo no português quando a tradução
	 * está vazia. É o único ponto por onde a API lê texto traduzível.
	 */
	public static function texto( $post, $campo, $lang = 'pt', $padrao_pt = null ) {
		$id  = is_object( $post ) ? $post->ID : (int) $post;
		$obj = is_object( $post ) ? $post : get_post( $id );
		if ( null === $padrao_pt ) {
			$campos = self::campos( $obj ? $obj->post_type : '' );
			$origem = isset( $campos[ $campo ]['origem'] ) ? $campos[ $campo ]['origem'] : '';
			if ( 'post_title' === $origem ) {
				$padrao_pt = $obj ? $obj->post_title : '';
			} elseif ( 'post_content' === $origem ) {
				$padrao_pt = $obj ? $obj->post_content : '';
			} elseif ( 'post_excerpt' === $origem ) {
				$padrao_pt = $obj ? $obj->post_excerpt : '';
			} elseif ( $origem ) {
				$padrao_pt = get_post_meta( $id, $origem, true );
			} else {
				$padrao_pt = '';
			}
		}
		if ( 'en' !== $lang ) {
			return $padrao_pt;
		}
		$en = get_post_meta( $id, self::PREFIXO . $campo, true );
		return ( '' === $en || null === $en ) ? $padrao_pt : $en;
	}

	/**
	 * Lista traduzida item a item, preservando o comprimento da lista em
	 * português — traduzir a lista não pode mudar quantos itens ela tem.
	 */
	public static function lista( $post, $campo, $lang, $pt ) {
		if ( 'en' !== $lang ) {
			return $pt;
		}
		$bruto = (string) get_post_meta( is_object( $post ) ? $post->ID : (int) $post, self::PREFIXO . $campo, true );
		if ( '' === trim( $bruto ) ) {
			return $pt;
		}
		$linhas = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $bruto ) ), 'strlen' ) );
		$out    = array();
		foreach ( array_values( (array) $pt ) as $i => $valor ) {
			$out[] = isset( $linhas[ $i ] ) ? $linhas[ $i ] : $valor;
		}
		return $out;
	}

	public static function add_box() {
		foreach ( self::tipos() as $tipo ) {
			add_meta_box(
				'stcms_en',
				'Versão em inglês (/en)',
				array( __CLASS__, 'render' ),
				$tipo,
				'normal',
				'default'
			);
		}
	}

	public static function render( $post ) {
		$campos = self::campos( $post->post_type );
		if ( ! $campos ) {
			return;
		}
		wp_nonce_field( 'stcms_en', 'stcms_en_nonce' );

		echo '<p class="description" style="margin:0 0 14px">'
			. 'Preencha só o que quiser traduzir. Campo em branco mostra o texto em português no <code>/en</code>. '
			. 'Isto fica dentro deste mesmo item — nenhuma página ou conteúdo é duplicado.'
			. '</p>';

		foreach ( $campos as $chave => $cfg ) {
			$valor = get_post_meta( $post->ID, self::PREFIXO . $chave, true );
			$id    = 'stcms_en_' . $chave;

			printf( '<p style="margin-bottom:4px"><label for="%s" style="font-weight:600">%s</label></p>', esc_attr( $id ), esc_html( $cfg['rotulo'] ) );

			if ( 'rico' === $cfg['formato'] ) {
				wp_editor(
					$valor,
					$id,
					array(
						'textarea_name' => $id,
						'textarea_rows' => 10,
						'media_buttons' => true,
						'teeny'         => false,
					)
				);
				echo '<div style="margin-bottom:18px"></div>';
			} elseif ( 'area' === $cfg['formato'] || 'linhas' === $cfg['formato'] ) {
				printf(
					'<textarea id="%s" name="%s" rows="%d" style="width:100%%;margin-bottom:16px">%s</textarea>',
					esc_attr( $id ),
					esc_attr( $id ),
					'linhas' === $cfg['formato'] ? 4 : 5,
					esc_textarea( $valor )
				);
			} else {
				printf(
					'<input type="text" id="%s" name="%s" value="%s" style="width:100%%;margin-bottom:16px" />',
					esc_attr( $id ),
					esc_attr( $id ),
					esc_attr( $valor )
				);
			}
		}
	}

	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST['stcms_en_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['stcms_en_nonce'] ) ), 'stcms_en' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( self::campos( $post->post_type ) as $chave => $cfg ) {
			$campo = 'stcms_en_' . $chave;
			if ( ! isset( $_POST[ $campo ] ) ) {
				continue;
			}
			$bruto = wp_unslash( $_POST[ $campo ] );
			if ( 'rico' === $cfg['formato'] ) {
				$limpo = wp_kses_post( $bruto );
			} elseif ( 'texto' === $cfg['formato'] ) {
				$limpo = sanitize_text_field( $bruto );
			} else {
				$limpo = sanitize_textarea_field( $bruto );
			}
			if ( '' === trim( (string) $limpo ) ) {
				delete_post_meta( $post_id, self::PREFIXO . $chave );
			} else {
				update_post_meta( $post_id, self::PREFIXO . $chave, $limpo );
			}
		}
	}

	/**
	 * Quantos itens de cada tipo ainda não têm nenhum campo traduzido.
	 */
	public static function pendentes() {
		$out = array();
		foreach ( self::tipos() as $tipo ) {
			$faltam = 0;
			foreach ( self::itens( $tipo ) as $p ) {
				if ( ! self::tem_traducao( $p ) ) {
					$faltam++;
				}
			}
			$out[ $tipo ] = $faltam;
		}
		return $out;
	}

	public static function tem_traducao( $post ) {
		foreach ( self::campos( $post->post_type ) as $chave => $cfg ) {
			if ( '' !== (string) get_post_meta( $post->ID, self::PREFIXO . $chave, true ) ) {
				return true;
			}
		}
		return false;
	}

	private static function itens( $tipo ) {
		return get_posts(
			array(
				'post_type'   => $tipo,
				'post_status' => 'publish',
				'numberposts' => -1,
				'orderby'     => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
				'order'       => 'ASC',
			)
		);
	}

	/**
	 * Preenche os campos em inglês do conteúdo que o plugin instala por padrão,
	 * casando pelo texto em português. Não cria nem apaga nenhum post e nunca
	 * sobrescreve um campo que já tenha tradução.
	 */
	public static function maybe_preencher() {
		if ( empty( $_GET['stcms_preencher_en'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		check_admin_referer( 'stcms_preencher_en' );

		$total = 0;
		foreach ( self::tipos() as $tipo ) {
			$total += self::preencher_tipo( $tipo );
		}
		wp_safe_redirect( admin_url( 'admin.php?page=studio-tabi&stcms_lang=en&stcms_preenchidos=' . $total ) );
		exit;
	}

	public static function preencher_tipo( $tipo ) {
		$dicionario = function_exists( 'stcms_traducoes_en' ) ? stcms_traducoes_en() : array();
		$chaves     = array(
			'st_service' => 'servicos',
			'st_faq'     => 'faq',
			'st_project' => 'projetos',
			'post'       => 'posts',
			'page'       => 'paginas',
		);
		$tabela = isset( $chaves[ $tipo ], $dicionario[ $chaves[ $tipo ] ] ) ? $dicionario[ $chaves[ $tipo ] ] : array();
		if ( ! $tabela ) {
			return 0;
		}

		$mudados = 0;
		foreach ( self::itens( $tipo ) as $p ) {
			if ( ! isset( $tabela[ $p->post_title ] ) ) {
				continue;
			}
			$pronto = $tabela[ $p->post_title ];
			$tocou  = false;
			foreach ( self::campos( $tipo ) as $chave => $cfg ) {
				if ( ! isset( $pronto[ $chave ] ) ) {
					continue;
				}
				if ( '' !== (string) get_post_meta( $p->ID, self::PREFIXO . $chave, true ) ) {
					continue;
				}
				$valor = $pronto[ $chave ];
				if ( is_array( $valor ) ) {
					$valor = implode( "\n", $valor );
				}
				update_post_meta( $p->ID, self::PREFIXO . $chave, $valor );
				$tocou = true;
			}
			if ( $tocou ) {
				$mudados++;
			}
		}
		return $mudados;
	}

	/**
	 * Desfaz os posts gêmeos criados pela versão anterior do plugin: aproveita o
	 * texto deles preenchendo os campos em inglês do original e manda a cópia
	 * para a lixeira (não apaga em definitivo).
	 */
	public static function duplicatas() {
		return get_posts(
			array(
				'post_type'   => self::tipos(),
				'post_status' => 'any',
				'numberposts' => -1,
				'meta_query'  => array(
					array( 'key' => 'stcms_traducao_de', 'compare' => 'EXISTS' ),
				),
			)
		);
	}

	public static function maybe_limpar_duplicatas() {
		if ( empty( $_GET['stcms_limpar_duplicatas'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		check_admin_referer( 'stcms_limpar_duplicatas' );

		$removidas = 0;
		foreach ( self::duplicatas() as $copia ) {
			$origem_id = (int) get_post_meta( $copia->ID, 'stcms_traducao_de', true );
			$origem    = $origem_id ? get_post( $origem_id ) : null;

			if ( $origem && $origem->post_type === $copia->post_type ) {
				self::absorver( $origem, $copia );
			}
			wp_trash_post( $copia->ID );
			$removidas++;
		}

		// A meta de idioma pertencia ao modelo antigo e não é mais lida.
		foreach ( self::tipos() as $tipo ) {
			foreach ( self::itens( $tipo ) as $p ) {
				delete_post_meta( $p->ID, 'stcms_lang' );
			}
		}

		wp_safe_redirect( admin_url( 'admin.php?page=studio-tabi&stcms_lang=en&stcms_limpas=' . $removidas ) );
		exit;
	}

	/**
	 * Copia o texto da duplicata para os campos em inglês do original, sem
	 * sobrescrever o que já estiver preenchido lá.
	 */
	private static function absorver( $origem, $copia ) {
		foreach ( self::campos( $origem->post_type ) as $chave => $cfg ) {
			if ( '' !== (string) get_post_meta( $origem->ID, self::PREFIXO . $chave, true ) ) {
				continue;
			}

			$valor = '';
			switch ( $cfg['origem'] ) {
				case 'post_title':
					$valor = $copia->post_title;
					break;
				case 'post_content':
					$valor = $copia->post_content;
					break;
				case 'post_excerpt':
					$valor = $copia->post_excerpt;
					break;
				default:
					$valor = get_post_meta( $copia->ID, $cfg['origem'], true );
			}

			if ( 'linhas' === $cfg['formato'] && is_array( $valor ) ) {
				$itens = array();
				foreach ( $valor as $linha ) {
					if ( is_array( $linha ) ) {
						$itens[] = isset( $linha['item'] ) ? $linha['item'] : ( isset( $linha['label'] ) ? $linha['label'] : '' );
					} else {
						$itens[] = (string) $linha;
					}
				}
				$valor = implode( "\n", array_filter( $itens, 'strlen' ) );
			}

			if ( ! is_scalar( $valor ) || '' === trim( (string) $valor ) ) {
				continue;
			}

			// Só guarda se for mesmo diferente do português: cópia idêntica não é tradução.
			$pt = 'post_title' === $cfg['origem'] ? $origem->post_title
				: ( 'post_content' === $cfg['origem'] ? $origem->post_content
				: ( 'post_excerpt' === $cfg['origem'] ? $origem->post_excerpt
				: get_post_meta( $origem->ID, $cfg['origem'], true ) ) );

			if ( is_scalar( $pt ) && trim( (string) $pt ) === trim( (string) $valor ) ) {
				continue;
			}

			update_post_meta( $origem->ID, self::PREFIXO . $chave, $valor );
		}
	}
}
