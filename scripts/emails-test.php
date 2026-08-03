<?php
error_reporting( E_ALL & ~E_DEPRECATED );
define( 'ABSPATH', __DIR__ . '/' );
define( 'HOUR_IN_SECONDS', 3600 );
$P = dirname( __DIR__ ) . '/wordpress/wp-content/plugins/studio-tabi-cms';

$GLOBALS['__o'] = array();
$GLOBALS['__enviados'] = array();
$GLOBALS['__posts'] = array();
$GLOBALS['__cron'] = array();

function get_option( $k, $d = false ) { return $GLOBALS['__o'][ $k ] ?? $d; }
function update_option( $k, $v ) { $GLOBALS['__o'][ $k ] = $v; return true; }
function add_option( $k, $v ) { return update_option( $k, $v ); }
function delete_option( $k ) { unset( $GLOBALS['__o'][ $k ] ); return true; }
function get_transient( $k ) { return false; }
function set_transient( $k, $v, $t ) { return true; }
function add_action( ...$a ) { return true; }
function add_filter( $hook, $cb = null, ...$r ) { if ( $cb ) { $GLOBALS['__filtros'][ $hook ][] = $cb; } return true; }
function remetente_atual() {
	$v = 'wordpress@srv952.main-hosting.eu';
	foreach ( $GLOBALS['__filtros']['wp_mail_from'] ?? array() as $cb ) { $v = call_user_func( $cb, $v ); }
	return $v;
}
function nome_remetente_atual() {
	$v = 'WordPress';
	foreach ( $GLOBALS['__filtros']['wp_mail_from_name'] ?? array() as $cb ) { $v = call_user_func( $cb, $v ); }
	return $v;
}
function remove_filter( ...$a ) { return true; }
function register_rest_route( ...$a ) { return true; }
function apply_filters( $t, $v ) {
	foreach ( $GLOBALS['__filtros'][ $t ] ?? array() as $cb ) { $v = call_user_func( $cb, $v ); }
	return $v;
}
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
function add_query_arg( ...$a ) {
	$args = is_array( $a[0] ) ? $a[0] : array( $a[0] => $a[1] );
	$url  = is_array( $a[0] ) ? $a[1] : $a[2];
	return $url . ( false === strpos( $url, '?' ) ? '?' : '&' ) . http_build_query( $args );
}
function wp_get_attachment_image_url( $i, $s = null ) { return ''; }
function get_post_meta( $i, $k = '', $s = true ) { return $GLOBALS['__pmeta'][ $i ][ $k ] ?? ''; }
function update_post_meta( $i, $k, $v ) { $GLOBALS['__pmeta'][ $i ][ $k ] = $v; return true; }
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
function get_post( $i ) { return $GLOBALS['__posts'][ $i ] ?? null; }
function wp_next_scheduled( $h ) { return $GLOBALS['__cron'][ $h ] ?? false; }
function wp_schedule_single_event( $t, $h ) { $GLOBALS['__cron'][ $h ] = $t; return true; }
function checked( $a, $b = true, $e = true ) { return $a == $b ? ' checked' : ''; }
function human_time_diff( $de, $ate = 0 ) { return (string) abs( ( $ate ?: time() ) - $de ) . 's'; }
function home_url( $p = '' ) { return 'https://cms.studiotabi.com.br' . $p; }
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
function add_meta_box( $id, $titulo, $cb, $tela, $ctx = '', $pri = '' ) {
	$GLOBALS['__metabox'][] = array( 'id' => $id, 'titulo' => $titulo, 'cb' => $cb, 'tela' => $tela, 'ctx' => $ctx );
	return true;
}
function get_edit_post_link( $i, $ctx = '' ) { return get_post( $i ) ? "/wp-admin/post.php?post=$i&action=edit" : null; }
function wp_nonce_url( $u, $a = -1 ) { return $u . ( false === strpos( $u, '?' ) ? '?' : '&' ) . '_wpnonce=nonce'; }
function get_date_from_gmt( $d, $f = 'Y-m-d H:i:s' ) { return gmdate( $f, strtotime( $d . ' UTC' ) ); }
function get_current_screen() { return $GLOBALS['__screen'] ?? null; }
require_once "$P/includes/class-campanhas.php";

function metabox_html( $post ) {
	ob_start();
	STCMS_Campanhas::render_metabox( $post );
	return ob_get_clean();
}
function disparar_post( $id, $forcar = false ) {
	$_GET = array( 'post' => $id, '_wpnonce' => 'nonce' );
	if ( $forcar ) { $_GET['forcar'] = '1'; }
	try { STCMS_Campanhas::enviar_post(); } catch ( Redirecionou $e ) { return $e->getMessage(); }
	return '';
}
function aviso_html( $chave ) {
	$tela = new stdClass();
	$tela->base = 'post'; $tela->post_type = 'post';
	$GLOBALS['__screen'] = $tela;
	$_GET = array( 'stcms_aviso' => $chave );
	ob_start();
	STCMS_Campanhas::aviso_no_post();
	return ob_get_clean();
}

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

echo "\n== Idioma chega do formulário ==\n";
$GLOBALS['__o']['stcms_options'] = array();
limpar();
// Como o site manda agora: lang no corpo do JSON.
STCMS_Rest::submit_contact( new WP_REST_Request( array_merge( $payload, array( 'email' => 'en-body@exemplo.com', 'lang' => 'en' ) ) ) );
$v = ultimo_para( 'en-body@exemplo.com' );
ok( 'lang no corpo vira e-mail em inglês', null !== $v && false !== strpos( $v['corpo'], 'lang="en"' ) );

limpar();
STCMS_Rest::submit_contact( new WP_REST_Request( array_merge( $payload, array( 'email' => 'pt-sem@exemplo.com' ) ) ) );
$v = ultimo_para( 'pt-sem@exemplo.com' );
ok( 'sem lang continua português', null !== $v && false !== strpos( $v['corpo'], 'lang="pt-BR"' ) );

limpar();
STCMS_Rest::subscribe_newsletter( new WP_REST_Request( array( 'email' => 'assina-en@exemplo.com', 'lang' => 'en' ) ) );
$v = ultimo_para( 'assina-en@exemplo.com' );
ok( 'newsletter em inglês', null !== $v && false !== strpos( $v['corpo'], 'lang="en"' ) );
$achou = null;
foreach ( STCMS_Emails::lista() as $i ) { if ( 'assina-en@exemplo.com' === $i['email'] ) { $achou = $i; } }
ok( 'lista guarda o idioma inglês', null !== $achou && 'en' === $achou['lang'], $achou['lang'] ?? '?' );

echo "\n== Remetente ==\n";
STCMS_Emails::init();
$GLOBALS['__o']['stcms_options'] = array( 'site' => array( 'title' => 'Studio Tabi', 'from_email' => 'contato@studiotabi.com.br' ) );
$de = remetente_atual();
ok( 'usa o endereço configurado', 'contato@studiotabi.com.br' === $de, $de );
ok( 'não contém wordpress', false === stripos( $de, 'wordpress' ), $de );
ok( 'não contém cms', false === stripos( $de, 'cms' ), $de );
ok( 'não é do servidor de hospedagem', false === stripos( $de, 'main-hosting' ), $de );
ok( 'nome do remetente é o do site', 'Studio Tabi' === nome_remetente_atual(), nome_remetente_atual() );

$GLOBALS['__o']['stcms_options'] = array( 'site' => array( 'title' => 'Studio Tabi', 'from_email' => '', 'form_email' => 'oi@studiotabi.com.br' ) );
ok( 'sem remetente, cai no e-mail que recebe', 'oi@studiotabi.com.br' === remetente_atual(), remetente_atual() );

$GLOBALS['__o']['stcms_options'] = array( 'site' => array( 'title' => 'Studio Tabi' ) );
$de = remetente_atual();
ok( 'sem nada configurado, monta pelo domínio do site', 'contato@studiotabi.com.br' === $de, $de );
ok( 'e ainda assim sem wordpress nem cms', false === stripos( $de, 'wordpress' ) && false === stripos( $de, 'cms' ), $de );

$GLOBALS['__o']['stcms_options'] = array( 'site' => array( 'from_email' => 'isso não é e-mail' ) );
ok( 'endereço inválido não é usado', 'contato@studiotabi.com.br' === remetente_atual(), remetente_atual() );

echo "\n== Disparo ao publicar artigo ==\n";
$GLOBALS['__o']['stcms_options'] = array();
$GLOBALS['__pmeta'] = array();
$GLOBALS['__o']['stcms_newsletter'] = array();
STCMS_Emails::inscrever( 'pt1@exemplo.com', 'pt' );
STCMS_Emails::inscrever( 'en1@exemplo.com', 'en' );
$GLOBALS['__o']['stcms_campanha'] = array();

$artigo = new stdClass();
$artigo->ID = 900; $artigo->post_type = 'post'; $artigo->post_status = 'publish';
$artigo->post_name = 'tipografia-que-vende'; $artigo->post_title = 'Tipografia que vende';
$artigo->post_excerpt = 'Como a tipografia muda a decisão.'; $artigo->post_content = '<p>Texto.</p>';
$GLOBALS['__posts'][900] = $artigo;
$GLOBALS['__pmeta'][900] = array( 'stcms_en_title' => 'Typography that sells', 'stcms_en_excerpt' => 'How type shapes the decision.' );

// Desligado por padrão: publicar não pode disparar nada.
limpar();
STCMS_Campanhas::ao_publicar( 'publish', 'draft', $artigo );
ok( 'desligado, publicar não enfileira', 'rascunho' === STCMS_Campanhas::campanha()['estado'], STCMS_Campanhas::campanha()['estado'] );

update_option( 'stcms_auto_newsletter', '1' );
ok( 'interruptor lê ligado', true === STCMS_Campanhas::auto_ligado() );

STCMS_Campanhas::ao_publicar( 'publish', 'draft', $artigo );
$c = STCMS_Campanhas::campanha();
ok( 'publicar enfileira a lista', 2 === $c['total'], (string) $c['total'] );
ok( 'guarda o artigo de origem', 900 === (int) $c['post_id'], (string) $c['post_id'] );
ok( 'agendou a continuação', false !== wp_next_scheduled( 'stcms_campanha_tick' ) );

limpar();
lote();
ok( 'os dois receberam', 2 === count( $GLOBALS['__enviados'] ), (string) count( $GLOBALS['__enviados'] ) );
$em_pt = ultimo_para( 'pt1@exemplo.com' );
$em_en = ultimo_para( 'en1@exemplo.com' );
ok( 'assunto em português traz o título', false !== strpos( $em_pt['assunto'], 'Tipografia que vende' ), $em_pt['assunto'] );
ok( 'assunto em inglês usa a tradução do post', false !== strpos( $em_en['assunto'], 'Typography that sells' ), $em_en['assunto'] );
ok( 'corpo em inglês traz o resumo traduzido', false !== strpos( $em_en['corpo'], 'How type shapes' ) );
ok( 'link em português vai para /blog/', false !== strpos( $em_pt['corpo'], '/blog/tipografia-que-vende' ) );
ok( 'link em inglês vai para /en/blog/', false !== strpos( $em_en['corpo'], '/en/blog/tipografia-que-vende' ) );
ok( 'cada um com o seu descadastro', false !== strpos( $em_pt['corpo'], '/unsubscribe' ) );

echo "\n== Não dispara duas vezes ==\n";
$GLOBALS['__o']['stcms_campanha']['estado'] = 'rascunho';
limpar();
STCMS_Campanhas::ao_publicar( 'publish', 'draft', $artigo );
ok( 'mesmo artigo não reenfileira', 'rascunho' === STCMS_Campanhas::campanha()['estado'], STCMS_Campanhas::campanha()['estado'] );
STCMS_Campanhas::ao_publicar( 'publish', 'publish', $artigo );
ok( 'editar artigo publicado não dispara', 'rascunho' === STCMS_Campanhas::campanha()['estado'] );

echo "\n== Só artigos do blog ==\n";
$pagina = clone $artigo;
$pagina->ID = 901; $pagina->post_type = 'page'; $pagina->post_name = 'sobre';
$GLOBALS['__posts'][901] = $pagina;
STCMS_Campanhas::ao_publicar( 'publish', 'draft', $pagina );
ok( 'página não dispara newsletter', 'rascunho' === STCMS_Campanhas::campanha()['estado'] );
$servico = clone $artigo;
$servico->ID = 902; $servico->post_type = 'st_service';
$GLOBALS['__posts'][902] = $servico;
STCMS_Campanhas::ao_publicar( 'publish', 'draft', $servico );
ok( 'serviço não dispara newsletter', 'rascunho' === STCMS_Campanhas::campanha()['estado'] );

echo "\n== Não atropela um envio em andamento ==\n";
$novo_artigo = clone $artigo;
$novo_artigo->ID = 903; $novo_artigo->post_name = 'outro';
$GLOBALS['__posts'][903] = $novo_artigo;
$GLOBALS['__o']['stcms_campanha']['estado'] = 'enviando';
$GLOBALS['__o']['stcms_campanha']['post_id'] = 900;
STCMS_Campanhas::ao_publicar( 'publish', 'draft', $novo_artigo );
ok( 'espera o envio atual terminar', 900 === (int) STCMS_Campanhas::campanha()['post_id'], (string) STCMS_Campanhas::campanha()['post_id'] );
ok( 'e não marca o artigo como enviado', '' === get_post_meta( 903, '_stcms_newsletter_enviada', true ) );

echo "\n== Sem inscritos não enfileira ==\n";
$GLOBALS['__o']['stcms_newsletter'] = array();
$GLOBALS['__o']['stcms_campanha']['estado'] = 'rascunho';
$so_agora = clone $artigo;
$so_agora->ID = 904; $so_agora->post_name = 'terceiro';
$GLOBALS['__posts'][904] = $so_agora;
STCMS_Campanhas::ao_publicar( 'publish', 'draft', $so_agora );
ok( 'lista vazia não vira campanha', 'rascunho' === STCMS_Campanhas::campanha()['estado'] );
ok( 'nem marca o artigo', '' === get_post_meta( 904, '_stcms_newsletter_enviada', true ) );

echo "\n== Continuação sem o painel aberto ==\n";
$GLOBALS['__o']['stcms_newsletter'] = array();
foreach ( range( 1, 20 ) as $i ) { STCMS_Emails::inscrever( "c{$i}@exemplo.com", 'pt' ); }
$GLOBALS['__o']['stcms_campanha'] = array( 'assunto' => 'A', 'titulo' => 'B', 'texto' => 'C', 'estado' => 'rascunho' );
try { STCMS_Campanhas::iniciar(); } catch ( Redirecionou $e ) {}
limpar();
$GLOBALS['__cron'] = array();
// Orçamento zerado: cada visita manda exatamente um lote, que é o caminho em
// que o reagendamento importa.
add_filter( 'stcms_cron_segundos', function () { return 0; } );
STCMS_Campanhas::processar_tick();
ok( 'o cron envia um lote', 15 === count( $GLOBALS['__enviados'] ), (string) count( $GLOBALS['__enviados'] ) );
ok( 'e reagenda porque sobrou fila', false !== wp_next_scheduled( 'stcms_campanha_tick' ) );
$GLOBALS['__cron'] = array();
STCMS_Campanhas::processar_tick();
ok( 'termina no lote seguinte', 'concluido' === STCMS_Campanhas::campanha()['estado'] );
ok( 'e não reagenda mais', false === wp_next_scheduled( 'stcms_campanha_tick' ) );
$GLOBALS['__filtros']['stcms_cron_segundos'] = array();

echo "\n== Cron: aproveita a visita inteira ==\n";
$GLOBALS['__o']['stcms_newsletter'] = array();
foreach ( range( 1, 60 ) as $i ) { STCMS_Emails::inscrever( "m{$i}@exemplo.com", 'pt' ); }
$GLOBALS['__o']['stcms_campanha'] = array( 'assunto' => 'A', 'titulo' => 'B', 'texto' => 'C', 'estado' => 'rascunho' );
try { STCMS_Campanhas::iniciar(); } catch ( Redirecionou $e ) {}
limpar();
$GLOBALS['__cron'] = array();
STCMS_Campanhas::processar_tick();
ok( 'uma visita do cron manda mais que um lote', count( $GLOBALS['__enviados'] ) === 60, (string) count( $GLOBALS['__enviados'] ) );
ok( 'e conclui a campanha', 'concluido' === STCMS_Campanhas::campanha()['estado'] );
ok( 'não deixa agendamento pendente', false === wp_next_scheduled( 'stcms_campanha_tick' ) );

echo "\n== Registro de execução ==\n";
ok( 'grava quando rodou', (int) get_option( 'stcms_cron_ultimo' ) > 0 );
$s = STCMS_Campanhas::saude_cron();
ok( 'saúde reporta o último', $s['ultimo'] > 0 );
ok( 'nada parado quando não há envio', false === $s['parado'] );

echo "\n== Detecta fila parada ==\n";
$GLOBALS['__o']['stcms_campanha']['estado'] = 'enviando';
$GLOBALS['__o']['stcms_campanha']['fila']   = array_slice( STCMS_Emails::lista(), 0, 5 );
$GLOBALS['__cron']['stcms_campanha_tick']   = time() - 600;
update_option( 'stcms_cron_ultimo', time() - 600 );
$s = STCMS_Campanhas::saude_cron();
ok( 'acusa parada com envio pendente e cron atrasado', true === $s['parado'] );

update_option( 'stcms_cron_ultimo', time() );
$s = STCMS_Campanhas::saude_cron();
ok( 'não acusa se acabou de rodar', false === $s['parado'] );

echo "\n== Processar agora ==\n";
$GLOBALS['__o']['stcms_campanha']['estado'] = 'enviando';
$GLOBALS['__o']['stcms_campanha']['fila']   = array_slice( STCMS_Emails::lista(), 0, 5 );
limpar();
try { STCMS_Campanhas::processar_agora(); } catch ( Redirecionou $e ) { $destino = $e->getMessage(); }
ok( 'envia o que estava parado', 5 === count( $GLOBALS['__enviados'] ), (string) count( $GLOBALS['__enviados'] ) );
ok( 'e avisa na volta', false !== strpos( $destino, 'processado' ), $destino );

echo "\n== Botão de envio dentro do artigo ==\n";
$GLOBALS['__o']['stcms_options']    = array();
$GLOBALS['__pmeta']                 = array();
$GLOBALS['__cron']                  = array();
$GLOBALS['__o']['stcms_newsletter'] = array();
$GLOBALS['__o']['stcms_campanha']   = array();
update_option( 'stcms_auto_newsletter', '' );
STCMS_Emails::inscrever( 'pt9@exemplo.com', 'pt' );
STCMS_Emails::inscrever( 'en9@exemplo.com', 'en' );

$manual = new stdClass();
$manual->ID = 910; $manual->post_type = 'post'; $manual->post_status = 'publish';
$manual->post_name = 'manual'; $manual->post_title = 'Envio manual';
$manual->post_excerpt = 'Resumo do artigo.'; $manual->post_content = '<p>Texto.</p>';
$GLOBALS['__posts'][910] = $manual;

$GLOBALS['__metabox'] = array();
STCMS_Campanhas::metabox();
ok( 'registra a caixa no artigo', 1 === count( $GLOBALS['__metabox'] ) && 'post' === $GLOBALS['__metabox'][0]['tela'] );
ok( 'fica na barra lateral', 'side' === $GLOBALS['__metabox'][0]['ctx'] );

$html = metabox_html( $manual );
ok( 'oferece o botão de envio', false !== strpos( $html, 'Enviar para a lista' ) );
ok( 'mostra quantos vão receber', false !== strpos( $html, '<strong>2</strong> inscritos' ) );
ok( 'o link carrega nonce', false !== strpos( $html, '_wpnonce=' ) );
ok( 'o link aponta para a ação certa', false !== strpos( $html, 'action=stcms_campanha_post&#038;post=910' ) || false !== strpos( $html, 'action=stcms_campanha_post&post=910' ) );
ok( 'pede confirmação antes', false !== strpos( $html, 'onclick="return confirm(' ) );
ok( 'avisa que o automático está desligado', false !== strpos( $html, 'automático está desligado' ) );

limpar();
$destino = disparar_post( 910 );
$c = STCMS_Campanhas::campanha();
ok( 'botão enfileira a lista', 'enviando' === $c['estado'] && 2 === (int) $c['total'], $c['estado'] . '/' . $c['total'] );
ok( 'guarda o artigo de origem', 910 === (int) $c['post_id'], (string) $c['post_id'] );
ok( 'agenda a continuação', false !== wp_next_scheduled( 'stcms_campanha_tick' ) );
ok( 'volta para a tela do artigo', false !== strpos( $destino, 'post.php?post=910' ), $destino );
ok( 'com aviso de iniciado', false !== strpos( $destino, 'stcms_aviso=iniciado' ), $destino );

lote();
$pt = ultimo_para( 'pt9@exemplo.com' );
$en = ultimo_para( 'en9@exemplo.com' );
ok( 'os dois receberam', 2 === count( $GLOBALS['__enviados'] ), (string) count( $GLOBALS['__enviados'] ) );
ok( 'português usa o título do artigo', false !== strpos( (string) $pt['assunto'], 'Envio manual' ), (string) $pt['assunto'] );
ok( 'inglês cai no /en/blog/', false !== strpos( (string) $en['corpo'], '/en/blog/manual' ) );

echo "\n== O botão respeita as mesmas travas ==\n";
$GLOBALS['__o']['stcms_campanha']['estado'] = 'rascunho';
ok( 'segundo clique não reenvia', 'ja_enviado' === STCMS_Campanhas::enfileirar_post( 910 ) );
$html = metabox_html( $manual );
ok( 'a caixa passa a mostrar a data do envio', false !== strpos( $html, 'Já enviado em' ) );
ok( 'e troca o botão por “Enviar de novo”', false !== strpos( $html, 'Enviar de novo' ) );
ok( 'avisando que quem já recebeu recebe outra vez', false !== strpos( $html, 'já recebeu vai receber outra vez' ) );

limpar();
disparar_post( 910, true );
ok( 'forçar reenvia de propósito', 'enviando' === STCMS_Campanhas::campanha()['estado'] );

$GLOBALS['__o']['stcms_campanha']['estado']  = 'enviando';
$GLOBALS['__o']['stcms_campanha']['post_id'] = 910;
$outro = clone $manual;
$outro->ID = 911; $outro->post_name = 'outro-manual';
$GLOBALS['__posts'][911] = $outro;
ok( 'não atropela envio em andamento', 'ocupado' === STCMS_Campanhas::enfileirar_post( 911 ) );
ok( 'nem marca o artigo', '' === get_post_meta( 911, '_stcms_newsletter_enviada', true ) );
$html = metabox_html( $outro );
ok( 'a caixa explica a espera', false !== strpos( $html, 'outro envio em andamento' ) );
$html = metabox_html( $manual );
ok( 'no artigo em envio mostra o progresso', false !== strpos( $html, 'Enviando agora' ) );

$GLOBALS['__o']['stcms_campanha']['estado'] = 'rascunho';
$rascunho = clone $manual;
$rascunho->ID = 912; $rascunho->post_status = 'draft'; $rascunho->post_name = 'rascunho';
$GLOBALS['__posts'][912] = $rascunho;
ok( 'rascunho não vai para a lista', 'nao_publicado' === STCMS_Campanhas::enfileirar_post( 912 ) );
ok( 'a caixa pede para publicar antes', false !== strpos( metabox_html( $rascunho ), 'Publique o artigo' ) );

$pagina2 = clone $manual;
$pagina2->ID = 913; $pagina2->post_type = 'page';
$GLOBALS['__posts'][913] = $pagina2;
ok( 'página não vira newsletter', 'post_invalido' === STCMS_Campanhas::enfileirar_post( 913 ) );
ok( 'id inexistente também não', 'post_invalido' === STCMS_Campanhas::enfileirar_post( 99999 ) );

$GLOBALS['__o']['stcms_newsletter'] = array();
$vazio = clone $manual;
$vazio->ID = 914; $vazio->post_name = 'sem-lista';
$GLOBALS['__posts'][914] = $vazio;
ok( 'lista vazia não enfileira', 'sem_lista' === STCMS_Campanhas::enfileirar_post( 914 ) );
ok( 'nem marca o artigo', '' === get_post_meta( 914, '_stcms_newsletter_enviada', true ) );
ok( 'a caixa diz que ninguém se inscreveu', false !== strpos( metabox_html( $vazio ), 'Ninguém inscrito' ) );

STCMS_Emails::inscrever( 'pt10@exemplo.com', 'pt' );
$GLOBALS['__o']['stcms_options'] = array( 'emails' => array( 'auto_assunto' => '' ) );
ok( 'modelo em branco não dispara e-mail vazio', 'sem_modelo' === STCMS_Campanhas::enfileirar_post( 914 ) );
ok( 'nem marca o artigo', '' === get_post_meta( 914, '_stcms_newsletter_enviada', true ) );
$GLOBALS['__o']['stcms_options'] = array();

echo "\n== Avisos na tela do artigo ==\n";
ok( 'iniciado aparece como sucesso', false !== strpos( aviso_html( 'iniciado' ), 'notice-success' ) );
ok( 'sem lista aparece como erro', false !== strpos( aviso_html( 'sem_lista' ), 'notice-error' ) );
ok( 'já enviado é só aviso', false !== strpos( aviso_html( 'ja_enviado' ), 'notice-warning' ) );
ok( 'chave desconhecida não imprime nada', '' === aviso_html( 'inventado' ) );
$GLOBALS['__screen'] = null;
$_GET = array( 'stcms_aviso' => 'iniciado' );
ob_start();
STCMS_Campanhas::aviso_no_post();
ok( 'fora da tela de artigo não imprime', '' === ob_get_clean() );
$_GET = array();

echo "\n$ok passaram, $ko falharam\n";
exit( $ko ? 1 : 0 );
