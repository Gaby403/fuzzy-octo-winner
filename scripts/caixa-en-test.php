<?php
error_reporting( E_ALL & ~E_DEPRECATED );
define( 'ABSPATH', __DIR__ . '/' );
$P = dirname( __DIR__ ) . '/wordpress/wp-content/plugins/studio-tabi-cms';
define( 'STCMS_DIR', $P . '/' );
define( 'STCMS_URL', 'http://exemplo/wp-content/plugins/studio-tabi-cms/' );

$GLOBALS['__meta'] = array();
$GLOBALS['__caixas'] = array();
$GLOBALS['__enfileirados'] = array();
$GLOBALS['__screen'] = null;

function add_action( ...$a ) { return true; }
function add_filter( ...$a ) { return true; }
function get_post_meta( $id, $k = '', $s = true ) { return $GLOBALS['__meta'][ $id ][ $k ] ?? ''; }
function update_post_meta( $id, $k, $v ) { $GLOBALS['__meta'][ $id ][ $k ] = $v; return true; }
function delete_post_meta( $id, $k ) { unset( $GLOBALS['__meta'][ $id ][ $k ] ); return true; }
function get_post( $id ) { return $GLOBALS['__posts'][ $id ] ?? null; }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_textarea( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_url( $s ) { return $s; }
function wp_nonce_field( ...$a ) { echo '<input type="hidden" name="stcms_en_nonce" value="x" />'; }
function wp_verify_nonce( ...$a ) { return 1; }
function wp_unslash( $v ) { return $v; }
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_textarea_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_key( $s ) { return strtolower( preg_replace( '/[^a-z0-9_]/i', '', (string) $s ) ); }
function wp_kses_post( $s ) { return $s; }
function wp_is_post_revision( $id ) { return false; }
function current_user_can( ...$a ) { return true; }
function get_page_by_path( ...$a ) { return null; }
function get_term_meta( $id, $k = '', $s = true ) { return $GLOBALS['__termmeta'][ $id ][ $k ] ?? ''; }
function update_term_meta( $id, $k, $v ) { $GLOBALS['__termmeta'][ $id ][ $k ] = $v; return true; }
function delete_term_meta( $id, $k ) { unset( $GLOBALS['__termmeta'][ $id ][ $k ] ); return true; }
function get_posts( $a ) { return array(); }
function get_edit_post_link( $id, $c = null ) { return '/wp-admin/post.php?post=' . $id; }
function admin_url( $p = '' ) { return '/wp-admin/' . $p; }
function wp_nonce_url( $u, $a ) { return $u; }
function get_the_title( $p ) { return is_object( $p ) ? $p->post_title : ''; }
function wp_enqueue_media() { $GLOBALS['__enfileirados'][] = 'media'; }
function wp_enqueue_script( $h, ...$a ) { $GLOBALS['__enfileirados'][] = $h; }
function wp_enqueue_style( $h, ...$a ) { $GLOBALS['__enfileirados'][] = $h; }
function get_current_screen() { return $GLOBALS['__screen']; }
function add_meta_box( $id, $titulo, $cb, $tela, ...$r ) { $GLOBALS['__caixas'][ $tela ][] = $id; }
function wp_editor( $conteudo, $id, $args = array() ) {
	printf(
		'<textarea id="%s" name="%s" rows="8">%s</textarea>',
		esc_attr( $id ),
		esc_attr( $args['textarea_name'] ?? $id ),
		esc_textarea( $conteudo )
	);
}
if ( ! defined( 'OBJECT' ) ) { define( 'OBJECT', 'OBJECT' ); }

require_once "$P/includes/class-traducao.php";

$ok = 0; $ko = 0;
function ok( $r, $c, $v = '' ) { global $ok, $ko; $c ? $ok++ : $ko++; echo '  ' . ( $c ? '✓' : '✗' ) . "  $r" . ( '' !== $v ? ": $v" : '' ) . "\n"; }

function post_falso( $id, $tipo ) {
	$p = new stdClass();
	$p->ID = $id; $p->post_type = $tipo; $p->post_title = 'Título PT'; $p->post_content = '<p>Corpo PT</p>';
	$p->post_excerpt = 'Resumo PT'; $p->post_name = 'slug'; $p->post_status = 'publish';
	return $p;
}

echo "== A caixa é registrada em todos os tipos ==\n";
STCMS_Traducao::add_box();
foreach ( STCMS_Traducao::tipos() as $tipo ) {
	ok( "caixa registrada em {$tipo}", in_array( 'stcms_en', $GLOBALS['__caixas'][ $tipo ] ?? array(), true ) );
}

echo "\n== O painel em inglês é editável (não sai escondido no HTML) ==\n";
foreach ( STCMS_Traducao::tipos() as $tipo ) {
	$post = post_falso( 100, $tipo );
	$GLOBALS['__posts'][100] = $post;
	ob_start();
	STCMS_Traducao::render( $post );
	$html = ob_get_clean();

	$campos = STCMS_Traducao::campos( $tipo );
	ok( "{$tipo}: painel EN sem display:none embutido", ! preg_match( '/data-painel="en"[^>]*display\s*:\s*none/i', $html ), $tipo );

	$faltando = array();
	foreach ( array_keys( $campos ) as $chave ) {
		if ( ! preg_match( '/name="stcms_en_' . preg_quote( $chave, '/' ) . '"/', $html ) ) {
			$faltando[] = $chave;
		}
	}
	ok( "{$tipo}: todos os campos têm input", empty( $faltando ), $faltando ? implode( ',', $faltando ) : '' );
	ok( "{$tipo}: nenhum campo readonly ou disabled", ! preg_match( '/(readonly|disabled)/i', $html ) );
}

echo "\n== IDs de editor sem underscore (senão o TinyMCE não devolve o texto) ==\n";
foreach ( STCMS_Traducao::tipos() as $tipo ) {
	foreach ( array_keys( STCMS_Traducao::campos( $tipo ) ) as $chave ) {
		$id = STCMS_Traducao::id_editor( $chave );
		if ( false !== strpos( $id, '_' ) ) {
			ok( "id do editor {$id}", false, 'tem underscore' );
		}
	}
}
ok( 'todos os ids de editor são válidos', true );

echo "\n== Os assets do admin carregam em cada tela de edição ==\n";
$fonte = file_get_contents( "$P/studio-tabi-cms.php" );
preg_match( '/function stcms_admin_assets.*?\n}/s', $fonte, $m ) || exit( "não achei stcms_admin_assets\n" );
eval( $m[0] );
foreach ( STCMS_Traducao::tipos() as $tipo ) {
	$GLOBALS['__enfileirados'] = array();
	$GLOBALS['__screen'] = (object) array( 'post_type' => $tipo );
	stcms_admin_assets( 'post.php' );
	ok( "{$tipo}: admin.js enfileirado", in_array( 'stcms-admin', $GLOBALS['__enfileirados'], true ), implode( ',', $GLOBALS['__enfileirados'] ) ?: '(nada)' );
}
$GLOBALS['__enfileirados'] = array();
$GLOBALS['__screen'] = (object) array( 'post_type' => '' );
stcms_admin_assets( 'toplevel_page_studio-tabi' );
ok( 'tela de opções também recebe os assets', in_array( 'stcms-admin', $GLOBALS['__enfileirados'], true ) );

$GLOBALS['__enfileirados'] = array();
$GLOBALS['__screen'] = (object) array( 'post_type' => 'attachment' );
stcms_admin_assets( 'post.php' );
ok( 'tela sem tradução não carrega nada', ! in_array( 'stcms-admin', $GLOBALS['__enfileirados'], true ) );

echo "\n== Gravar preenche o meta do post ==\n";
$post = post_falso( 200, 'post' );
$GLOBALS['__posts'][200] = $post;
$_POST = array(
	'stcms_en_nonce'   => 'x',
	'stcms_en_title'   => 'English title',
	'stcms_en_excerpt' => 'English summary',
	'stcms_en_body'    => '<p>English <strong>body</strong></p>',
);
STCMS_Traducao::save( 200, $post );
ok( 'título gravado', 'English title' === get_post_meta( 200, 'stcms_en_title' ), get_post_meta( 200, 'stcms_en_title' ) );
ok( 'resumo gravado', 'English summary' === get_post_meta( 200, 'stcms_en_excerpt' ) );
ok( 'corpo mantém o HTML', false !== strpos( (string) get_post_meta( 200, 'stcms_en_body' ), '<strong>' ), get_post_meta( 200, 'stcms_en_body' ) );
ok( 'lido de volta em EN', 'English title' === STCMS_Traducao::texto( $post, 'title', 'en' ) );
ok( 'PT segue intacto', 'Título PT' === STCMS_Traducao::texto( $post, 'title', 'pt' ) );

$_POST['stcms_en_title'] = '';
STCMS_Traducao::save( 200, $post );
ok( 'campo esvaziado volta a herdar o PT', 'Título PT' === STCMS_Traducao::texto( $post, 'title', 'en' ), STCMS_Traducao::texto( $post, 'title', 'en' ) );

echo "\n== Nome da categoria em inglês ==\n";
$GLOBALS['__termmeta'] = array();
$termo = (object) array( 'term_id' => 7, 'name' => 'Estratégia', 'slug' => 'estrategia' );

ob_start();
STCMS_Traducao::campo_termo( $termo );
$html = ob_get_clean();
ok( 'campo aparece na edição da categoria', (bool) preg_match( '/name="stcms_en_name"/', $html ) );
ok( 'campo não é readonly', ! preg_match( '/(readonly|disabled)/i', $html ) );

ob_start();
STCMS_Traducao::campo_termo_novo();
$html = ob_get_clean();
ok( 'campo aparece ao criar categoria', (bool) preg_match( '/name="stcms_en_name"/', $html ) );

$colunas = STCMS_Traducao::coluna_termo( array( 'cb' => '', 'name' => 'Nome', 'slug' => 'Slug', 'posts' => 'Contagem' ) );
ok( 'coluna acrescentada na lista', isset( $colunas['stcms_en'] ), implode( ',', array_keys( $colunas ) ) );
ok( 'coluna vem logo depois do nome', array_keys( $colunas )[2] === 'stcms_en', implode( ',', array_keys( $colunas ) ) );
ok( 'colunas originais preservadas', isset( $colunas['cb'], $colunas['name'], $colunas['slug'], $colunas['posts'] ) );

ok( 'sem tradução a célula avisa', false !== strpos( STCMS_Traducao::celula_termo( '', 'stcms_en', 7 ), 'usando o português' ) );

$_POST = array( 'stcms_en_termo_nonce' => 'x', 'stcms_en_name' => 'Strategy' );
STCMS_Traducao::salvar_termo( 7 );
ok( 'nome em inglês gravado', 'Strategy' === get_term_meta( 7, 'stcms_en_name' ), get_term_meta( 7, 'stcms_en_name' ) );
ok( 'célula mostra o valor', 'Strategy' === STCMS_Traducao::celula_termo( '', 'stcms_en', 7 ) );
ok( 'lido em EN', 'Strategy' === STCMS_Traducao::nome_termo( $termo, 'en' ) );
ok( 'PT continua o português', 'Estratégia' === STCMS_Traducao::nome_termo( $termo, 'pt' ) );

$_POST['stcms_en_name'] = '';
STCMS_Traducao::salvar_termo( 7 );
ok( 'esvaziar volta a herdar o português', 'Estratégia' === STCMS_Traducao::nome_termo( $termo, 'en' ), STCMS_Traducao::nome_termo( $termo, 'en' ) );

$_POST = array( 'stcms_en_name' => 'Sem nonce' );
STCMS_Traducao::salvar_termo( 7 );
ok( 'sem nonce não grava', '' === get_term_meta( 7, 'stcms_en_name' ), get_term_meta( 7, 'stcms_en_name' ) );

ok( 'outra coluna não é afetada', 'intacto' === STCMS_Traducao::celula_termo( 'intacto', 'slug', 7 ) );

echo "\n$ok passaram, $ko falharam\n";
exit( $ko ? 1 : 0 );
