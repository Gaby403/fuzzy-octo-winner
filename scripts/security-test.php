<?php
error_reporting(E_ALL & ~E_DEPRECATED);
define('ABSPATH', __DIR__.'/');
define('HOUR_IN_SECONDS', 3600);
$P = __DIR__.'/../wordpress/wp-content/plugins/studio-tabi-cms';
$GLOBALS['__o']=[]; $GLOBALS['__t']=[];
function get_option($k,$d=false){return $GLOBALS['__o'][$k]??$d;}
function update_option($k,$v){$GLOBALS['__o'][$k]=$v;return true;} function add_option($k,$v){return update_option($k,$v);}
function get_transient($k){return $GLOBALS['__t'][$k]??false;} function set_transient($k,$v,$t){$GLOBALS['__t'][$k]=$v;return true;}
function get_home_url(){return 'https://studiotabi.com.br';}
function untrailingslashit($s){return rtrim($s,'/');}
function esc_url_raw($u,$p=null){return $u;} function wp_unslash($v){return $v;}
function wp_parse_url($u,$c=-1){return $c===PHP_URL_HOST?parse_url($u,PHP_URL_HOST):parse_url($u);}
function sanitize_text_field($s){return trim((string)$s);} function sanitize_textarea_field($s){return trim((string)$s);}
function sanitize_email($s){return $s;} function is_email($s){return (bool)filter_var($s,FILTER_VALIDATE_EMAIL);}
function get_post_meta($i,$k,$s=true){return '';} function wp_strip_all_tags($s){return $s;} function apply_filters($t,$v){return $v;}
function esc_html($s){return $s;} function esc_attr($s){return $s;} function get_page_by_path($s,$o=null,$t=null){return null;}
function wp_get_attachment_image_url($i,$s=null){return '';} function get_posts($a){return [];}
function get_bloginfo($x=''){return 'Studio Tabi';} function wp_mail($a,$b,$c,$d=[]){return true;}
function get_the_post_thumbnail_url($p,$s=null){return '';} function get_the_title($p){return '';}
if(!defined('OBJECT')) define('OBJECT','OBJECT');
class WP_REST_Response{public $data;public $status;function __construct($d,$s=200){$this->data=$d;$this->status=$s;}}
class WP_REST_Request{private $j;function __construct($j){$this->j=$j;} function get_json_params(){return $this->j;} function get_params(){return $this->j;} function get_param($k){return $this->j[$k]??null;}}
require_once "$P/includes/defaults.php"; require_once "$P/includes/class-options.php"; require_once "$P/includes/class-rest.php";

$payload=['name'=>'Ana','email'=>'ana@exemplo.com','subject'=>'Oi','message'=>'Teste de mensagem'];
echo "== Origem não autorizada ==\n";
$_SERVER['REMOTE_ADDR']='203.0.113.9'; $_SERVER['HTTP_ORIGIN']='https://site-malicioso.com';
$r=STCMS_Rest::submit_contact(new WP_REST_Request($payload));
echo "  status {$r->status} — ".($r->status===403?"BLOQUEADO ✓":"PASSOU ✗")."\n";

echo "== Origem do próprio site ==\n";
$_SERVER['HTTP_ORIGIN']='https://studiotabi.com.br'; $_SERVER['REMOTE_ADDR']='203.0.113.10';
$r=STCMS_Rest::submit_contact(new WP_REST_Request($payload));
echo "  status {$r->status} — ".($r->status===200?"ACEITO ✓":"REJEITADO ✗")."\n";

echo "== Limite de envios (6 tentativas seguidas) ==\n";
$_SERVER['REMOTE_ADDR']='203.0.113.55';
for($i=1;$i<=6;$i++){ $r=STCMS_Rest::submit_contact(new WP_REST_Request($payload)); echo "  #$i → {$r->status}".($r->status===429?" (BLOQUEADO)":"")."\n"; }

echo "== Secret do reCAPTCHA na API pública ==\n";
$GLOBALS['__o']['stcms_options']=['site'=>['recaptcha_secret'=>'SEGREDO-ULTRA']];
$j=json_encode(STCMS_Rest::get_content()->data);
echo "  ".(strpos($j,'SEGREDO-ULTRA')===false?"não exposto ✓":"VAZOU ✗")."\n";
