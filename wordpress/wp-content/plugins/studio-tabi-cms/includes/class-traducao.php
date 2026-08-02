<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STCMS_Traducao {

	const PREFIXO = 'stcms_en_';

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_box' ) );
		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'admin_init', array( __CLASS__, 'maybe_preencher' ) );
		add_action( 'admin_init', array( __CLASS__, 'maybe_limpar_duplicatas' ) );
		foreach ( array( 'category', 'post_tag' ) as $tax ) {
			add_action( $tax . '_edit_form_fields', array( __CLASS__, 'campo_termo' ), 10, 1 );
			add_action( $tax . '_add_form_fields', array( __CLASS__, 'campo_termo_novo' ) );
			add_action( 'edited_' . $tax, array( __CLASS__, 'salvar_termo' ) );
			add_action( 'created_' . $tax, array( __CLASS__, 'salvar_termo' ) );
		}
	}

	public static function nome_termo( $termo, $lang = 'pt' ) {
		$id = is_object( $termo ) ? $termo->term_id : (int) $termo;
		$pt = is_object( $termo ) ? $termo->name : '';
		if ( 'en' !== $lang ) {
			return $pt;
		}
		$en = get_term_meta( $id, self::PREFIXO . 'name', true );
		return ( '' === $en || null === $en ) ? $pt : $en;
	}

	public static function campo_termo( $termo ) {
		$valor = get_term_meta( $termo->term_id, self::PREFIXO . 'name', true );
		wp_nonce_field( 'stcms_en_termo', 'stcms_en_termo_nonce' );
		echo '<tr class="form-field"><th scope="row"><label for="stcms_en_name">Nome em inglês</label></th><td>';
		printf(
			'<input type="text" id="stcms_en_name" name="stcms_en_name" value="%s" style="width:95%%" />',
			esc_attr( $valor )
		);
		echo '<p class="description">Aparece no site em <code>/en</code>. Deixe vazio para usar o nome em português.</p>';
		echo '</td></tr>';
	}

	public static function campo_termo_novo() {
		wp_nonce_field( 'stcms_en_termo', 'stcms_en_termo_nonce' );
		echo '<div class="form-field"><label for="stcms_en_name">Nome em inglês</label>';
		echo '<input type="text" id="stcms_en_name" name="stcms_en_name" value="" />';
		echo '<p>Aparece no site em <code>/en</code>. Deixe vazio para usar o nome em português.</p></div>';
	}

	public static function salvar_termo( $term_id ) {
		if ( ! isset( $_POST['stcms_en_termo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['stcms_en_termo_nonce'] ) ), 'stcms_en_termo' ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_categories' ) ) {
			return;
		}
		if ( ! isset( $_POST['stcms_en_name'] ) ) {
			return;
		}
		$valor = sanitize_text_field( wp_unslash( $_POST['stcms_en_name'] ) );
		if ( '' === $valor ) {
			delete_term_meta( $term_id, self::PREFIXO . 'name' );
		} else {
			update_term_meta( $term_id, self::PREFIXO . 'name', $valor );
		}
	}

	public static function id_editor( $chave ) {
		return 'stcms-en-' . str_replace( '_', '-', $chave );
	}

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
				'Idioma do conteúdo — Português / English',
				array( __CLASS__, 'render' ),
				$tipo,
				'normal',
				'high',
				array(
					'__block_editor_compatible_meta_box' => true,
					'__back_compat_meta_box'             => false,
				)
			);
		}
	}

	private static function valor_pt( $post, $chave, $cfg ) {
		switch ( $cfg['origem'] ) {
			case 'post_title':
				return (string) $post->post_title;
			case 'post_content':
				return (string) $post->post_content;
			case 'post_excerpt':
				return (string) $post->post_excerpt;
		}
		$valor = get_post_meta( $post->ID, $cfg['origem'], true );
		if ( 'linhas' === $cfg['formato'] && is_array( $valor ) ) {
			$itens = array();
			foreach ( $valor as $linha ) {
				if ( is_array( $linha ) ) {
					$itens[] = isset( $linha['item'] ) ? $linha['item'] : ( isset( $linha['label'] ) ? $linha['label'] : '' );
				} else {
					$itens[] = (string) $linha;
				}
			}
			return implode( "\n", array_filter( $itens, 'strlen' ) );
		}
		return is_scalar( $valor ) ? (string) $valor : '';
	}

	private static function onde_editar( $cfg ) {
		switch ( $cfg['origem'] ) {
			case 'post_title':
				return 'no campo de título, no topo desta tela';
			case 'post_content':
				return 'no editor principal, acima';
			case 'post_excerpt':
				return 'no box “Resumo”';
		}
		return 'no box “Dados do projeto”, abaixo';
	}

	public static function render( $post ) {
		$campos = self::campos( $post->post_type );
		if ( ! $campos ) {
			return;
		}
		wp_nonce_field( 'stcms_en', 'stcms_en_nonce' );

		echo '<div class="stcms-idiomas">';

		echo '<h2 class="nav-tab-wrapper stcms-abas" style="margin:0 0 16px">'
			. '<a href="#" class="nav-tab nav-tab-active" data-aba="pt">Português</a>'
			. '<a href="#" class="nav-tab" data-aba="en">English <span class="stcms-aba-status"></span></a>'
			. '</h2>';

		echo '<div class="stcms-painel" data-painel="pt">';
		echo '<p class="description" style="margin:0 0 14px">'
			. 'O texto em português deste item, para você comparar enquanto traduz. '
			. 'Para alterá-lo, use o campo original — cada texto tem um lugar só de edição.'
			. '</p>';
		foreach ( $campos as $chave => $cfg ) {
			$pt = self::valor_pt( $post, $chave, $cfg );
			printf(
				'<p style="margin:0 0 4px"><strong>%s</strong> <span style="color:#787c82;font-weight:400">— edite %s</span></p>',
				esc_html( $cfg['rotulo'] ),
				esc_html( self::onde_editar( $cfg ) )
			);
			if ( '' === trim( $pt ) ) {
				echo '<p style="margin:0 0 16px;color:#8a6d00">(vazio)</p>';
			} else {
				printf(
					'<div style="margin:0 0 16px;padding:10px 12px;background:#f6f7f7;border-left:3px solid #dcdcde;white-space:pre-wrap;max-height:180px;overflow:auto">%s</div>',
					esc_html( $pt )
				);
			}
		}
		echo '</div>';

		echo '<div class="stcms-painel" data-painel="en">';
		echo '<p class="description" style="margin:0 0 14px">'
			. 'Preencha só o que quiser traduzir. Campo em branco mostra o texto em português no <code>/en</code>. '
			. 'Tudo fica dentro deste mesmo item — nada é duplicado.'
			. '</p>';

		foreach ( $campos as $chave => $cfg ) {
			$valor = get_post_meta( $post->ID, self::PREFIXO . $chave, true );
			$nome  = 'stcms_en_' . $chave;
			$id    = self::id_editor( $chave );

			printf(
				'<p style="margin-bottom:4px"><label for="%s" style="font-weight:600">%s</label></p>',
				esc_attr( $id ),
				esc_html( $cfg['rotulo'] )
			);

			if ( 'rico' === $cfg['formato'] ) {
				wp_editor(
					$valor,
					$id,
					array(
						'textarea_name' => $nome,
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
					esc_attr( $nome ),
					'linhas' === $cfg['formato'] ? 4 : 5,
					esc_textarea( $valor )
				);
			} else {
				printf(
					'<input type="text" id="%s" name="%s" value="%s" style="width:100%%;margin-bottom:16px" />',
					esc_attr( $id ),
					esc_attr( $nome ),
					esc_attr( $valor )
				);
			}
		}
		echo '</div>';

		echo '</div>';
	}

	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST['stcms_en_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['stcms_en_nonce'] ) ), 'stcms_en' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
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
			if ( 'texto' === $cfg['formato'] ) {
				$limpo = sanitize_text_field( $bruto );
			} elseif ( 'linhas' === $cfg['formato'] ) {
				$limpo = sanitize_textarea_field( $bruto );
			} else {
				$limpo = wp_kses_post( $bruto );
			}
			if ( '' === trim( (string) $limpo ) ) {
				delete_post_meta( $post_id, self::PREFIXO . $chave );
			} else {
				update_post_meta( $post_id, self::PREFIXO . $chave, $limpo );
			}
		}
	}

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

	public static function duplicatas() {
		$achados = array();

		foreach ( get_posts(
			array(
				'post_type'   => self::tipos(),
				'post_status' => 'any',
				'numberposts' => -1,
				'meta_query'  => array(
					array( 'key' => 'stcms_traducao_de', 'compare' => 'EXISTS' ),
				),
			)
		) as $p ) {
			$achados[ $p->ID ] = $p;
		}

		foreach ( get_posts(
			array(
				'post_type'   => self::tipos(),
				'post_status' => 'any',
				'numberposts' => -1,
			)
		) as $p ) {
			if ( isset( $achados[ $p->ID ] ) ) {
				continue;
			}
			if ( self::origem_por_slug( $p ) ) {
				$achados[ $p->ID ] = $p;
			}
		}

		return array_values( $achados );
	}

	private static function origem_por_slug( $copia ) {
		if ( ! preg_match( '/^(.*)-en$/', (string) $copia->post_name, $m ) || '' === $m[1] ) {
			return null;
		}
		$base = get_page_by_path( $m[1], OBJECT, $copia->post_type );
		if ( ! $base || $base->ID === $copia->ID ) {
			return null;
		}
		return $base;
	}

	private static function origem_da_copia( $copia ) {
		$id = (int) get_post_meta( $copia->ID, 'stcms_traducao_de', true );
		if ( $id ) {
			$origem = get_post( $id );
			if ( $origem && $origem->post_type === $copia->post_type ) {
				return $origem;
			}
		}
		return self::origem_por_slug( $copia );
	}

	public static function maybe_limpar_duplicatas() {
		if ( empty( $_GET['stcms_limpar_duplicatas'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		check_admin_referer( 'stcms_limpar_duplicatas' );

		$removidas = 0;
		foreach ( self::duplicatas() as $copia ) {
			$origem = self::origem_da_copia( $copia );
			if ( $origem ) {
				self::absorver( $origem, $copia );
			}
			wp_trash_post( $copia->ID );
			$removidas++;
		}

		foreach ( self::tipos() as $tipo ) {
			foreach ( self::itens( $tipo ) as $p ) {
				delete_post_meta( $p->ID, 'stcms_lang' );
			}
		}

		wp_safe_redirect( admin_url( 'admin.php?page=studio-tabi&stcms_lang=en&stcms_limpas=' . $removidas ) );
		exit;
	}

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
