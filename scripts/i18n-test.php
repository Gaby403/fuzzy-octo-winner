<?php
error_reporting(E_ALL & ~E_DEPRECATED);
define('ABSPATH', __DIR__.'/'); define('HOUR_IN_SECONDS',3600);
$P = __DIR__.'/../wordpress/wp-content/plugins/studio-tabi-cms';
$GLOBALS['__o']=[];
function get_option($k,$d=false){return $GLOBALS['__o'][$k]??$d;}
function update_option($k,$v){$GLOBALS['__o'][$k]=$v;return true;} function add_option($k,$v){return update_option($k,$v);}
function get_transient($k){return false;} function set_transient($k,$v,$t){return true;}
function get_home_url(){return 'https://studiotabi.com.br';} function untrailingslashit($s){return rtrim($s,'/');}
function esc_url_raw($u,$p=null){return $u;} function wp_unslash($v){return $v;} function sanitize_key($s){return strtolower(preg_replace('/[^a-z0-9_]/i','',$s));}
function wp_parse_url($u,$c=-1){return $c===PHP_URL_HOST?parse_url($u,PHP_URL_HOST):parse_url($u);}
function sanitize_text_field($s){return trim((string)$s);} function sanitize_textarea_field($s){return trim((string)$s);}
function sanitize_email($s){return $s;} function is_email($s){return (bool)filter_var($s,FILTER_VALIDATE_EMAIL);}
function get_post_meta($i,$k,$s=true){return '';} function wp_strip_all_tags($s){return $s;} function apply_filters($t,$v){return $v;}
function esc_html($s){return $s;} function esc_attr($s){return $s;} function get_page_by_path($s,$o=null,$t=null){return null;}
function wp_get_attachment_image_url($i,$s=null){return '';} function get_posts($a){return [];}
function get_bloginfo($x=''){return 'Studio Tabi';} function wp_mail(...$a){return true;}
function get_the_post_thumbnail_url($p,$s=null){return '';} function get_the_title($p){return '';}
function sanitize_title($s){return $s;} function esc_textarea($s){return $s;} function checked($a,$b,$c=true){return '';}
if(!defined('OBJECT')) define('OBJECT','OBJECT');
class WP_REST_Response{public $data;public $status;function __construct($d,$s=200){$this->data=$d;$this->status=$s;}}
class WP_REST_Request{private $p;function __construct($p=[]){$this->p=$p;} function get_json_params(){return $this->p;} function get_params(){return $this->p;} function get_param($k){return $this->p[$k]??null;}}
require_once "$P/includes/defaults.php"; require_once "$P/includes/class-options.php"; require_once "$P/includes/class-rest.php";

echo "== Sem tradução: /en cai no português ==\n";
$en = STCMS_Rest::get_content(new WP_REST_Request(['lang'=>'en']))->data;
echo "  locale={$en['locale']}  hero='{$en['hero']['titleLines'][0]}'  ".($en['hero']['titleLines'][0]==='TRANSFORMAMOS'?"fallback PT ✓":"✗")."\n";

echo "== Com tradução parcial ==\n";
$GLOBALS['__o']['stcms_options_en'] = ['hero'=>['title_lines'=>['WE TURN','YOUR BRAND'],'highlight'=>'DIGITAL.'],'nav'=>['cta_label'=>'START A PROJECT']];
$en = STCMS_Rest::get_content(new WP_REST_Request(['lang'=>'en']))->data;
$pt = STCMS_Rest::get_content(new WP_REST_Request(['lang'=>'pt']))->data;
echo "  EN hero: '".implode(' ',$en['hero']['titleLines'])."'  cta='{$en['nav']['ctaLabel']}'\n";
echo "  EN campo não traduzido (descrição): '".substr($en['hero']['description'],0,28)."…'  ".(str_starts_with($en['hero']['description'],'Design, estratégia')?"herdou do PT ✓":"✗")."\n";
echo "  PT intacto: '".implode(' ',$pt['hero']['titleLines'])."'  ".($pt['hero']['titleLines'][0]==='TRANSFORMAMOS'?"✓":"✗")."\n";
echo "  locale PT={$pt['locale']} EN={$en['locale']}\n";
