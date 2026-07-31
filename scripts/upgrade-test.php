<?php

error_reporting( E_ALL & ~E_DEPRECATED );

define( 'ABSPATH', __DIR__ . '/' );

$PLUGIN = __DIR__ . '/../wordpress/wp-content/plugins/studio-tabi-cms';

$GLOBALS['__options'] = array();
$GLOBALS['__posts']   = array();
$GLOBALS['__meta']    = array();
$GLOBALS['__thumbs']  = array();
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

function get_post($id){return $GLOBALS['__posts'][$id] ?? null;}
function delete_post_meta($id,$k){return true;}
require_once "$PLUGIN/includes/defaults.php";
require_once "$PLUGIN/includes/class-options.php";
require_once "$PLUGIN/includes/class-traducao.php";
require_once "$PLUGIN/includes/class-rest.php";

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
			'stcms_home'      => ( ! isset( $p['home'] ) || $p['home'] ) ? '1' : '',
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

$GLOBALS['__options']['stcms_options'] = array(
	'site' => array( 'title' => 'Studio Tabi', 'tagline' => 'Antigo', 'logo_id' => 0, 'favicon_id' => 0, 'meta_description' => 'desc antiga' ),
	'nav'  => array( 'brand' => 'STUDIO TABI', 'cta_label' => 'INICIAR PROJETO', 'cta_url' => '/contato',
		'links' => array( array( 'label' => 'TRABALHOS', 'url' => '#trabalhos' ) ) ),
	'hero' => array( 'eyebrow' => 'X', 'title_lines' => array( 'A', 'B' ), 'highlight' => 'C', 'description' => 'D', 'image_id' => 0 ),
	'sections' => array( 'about_eyebrow' => 'SOBRE', 'faq_eyebrow' => 'FAQ' ),
	'footer'   => array( 'brand' => 'STUDIO TABI', 'email' => 'oi@studiotabi.com.br' ),
	'about'    => array( 'paragraph1' => 'p1', 'paragraph2' => 'p2', 'stats' => array(), 'pillars' => array() ),
);

echo "== Upgrade v1.11 -> v1.13 com opcoes antigas no banco ==\n";
$r = STCMS_Rest::get_content();
$d = $r->data;
$checks = array(
	'site.ga4Id existe'        => array_key_exists( 'ga4Id', $d['site'] ),
	'site.recaptchaSite existe'=> array_key_exists( 'recaptchaSite', $d['site'] ),
	'SECRET NAO exposto'       => ! array_key_exists( 'recaptchaSecret', $d['site'] ) && ! array_key_exists( 'recaptcha_secret', $d['site'] ),
	'sections.blog existe'     => isset( $d['sections']['blog'] ) && is_array( $d['sections']['blog'] ),
	'blog.title com fallback'  => ! empty( $d['sections']['blog']['title'] ),
	'footer.ctaTitle fallback' => ! empty( $d['footer']['ctaTitle'] ),
	'footer.ctaUrl preservado' => $d['footer']['ctaUrl'] === '/contato',
	'nav.ctaUrl preservado'    => $d['nav']['ctaUrl'] === '/contato',
	'valor antigo mantido'     => $d['site']['tagline'] === 'Antigo',
	'process presente'         => isset( $d['process'] ) && count( $d['process'] ) === 4,
);
$fail = 0;
foreach ( $checks as $name => $ok ) { echo ( $ok ? "  OK   " : "  FALHA" ) . "  $name\n"; if ( ! $ok ) { $fail++; } }
echo $fail ? "\n$fail FALHA(S)\n" : "\nUpgrade seguro: nenhum erro fatal, defaults preenchem as chaves novas\n";
