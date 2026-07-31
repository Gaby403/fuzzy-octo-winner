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
function get_post_meta($i,$k,$s=true){return '';}
function mysql2date($f,$d,$t=true){return date($f, strtotime($d));} function wp_strip_all_tags($s){return $s;} function apply_filters($t,$v){return $v;}
function esc_html($s){return $s;} function esc_attr($s){return $s;} function get_page_by_path($s,$o=null,$t=null){return null;}
function wp_get_attachment_image_url($i,$s=null){return '';}
$GLOBALS['__servicos'] = ['branding','ui-ux'];
function get_posts($a){
  $tipos = (array) ($a['post_type'] ?? '');
  if (in_array('st_service', $tipos, true)) {
    $out=[]; $i=1;
    foreach ($GLOBALS['__servicos'] as $slug) {
      $p=new stdClass; $p->ID=$i++; $p->post_type='st_service'; $p->post_name=$slug; $p->post_title=$slug;
      $p->post_content=''; $p->post_excerpt=''; $p->post_status='publish';
      $p->post_modified_gmt=gmdate('Y-m-d H:i:s'); $out[]=$p;
    }
    return $out;
  }
  return [];
}
function get_bloginfo($x=''){return 'Studio Tabi';} function wp_mail(...$a){return true;}
function get_the_post_thumbnail_url($p,$s=null){return '';} function get_the_title($p){return '';}
function sanitize_title($s){return $s;} function esc_textarea($s){return $s;} function checked($a,$b,$c=true){return '';}
function get_post($id){return $GLOBALS['__posts'][$id] ?? null;}
function delete_post_meta($id,$k){return true;}
if(!defined('OBJECT')) define('OBJECT','OBJECT');
class WP_REST_Response{public $data;public $status;function __construct($d,$s=200){$this->data=$d;$this->status=$s;}}
class WP_REST_Request{private $p;function __construct($p=[]){$this->p=$p;} function get_json_params(){return $this->p;} function get_params(){return $this->p;} function get_param($k){return $this->p[$k]??null;}}
require_once "$P/includes/defaults.php"; require_once "$P/includes/class-options.php"; require_once "$P/includes/class-traducao.php"; require_once "$P/includes/class-rest.php";

$ok = 0; $ko = 0;
function ok($rotulo, $cond, $valor = '') { global $ok, $ko; $cond ? $ok++ : $ko++; echo '  '.($cond?'✓':'✗')."  $rotulo".($valor!==''?": $valor":'')."\n"; }

echo "== Sem nada salvo: /en já vem em inglês pelos padrões ==\n";
$en = STCMS_Rest::get_content(new WP_REST_Request(['lang'=>'en']))->data;
$pt = STCMS_Rest::get_content(new WP_REST_Request(['lang'=>'pt']))->data;
ok('locale', $en['locale'] === 'en', $en['locale']);
ok('hero traduzido', $en['hero']['titleLines'] === ['YOUR BRAND,','A DIGITAL'], implode(' ', $en['hero']['titleLines']));
ok('nav aponta para /en', $en['nav']['ctaUrl'] === '/en/contact', $en['nav']['ctaUrl']);
ok('links de nav em inglês', $en['nav']['links'][0]['label'] === 'WORK', $en['nav']['links'][0]['label']);
ok('rodapé traduzido', $en['footer']['ctaHighlight'] === 'digital presence.', $en['footer']['ctaHighlight']);
ok('PT intacto', $pt['hero']['titleLines'][0] === 'SUA MARCA,', $pt['hero']['titleLines'][0]);

echo "== Campo ausente do dicionário EN herda do PT ==\n";
ok('site.formEmail herdado', ($en['site']['ga4Id'] ?? '') === ($pt['site']['ga4Id'] ?? ''));
ok('process mantém slugs PT (URLs estáveis)', $en['process'][0]['slug'] === 'diagnostico', $en['process'][0]['slug']);
ok('process traduzido', $en['process'][0]['title'] === 'Diagnosis', $en['process'][0]['title']);

echo "== Edição no CMS sobrepõe o padrão EN ==\n";
$GLOBALS['__o']['stcms_options_en'] = ['hero'=>['title_lines'=>['WE SHAPE','BRANDS'],'highlight'=>'ONLINE.'],'nav'=>['cta_label'=>'BOOK A CALL']];
$en = STCMS_Rest::get_content(new WP_REST_Request(['lang'=>'en']))->data;
$pt = STCMS_Rest::get_content(new WP_REST_Request(['lang'=>'pt']))->data;
ok('hero sobrescrito', implode(' ', $en['hero']['titleLines']) === 'WE SHAPE BRANDS', implode(' ', $en['hero']['titleLines']));
ok('cta sobrescrito', $en['nav']['ctaLabel'] === 'BOOK A CALL', $en['nav']['ctaLabel']);
ok('campo não editado mantém o padrão EN', str_starts_with($en['hero']['description'], 'Design, strategy'), substr($en['hero']['description'], 0, 24).'…');
ok('PT segue intacto', $pt['hero']['titleLines'][0] === 'SUA MARCA,' && $pt['nav']['ctaLabel'] === 'INICIAR PROJETO');

echo "== Sitemap por idioma ==\n";
$GLOBALS['__o']['stcms_site_origin'] = 'https://studiotabi.com.br';
$mapa = STCMS_Rest::get_sitemap()->data;
$xml  = $mapa['xml'];
ok('base do site', $mapa['base'] === 'https://studiotabi.com.br', $mapa['base']);
ok('XML bem formado', (bool) @simplexml_load_string($xml));
ok('declara o namespace xhtml', str_contains($xml, 'xmlns:xhtml="http://www.w3.org/1999/xhtml"'));
foreach (['https://studiotabi.com.br/', 'https://studiotabi.com.br/en', 'https://studiotabi.com.br/projetos',
          'https://studiotabi.com.br/en/work', 'https://studiotabi.com.br/en/services',
          'https://studiotabi.com.br/en/about', 'https://studiotabi.com.br/en/contact'] as $u) {
    ok("lista $u", str_contains($xml, "<loc>$u</loc>"));
}
ok('hreflang recíproco na home',
    str_contains($xml, '<xhtml:link rel="alternate" hreflang="en" href="https://studiotabi.com.br/en"/>')
    && str_contains($xml, '<xhtml:link rel="alternate" hreflang="x-default" href="https://studiotabi.com.br/"/>'));
ok('serviço listado em português', str_contains($xml, '<loc>https://studiotabi.com.br/servicos/branding</loc>'));
ok('serviço sem tradução não entra no /en', ! str_contains($xml, '/en/services/branding'));
ok('serviço não é duplicado', 1 === substr_count($xml, '<loc>https://studiotabi.com.br/servicos/branding</loc>'));
ok('não indexa a página de obrigado', ! str_contains($xml, '/obrigado') && ! str_contains($xml, '/thank-you'));

echo "\n$ok passaram, $ko falharam\n";
exit($ko ? 1 : 0);
