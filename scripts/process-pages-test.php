<?php
error_reporting(E_ALL & ~E_DEPRECATED);
define('ABSPATH', __DIR__.'/'); define('HOUR_IN_SECONDS',3600);
$P = __DIR__.'/../wordpress/wp-content/plugins/studio-tabi-cms';

$GLOBALS['__o']    = [];
$GLOBALS['__pags'] = [];   // slug => objeto
$GLOBALS['__meta'] = [];   // id => [chave => valor]
$GLOBALS['__id']   = 100;
$GLOBALS['__saida'] = null;

function get_option($k,$d=false){return $GLOBALS['__o'][$k]??$d;}
function update_option($k,$v){$GLOBALS['__o'][$k]=$v;return true;} function add_option($k,$v){return update_option($k,$v);}
function get_transient($k){return false;} function set_transient($k,$v,$t){return true;}
function get_home_url(){return 'https://studiotabi.com.br';} function untrailingslashit($s){return rtrim($s,'/');}
function esc_url_raw($u,$p=null){return $u;} function esc_url($u){return $u;} function wp_unslash($v){return $v;}
function sanitize_key($s){return strtolower(preg_replace('/[^a-z0-9_]/i','',$s));}
function wp_parse_url($u,$c=-1){return $c===PHP_URL_HOST?parse_url($u,PHP_URL_HOST):parse_url($u);}
function sanitize_text_field($s){return trim((string)$s);} function sanitize_textarea_field($s){return trim((string)$s);}
function sanitize_email($s){return $s;} function is_email($s){return (bool)filter_var($s,FILTER_VALIDATE_EMAIL);}
function wp_strip_all_tags($s){return $s;} function apply_filters($t,$v){return $v;}
function esc_html($s){return $s;} function esc_attr($s){return $s;} function esc_textarea($s){return $s;}
function wp_get_attachment_image_url($i,$s=null){return '';} function get_bloginfo($x=''){return 'Studio Tabi';}
function wp_mail(...$a){return true;} function get_the_post_thumbnail_url($p,$s=null){return '';}
function sanitize_title($s){return $s;} function checked($a,$b,$c=true){return '';}
function mysql2date($f,$d,$t=true){return date($f, strtotime($d));}
function wpautop($s){return "<p>$s</p>";} function is_wp_error($x){return false;}
function current_user_can($c){return true;} function check_admin_referer($a){return true;}
function admin_url($p=''){return 'https://cms.studiotabi.com.br/wp-admin/'.$p;}
function wp_nonce_url($u,$a){return $u.'&_wpnonce=x';}
function get_edit_post_link($id){return 'https://cms.studiotabi.com.br/wp-admin/post.php?post='.$id;}
function get_the_title($p){return is_object($p)?$p->post_title:'';}
class Redirecionou extends Exception {}
function wp_safe_redirect($u){$GLOBALS['__saida']=$u;throw new Redirecionou($u);}
if(!defined('OBJECT')) define('OBJECT','OBJECT');

function get_page_by_path($slug,$o=null,$t=null){return $GLOBALS['__pags'][$slug]??null;}
function wp_insert_post($a){
    $p = new stdClass;
    $p->ID = ++$GLOBALS['__id'];
    $p->post_name = $a['post_name']; $p->post_title = $a['post_title'];
    $p->post_content = $a['post_content']; $p->post_status = $a['post_status'];
    $p->post_modified_gmt = gmdate('Y-m-d H:i:s');
    $GLOBALS['__pags'][$p->post_name] = $p;
    return $p->ID;
}
function update_post_meta($id,$k,$v){$GLOBALS['__meta'][$id][$k]=$v;return true;}
function get_post_meta($id,$k,$s=true){return $GLOBALS['__meta'][$id][$k]??'';}
function get_posts($a){
    if (($a['post_type']??'') !== 'page') { return []; }
    return array_values($GLOBALS['__pags']);
}
class WP_REST_Response{public $data;public $status;function __construct($d,$s=200){$this->data=$d;$this->status=$s;}}
class WP_REST_Request{private $p;function __construct($p=[]){$this->p=$p;} function get_json_params(){return $this->p;} function get_params(){return $this->p;} function get_param($k){return $this->p[$k]??null;}}

require_once "$P/includes/defaults.php"; require_once "$P/includes/class-options.php"; require_once "$P/includes/class-rest.php";

$ok = 0; $ko = 0;
function ok($rotulo,$cond,$valor=''){global $ok,$ko;$cond?$ok++:$ko++;echo '  '.($cond?'✓':'✗')."  $rotulo".($valor!==''?": $valor":'')."\n";}

// O plugin encerra com exit após redirecionar; a exceção devolve o controle ao teste.
function criar($lang) {
    $_GET = ['stcms_create_pages'=>'1','stcms_lang'=>$lang];
    try { STCMS_Options::maybe_create_process_pages(); }
    catch (Redirecionou $e) { }
    $_GET = [];
    return $GLOBALS['__saida'];
}

echo "== Criação das páginas em português ==\n";
$destino = criar('pt');
ok('4 páginas criadas', count($GLOBALS['__pags']) === 4, (string) count($GLOBALS['__pags']));
ok('slug sem sufixo', isset($GLOBALS['__pags']['diagnostico']), implode(', ', array_keys($GLOBALS['__pags'])));
ok('título em português', ($GLOBALS['__pags']['diagnostico']->post_title ?? '') === 'Diagnóstico', $GLOBALS['__pags']['diagnostico']->post_title ?? '—');
ok('marcada como pt', get_post_meta($GLOBALS['__pags']['diagnostico']->ID,'stcms_lang') === 'pt');
ok('volta para a aba pt', str_contains((string) $destino, 'stcms_lang=pt&stcms_created=4'), (string) $destino);

echo "== Criação das páginas em inglês ==\n";
$destino = criar('en');
ok('mais 4 páginas', count($GLOBALS['__pags']) === 8, (string) count($GLOBALS['__pags']));
ok('slug com sufixo -en', isset($GLOBALS['__pags']['diagnostico-en']));
ok('título em inglês', ($GLOBALS['__pags']['diagnostico-en']->post_title ?? '') === 'Diagnosis', $GLOBALS['__pags']['diagnostico-en']->post_title ?? '—');
ok('conteúdo em inglês', str_contains($GLOBALS['__pags']['diagnostico-en']->post_content ?? '', 'We dive into the business'));
ok('marcada como en', get_post_meta($GLOBALS['__pags']['diagnostico-en']->ID,'stcms_lang') === 'en');
ok('a portuguesa continua intacta', ($GLOBALS['__pags']['diagnostico']->post_title ?? '') === 'Diagnóstico');
ok('volta para a aba en', str_contains((string) $destino, 'stcms_lang=en&stcms_created=4'), (string) $destino);

echo "== Idempotência ==\n";
criar('en'); criar('pt');
ok('não duplica nada', count($GLOBALS['__pags']) === 8, (string) count($GLOBALS['__pags']));

echo "== A API entrega a página do idioma pedido ==\n";
$pt = STCMS_Rest::get_page(new WP_REST_Request(['slug'=>'diagnostico','lang'=>'pt']))->data;
$en = STCMS_Rest::get_page(new WP_REST_Request(['slug'=>'diagnostico','lang'=>'en']))->data;
ok('/processo/diagnostico em português', $pt['title'] === 'Diagnóstico', $pt['title']);
ok('/en/process/diagnostico em inglês', $en['title'] === 'Diagnosis', $en['title']);
ok('slug servido no EN', $en['slug'] === 'diagnostico-en', $en['slug']);

echo "== Sitemap passa a anunciar as etapas em inglês ==\n";
$GLOBALS['__o']['stcms_site_origin'] = 'https://studiotabi.com.br';
$xml = STCMS_Rest::get_sitemap()->data['xml'];
ok('etapa PT no sitemap', str_contains($xml, '<loc>https://studiotabi.com.br/processo/diagnostico</loc>'));
ok('etapa EN no sitemap', str_contains($xml, '<loc>https://studiotabi.com.br/en/process/diagnostico</loc>'));

echo "== Sem a versão em inglês, o sitemap não anuncia a URL /en ==\n";
foreach (array_keys($GLOBALS['__pags']) as $slug) {
    if (str_ends_with($slug, '-en')) { unset($GLOBALS['__pags'][$slug]); }
}
$xml = STCMS_Rest::get_sitemap()->data['xml'];
ok('etapa PT continua', str_contains($xml, '<loc>https://studiotabi.com.br/processo/diagnostico</loc>'));
ok('etapa EN some', ! str_contains($xml, '<loc>https://studiotabi.com.br/en/process/diagnostico</loc>'));

echo "\n$ok passaram, $ko falharam\n";
exit($ko ? 1 : 0);
