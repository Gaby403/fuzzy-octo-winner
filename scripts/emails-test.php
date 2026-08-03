<?php
error_reporting( E_ALL & ~E_DEPRECATED );
define( 'ABSPATH', __DIR__ . '/' );
define( 'HOUR_IN_SECONDS', 3600 );
$P = dirname( __DIR__ ) . '/wordpress/wp-content/plugins/studio-tabi-cms';

$GLOBALS['__o'] = array();
$GLOBALS['__enviados'] = array();

function get_option( $k, $d = false ) { return $GLOBALS['__o'][ $k ] ?? $d; }
function update_option( $k, $v ) { $GLOBALS['__o'][ $k ] = $v; return true; }
function add_option( $k, $v ) { return update_option( $k, $v ); }
function delete_option( $k ) { unset( $GLOBALS['__o'][ $k ] ); return true; }
function get_transient( $k ) { return false; }
function set_transient( $k, $v, $t ) { return true; }
function add_action( ...$a ) { return true; }
function add_filter( ...$a ) { return true; }
function remove_filter( ...$a ) { return true; }
function register_rest_route( ...$a ) { return true; }
function apply_filters( $t, $v ) { return $v; }
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_textarea_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_title( $s ) { return strtolower( preg_replace( '/[^a-z0-9]+/i', '-', (string) $s ) ); }
function sanitize_key( $s ) { return strtolower( preg_replace( '/[^a-z0-9_]/i', '', (string) $s ) ); }
function sanitize_email( $s ) { return trim( (string) $s ); }
function is_email( $s ) { return (bool) filter_var( $s, FILTER_VALIDATE_EMAIL ); }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $s ) { return (string) $s; }
function esc_url_raw( $s, $p = null ) { return (string) $s; }
function wp_kses_post( $s ) { return (string) $s; }
function wp_unslash( $v ) { return $v; }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
function untrailingslashit( $s ) { return rtrim( (string) $s, '/' ); }
function get_home_url() { return 'https://studiotabi.com.br'; }
function get_bloginfo( $x = '' ) { return 'Studio Tabi'; }
function wp_generate_password( $n = 12, ...$r ) { return substr( str_replace( array( '/', '+', '=' ), 'a', base64_encode( random_bytes( $n ) ) ), 0, $n ); }
function wpautop( $s ) { return '<p>' . str_replace( "\n\n", '</p><p>', (string) $s ) . '</p>'; }
function add_query_arg( $args, $url ) { return $url . ( false === strpos( $url, '?' ) ? '?' : '&' ) . http_build_query( $args ); }
function wp_get_attachment_image_url( $i, $s = null ) { return ''; }
function get_post_meta( $i, $k = '', $s = true ) { return ''; }
function get_page_by_path( ...$a ) { return null; }
function get_posts( $a ) { return array(); }
function get_the_title( $p ) { return ''; }
function wp_strip_all_tags( $s ) { return strip_tags( (string) $s ); }
function wp_trim_words( $s, $n = 55, $m = '…' ) { return $s; }
function get_the_category( $i ) { return array(); }
function get_the_tags( $i ) { return array(); }
function get_categories( $a = array() ) { return array(); }
function get_term_meta( $i, $k = '', $s = true ) { return ''; }
function has_excerpt( $p ) { return false; }
function get_the_excerpt( $p ) { return ''; }
function get_the_date( $f, $p ) { return ''; }
function get_the_author_meta( $c, $i ) { return ''; }
function get_the_post_thumbnail_url( $p, $s = null ) { return ''; }
function get_avatar_url( $i, $a = array() ) { return ''; }
function wp_reset_postdata() { return true; }
function get_post( $i ) { return null; }
function wp_get_post_categories( $i ) { return array(); }
function wp_mail( $para, $assunto, $corpo, $headers = array() ) {
	$GLOBALS['__enviados'][] = array( 'para' => $para, 'assunto' => $assunto, 'corpo' => $corpo, 'headers' => $headers );
	return true;
}
if ( ! defined( 'OBJECT' ) ) { define( 'OBJECT', 'OBJECT' ); }

class WP_REST_Response {
	public $data; public $status; public $headers = array();
	function __construct( $d, $s = 200 ) { $this->data = $d; $this->status = $s; }
	function header( $k, $v ) { $this->headers[ $k ] = $v; }
}
class WP_REST_Request {
	private $p;
	function __construct( $p = array() ) { $this->p = $p; }
	function get_param( $k ) { return $this->p[ $k ] ?? null; }
	function get_params() { return $this->p; }
	function get_json_params() { return $this->p; }
}
class WP_Query { public $posts = array(); public $found_posts = 0; public $max_num_pages = 1; function __construct( $a ) {} }

require_once "$P/includes/defaults.php";
require_once "$P/includes/class-options.php";
require_once "$P/includes/class-traducao.php";
require_once "$P/includes/class-emails.php";
require_once "$P/includes/class-rest.php";

function add_submenu_page( ...$a ) { return true; }
function current_user_can( ...$a ) { return true; }
function check_admin_referer( ...$a ) { return true; }
function check_ajax_referer( ...$a ) { return true; }
class RespondeuJson extends Exception {}
function wp_send_json_success( $d ) { $GLOBALS['__json'] = $d; throw new RespondeuJson(); }
function wp_send_json_error( $d, $s = 400 ) { $GLOBALS['__json'] = $d; throw new RespondeuJson(); }
function lote() { try { STCMS_Campanhas::ajax_lote(); } catch ( RespondeuJson $e ) {} return $GLOBALS['__json']; }
function admin_url( $p = '' ) { return '/wp-admin/' . $p; }
function wp_create_nonce( $a ) { return 'nonce'; }
function wp_json_encode( $v ) { return json_encode( $v ); }
function wp_nonce_field( ...$a ) { return true; }
function disabled( ...$a ) { return ''; }
function selected( $a, $b, $e = true ) { return (string) $a === (string) $b ? ' selected' : ''; }
function esc_textarea( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
class Redirecionou extends Exception {}
function wp_safe_redirect( $u ) { throw new Redirecionou( $u ); }
function wp_die( $m ) { throw new Exception( $m ); }
require_once "$P/includes/class-campanhas.php";

$ok = 0; $ko = 0;
function ok( $r, $c, $v = '' ) { global $ok, $ko; $c ? $ok++ : $ko++; echo '  ' . ( $c ? '✓' : '✗' ) . "  $r" . ( '' !== $v ? ": $v" : '' ) . "\n"; }
function limpar() { $GLOBALS['__enviados'] = array(); }
function ultimo_para( $email ) {
	foreach ( array_reverse( $GLOBALS['__enviados'] ) as $e ) {
		if ( $e['para'] === $email ) { return $e; }
	}
	return null;
}

$_SERVER['HTTP_ORIGIN'] = 'https://studiotabi.com.br';
$_SERVER['REMOTE_ADDR'] = '203.0.113.7';

$payload = array( 'name' => 'Ana Costa', 'email' => 'ana@exemplo.com', 'subject' => 'Site novo', 'message' => "Oi!\nQueria um orçamento." );

echo "== Contato: quem enviou recebe confirmação ==\n";
limpar();
$r = STCMS_Rest::submit_contact( new WP_REST_Request( $payload ) );
ok( 'formulário aceito', 200 === $r->status, (string) $r->status );
$estudio = ultimo_para( 'contato@studiotabi.com.br' );
$visitante = ultimo_para( 'ana@exemplo.com' );
ok( 'estúdio recebe o aviso', null !== $estudio );
ok( 'quem preencheu recebe a confirmação', null !== $visitante );
ok( 'assunto usa o nome do site', false !== strpos( (string) ( $visitante['assunto'] ?? '' ), 'Studio Tabi' ), $visitante['assunto'] ?? '' );
ok( 'confirmação é HTML', false !== strpos( (string) ( $visitante['corpo'] ?? '' ), '<!doctype html>' ) );
ok( 'traz o nome de quem escreveu', false !== strpos( (string) ( $visitante['corpo'] ?? '' ), 'Ana Costa' ) );
ok( 'devolve a mensagem enviada', false !== strpos( (string) ( $visitante['corpo'] ?? '' ), 'orçamento' ) );
ok( 'não vaza o e-mail do estúdio no corpo', false === strpos( (string) ( $visitante['corpo'] ?? '' ), 'contato@studiotabi.com.br' ) );
ok( 'confirmação não tem link de descadastro', false === strpos( (string) ( $visitante['corpo'] ?? '' ), 'unsubscribe' ) );

echo "\n== Contato em inglês ==\n";
limpar();
$r = STCMS_Rest::submit_contact( new WP_REST_Request( array_merge( $payload, array( 'email' => 'john@example.com', 'lang' => 'en' ) ) ) );
$v = ultimo_para( 'john@example.com' );
ok( 'confirmação enviada', null !== $v );
ok( 'texto em inglês', false !== strpos( (string) ( $v['corpo'] ?? '' ), 'Thank you for writing' ), substr( strip_tags( (string) ( $v['corpo'] ?? '' ) ), 0, 60 ) );
ok( 'lang do html é en', false !== strpos( (string) ( $v['corpo'] ?? '' ), 'lang="en"' ) );

echo "\n== Newsletter: boas-vindas e lista ==\n";
limpar();
$GLOBALS['__o']['stcms_newsletter'] = array();
$r = STCMS_Rest::subscribe_newsletter( new WP_REST_Request( array( 'email' => 'leitor@exemplo.com' ) ) );
ok( 'inscrição aceita', 200 === $r->status, (string) $r->status );
$b = ultimo_para( 'leitor@exemplo.com' );
ok( 'assinante recebe boas-vindas', null !== $b );
ok( 'boas-vindas é HTML', false !== strpos( (string) ( $b['corpo'] ?? '' ), '<!doctype html>' ) );
ok( 'tem link de cancelar inscrição', false !== strpos( (string) ( $b['corpo'] ?? '' ), '/unsubscribe' ), 'sem link' );
ok( 'link vai pelo domínio do site', false !== strpos( (string) ( $b['corpo'] ?? '' ), 'studiotabi.com.br/wp-json' ) );

$lista = STCMS_Emails::lista();
ok( 'entrou na lista', 1 === count( $lista ), (string) count( $lista ) );
ok( 'guarda o idioma', 'pt' === $lista[0]['lang'] );
ok( 'guarda a data', '' !== $lista[0]['data'] );
ok( 'tem token', strlen( $lista[0]['token'] ) >= 20 );

limpar();
STCMS_Rest::subscribe_newsletter( new WP_REST_Request( array( 'email' => 'leitor@exemplo.com' ) ) );
ok( 'inscrever de novo não duplica', 1 === count( STCMS_Emails::lista() ), (string) count( STCMS_Emails::lista() ) );
ok( 'e não manda boas-vindas outra vez', null === ultimo_para( 'leitor@exemplo.com' ) );

echo "\n== Cancelar inscrição ==\n";
$token = STCMS_Emails::lista()[0]['token'];
$r = STCMS_Rest::unsubscribe( new WP_REST_Request( array( 'e' => 'leitor@exemplo.com', 't' => 'token-errado' ) ) );
ok( 'token errado não remove', 404 === $r->status && 1 === count( STCMS_Emails::lista() ), (string) $r->status );
$r = STCMS_Rest::unsubscribe( new WP_REST_Request( array( 'e' => 'leitor@exemplo.com', 't' => $token ) ) );
ok( 'token certo remove', 200 === $r->status && 0 === count( STCMS_Emails::lista() ), (string) $r->status );
ok( 'responde página HTML', false !== strpos( (string) $r->data, '<!doctype html>' ) );
ok( 'com Content-Type de HTML', false !== strpos( (string) ( $r->headers['Content-Type'] ?? '' ), 'text/html' ) );
$r = STCMS_Rest::unsubscribe( new WP_REST_Request( array( 'e' => 'leitor@exemplo.com', 't' => $token ) ) );
ok( 'usar o link duas vezes não quebra', 404 === $r->status );

echo "\n== Lista antiga (só strings) é migrada ==\n";
$GLOBALS['__o']['stcms_newsletter'] = array( 'velho@exemplo.com', 'outro@exemplo.com' );
$lista = STCMS_Emails::lista();
ok( 'os dois continuam', 2 === count( $lista ), (string) count( $lista ) );
ok( 'ganharam token', '' !== $lista[0]['token'] && '' !== $lista[1]['token'] );
ok( 'gravou o formato novo', is_array( $GLOBALS['__o']['stcms_newsletter'][0] ) );
ok( 'e-mail preservado', 'velho@exemplo.com' === $lista[0]['email'] );

echo "\n== Assunto em branco desliga o e-mail ==\n";
limpar();
$GLOBALS['__o']['stcms_options'] = array( 'emails' => array( 'contato_assunto' => '', 'contato_titulo' => '' ) );
STCMS_Rest::submit_contact( new WP_REST_Request( array_merge( $payload, array( 'email' => 'ana2@exemplo.com' ) ) ) );
ok( 'nada é enviado ao visitante', null === ultimo_para( 'ana2@exemplo.com' ) );
ok( 'mas o estúdio continua avisado', null !== ultimo_para( 'contato@studiotabi.com.br' ) );

echo "\n== Escapes ==\n";
$GLOBALS['__o']['stcms_options'] = array();
limpar();
STCMS_Rest::submit_contact( new WP_REST_Request( array(
	'name' => 'Ana <script>alert(1)</script>',
	'email' => 'xss@exemplo.com',
	'subject' => 'Oi',
	'message' => 'texto <img src=x onerror=alert(1)>',
) ) );
$v = ultimo_para( 'xss@exemplo.com' );
ok( 'nome com script não vira tag', false === strpos( (string) ( $v['corpo'] ?? '' ), '<script>' ), 'vazou script' );
ok( 'mensagem com html não vira tag', false === strpos( (string) ( $v['corpo'] ?? '' ), '<img src=x' ), 'vazou img' );

echo "\n== Campanha: envio em lotes ==\n";
limpar();
$GLOBALS['__o']['stcms_newsletter'] = array();
foreach ( range( 1, 32 ) as $i ) {
	STCMS_Emails::inscrever( "pessoa{$i}@exemplo.com", $i % 4 === 0 ? 'en' : 'pt' );
}
limpar();
$GLOBALS['__o']['stcms_campanha'] = array(
	'assunto' => 'Novidades do estúdio',
	'titulo'  => 'O que andamos construindo',
	'texto'   => "Primeiro parágrafo.\n\nSegundo parágrafo.",
	'cta_label' => 'Ler no blog',
	'cta_url'   => 'https://studiotabi.com.br/blog',
	'idioma'  => 'todos',
);
try { STCMS_Campanhas::iniciar(); } catch ( Redirecionou $e ) {}
$c = STCMS_Campanhas::campanha();
ok( 'fila montada com todos', 32 === $c['total'], (string) $c['total'] );
ok( 'estado é enviando', 'enviando' === $c['estado'], $c['estado'] );

$p1 = lote();
ok( 'primeiro lote envia 15', 15 === $p1['enviados'], (string) $p1['enviados'] );
ok( 'não envia a lista toda de uma vez', $p1['restam'] > 0, (string) $p1['restam'] );
ok( 'contou os e-mails de verdade', 15 === count( $GLOBALS['__enviados'] ), (string) count( $GLOBALS['__enviados'] ) );

lote();
$fim = lote();
ok( 'termina a fila', 32 === $fim['enviados'], (string) $fim['enviados'] );
ok( 'estado vira concluido', 'concluido' === $fim['estado'], $fim['estado'] );
ok( 'ninguém recebeu duas vezes', 32 === count( array_unique( array_column( $GLOBALS['__enviados'], 'para' ) ) ), (string) count( $GLOBALS['__enviados'] ) );

echo "\n== Cada um recebe o seu link de descadastro ==\n";
$lista = STCMS_Emails::lista();
$primeiro = null;
foreach ( $GLOBALS['__enviados'] as $e ) {
	if ( $e['para'] === $lista[0]['email'] ) { $primeiro = $e; break; }
}
ok( 'e-mail tem o token do próprio inscrito', null !== $primeiro && false !== strpos( $primeiro['corpo'], $lista[0]['token'] ) );
ok( 'não carrega o token de outro', false === strpos( (string) ( $primeiro['corpo'] ?? '' ), $lista[1]['token'] ) );
ok( 'tem o botão configurado', false !== strpos( (string) ( $primeiro['corpo'] ?? '' ), 'Ler no blog' ) );

echo "\n== Idioma por destinatário ==\n";
$em_ingles = null;
foreach ( $lista as $i ) { if ( 'en' === $i['lang'] ) { $em_ingles = $i['email']; break; } }
$e = null;
foreach ( $GLOBALS['__enviados'] as $x ) { if ( $x['para'] === $em_ingles ) { $e = $x; break; } }
ok( 'quem assinou em inglês recebe em inglês', null !== $e && false !== strpos( $e['corpo'], 'lang="en"' ) );
ok( 'e o rodapé em inglês', null !== $e && false !== strpos( $e['corpo'], 'Unsubscribe' ) );

echo "\n== Pausar e retomar ==\n";
limpar();
$GLOBALS['__o']['stcms_campanha']['estado'] = 'enviando';
$GLOBALS['__o']['stcms_campanha']['fila']   = array_slice( $lista, 0, 20 );
$GLOBALS['__o']['stcms_campanha']['total']  = 20;
$GLOBALS['__o']['stcms_campanha']['enviados'] = 0;
lote();
ok( 'enviou o primeiro lote', 15 === count( $GLOBALS['__enviados'] ), (string) count( $GLOBALS['__enviados'] ) );
try { STCMS_Campanhas::parar(); } catch ( Redirecionou $e ) {}
limpar();
lote();
ok( 'pausado não envia mais nada', 0 === count( $GLOBALS['__enviados'] ), (string) count( $GLOBALS['__enviados'] ) );
$c = STCMS_Campanhas::campanha();
ok( 'a fila que sobrou é preservada', 5 === count( $c['fila'] ), (string) count( $c['fila'] ) );

echo "\n== Segmento por idioma ==\n";
$GLOBALS['__o']['stcms_campanha']['idioma'] = 'en';
$GLOBALS['__o']['stcms_campanha']['estado'] = 'rascunho';
try { STCMS_Campanhas::iniciar(); } catch ( Redirecionou $e ) {}
$c = STCMS_Campanhas::campanha();
ok( 'só os inscritos em inglês entram', 8 === $c['total'], (string) $c['total'] );

echo "\n== Sem assunto não dispara ==\n";
$GLOBALS['__o']['stcms_campanha'] = array( 'assunto' => '', 'titulo' => '', 'estado' => 'rascunho' );
$destino = '';
try { STCMS_Campanhas::iniciar(); } catch ( Redirecionou $e ) { $destino = $e->getMessage(); }
ok( 'recusa e avisa', false !== strpos( $destino, 'faltando' ), $destino );

echo "\n$ok passaram, $ko falharam\n";
exit( $ko ? 1 : 0 );
