<?php
error_reporting( E_ALL & ~E_DEPRECATED );
define( 'ABSPATH', __DIR__ . '/' );
define( 'HOUR_IN_SECONDS', 3600 );
$P = dirname( __DIR__ ) . '/wordpress/wp-content/plugins/studio-tabi-cms';

$GLOBALS['__o'] = array();
$GLOBALS['__meta'] = array();
$GLOBALS['__posts'] = array();

function get_option( $k, $d = false ) { return $GLOBALS['__o'][ $k ] ?? $d; }
function update_option( $k, $v ) { $GLOBALS['__o'][ $k ] = $v; return true; }
function add_option( $k, $v ) { return update_option( $k, $v ); }
function get_transient( $k ) { return false; }
function set_transient( $k, $v, $t ) { return true; }
function add_action( ...$a ) { return true; }
function add_filter( ...$a ) { return true; }
function remove_filter( ...$a ) { return true; }
function register_rest_route( ...$a ) { return true; }
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_textarea_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_title( $s ) { return strtolower( preg_replace( '/[^a-z0-9]+/i', '-', (string) $s ) ); }
function sanitize_key( $s ) { return strtolower( preg_replace( '/[^a-z0-9_]/i', '', (string) $s ) ); }
function sanitize_email( $s ) { return $s; }
function is_email( $s ) { return (bool) filter_var( $s, FILTER_VALIDATE_EMAIL ); }
function esc_html( $s ) { return $s; }
function esc_attr( $s ) { return $s; }
function esc_url_raw( $u, $p = null ) { return $u; }
function wp_unslash( $v ) { return $v; }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
function untrailingslashit( $s ) { return rtrim( (string) $s, '/' ); }
function get_home_url() { return 'https://studiotabi.com.br'; }
function get_bloginfo( $x = '' ) { return 'Studio Tabi'; }
function apply_filters( $t, $v ) { return $v; }
function wp_strip_all_tags( $s ) { return strip_tags( (string) $s ); }
function wp_trim_words( $s, $n = 55, $more = '…' ) {
	$p = preg_split( '/\s+/', trim( (string) $s ) );
	return count( $p ) <= $n ? implode( ' ', $p ) : implode( ' ', array_slice( $p, 0, $n ) ) . $more;
}
function get_post_meta( $id, $k = '', $single = true ) {
	$v = $GLOBALS['__meta'][ $id ][ $k ] ?? '';
	return $single ? $v : ( '' === $v ? array() : array( $v ) );
}
function update_post_meta( $id, $k, $v ) { $GLOBALS['__meta'][ $id ][ $k ] = $v; return true; }
function delete_post_meta( $id, $k ) { unset( $GLOBALS['__meta'][ $id ][ $k ] ); return true; }
function get_post( $id ) { return $GLOBALS['__posts'][ $id ] ?? null; }
function get_page_by_path( $slug, $o = null, $tipo = null ) {
	foreach ( $GLOBALS['__posts'] as $p ) {
		if ( $p->post_name === $slug && ( null === $tipo || $p->post_type === $tipo ) ) { return $p; }
	}
	return null;
}
function get_the_category( $id ) { return $GLOBALS['__cats'][ $id ] ?? array(); }
function get_the_tags( $id ) { return array(); }
function wp_get_post_categories( $id ) { return array( 1 ); }
function get_categories( $a = array() ) { return $GLOBALS['__todascats'] ?? array(); }
function has_excerpt( $p ) { return false; }
function get_the_excerpt( $p ) { return ''; }
function get_the_date( $f, $p ) { return '2026-01-15'; }
function get_the_author_meta( $c, $id ) { return 'Equipe Tabi'; }
function get_the_post_thumbnail_url( $p, $s = null ) { return ''; }
function get_avatar_url( $id, $a = array() ) { return ''; }
function wp_reset_postdata() { return true; }
function get_posts( $a ) { return array(); }
function wp_get_attachment_image_url( $i, $s = null ) { return ''; }
function get_the_title( $p ) { return is_object( $p ) ? $p->post_title : ''; }
function wp_mail( ...$a ) { return true; }
if ( ! defined( 'OBJECT' ) ) { define( 'OBJECT', 'OBJECT' ); }

class WP_REST_Response { public $data; public $status; function __construct( $d, $s = 200 ) { $this->data = $d; $this->status = $s; } }
class WP_REST_Request {
	private $p;
	function __construct( $p = array() ) { $this->p = $p; }
	function get_param( $k ) { return $this->p[ $k ] ?? null; }
	function get_params() { return $this->p; }
	function get_json_params() { return $this->p; }
}
class WP_Query {
	public $posts = array();
	public $found_posts = 0;
	public $max_num_pages = 1;
	function __construct( $args ) {
		$todos = array_values( $GLOBALS['__posts'] );
		$todos = array_filter( $todos, function ( $p ) use ( $args ) {
			if ( ( $args['post_type'] ?? 'post' ) !== $p->post_type ) { return false; }
			if ( 'publish' !== $p->post_status ) { return false; }
			if ( ! empty( $args['post__not_in'] ) && in_array( $p->ID, $args['post__not_in'], true ) ) { return false; }
			if ( ! empty( $args['s'] ) && false === stripos( $p->post_title . ' ' . $p->post_content, $args['s'] ) ) { return false; }
			return true;
		} );
		$this->found_posts   = count( $todos );
		$per                 = $args['posts_per_page'] ?? 9;
		$this->max_num_pages = max( 1, (int) ceil( $this->found_posts / $per ) );
		$this->posts         = array_slice( array_values( $todos ), ( ( $args['paged'] ?? 1 ) - 1 ) * $per, $per );
	}
}

require_once "$P/includes/defaults.php";
require_once "$P/includes/class-options.php";
require_once "$P/includes/class-traducao.php";
require_once "$P/includes/class-rest.php";

$ok = 0; $ko = 0;
function ok( $r, $c, $v = '' ) { global $ok, $ko; $c ? $ok++ : $ko++; echo '  ' . ( $c ? '✓' : '✗' ) . "  $r" . ( '' !== $v ? ": $v" : '' ) . "\n"; }

function post_falso( $id, $slug, $titulo, $conteudo ) {
	$p = new stdClass();
	$p->ID = $id; $p->post_name = $slug; $p->post_title = $titulo; $p->post_content = $conteudo;
	$p->post_type = 'post'; $p->post_status = 'publish'; $p->post_author = 1; $p->post_excerpt = '';
	return $p;
}

$c = new stdClass(); $c->name = 'Design'; $c->slug = 'design'; $c->count = 2;
$GLOBALS['__todascats'] = array( $c );
$GLOBALS['__posts'] = array(
	10 => post_falso( 10, 'tipografia-que-vende', 'Tipografia que vende', '<p>Um texto sobre tipografia e conversão.</p>' ),
	11 => post_falso( 11, 'cor-e-confianca', 'Cor e confiança', '<p>Como a paleta muda a percepção de valor.</p>' ),
);
$GLOBALS['__cats'] = array( 10 => array( $c ), 11 => array( $c ) );

// Tradução guardada no próprio post, modelo atual do plugin.
update_post_meta( 10, 'stcms_en_title', 'Typography that sells' );
update_post_meta( 10, 'stcms_en_body', '<p>A piece about typography and conversion.</p>' );
update_post_meta( 10, 'stcms_en_excerpt', 'How type shapes the decision.' );

echo "== Listagem do blog ==\n";
$r = STCMS_Rest::get_posts_list( new WP_REST_Request( array( 'page' => 1, 'per_page' => 9 ) ) );
ok( 'PT responde 200', 200 === $r->status, (string) $r->status );
ok( 'PT devolve os dois artigos', 2 === count( $r->data['items'] ?? array() ), (string) count( $r->data['items'] ?? array() ) );
ok( 'PT com o título em português', 'Tipografia que vende' === ( $r->data['items'][0]['title'] ?? '' ), $r->data['items'][0]['title'] ?? '(vazio)' );

$r = STCMS_Rest::get_posts_list( new WP_REST_Request( array( 'page' => 1, 'per_page' => 9, 'lang' => 'en' ) ) );
ok( 'EN responde 200', 200 === $r->status, (string) $r->status );
ok( 'EN devolve os dois artigos', 2 === count( $r->data['items'] ?? array() ), (string) count( $r->data['items'] ?? array() ) );
ok( 'EN usa a tradução do post', 'Typography that sells' === ( $r->data['items'][0]['title'] ?? '' ), $r->data['items'][0]['title'] ?? '(vazio)' );
ok( 'EN sem tradução herda o português', 'Cor e confiança' === ( $r->data['items'][1]['title'] ?? '' ), $r->data['items'][1]['title'] ?? '(vazio)' );
ok( 'paginação preenchida', ( $r->data['total'] ?? 0 ) === 2 && ( $r->data['totalPages'] ?? 0 ) >= 1, json_encode( array( $r->data['total'] ?? null, $r->data['totalPages'] ?? null ) ) );

echo "\n== Artigo ==\n";
$r = STCMS_Rest::get_single_post( new WP_REST_Request( array( 'slug' => 'tipografia-que-vende' ) ) );
ok( 'PT responde 200', 200 === $r->status, (string) $r->status );
ok( 'PT com o título em português', 'Tipografia que vende' === ( $r->data['title'] ?? '' ), $r->data['title'] ?? '' );
ok( 'PT com o conteúdo em português', false !== strpos( (string) ( $r->data['content'] ?? '' ), 'tipografia e conversão' ) );

$r = STCMS_Rest::get_single_post( new WP_REST_Request( array( 'slug' => 'tipografia-que-vende', 'lang' => 'en' ) ) );
ok( 'EN responde 200', 200 === $r->status, (string) $r->status );
ok( 'EN com o título traduzido', 'Typography that sells' === ( $r->data['title'] ?? '' ), $r->data['title'] ?? '' );
ok( 'EN com o conteúdo traduzido', false !== strpos( (string) ( $r->data['content'] ?? '' ), 'typography and conversion' ), substr( (string) ( $r->data['content'] ?? '' ), 0, 40 ) );
ok( 'EN traz relacionados', isset( $r->data['related'] ) && is_array( $r->data['related'] ) );

$r = STCMS_Rest::get_single_post( new WP_REST_Request( array( 'slug' => 'cor-e-confianca', 'lang' => 'en' ) ) );
ok( 'artigo sem tradução ainda abre em EN', 200 === $r->status && 'Cor e confiança' === ( $r->data['title'] ?? '' ), $r->data['title'] ?? '' );

$r = STCMS_Rest::get_single_post( new WP_REST_Request( array( 'slug' => 'nao-existe', 'lang' => 'en' ) ) );
ok( 'slug inexistente devolve 404', 404 === $r->status, (string) $r->status );

echo "\n== Categorias ==\n";
$r = STCMS_Rest::get_categories_list();
ok( 'categorias respondem 200', 200 === $r->status, (string) $r->status );
ok( 'categoria presente', 'design' === ( $r->data[0]['slug'] ?? '' ), $r->data[0]['slug'] ?? '' );

echo "\n== Busca e paginação ==\n";
$r = STCMS_Rest::get_posts_list( new WP_REST_Request( array( 'search' => 'Cor', 'lang' => 'en' ) ) );
ok( 'busca filtra em EN', 1 === count( $r->data['items'] ?? array() ), (string) count( $r->data['items'] ?? array() ) );
$r = STCMS_Rest::get_posts_list( new WP_REST_Request( array( 'page' => 1, 'per_page' => 1, 'lang' => 'en' ) ) );
ok( 'per_page respeitado', 1 === count( $r->data['items'] ?? array() ), (string) count( $r->data['items'] ?? array() ) );
ok( 'totalPages calculado', 2 === ( $r->data['totalPages'] ?? 0 ), (string) ( $r->data['totalPages'] ?? 0 ) );

echo "\n$ok passaram, $ko falharam\n";
exit( $ko ? 1 : 0 );
