<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STCMS_Campanhas {

	const OPCAO   = 'stcms_campanha';
	const POR_VEZ = 15;

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 20 );
		add_action( 'wp_ajax_stcms_campanha_lote', array( __CLASS__, 'ajax_lote' ) );
		add_action( 'admin_post_stcms_campanha_salvar', array( __CLASS__, 'salvar' ) );
		add_action( 'admin_post_stcms_campanha_teste', array( __CLASS__, 'teste' ) );
		add_action( 'admin_post_stcms_campanha_iniciar', array( __CLASS__, 'iniciar' ) );
		add_action( 'admin_post_stcms_campanha_parar', array( __CLASS__, 'parar' ) );
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
		$blocos = array( wpautop( $c['texto'] ) );
		if ( '' !== trim( (string) $c['cta_label'] ) && '' !== trim( (string) $c['cta_url'] ) ) {
			$blocos[] = array( 'cta' => $c['cta_label'], 'url' => $c['cta_url'] );
		}
		$rodape = ( '' !== $email && '' !== $token ) ? STCMS_Emails::rodape_descadastro( $email, $token, $lang ) : '';
		return STCMS_Emails::modelo( $lang, $c['titulo'], $blocos, $rodape );
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

	public static function teste() {
		check_admin_referer( 'stcms_campanha' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Sem permissão.' );
		}
		$para = sanitize_email( wp_unslash( $_POST['teste_email'] ?? '' ) );
		$c    = self::campanha();
		if ( ! is_email( $para ) || '' === trim( $c['assunto'] ) ) {
			self::voltar( 'teste_invalido' );
		}
		$lang = 'en' === $c['idioma'] ? 'en' : 'pt';
		$GLOBALS['stcms_email_html'] = true;
		$enviado = wp_mail(
			$para,
			'[teste] ' . $c['assunto'],
			self::corpo( $c, $lang, $para, 'token-de-teste-000000' ),
			array( 'Content-Type: text/html; charset=UTF-8' )
		);
		unset( $GLOBALS['stcms_email_html'] );
		self::voltar( $enviado ? 'teste_ok' : 'teste_falhou' );
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
		$c['fila']     = $destinos;
		$c['total']    = count( $destinos );
		$c['enviados'] = 0;
		$c['falhas']   = 0;
		$c['estado']   = 'enviando';
		$c['quando']   = gmdate( 'Y-m-d H:i:s' );
		self::guardar( $c );
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

		$GLOBALS['stcms_email_html'] = true;
		$n = 0;
		while ( $c['fila'] && $n < self::POR_VEZ ) {
			$item = array_shift( $c['fila'] );
			$lang = 'en' === ( $item['lang'] ?? 'pt' ) ? 'en' : 'pt';
			$ok   = wp_mail(
				$item['email'],
				$c['assunto'],
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
		wp_send_json_success( self::progresso( $c ) );
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
			'teste_invalido' => array( 'error', 'Informe um e-mail válido e preencha o assunto antes de testar.' ),
			'faltando'       => array( 'error', 'Preencha pelo menos o assunto e o título.' ),
			'sem_lista'      => array( 'error', 'Não há ninguém inscrito nesse idioma.' ),
			'ocupado'        => array( 'error', 'Há um envio em andamento. Pause antes de editar.' ),
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
					<p style="margin:0;font-size:14px">
						<strong><?php echo (int) count( $lista ); ?></strong> inscritos —
						<?php echo (int) $pt; ?> em português, <?php echo (int) $en; ?> em inglês.
					</p>
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

			<?php if ( ! $enviando ) : ?>
			<div class="stcms-card" style="margin-top:16px">
				<div class="stcms-card-body" style="padding:18px 20px">
					<h2 style="margin:0 0 12px;font-size:15px">Antes de disparar</h2>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:16px">
						<input type="hidden" name="action" value="stcms_campanha_teste" />
						<?php wp_nonce_field( 'stcms_campanha' ); ?>
						<input type="email" name="teste_email" placeholder="seu@email.com" style="width:260px" />
						<button type="submit" class="button">Enviar teste para mim</button>
						<span class="description" style="margin-left:8px">Manda uma cópia só para este endereço.</span>
					</form>
					<details>
						<summary style="cursor:pointer;font-weight:600">Ver como vai ficar</summary>
						<iframe style="width:100%;max-width:620px;height:520px;border:1px solid #dcdcdc;border-radius:8px;margin-top:12px"
							srcdoc="<?php echo esc_attr( self::corpo( $c, 'pt', 'exemplo@exemplo.com', 'exemplo' ) ); ?>"></iframe>
					</details>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:18px"
						onsubmit="return confirm('Enviar para <?php echo (int) count( self::destinatarios( $c['idioma'] ) ); ?> pessoas? Não dá para voltar atrás.');">
						<input type="hidden" name="action" value="stcms_campanha_iniciar" />
						<?php wp_nonce_field( 'stcms_campanha' ); ?>
						<button type="submit" class="button button-primary">Enviar para a lista</button>
					</form>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
