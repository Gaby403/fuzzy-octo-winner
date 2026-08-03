<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STCMS_Emails {

	const OPCAO_LISTA = 'stcms_newsletter';

	public static function init() {
		add_filter( 'wp_mail_content_type', array( __CLASS__, 'tipo_html' ) );
		add_filter( 'wp_mail_from_name', array( __CLASS__, 'nome_remetente' ) );
	}

	public static function tipo_html( $tipo ) {
		return empty( $GLOBALS['stcms_email_html'] ) ? $tipo : 'text/html';
	}

	public static function nome_remetente( $nome ) {
		$o = STCMS_Options::get();
		$def = trim( (string) ( $o['site']['title'] ?? '' ) );
		return '' !== $def ? $def : $nome;
	}

	private static function cor( $chave ) {
		$cores = array(
			'tinta'  => '#111111',
			'papel'  => '#EFEFEF',
			'marca'  => '#F20C25',
			'fraco'  => '#6B6B6B',
			'linha'  => '#DCDCDC',
		);
		return $cores[ $chave ];
	}

	/**
	 * Monta o e-mail com a identidade do site. Tabela e estilo embutido porque
	 * cliente de e-mail ignora folha de estilo externa e boa parte do flexbox.
	 */
	public static function modelo( $lang, $titulo, $blocos, $rodape_extra = '' ) {
		$o     = STCMS_Options::get( $lang );
		$site  = (string) ( $o['site']['title'] ?? 'Studio Tabi' );
		$home  = untrailingslashit( self::url_site() );
		$corpo = '';

		foreach ( (array) $blocos as $b ) {
			if ( is_array( $b ) && isset( $b['cta'] ) ) {
				$corpo .= sprintf(
					'<tr><td style="padding:8px 0 24px"><a href="%s" style="display:inline-block;background:%s;color:%s;text-decoration:none;font-weight:700;font-size:13px;letter-spacing:0.08em;text-transform:uppercase;padding:14px 26px;border-radius:8px">%s</a></td></tr>',
					esc_url( $b['url'] ),
					self::cor( 'marca' ),
					self::cor( 'papel' ),
					esc_html( $b['cta'] )
				);
				continue;
			}
			if ( is_array( $b ) && isset( $b['citacao'] ) ) {
				$corpo .= sprintf(
					'<tr><td style="padding:0 0 20px"><div style="border-left:3px solid %s;padding:4px 0 4px 16px;color:%s;font-size:14px;line-height:1.65;white-space:pre-wrap">%s</div></td></tr>',
					self::cor( 'linha' ),
					self::cor( 'fraco' ),
					nl2br( esc_html( (string) $b['citacao'] ) )
				);
				continue;
			}
			$corpo .= sprintf(
				'<tr><td style="padding:0 0 18px;color:%s;font-size:15px;line-height:1.7">%s</td></tr>',
				self::cor( 'tinta' ),
				wp_kses_post( (string) $b )
			);
		}

		return sprintf(
			'<!doctype html><html lang="%1$s"><head><meta charset="utf-8" />'
			. '<meta name="viewport" content="width=device-width,initial-scale=1" />'
			. '<title>%2$s</title></head>'
			. '<body style="margin:0;padding:0;background:%3$s">'
			. '<table role="presentation" width="100%%" cellpadding="0" cellspacing="0" style="background:%3$s;padding:32px 16px">'
			. '<tr><td align="center">'
			. '<table role="presentation" width="100%%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#FFFFFF;border-radius:14px;overflow:hidden">'
			. '<tr><td style="background:%4$s;padding:22px 32px">'
			. '<a href="%5$s" style="color:%3$s;text-decoration:none;font-weight:900;font-size:15px;letter-spacing:0.16em;text-transform:uppercase">%6$s</a>'
			. '</td></tr>'
			. '<tr><td style="padding:34px 32px 8px">'
			. '<h1 style="margin:0 0 20px;font-size:23px;line-height:1.25;color:%4$s;font-weight:800;letter-spacing:-0.02em">%2$s</h1>'
			. '<table role="presentation" width="100%%" cellpadding="0" cellspacing="0">%7$s</table>'
			. '</td></tr>'
			. '<tr><td style="padding:8px 32px 30px;border-top:1px solid %8$s">'
			. '<p style="margin:18px 0 0;color:%9$s;font-size:12px;line-height:1.6">%10$s</p>%11$s'
			. '</td></tr>'
			. '</table></td></tr></table></body></html>',
			esc_attr( 'en' === $lang ? 'en' : 'pt-BR' ),
			esc_html( $titulo ),
			self::cor( 'papel' ),
			self::cor( 'tinta' ),
			esc_url( $home ),
			esc_html( $site ),
			$corpo,
			self::cor( 'linha' ),
			self::cor( 'fraco' ),
			esc_html( $site . ' — ' . ( 'en' === $lang ? 'Rio de Janeiro, Brazil' : 'Rio de Janeiro, RJ' ) ),
			$rodape_extra
		);
	}

	public static function url_site() {
		$o   = STCMS_Options::get();
		$url = trim( (string) ( $o['site']['site_url'] ?? '' ) );
		if ( '' === $url ) {
			$url = (string) get_option( 'stcms_site_origin', '' );
			$url = trim( explode( ',', $url )[0] );
		}
		if ( '' === $url ) {
			$url = (string) get_home_url();
		}
		return untrailingslashit( $url );
	}

	private static function troca( $texto, $valores ) {
		foreach ( $valores as $chave => $valor ) {
			$texto = str_replace( '{' . $chave . '}', $valor, $texto );
		}
		return $texto;
	}

	private static function enviar( $para, $assunto, $html ) {
		$GLOBALS['stcms_email_html'] = true;
		$enviado = wp_mail( $para, $assunto, $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
		unset( $GLOBALS['stcms_email_html'] );
		return $enviado;
	}

	/**
	 * Confirmação para quem preencheu o formulário de contato.
	 * O envio para o estúdio é separado: se este falhar, a mensagem não se perde.
	 */
	public static function confirmar_contato( $dados, $lang = 'pt' ) {
		$o = STCMS_Options::get( $lang );
		$e = isset( $o['emails'] ) ? $o['emails'] : array();

		$valores = array(
			'nome'     => esc_html( $dados['name'] ),
			'assunto'  => esc_html( $dados['subject'] ),
			'mensagem' => $dados['message'],
			'site'     => esc_html( (string) ( $o['site']['title'] ?? '' ) ),
		);

		$assunto = self::troca( (string) ( $e['contato_assunto'] ?? '' ), $valores );
		$titulo  = self::troca( (string) ( $e['contato_titulo'] ?? '' ), $valores );
		$texto   = self::troca( (string) ( $e['contato_texto'] ?? '' ), $valores );

		if ( '' === trim( $assunto ) || '' === trim( $titulo ) ) {
			return false;
		}

		$blocos = array( wpautop( $texto ) );
		if ( '' !== trim( (string) $dados['message'] ) ) {
			$rotulo   = 'en' === $lang ? 'What you sent us:' : 'O que você enviou:';
			$blocos[] = '<strong style="font-size:13px;text-transform:uppercase;letter-spacing:0.08em">' . esc_html( $rotulo ) . '</strong>';
			$blocos[] = array( 'citacao' => $dados['message'] );
		}

		return self::enviar( $dados['email'], $assunto, self::modelo( $lang, $titulo, $blocos ) );
	}

	public static function boas_vindas_newsletter( $email, $token, $lang = 'pt' ) {
		$o = STCMS_Options::get( $lang );
		$e = isset( $o['emails'] ) ? $o['emails'] : array();

		$assunto = (string) ( $e['news_assunto'] ?? '' );
		$titulo  = (string) ( $e['news_titulo'] ?? '' );
		$texto   = (string) ( $e['news_texto'] ?? '' );

		if ( '' === trim( $assunto ) || '' === trim( $titulo ) ) {
			return false;
		}

		$blocos = array( wpautop( $texto ) );
		$blog   = self::url_site() . ( 'en' === $lang ? '/en/blog' : '/blog' );
		$blocos[] = array( 'cta' => 'en' === $lang ? 'Read the blog' : 'Ler o blog', 'url' => $blog );

		return self::enviar( $email, $assunto, self::modelo( $lang, $titulo, $blocos, self::rodape_descadastro( $email, $token, $lang ) ) );
	}

	public static function rodape_descadastro( $email, $token, $lang ) {
		$url = add_query_arg(
			array( 'e' => rawurlencode( $email ), 't' => $token ),
			self::url_site() . '/wp-json/studio-tabi/v1/unsubscribe'
		);
		$texto = 'en' === $lang
			? 'You are receiving this because you subscribed on our website. %sUnsubscribe%s at any time.'
			: 'Você recebe este e-mail porque se inscreveu no nosso site. %sCancelar inscrição%s quando quiser.';
		return sprintf(
			'<p style="margin:10px 0 0;color:%s;font-size:12px;line-height:1.6">' . $texto . '</p>',
			self::cor( 'fraco' ),
			'<a href="' . esc_url( $url ) . '" style="color:' . self::cor( 'fraco' ) . '">',
			'</a>'
		);
	}

	public static function lista() {
		$bruto = get_option( self::OPCAO_LISTA, array() );
		if ( ! is_array( $bruto ) ) {
			return array();
		}
		$saida    = array();
		$migrou   = false;
		foreach ( $bruto as $item ) {
			if ( is_string( $item ) ) {
				$item   = array( 'email' => $item, 'lang' => 'pt', 'data' => '', 'token' => self::token() );
				$migrou = true;
			}
			if ( ! is_array( $item ) || empty( $item['email'] ) ) {
				continue;
			}
			if ( empty( $item['token'] ) ) {
				$item['token'] = self::token();
				$migrou        = true;
			}
			$saida[] = array(
				'email' => (string) $item['email'],
				'lang'  => 'en' === ( $item['lang'] ?? 'pt' ) ? 'en' : 'pt',
				'data'  => (string) ( $item['data'] ?? '' ),
				'token' => (string) $item['token'],
			);
		}
		if ( $migrou ) {
			update_option( self::OPCAO_LISTA, $saida );
		}
		return $saida;
	}

	public static function token() {
		return function_exists( 'wp_generate_password' ) ? wp_generate_password( 24, false, false ) : substr( md5( uniqid( '', true ) ), 0, 24 );
	}

	public static function inscrever( $email, $lang = 'pt' ) {
		$lista = self::lista();
		foreach ( $lista as $item ) {
			if ( strtolower( $item['email'] ) === strtolower( $email ) ) {
				return null;
			}
		}
		$novo = array(
			'email' => $email,
			'lang'  => 'en' === $lang ? 'en' : 'pt',
			'data'  => gmdate( 'Y-m-d H:i:s' ),
			'token' => self::token(),
		);
		$lista[] = $novo;
		update_option( self::OPCAO_LISTA, $lista );
		return $novo;
	}

	public static function descadastrar( $email, $token ) {
		$lista = self::lista();
		$saida = array();
		$achou = false;
		foreach ( $lista as $item ) {
			if ( strtolower( $item['email'] ) === strtolower( $email ) && hash_equals( $item['token'], (string) $token ) ) {
				$achou = true;
				continue;
			}
			$saida[] = $item;
		}
		if ( $achou ) {
			update_option( self::OPCAO_LISTA, $saida );
		}
		return $achou;
	}
}
