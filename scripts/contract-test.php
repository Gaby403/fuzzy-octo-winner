<?php
/**
 * Contract test (sem WordPress).
 *
 * Stuba as poucas funções do WordPress usadas pela camada REST e executa
 * de verdade STCMS_Rest::get_content(), validando que o JSON produzido tem
 * exatamente o formato que o front-end React consome (SiteContent).
 *
 * Uso:  php scripts/contract-test.php
 */

error_reporting( E_ALL & ~E_DEPRECATED );

define( 'ABSPATH', __DIR__ . '/' );

$PLUGIN = __DIR__ . '/../wordpress/wp-content/plugins/studio-tabi-cms';

/* ------------------------------------------------- WordPress stubs (mínimos) */

$GLOBALS['__options'] = array();
$GLOBALS['__posts']   = array(); // type => [ {ID, post_title, post_content, menu_order, post_name, post_status, post_type} ]
$GLOBALS['__meta']    = array(); // ID => [ key => value ]
$GLOBALS['__thumbs']  = array(); // ID => url
$GLOBALS['__next_id'] = 1;

function get_option( $k, $d = false ) { return $GLOBALS['__options'][ $k ] ?? $d; }
function update_option( $k, $v ) { $GLOBALS['__options'][ $k ] = $v; return true; }
function add_option( $k, $v ) { $GLOBALS['__options'][ $k ] = $v; return true; }

function get_post_meta( $id, $key, $single = true ) {
	$v = $GLOBALS['__meta'][ $id ][ $key ] ?? '';
	return $v;
}
function update_post_meta( $id, $key, $value ) { $GLOBALS['__meta'][ $id ][ $key ] = $value; return true; }

function get_posts( $args ) {
	$type = $args['post_type'];
	$rows = $GLOBALS['__posts'][ $type ] ?? array();
	usort( $rows, fn( $a, $b ) => $a->menu_order <=> $b->menu_order );
	return $rows;
}
function get_the_title( $p ) { return is_object( $p ) ? $p->post_title : ''; }
function get_the_post_thumbnail_url( $p, $size = 'full' ) { return $GLOBALS['__thumbs'][ $p->ID ] ?? ''; }
function wp_get_attachment_image_url( $id, $size = 'full' ) { return $id ? "https://cms.local/wp-content/uploads/img-$id.png" : false; }
function get_page_by_path( $slug, $output = OBJECT, $type = 'page' ) { return null; }

function wp_strip_all_tags( $s ) { return trim( preg_replace( '/<[^>]*>/', '', (string) $s ) ); }
function apply_filters( $tag, $value ) { return $value; }
function sanitize_text_field( $s ) { return is_string( $s ) ? trim( $s ) : $s; }
function sanitize_textarea_field( $s ) { return is_string( $s ) ? trim( $s ) : $s; }
function sanitize_email( $s ) { return $s; }
function esc_html( $s ) { return $s; }
function esc_attr( $s ) { return $s; }

if ( ! defined( 'OBJECT' ) ) { define( 'OBJECT', 'OBJECT' ); }

class WP_REST_Response {
	public $data;
	public $status;
	public function __construct( $data, $status = 200 ) { $this->data = $data; $this->status = $status; }
}
class WP_REST_Request {}
class WP_Query {
	public $found_posts = 0;
	public function __construct( $args ) {
		$type = $args['post_type'] ?? '';
		$this->found_posts = count( $GLOBALS['__posts'][ $type ] ?? array() );
	}
}

/* ------------------------------------------------ carrega o código real do plugin */

require_once "$PLUGIN/includes/defaults.php";
require_once "$PLUGIN/includes/class-options.php";
require_once "$PLUGIN/includes/class-rest.php";

/* ---------------------------------------------------------- semeia como a ativação */

function stub_insert( $type, $title, $content, $order, $meta = array() ) {
	$id = $GLOBALS['__next_id']++;
	$p  = (object) array(
		'ID'           => $id,
		'post_title'   => $title,
		'post_content' => $content,
		'menu_order'   => $order,
		'post_name'    => sanitize_title_stub( $title ),
		'post_status'  => 'publish',
		'post_type'    => $type,
	);
	$GLOBALS['__posts'][ $type ][] = $p;
	$GLOBALS['__meta'][ $id ]      = $meta;
	return $id;
}
function sanitize_title_stub( $t ) { return strtolower( preg_replace( '/[^a-z0-9]+/i', '-', $t ) ); }

update_option( STCMS_Options::OPTION, stcms_default_options() );

$order = 0;
foreach ( stcms_default_services() as $s ) {
	stub_insert( 'st_service', $s['title'], $s['body'], $order++, array( 'stcms_num' => $s['num'] ) );
}
$order = 0;
foreach ( stcms_default_faq() as $f ) {
	stub_insert( 'st_faq', $f['q'], $f['a'], $order++ );
}
$order = 0;
foreach ( stcms_default_projects() as $p ) {
	stub_insert(
		'st_project', $p['name'], '', $order++,
		array(
			'stcms_num'       => $p['id'],
			'stcms_name'      => $p['name'],
			'stcms_category'  => $p['category'],
			'stcms_year'      => $p['year'],
			'stcms_bg'        => $p['bg'],
			'stcms_accent'    => $p['accent'],
			'stcms_featured'  => $p['featured'] ? '1' : '',
			'stcms_client'    => $p['client'],
			'stcms_duration'  => $p['duration'],
			'stcms_challenge' => $p['challenge'],
			'stcms_solution'  => $p['solution'],
			'stcms_scope'     => array_map( fn( $v ) => array( 'item' => $v ), $p['scope'] ),
			'stcms_mockup'    => array_map( fn( $v ) => array( 'item' => $v ), $p['mockup_lines'] ),
			'stcms_results'   => $p['results'],
		)
	);
}

/* --------------------------------------------------------------- executa e valida */

$resp = STCMS_Rest::get_content();
$data = $resp->data;

$fail = 0;
function check( $cond, $msg ) {
	global $fail;
	echo ( $cond ? "  ✓ " : "  ✗ " ) . $msg . "\n";
	if ( ! $cond ) { $fail++; }
}

echo "\nContrato backend → frontend (SiteContent):\n";
foreach ( array( 'site', 'hero', 'about', 'services', 'projects', 'faq', 'footer', 'pages' ) as $k ) {
	check( array_key_exists( $k, $data ), "chave raiz \"$k\" presente" );
}

check( $data['site']['title'] === 'Studio Tabi', 'site.title' );
check( is_array( $data['hero']['titleLines'] ) && count( $data['hero']['titleLines'] ) === 3, 'hero.titleLines (3 linhas)' );
check( count( $data['about']['stats'] ) === 4 && isset( $data['about']['stats'][0]['numeric'], $data['about']['stats'][0]['suffix'], $data['about']['stats'][0]['label'] ), 'about.stats [numeric/suffix/label]' );
check( count( $data['about']['pillars'] ) === 4, 'about.pillars (4)' );
check( count( $data['services'] ) === 6 && isset( $data['services'][0]['num'], $data['services'][0]['title'], $data['services'][0]['body'] ), 'services [num/title/body]' );
check( $data['services'][0]['num'] === '01', 'services[0].num = 01 (ordem preservada)' );

check( count( $data['projects'] ) === 4, 'projects (4)' );
$pr = $data['projects'][0];
check( isset( $pr['id'], $pr['name'], $pr['category'], $pr['year'], $pr['bg'], $pr['accent'], $pr['featured'], $pr['detail'] ), 'project top-level' );
check( $pr['featured'] === true, 'project.featured é boolean' );
check( isset( $pr['detail']['client'], $pr['detail']['scope'], $pr['detail']['duration'], $pr['detail']['challenge'], $pr['detail']['solution'], $pr['detail']['results'], $pr['detail']['mockupLines'] ), 'project.detail completo' );
check( $pr['detail']['scope'] === array( 'Identidade Visual', 'UI/UX Design', 'Design System' ), 'project.detail.scope achatado corretamente' );
check( isset( $pr['detail']['results'][0]['label'], $pr['detail']['results'][0]['value'] ), 'project.detail.results [label/value]' );
check( $pr['detail']['mockupLines'] === array( 'DASHBOARD', 'PORTFÓLIO', 'ANÁLISE', 'RELATÓRIOS' ), 'project.detail.mockupLines achatado' );

check( count( $data['faq'] ) === 6 && isset( $data['faq'][0]['q'], $data['faq'][0]['a'] ), 'faq [q/a]' );
check( isset( $data['footer']['tagline'], $data['footer']['email'], $data['footer']['phone'], $data['footer']['city'] ), 'footer completo' );

// Simula uma edição no backend e confirma que reflete na saída da API.
$opts = STCMS_Options::get();
$opts['hero']['title_lines'] = array( 'NOVO', 'TÍTULO' );
$opts['footer']['email']     = 'novo@studiotabi.com.br';
update_option( STCMS_Options::OPTION, $opts );
$data2 = STCMS_Rest::get_content()->data;
check( $data2['hero']['titleLines'] === array( 'NOVO', 'TÍTULO' ), 'edição do hero reflete na API' );
check( $data2['footer']['email'] === 'novo@studiotabi.com.br', 'edição do rodapé reflete na API' );

echo "\n" . ( $fail === 0 ? "RESULTADO: TODOS OS TESTES PASSARAM ✓\n" : "RESULTADO: $fail FALHA(S) ✗\n" );

// Amostra do JSON entregue ao front-end.
echo "\n--- amostra do JSON (hero + 1º serviço) ---\n";
echo json_encode(
	array( 'site' => $data['site'], 'hero' => $data['hero'], 'services[0]' => $data['services'][0] ),
	JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
) . "\n";

exit( $fail === 0 ? 0 : 1 );
