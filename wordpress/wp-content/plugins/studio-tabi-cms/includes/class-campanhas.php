<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STCMS_Campanhas {

	const OPCAO      = 'stcms_campanha';
	const OPCAO_AUTO = 'stcms_auto_newsletter';
	const META_ENVIO = '_stcms_newsletter_enviada';
	const TICK        = 'stcms_campanha_tick';
	const OPCAO_TICK  = 'stcms_cron_ultimo';
	const OPCAO_TESTE = 'stcms_teste_email';
	const POR_VEZ    = 15;
	const SEGUNDOS   = 20;

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 20 );
		add_action( 'transition_post_status', array( __CLASS__, 'ao_publicar' ), 10, 3 );
		add_action( self::TICK, array( __CLASS__, 'processar_tick' ) );
		add_action( 'admin_post_stcms_campanha_auto', array( __CLASS__, 'alternar_auto' ) );
		add_action( 'admin_post_stcms_campanha_agora', array( __CLASS__, 'processar_agora' ) );
		add_action( 'wp_ajax_stcms_campanha_lote', array( __CLASS__, 'ajax_lote' ) );
		add_action( 'admin_post_stcms_campanha_salvar', array( __CLASS__, 'salvar' ) );
		add_action( 'admin_post_stcms_campanha_teste', array( __CLASS__, 'teste' ) );
		add_action( 'admin_post_stcms_campanha_iniciar', array( __CLASS__, 'iniciar' ) );
		add_action( 'admin_post_stcms_campanha_parar', array( __CLASS__, 'parar' ) );
		add_action( 'admin_post_stcms_campanha_post', array( __CLASS__, 'enviar_post' ) );
		add_action( 'admin_post_stcms_campanha_teste_post', array( __CLASS__, 'teste_post' ) );
		add_action( 'admin_post_stcms_campanha_previa', array( __CLASS__, 'previa' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'metabox' ) );
		add_action( 'admin_notices', array( __CLASS__, 'aviso_no_post' ) );
	}

	public static function menu() {
		add_submenu_page(
			'studio-tabi',
			'Newsletter',
			'Newsletter',
			'manage_options',
			'studio-tabi-newsletter',
			array( __CLASS__, 'render' )
		);
	}

	public static function campanha() {
		$c = get_option( self::OPCAO, array() );
		if ( ! is_array( $c ) ) {
			$c = array();
		}
		return array_merge(
			array(
				'assunto'   => '',
				'titulo'    => '',
				'texto'     => '',
				'cta_label' => '',
				'cta_url'   => '',
				'idioma'    => 'todos',
				'fila'      => array(),
				'enviados'  => 0,
				'falhas'    => 0,
				'total'     => 0,
				'estado'    => 'rascunho',
				'quando'    => '',
			),
			$c
		);
	}

	private static function guardar( $c ) {
		update_option( self::OPCAO, $c );
	}

	private static function destinatarios( $idioma ) {
		$saida = array();
		foreach ( STCMS_Emails::lista() as $item ) {
			if ( 'todos' !== $idioma && $item['lang'] !== $idioma ) {
				continue;
			}
			$saida[] = $item;
		}
		return $saida;
	}

	public static function corpo( $c, $lang, $email = '', $token = '' ) {
		$c      = self::resolver( $c, $lang );
		$blocos = array( wpautop( $c['texto'] ) );
		if ( '' !== trim( (string) $c['cta_label'] ) && '' !== trim( (string) $c['cta_url'] ) ) {
			$blocos[] = array( 'cta' => $c['cta_label'], 'url' => $c['cta_url'] );
		}
		$rodape = ( '' !== $email && '' !== $token ) ? STCMS_Emails::rodape_descadastro( $email, $token, $lang ) : '';
		return STCMS_Emails::modelo( $lang, $c['titulo'], $blocos, $rodape );
	}

	public static function assunto( $c, $lang ) {
		$c = self::resolver( $c, $lang );
		return $c['assunto'];
	}

	/**
	 * Campanha vinda de um artigo é montada na hora de enviar, no idioma de
	 * cada inscrito, aproveitando a tradução guardada no próprio post.
	 */
	private static function resolver( $c, $lang ) {
		if ( empty( $c['post_id'] ) ) {
			return $c;
		}
		$post = get_post( (int) $c['post_id'] );
		if ( ! $post ) {
			return $c;
		}
		$o = STCMS_Options::get( $lang );
		$e = isset( $o['emails'] ) ? $o['emails'] : array();

		$titulo = (string) STCMS_Traducao::texto( $post, 'title', $lang );
		$resumo = (string) STCMS_Traducao::texto( $post, 'excerpt', $lang );
		if ( '' === trim( $resumo ) ) {
			$resumo = wp_trim_words( wp_strip_all_tags( (string) STCMS_Traducao::texto( $post, 'body', $lang ) ), 45, '…' );
		}

		$valores = array(
			'titulo' => $titulo,
			'resumo' => $resumo,
			'site'   => (string) ( $o['site']['title'] ?? '' ),
		);
		$aplicar = function ( $texto ) use ( $valores ) {
			foreach ( $valores as $k => $v ) {
				$texto = str_replace( '{' . $k . '}', $v, (string) $texto );
			}
			return $texto;
		};

		$c['assunto']   = $aplicar( $e['auto_assunto'] ?? '' );
		$c['titulo']    = $aplicar( $e['auto_titulo'] ?? '' );
		$c['texto']     = $aplicar( $e['auto_texto'] ?? '' );
		$c['cta_label'] = (string) ( $e['auto_cta'] ?? '' );
		$c['cta_url']   = STCMS_Emails::url_site() . ( 'en' === $lang ? '/en/blog/' : '/blog/' ) . $post->post_name;
		return $c;
	}

	public static function auto_ligado() {
		return '1' === (string) get_option( self::OPCAO_AUTO, '' );
	}

	public static function alternar_auto() {
		check_admin_referer( 'stcms_campanha' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Sem permissão.' );
		}
		update_option( self::OPCAO_AUTO, empty( $_POST['auto'] ) ? '' : '1' );
		self::voltar( 'auto' );
	}

	/**
	 * Só dispara quando um artigo sai de qualquer estado para publicado, uma
	 * única vez por artigo. Editar depois não reenvia.
	 */
	public static function ao_publicar( $novo, $antigo, $post ) {
		if ( 'publish' !== $novo || 'publish' === $antigo ) {
			return;
		}
		if ( ! $post || 'post' !== $post->post_type ) {
			return;
		}
		if ( ! self::auto_ligado() ) {
			return;
		}
		self::enfileirar_post( $post->ID );
	}

	/**
	 * Põe um artigo na fila da newsletter. Mesmo caminho para o disparo
	 * automático e para o botão no painel do artigo, então as duas portas têm
	 * as mesmas travas. Devolve o motivo quando não dá para enviar.
	 */
	public static function enfileirar_post( $post_id, $forcar = false ) {
		$post = get_post( (int) $post_id );
		if ( ! $post || 'post' !== $post->post_type ) {
			return 'post_invalido';
		}
		if ( 'publish' !== $post->post_status ) {
			return 'nao_publicado';
		}
		if ( ! $forcar && get_post_meta( $post->ID, self::META_ENVIO, true ) ) {
			return 'ja_enviado';
		}
		$c = self::campanha();
		if ( 'enviando' === $c['estado'] ) {
			return 'ocupado';
		}
		$o = STCMS_Options::get();
		if ( '' === trim( (string) ( $o['emails']['auto_assunto'] ?? '' ) ) ) {
			return 'sem_modelo';
		}
		$destinos = self::destinatarios( 'todos' );
		if ( ! $destinos ) {
			return 'sem_lista';
		}
		update_post_meta( $post->ID, self::META_ENVIO, gmdate( 'Y-m-d H:i:s' ) );

		$c['post_id']  = $post->ID;
		$c['idioma']   = 'todos';
		$c['fila']     = $destinos;
		$c['total']    = count( $destinos );
		$c['enviados'] = 0;
		$c['falhas']   = 0;
		$c['estado']   = 'enviando';
		$c['quando']   = gmdate( 'Y-m-d H:i:s' );
		self::guardar( $c );
		self::agendar();
		return 'iniciado';
	}

	/**
	 * Botão “Enviar para a lista” dentro do artigo. É um link com nonce, e não
	 * um formulário, porque a tela de edição já é um formulário e HTML não
	 * aceita um dentro do outro.
	 */
	public static function enviar_post() {
		$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
		check_admin_referer( 'stcms_campanha_post_' . $post_id );
		if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( 'Sem permissão.' );
		}
		$forcar = ! empty( $_GET['forcar'] );
		$aviso  = self::enfileirar_post( $post_id, $forcar );
		$volta  = get_edit_post_link( $post_id, 'raw' );
		if ( ! $volta ) {
			$volta = admin_url( 'admin.php?page=studio-tabi-newsletter' );
		}
		wp_safe_redirect( add_query_arg( 'stcms_aviso', $aviso, $volta ) );
		exit;
	}

	public static function metabox() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		add_meta_box( 'stcms-newsletter', 'Newsletter', array( __CLASS__, 'render_metabox' ), 'post', 'side', 'default' );
	}

	public static function render_metabox( $post ) {
		$enviado  = (string) get_post_meta( $post->ID, self::META_ENVIO, true );
		$lista    = count( STCMS_Emails::lista() );
		$c        = self::campanha();
		$ocupado  = 'enviando' === $c['estado'];
		$deste    = $ocupado && (int) ( $c['post_id'] ?? 0 ) === (int) $post->ID;
		$publicado = 'publish' === $post->post_status;
		$link     = function ( $forcar ) use ( $post ) {
			$url = admin_url( 'admin-post.php?action=stcms_campanha_post&post=' . (int) $post->ID );
			if ( $forcar ) {
				$url .= '&forcar=1';
			}
			return wp_nonce_url( $url, 'stcms_campanha_post_' . (int) $post->ID );
		};
		$teste = function ( $lang ) use ( $post ) {
			$url = admin_url( 'admin-post.php?action=stcms_campanha_teste_post&lang=' . $lang . '&post=' . (int) $post->ID );
			return wp_nonce_url( $url, 'stcms_campanha_post_' . (int) $post->ID );
		};
		?>
		<div style="font-size:13px;line-height:1.6">
			<?php if ( $deste ) : ?>
				<p style="margin:0 0 10px">
					<strong>Enviando agora</strong> — <?php echo (int) $c['enviados']; ?> de <?php echo (int) $c['total']; ?>.
				</p>
				<p style="margin:0"><a href="<?php echo esc_url( admin_url( 'admin.php?page=studio-tabi-newsletter' ) ); ?>">Acompanhar o envio</a></p>
			<?php elseif ( $ocupado ) : ?>
				<p style="margin:0">
					Há outro envio em andamento. Espere terminar para disparar este artigo.
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=studio-tabi-newsletter' ) ); ?>">Ver</a>
				</p>
			<?php elseif ( ! $publicado ) : ?>
				<p style="margin:0">Publique o artigo para poder avisar a lista.</p>
			<?php elseif ( ! $lista ) : ?>
				<p style="margin:0">Ninguém inscrito na newsletter ainda.</p>
			<?php elseif ( $enviado ) : ?>
				<p style="margin:0 0 10px">
					Já enviado em <strong><?php echo esc_html( get_date_from_gmt( $enviado, 'd/m/Y \à\s H:i' ) ); ?></strong>.
				</p>
				<p style="margin:0">
					<a href="<?php echo esc_url( $link( true ) ); ?>" class="button"
						onclick="return confirm('Enviar de novo para <?php echo (int) $lista; ?> pessoas? Quem já recebeu vai receber outra vez.');">Enviar de novo</a>
				</p>
			<?php else : ?>
				<p style="margin:0 0 10px">
					Avisar <strong><?php echo (int) $lista; ?></strong> inscritos sobre este artigo,
					cada um no idioma em que se inscreveu.
				</p>
				<p style="margin:0">
					<a href="<?php echo esc_url( $link( false ) ); ?>" class="button button-primary"
						onclick="return confirm('Enviar para <?php echo (int) $lista; ?> pessoas? Não dá para voltar atrás.');">Enviar para a lista</a>
				</p>
			<?php endif; ?>

			<?php if ( $publicado ) : ?>
				<hr style="margin:14px 0;border:none;border-top:1px solid #e0e0e0" />
				<p style="margin:0 0 6px;font-weight:600">Conferir antes</p>
				<p style="margin:0 0 6px">
					Ver como fica:
					<a href="<?php echo esc_url( self::url_previa( $post->ID, 'pt' ) ); ?>" target="_blank" rel="noopener">português</a> ·
					<a href="<?php echo esc_url( self::url_previa( $post->ID, 'en' ) ); ?>" target="_blank" rel="noopener">inglês</a>
				</p>
				<?php $meu = self::email_teste(); ?>
				<?php if ( $meu ) : ?>
					<p style="margin:0">
						Enviar teste para <code style="font-size:11px"><?php echo esc_html( $meu ); ?></code>:
						<a href="<?php echo esc_url( $teste( 'pt' ) ); ?>">português</a> ·
						<a href="<?php echo esc_url( $teste( 'en' ) ); ?>">inglês</a>
					</p>
				<?php else : ?>
					<p style="margin:0;color:#666">
						Para enviar um teste, informe um endereço em
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=studio-tabi-newsletter' ) ); ?>">Newsletter</a>.
					</p>
				<?php endif; ?>
			<?php endif; ?>

			<p style="margin:10px 0 0;color:#666;font-size:12px">
				<?php if ( self::auto_ligado() ) : ?>
					O aviso automático está ligado: artigos novos saem sozinhos ao publicar.
				<?php else : ?>
					O aviso automático está desligado: só sai o que você disparar por aqui.
				<?php endif; ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=studio-tabi-newsletter' ) ); ?>">Configurar</a>
			</p>
		</div>
		<?php
	}

	public static function aviso_no_post() {
		$tela = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $tela || 'post' !== $tela->base || 'post' !== $tela->post_type ) {
			return;
		}
		$mapa = array(
			'iniciado'      => array( 'success', 'Envio iniciado. A fila continua em segundo plano.' ),
			'ja_enviado'    => array( 'warning', 'Este artigo já foi enviado antes. Use “Enviar de novo” se for mesmo o caso.' ),
			'nao_publicado' => array( 'error', 'Publique o artigo antes de avisar a lista.' ),
			'ocupado'       => array( 'error', 'Há outro envio em andamento. Espere terminar.' ),
			'sem_lista'     => array( 'error', 'Não há ninguém inscrito na newsletter.' ),
			'sem_modelo'    => array( 'error', 'Preencha o modelo em Conteúdo → E-mails automáticos antes de disparar.' ),
			'post_invalido' => array( 'error', 'Só artigos do blog podem ser enviados para a lista.' ),
			'teste_ok'      => array( 'success', 'E-mail de teste enviado.' ),
			'teste_falhou'  => array( 'error', 'Não foi possível enviar o teste.' ),
			'teste_invalido' => array( 'error', 'Endereço de teste inválido.' ),
			'teste_sem_texto' => array( 'error', 'Preencha o modelo em Conteúdo → E-mails automáticos antes de testar.' ),
			'teste_sem_endereco' => array( 'error', 'Informe um endereço de teste na tela Newsletter primeiro.' ),
		);
		$chave = isset( $_GET['stcms_aviso'] ) ? sanitize_key( wp_unslash( $_GET['stcms_aviso'] ) ) : '';
		if ( ! isset( $mapa[ $chave ] ) ) {
			return;
		}
		printf(
			'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
			esc_attr( $mapa[ $chave ][0] ),
			esc_html( $mapa[ $chave ][1] )
		);
	}

	private static function agendar() {
		if ( ! wp_next_scheduled( self::TICK ) ) {
			wp_schedule_single_event( time() + 60, self::TICK );
		}
	}

	/**
	 * Continua o envio sem depender de alguém com o painel aberto. Se a fila
	 * ainda tiver gente no fim do lote, agenda o próximo.
	 */
	public static function processar_tick() {
		update_option( self::OPCAO_TICK, time() );
		$c = self::campanha();
		if ( 'enviando' !== $c['estado'] ) {
			return;
		}
		// Manda o quanto couber no orçamento em vez de um lote só: um cron de
		// servidor a cada 5 minutos não daria conta se cada visita enviasse 15.
		$orcamento = (int) apply_filters( 'stcms_cron_segundos', self::SEGUNDOS );
		$comeco    = time();
		do {
			$c = self::enviar_lote( $c );
		} while ( 'enviando' === $c['estado'] && ( time() - $comeco ) < $orcamento );

		if ( 'enviando' === $c['estado'] ) {
			wp_schedule_single_event( time() + 60, self::TICK );
		}
	}

	public static function processar_agora() {
		check_admin_referer( 'stcms_campanha' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Sem permissão.' );
		}
		self::processar_tick();
		self::voltar( 'processado' );
	}

	/**
	 * Diz se o agendamento está de fato sendo executado. Num WordPress headless
	 * o cron interno depende de visita ao site, e visita é o que não há — então
	 * uma fila parada precisa ser visível, não silenciosa.
	 */
	public static function saude_cron() {
		$ultimo   = (int) get_option( self::OPCAO_TICK, 0 );
		$proximo  = wp_next_scheduled( self::TICK );
		$campanha = self::campanha();
		$parado   = 'enviando' === $campanha['estado']
			&& $proximo
			&& $proximo < ( time() - 300 )
			&& ( ! $ultimo || $ultimo < ( time() - 300 ) );

		return array(
			'ultimo'   => $ultimo,
			'proximo'  => $proximo ? (int) $proximo : 0,
			'parado'   => $parado,
			'desligado' => defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON,
		);
	}

	public static function salvar() {
		check_admin_referer( 'stcms_campanha' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Sem permissão.' );
		}
		$c = self::campanha();
		if ( 'enviando' === $c['estado'] ) {
			self::voltar( 'ocupado' );
		}
		$c['assunto']   = sanitize_text_field( wp_unslash( $_POST['assunto'] ?? '' ) );
		$c['titulo']    = sanitize_text_field( wp_unslash( $_POST['titulo'] ?? '' ) );
		$c['texto']     = sanitize_textarea_field( wp_unslash( $_POST['texto'] ?? '' ) );
		$c['cta_label'] = sanitize_text_field( wp_unslash( $_POST['cta_label'] ?? '' ) );
		$c['cta_url']   = esc_url_raw( wp_unslash( $_POST['cta_url'] ?? '' ) );
		$idioma         = sanitize_key( wp_unslash( $_POST['idioma'] ?? 'todos' ) );
		$c['idioma']    = in_array( $idioma, array( 'todos', 'pt', 'en' ), true ) ? $idioma : 'todos';
		$c['estado']    = 'rascunho';
		self::guardar( $c );
		self::voltar( 'salvo' );
	}

	/**
	 * Para onde vai o teste quando ninguém digitou nada: o último endereço
	 * usado, senão o e-mail de quem está logado.
	 */
	public static function email_teste() {
		$salvo = trim( (string) get_option( self::OPCAO_TESTE, '' ) );
		if ( '' !== $salvo && is_email( $salvo ) ) {
			return $salvo;
		}
		$u = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : null;
		$e = $u && ! empty( $u->user_email ) ? (string) $u->user_email : '';
		return is_email( $e ) ? $e : '';
	}

	/**
	 * Um artigo vira campanha só no papel: os textos são montados na hora, no
	 * idioma pedido, a partir do próprio post.
	 */
	private static function campanha_do_post( $post_id ) {
		$c            = self::campanha();
		$c['post_id'] = (int) $post_id;
		return $c;
	}

	private static function enviar_teste( $para, $lang, $post_id = 0 ) {
		$c = $post_id ? self::campanha_do_post( $post_id ) : self::campanha();
		if ( ! $post_id && '' === trim( (string) $c['assunto'] ) ) {
			return 'teste_sem_texto';
		}
		if ( $post_id && ! get_post( $post_id ) ) {
			return 'teste_invalido';
		}
		$assunto = self::assunto( $c, $lang );
		if ( '' === trim( (string) $assunto ) ) {
			return 'teste_sem_texto';
		}
		update_option( self::OPCAO_TESTE, $para );

		$GLOBALS['stcms_email_html'] = true;
		$enviado = wp_mail(
			$para,
			'[teste] ' . $assunto,
			self::corpo( $c, $lang, $para, 'token-de-teste-000000' ),
			array( 'Content-Type: text/html; charset=UTF-8' )
		);
		unset( $GLOBALS['stcms_email_html'] );
		return $enviado ? 'teste_ok' : 'teste_falhou';
	}

	private static function lang_pedida( $bruto ) {
		return 'en' === sanitize_key( (string) $bruto ) ? 'en' : 'pt';
	}

	public static function teste() {
		check_admin_referer( 'stcms_campanha' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Sem permissão.' );
		}
		$para = sanitize_email( wp_unslash( $_POST['teste_email'] ?? '' ) );
		if ( '' === $para ) {
			$para = self::email_teste();
		}
		if ( ! is_email( $para ) ) {
			self::voltar( 'teste_invalido' );
		}
		$lang = self::lang_pedida( wp_unslash( $_POST['teste_lang'] ?? '' ) );
		self::voltar( self::enviar_teste( $para, $lang ) );
	}

	/**
	 * Teste disparado de dentro do artigo. Vai para o e-mail de quem está
	 * logado, porque a tela de edição já é um formulário e não cabe um campo
	 * de texto ali dentro.
	 */
	public static function teste_post() {
		$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
		check_admin_referer( 'stcms_campanha_post_' . $post_id );
		if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( 'Sem permissão.' );
		}
		$para = self::email_teste();
		$lang = self::lang_pedida( wp_unslash( $_GET['lang'] ?? '' ) );
		$aviso = is_email( $para ) ? self::enviar_teste( $para, $lang, $post_id ) : 'teste_sem_endereco';

		$volta = get_edit_post_link( $post_id, 'raw' );
		if ( ! $volta ) {
			$volta = admin_url( 'admin.php?page=studio-tabi-newsletter' );
		}
		wp_safe_redirect( add_query_arg( 'stcms_aviso', $aviso, $volta ) );
		exit;
	}

	/**
	 * Mostra o e-mail montado numa aba, sem gastar um envio de verdade.
	 */
	public static function previa() {
		$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
		check_admin_referer( 'stcms_campanha_previa' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Sem permissão.' );
		}
		$lang = self::lang_pedida( wp_unslash( $_GET['lang'] ?? '' ) );
		$c    = $post_id ? self::campanha_do_post( $post_id ) : self::campanha();
		if ( $post_id && ! get_post( $post_id ) ) {
			wp_die( 'Artigo não encontrado.' );
		}
		nocache_headers();
		header( 'Content-Type: text/html; charset=utf-8' );
		header( 'X-Content-Type-Options: nosniff' );
		header( "Content-Security-Policy: default-src 'none'; img-src https: data:; style-src 'unsafe-inline'" );
		echo self::corpo( $c, $lang, 'exemplo@exemplo.com', 'exemplo' );
		exit;
	}

	public static function url_previa( $post_id, $lang ) {
		$url = admin_url( 'admin-post.php?action=stcms_campanha_previa&lang=' . $lang );
		if ( $post_id ) {
			$url .= '&post=' . (int) $post_id;
		}
		return wp_nonce_url( $url, 'stcms_campanha_previa' );
	}

	public static function iniciar() {
		check_admin_referer( 'stcms_campanha' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Sem permissão.' );
		}
		$c = self::campanha();
		if ( '' === trim( $c['assunto'] ) || '' === trim( $c['titulo'] ) ) {
			self::voltar( 'faltando' );
		}
		$destinos = self::destinatarios( $c['idioma'] );
		if ( ! $destinos ) {
			self::voltar( 'sem_lista' );
		}
		$c['post_id']  = 0;
		$c['fila']     = $destinos;
		$c['total']    = count( $destinos );
		$c['enviados'] = 0;
		$c['falhas']   = 0;
		$c['estado']   = 'enviando';
		$c['quando']   = gmdate( 'Y-m-d H:i:s' );
		self::guardar( $c );
		self::agendar();
		self::voltar( 'iniciado' );
	}

	public static function parar() {
		check_admin_referer( 'stcms_campanha' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Sem permissão.' );
		}
		$c           = self::campanha();
		$c['estado'] = 'pausado';
		self::guardar( $c );
		self::voltar( 'pausado' );
	}

	/**
	 * Envia um lote e devolve o progresso. O navegador chama de novo enquanto
	 * sobrar fila — assim o PHP nunca fica preso num laço de centenas de envios
	 * e a pessoa vê o andamento.
	 */
	public static function ajax_lote() {
		check_ajax_referer( 'stcms_campanha_lote', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Sem permissão.' ), 403 );
			return;
		}
		$c = self::campanha();
		if ( 'enviando' !== $c['estado'] ) {
			wp_send_json_success( self::progresso( $c ) );
			return;
		}

		$c = self::enviar_lote( $c );
		wp_send_json_success( self::progresso( $c ) );
	}

	private static function enviar_lote( $c ) {
		$GLOBALS['stcms_email_html'] = true;
		$n = 0;
		while ( $c['fila'] && $n < self::POR_VEZ ) {
			$item = array_shift( $c['fila'] );
			$lang = 'en' === ( $item['lang'] ?? 'pt' ) ? 'en' : 'pt';
			$ok   = wp_mail(
				$item['email'],
				self::assunto( $c, $lang ),
				self::corpo( $c, $lang, $item['email'], $item['token'] ),
				array( 'Content-Type: text/html; charset=UTF-8' )
			);
			if ( $ok ) {
				$c['enviados']++;
			} else {
				$c['falhas']++;
			}
			$n++;
		}
		unset( $GLOBALS['stcms_email_html'] );

		if ( ! $c['fila'] ) {
			$c['estado'] = 'concluido';
		}
		self::guardar( $c );
		return $c;
	}

	private static function progresso( $c ) {
		return array(
			'estado'   => $c['estado'],
			'enviados' => (int) $c['enviados'],
			'falhas'   => (int) $c['falhas'],
			'total'    => (int) $c['total'],
			'restam'   => count( (array) $c['fila'] ),
		);
	}

	private static function voltar( $aviso ) {
		wp_safe_redirect( admin_url( 'admin.php?page=studio-tabi-newsletter&stcms_aviso=' . $aviso ) );
		exit;
	}

	private static function avisos() {
		$mapa = array(
			'salvo'          => array( 'success', 'Campanha salva.' ),
			'iniciado'       => array( 'success', 'Envio iniciado. Mantenha esta página aberta até terminar.' ),
			'pausado'        => array( 'warning', 'Envio pausado. Quem já recebeu não recebe de novo.' ),
			'teste_ok'       => array( 'success', 'E-mail de teste enviado.' ),
			'teste_falhou'   => array( 'error', 'Não foi possível enviar o teste.' ),
			'teste_invalido' => array( 'error', 'Informe um e-mail válido para receber o teste.' ),
			'teste_sem_texto' => array( 'error', 'Preencha pelo menos o assunto da campanha antes de testar.' ),
			'faltando'       => array( 'error', 'Preencha pelo menos o assunto e o título.' ),
			'sem_lista'      => array( 'error', 'Não há ninguém inscrito nesse idioma.' ),
			'ocupado'        => array( 'error', 'Há um envio em andamento. Pause antes de editar.' ),
			'auto'           => array( 'success', 'Preferência de disparo automático salva.' ),
			'processado'     => array( 'success', 'Fila processada.' ),
		);
		$chave = isset( $_GET['stcms_aviso'] ) ? sanitize_key( wp_unslash( $_GET['stcms_aviso'] ) ) : '';
		if ( ! isset( $mapa[ $chave ] ) ) {
			return;
		}
		printf(
			'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
			esc_attr( $mapa[ $chave ][0] ),
			esc_html( $mapa[ $chave ][1] )
		);
	}

	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$c     = self::campanha();
		$lista = STCMS_Emails::lista();
		$pt    = 0;
		$en    = 0;
		foreach ( $lista as $i ) {
			if ( 'en' === $i['lang'] ) {
				$en++;
			} else {
				$pt++;
			}
		}
		$enviando = 'enviando' === $c['estado'];
		?>
		<div class="wrap stcms-admin">
			<h1>Newsletter</h1>
			<?php self::avisos(); ?>

			<div class="stcms-card" style="margin-top:16px">
				<div class="stcms-card-body" style="padding:18px 20px">
					<p style="margin:0 0 14px;font-size:14px">
						<strong><?php echo (int) count( $lista ); ?></strong> inscritos —
						<?php echo (int) $pt; ?> em português, <?php echo (int) $en; ?> em inglês.
					</p>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="stcms_campanha_auto" />
						<?php wp_nonce_field( 'stcms_campanha' ); ?>
						<label style="display:flex;gap:8px;align-items:flex-start;font-size:14px">
							<input type="checkbox" name="auto" value="1" <?php checked( self::auto_ligado() ); ?> style="margin-top:3px" />
							<span>
								<strong>Avisar a lista quando eu publicar um artigo</strong><br />
								<span class="description">
									O e-mail sai sozinho ao publicar, com o título e o resumo do artigo, no
									idioma de cada inscrito. Cada artigo dispara uma vez só — editar depois
									não reenvia. O texto está em Conteúdo → E-mails automáticos.<br />
									Com isto desligado, cada artigo tem um botão <strong>Enviar para a lista</strong>
									na barra lateral da tela de edição, para você disparar quando quiser.
								</span>
							</span>
						</label>
						<p style="margin:12px 0 0"><button type="submit" class="button">Salvar preferência</button></p>
					</form>
				</div>
			</div>

			<?php
			$saude = self::saude_cron();
			$caminho = defined( 'ABSPATH' ) ? rtrim( ABSPATH, '/' ) . '/wp-cron.php' : 'wp-cron.php';
			$url_cron = home_url( '/wp-cron.php?doing_wp_cron' );
			?>
			<div class="stcms-card" style="margin-top:16px">
				<div class="stcms-card-body" style="padding:18px 20px">
					<h2 style="margin:0 0 10px;font-size:15px">Agendamento</h2>
					<?php if ( $saude['parado'] ) : ?>
						<div class="notice notice-error inline" style="margin:0 0 12px;padding:8px 12px"><p style="margin:0">
							<strong>A fila está parada.</strong> O envio está em andamento mas nada foi processado
							nos últimos 5 minutos. Use “Processar agora” abaixo e configure o cron do servidor.
						</p></div>
					<?php endif; ?>
					<p style="margin:0 0 4px;font-size:13px">
						Último processamento:
						<strong><?php echo $saude['ultimo'] ? esc_html( human_time_diff( $saude['ultimo'] ) ) . ' atrás' : 'nunca'; ?></strong>
						<?php if ( $saude['proximo'] ) : ?>
							· próximo previsto: <strong><?php
								echo $saude['proximo'] > time()
									? 'em ' . esc_html( human_time_diff( time(), $saude['proximo'] ) )
									: 'atrasado';
							?></strong>
						<?php endif; ?>
					</p>
					<p style="margin:0 0 12px;font-size:13px;color:#666">
						Cron interno do WordPress:
						<strong><?php echo $saude['desligado'] ? 'desligado (recomendado)' : 'ligado'; ?></strong>.
						<?php if ( ! $saude['desligado'] ) : ?>
							Neste modo ele só roda quando alguém visita o CMS — e num site headless quase ninguém visita.
						<?php endif; ?>
					</p>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline">
						<input type="hidden" name="action" value="stcms_campanha_agora" />
						<?php wp_nonce_field( 'stcms_campanha' ); ?>
						<button type="submit" class="button">Processar agora</button>
					</form>
					<details style="margin-top:14px">
						<summary style="cursor:pointer;font-weight:600">Como configurar o cron na Hostinger</summary>
						<div style="font-size:13px;line-height:1.7;margin-top:10px">
							<p style="margin:0 0 8px"><strong>1.</strong> No <code>wp-config.php</code>, antes de
							<code>/* That's all, stop editing! */</code>, acrescente:</p>
							<p style="margin:0 0 12px"><code style="display:block;padding:8px 10px;background:#f6f7f7;border-radius:6px">define( 'DISABLE_WP_CRON', true );</code></p>
							<p style="margin:0 0 8px"><strong>2.</strong> No hPanel: <em>Avançado → Cron Jobs</em>, a cada
							<strong>5 minutos</strong>, com o comando:</p>
							<p style="margin:0 0 12px"><code style="display:block;padding:8px 10px;background:#f6f7f7;border-radius:6px;word-break:break-all">/usr/bin/php <?php echo esc_html( $caminho ); ?></code></p>
							<p style="margin:0 0 8px">Se a Hostinger não aceitar comando de PHP, use a versão por URL:</p>
							<p style="margin:0 0 12px"><code style="display:block;padding:8px 10px;background:#f6f7f7;border-radius:6px;word-break:break-all">curl -s <?php echo esc_html( $url_cron ); ?> &gt; /dev/null</code></p>
							<p style="margin:0">
								Feito isso, esta tela deve mostrar “último processamento” de poucos minutos atrás.
								Enquanto não configurar, o envio continua funcionando — só depende de você manter
								esta página aberta.
							</p>
						</div>
					</details>
				</div>
			</div>

			<?php if ( $enviando || 'concluido' === $c['estado'] ) : ?>
				<div class="stcms-card" style="margin-top:16px">
					<div class="stcms-card-body" style="padding:18px 20px">
						<h2 style="margin:0 0 10px;font-size:15px">Envio</h2>
						<div style="background:#e5e5e5;border-radius:6px;height:10px;overflow:hidden;max-width:520px">
							<div id="stcms-barra" style="background:#F20C25;height:100%;width:0"></div>
						</div>
						<p id="stcms-progresso" style="margin:10px 0 0;font-size:13px">
							<?php echo (int) $c['enviados']; ?> de <?php echo (int) $c['total']; ?> enviados<?php
							echo $c['falhas'] ? ', ' . (int) $c['falhas'] . ' falharam' : ''; ?>.
						</p>
						<?php if ( $enviando ) : ?>
							<p style="font-size:12px;color:#666;margin:6px 0 12px">
								Não feche esta aba até terminar. Se fechar, o envio pausa e continua de onde parou.
							</p>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline">
								<input type="hidden" name="action" value="stcms_campanha_parar" />
								<?php wp_nonce_field( 'stcms_campanha' ); ?>
								<button type="submit" class="button">Pausar</button>
							</form>
						<?php endif; ?>
					</div>
				</div>
				<script>
				window.stcmsCampanha = <?php echo wp_json_encode( array(
					'ajax'  => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'stcms_campanha_lote' ),
					'ativo' => $enviando,
				) ); ?>;
				</script>
			<?php endif; ?>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="stcms-form">
				<input type="hidden" name="action" value="stcms_campanha_salvar" />
				<?php wp_nonce_field( 'stcms_campanha' ); ?>
				<div class="stcms-card" style="margin-top:16px">
					<div class="stcms-card-body" style="padding:6px 20px 18px">
						<table class="form-table stcms-fields" role="presentation">
							<tr><th scope="row"><label for="stcms-assunto">Assunto</label></th>
								<td><input type="text" id="stcms-assunto" name="assunto" value="<?php echo esc_attr( $c['assunto'] ); ?>" style="width:100%;max-width:640px" <?php disabled( $enviando ); ?> />
								<p class="description">É o que aparece na caixa de entrada.</p></td></tr>
							<tr><th scope="row"><label for="stcms-titulo">Título</label></th>
								<td><input type="text" id="stcms-titulo" name="titulo" value="<?php echo esc_attr( $c['titulo'] ); ?>" style="width:100%;max-width:640px" <?php disabled( $enviando ); ?> />
								<p class="description">O título grande dentro do e-mail.</p></td></tr>
							<tr><th scope="row"><label for="stcms-texto">Mensagem</label></th>
								<td><textarea id="stcms-texto" name="texto" rows="9" class="large-text" <?php disabled( $enviando ); ?>><?php echo esc_textarea( $c['texto'] ); ?></textarea>
								<p class="description">Uma linha em branco separa parágrafos.</p></td></tr>
							<tr><th scope="row"><label for="stcms-cta">Botão (opcional)</label></th>
								<td><input type="text" id="stcms-cta" name="cta_label" value="<?php echo esc_attr( $c['cta_label'] ); ?>" placeholder="Ler o artigo" style="width:220px" <?php disabled( $enviando ); ?> />
								<input type="url" name="cta_url" value="<?php echo esc_attr( $c['cta_url'] ); ?>" placeholder="https://studiotabi.com.br/blog/..." style="width:100%;max-width:400px;margin-left:8px" <?php disabled( $enviando ); ?> /></td></tr>
							<tr><th scope="row"><label for="stcms-idioma">Enviar para</label></th>
								<td><select id="stcms-idioma" name="idioma" <?php disabled( $enviando ); ?>>
									<option value="todos" <?php selected( $c['idioma'], 'todos' ); ?>>Todos (<?php echo (int) count( $lista ); ?>)</option>
									<option value="pt" <?php selected( $c['idioma'], 'pt' ); ?>>Só português (<?php echo (int) $pt; ?>)</option>
									<option value="en" <?php selected( $c['idioma'], 'en' ); ?>>Só inglês (<?php echo (int) $en; ?>)</option>
								</select>
								<p class="description">Cada pessoa recebe no idioma em que se inscreveu.</p></td></tr>
						</table>
						<p><button type="submit" class="button button-primary" <?php disabled( $enviando ); ?>>Salvar campanha</button></p>
					</div>
				</div>
			</form>

			<div class="stcms-card" style="margin-top:16px">
				<div class="stcms-card-body" style="padding:18px 20px">
					<h2 style="margin:0 0 12px;font-size:15px">Antes de disparar</h2>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:6px">
						<input type="hidden" name="action" value="stcms_campanha_teste" />
						<?php wp_nonce_field( 'stcms_campanha' ); ?>
						<input type="email" name="teste_email" value="<?php echo esc_attr( self::email_teste() ); ?>" placeholder="seu@email.com" style="width:260px" />
						<select name="teste_lang" style="margin-left:6px">
							<option value="pt">em português</option>
							<option value="en">em inglês</option>
						</select>
						<button type="submit" class="button" style="margin-left:6px">Enviar teste</button>
					</form>
					<p class="description" style="margin:0 0 16px">
						Manda uma cópia só para este endereço e guarda ele para a próxima vez.
						O link de descadastro vai com um código falso e não remove ninguém da lista.
					</p>

					<p style="margin:0 0 10px;font-size:13px">
						Abrir a prévia numa aba:
						<a href="<?php echo esc_url( self::url_previa( 0, 'pt' ) ); ?>" target="_blank" rel="noopener">português</a> ·
						<a href="<?php echo esc_url( self::url_previa( 0, 'en' ) ); ?>" target="_blank" rel="noopener">inglês</a>
					</p>
					<details>
						<summary style="cursor:pointer;font-weight:600">Ver aqui mesmo — português</summary>
						<iframe title="Prévia em português" style="width:100%;max-width:620px;height:520px;border:1px solid #dcdcdc;border-radius:8px;margin-top:12px"
							srcdoc="<?php echo esc_attr( self::corpo( $c, 'pt', 'exemplo@exemplo.com', 'exemplo' ) ); ?>"></iframe>
					</details>
					<details style="margin-top:6px">
						<summary style="cursor:pointer;font-weight:600">Ver aqui mesmo — inglês</summary>
						<iframe title="Prévia em inglês" style="width:100%;max-width:620px;height:520px;border:1px solid #dcdcdc;border-radius:8px;margin-top:12px"
							srcdoc="<?php echo esc_attr( self::corpo( $c, 'en', 'exemplo@exemplo.com', 'exemplo' ) ); ?>"></iframe>
					</details>

					<?php if ( ! $enviando ) : ?>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:18px"
							onsubmit="return confirm('Enviar para <?php echo (int) count( self::destinatarios( $c['idioma'] ) ); ?> pessoas? Não dá para voltar atrás.');">
							<input type="hidden" name="action" value="stcms_campanha_iniciar" />
							<?php wp_nonce_field( 'stcms_campanha' ); ?>
							<button type="submit" class="button button-primary">Enviar para a lista</button>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
