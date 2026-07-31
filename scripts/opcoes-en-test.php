<?php
error_reporting(E_ALL & ~E_DEPRECATED);
define('ABSPATH', __DIR__.'/');
$P = __DIR__.'/../wordpress/wp-content/plugins/studio-tabi-cms';

$GLOBALS['__o'] = [];
function get_option($k,$d=false){return $GLOBALS['__o'][$k]??$d;}
function update_option($k,$v){$GLOBALS['__o'][$k]=$v;return true;}
function add_option($k,$v){return update_option($k,$v);}
function delete_option($k){unset($GLOBALS['__o'][$k]);return true;}
function get_transient($k){return false;} function set_transient($k,$v,$t){return true;}
function get_home_url(){return 'https://studiotabi.com.br';} function untrailingslashit($s){return rtrim($s,'/');}
function esc_url_raw($u,$p=null){return trim((string)$u);} function esc_url($u){return $u;}
function wp_unslash($v){return is_array($v)?array_map('wp_unslash',$v):stripslashes((string)$v);}
function sanitize_key($s){return strtolower(preg_replace('/[^a-z0-9_]/i','',$s));}
function wp_parse_url($u,$c=-1){return parse_url($u,$c);}
function sanitize_text_field($s){return trim(strip_tags((string)$s));}
function sanitize_textarea_field($s){return trim(strip_tags((string)$s));}
function sanitize_email($s){return trim((string)$s);} function is_email($s){return (bool)filter_var($s,FILTER_VALIDATE_EMAIL);}
function esc_html($s){return $s;} function esc_attr($s){return $s;} function esc_textarea($s){return $s;}
function checked($a,$b,$c=true){return '';} function apply_filters($t,$v){return $v;}
function wp_get_attachment_image_url($i,$s=null){return '';} function get_bloginfo($x=''){return 'Studio Tabi';}
function sanitize_title($s){return $s;} function wp_strip_all_tags($s){return strip_tags((string)$s);}
function get_post_meta($i,$k='',$s=true){return '';} function get_page_by_path($s,$o=null,$t=null){return null;}
function get_posts($a){return [];} function add_action($h,$c,$p=10,$n=1){return true;}
function add_filter($h,$c,$p=10,$n=1){return true;} function current_user_can($c,$i=null){return true;}
function admin_url($p=''){return '/wp-admin/'.$p;} function wp_nonce_url($u,$a){return $u;}
function get_edit_post_link($i,$c=null){return '/wp-admin/post.php?post='.$i.'&action=edit';}
function selected($a,$b,$e=true){return (string)$a===(string)$b?' selected':'';}
function wp_json_encode($v){return json_encode($v);}
function get_the_title($p){return is_object($p)?$p->post_title:'';} function mysql2date($f,$d,$t=true){return $d;}
class Redirecionou extends Exception {}
function wp_safe_redirect($u){throw new Redirecionou($u);} function check_admin_referer($a){return true;}
function get_post($i){return null;} function wp_is_post_revision($i){return false;}
function add_meta_box(...$a){return true;} function wp_editor(...$a){return true;}
function wp_nonce_field(...$a){return true;} function wp_verify_nonce(...$a){return 1;}
function update_post_meta(...$a){return true;} function delete_post_meta(...$a){return true;}
function wp_kses_post($s){return $s;} function register_setting(...$a){return true;}
if(!defined('OBJECT')) define('OBJECT','OBJECT');

require_once "$P/includes/defaults.php";
require_once "$P/includes/class-options.php";
require_once "$P/includes/class-traducao.php";

$ok=0; $ko=0;
function ok($r,$c,$v=''){global $ok,$ko;$c?$ok++:$ko++;echo '  '.($c?'✓':'✗')."  $r".($v!==''?": $v":'')."\n";}

function formulario_en() {
    $en = stcms_default_options_en();
    $pt = stcms_default_options();
    $tela = STCMS_Options::get('en');
    $tela['hero']['title_lines'] = implode("\n", (array) $tela['hero']['title_lines']);
    return $tela;
}

echo "== Salvar a aba English sem mudar nada deve preservar tudo ==\n";
$antes = STCMS_Options::get('en');
$salvo = STCMS_Options::sanitize_en( formulario_en() );
$GLOBALS['__o']['stcms_options_en'] = $salvo;
$depois = STCMS_Options::get('en');

ok('etapa 3 mantém o título', 'Design' === ($depois['process'][2]['title'] ?? ''), $depois['process'][2]['title'] ?? '(sumiu)');
ok('etapa 3 mantém o slug', 'design' === ($depois['process'][2]['slug'] ?? ''), $depois['process'][2]['slug'] ?? '(sumiu)');
ok('etapa 1 mantém o ícone', 'diagnostico' === ($depois['process'][0]['icon'] ?? ''), $depois['process'][0]['icon'] ?? '(sumiu)');
ok('as 4 etapas continuam', 4 === count($depois['process'] ?? []), (string) count($depois['process'] ?? []));
ok('índices sem buraco', array_keys($depois['process'] ?? []) === array(0,1,2,3), implode(',', array_keys($depois['process'] ?? [])));

ok('estatística mantém o número', 7 === ($depois['about']['stats'][0]['numeric'] ?? 0), (string)($depois['about']['stats'][0]['numeric'] ?? 0));
ok('estatística mantém o sufixo', '+' === ($depois['about']['stats'][0]['suffix'] ?? ''), $depois['about']['stats'][0]['suffix'] ?? '(sumiu)');
ok('estatística mantém o rótulo', 'YEARS IN THE MARKET' === ($depois['about']['stats'][0]['label'] ?? ''), $depois['about']['stats'][0]['label'] ?? '(sumiu)');

ok('link do rodapé mantém o rótulo', '' !== ($depois['footer']['col1_links'][0]['label'] ?? ''), $depois['footer']['col1_links'][0]['label'] ?? '(sumiu)');
ok('link do rodapé aponta para /en', 0 === strpos((string)($depois['footer']['col1_links'][0]['url'] ?? ''), '/en'), $depois['footer']['col1_links'][0]['url'] ?? '(sumiu)');
ok('link legal mantém a URL', '' !== ($depois['footer']['legal'][0]['url'] ?? ''), $depois['footer']['legal'][0]['url'] ?? '(sumiu)');
ok('link legal mantém o rótulo', 'Privacy Policy' === ($depois['footer']['legal'][0]['label'] ?? ''), $depois['footer']['legal'][0]['label'] ?? '(sumiu)');

ok('nav em inglês', 'WORK' === ($depois['nav']['links'][0]['label'] ?? ''), $depois['nav']['links'][0]['label'] ?? '(sumiu)');
ok('hero em inglês', 'WE TURN' === ($depois['hero']['title_lines'][0] ?? ''), $depois['hero']['title_lines'][0] ?? '(sumiu)');

echo "\n== Editar um campo salva o que foi digitado ==\n";
$form = formulario_en();
$linhas = explode("\n", $form['hero']['title_lines']);
$linhas[0] = 'WE SHAPE';
$form['hero']['title_lines'] = implode("\n", $linhas);
$form['process'][2]['title'] = 'Craft';
$GLOBALS['__o']['stcms_options_en'] = STCMS_Options::sanitize_en( $form );
$depois = STCMS_Options::get('en');
ok('hero editado', 'WE SHAPE' === $depois['hero']['title_lines'][0], $depois['hero']['title_lines'][0]);
ok('etapa editada', 'Craft' === $depois['process'][2]['title'], $depois['process'][2]['title']);
ok('etapa vizinha intacta', 'Narrative' === $depois['process'][1]['title'], $depois['process'][1]['title']);

echo "\n== Campo esvaziado volta a herdar o português ==\n";
$form = formulario_en();
$form['contact']['title'] = '';
$GLOBALS['__o']['stcms_options_en'] = STCMS_Options::sanitize_en( $form );
$depois = STCMS_Options::get('en');
ok('título de contato herda o padrão em inglês', 'LET\'S' === $depois['contact']['title'], $depois['contact']['title']);

echo "\n== Restaurar os padrões em inglês ==\n";
$GLOBALS['__o']['stcms_options_en'] = array('hero'=>array('highlight'=>'QUEBRADO.'));
ok('opção corrompida presente', 'QUEBRADO.' === STCMS_Options::get('en')['hero']['highlight']);
$_GET = array('stcms_restaurar_en'=>'1');
try { STCMS_Options::maybe_restaurar_en(); } catch (Redirecionou $e) {}
$_GET = array();
ok('opção removida', ! isset($GLOBALS['__o']['stcms_options_en']));
$depois = STCMS_Options::get('en');
ok('volta ao padrão em inglês', 'DIGITAL.' === $depois['hero']['highlight'], $depois['hero']['highlight']);
ok('etapas de volta', 4 === count($depois['process']) && 'Design' === $depois['process'][2]['title']);

echo "\n== Coluna de serviços do rodapé ==\n";
$GLOBALS['__o'] = [];
$en = STCMS_Options::get('en');
ok('EN traz os links de serviço', 5 === count($en['footer']['col2_links'] ?? []), (string) count($en['footer']['col2_links'] ?? []));
ok('EN com rótulo em inglês', 'Web Development' === ($en['footer']['col2_links'][2]['label'] ?? ''), $en['footer']['col2_links'][2]['label'] ?? '(vazio)');
ok('EN apontando para /en/services', 0 === strpos((string)($en['footer']['col2_links'][0]['url'] ?? ''), '/en/services'), $en['footer']['col2_links'][0]['url'] ?? '(vazio)');

$form = formulario_en();
$form['footer']['col2_links'][0] = array('label'=>'Brand Systems','url'=>'/en/services/branding-identidade-visual');
$GLOBALS['__o']['stcms_options_en'] = STCMS_Options::sanitize_en( $form );
$depois = STCMS_Options::get('en');
ok('rótulo editado é salvo', 'Brand Systems' === $depois['footer']['col2_links'][0]['label'], $depois['footer']['col2_links'][0]['label']);
ok('a coluna mantém 5 links', 5 === count($depois['footer']['col2_links']), (string) count($depois['footer']['col2_links']));
ok('os demais links seguem intactos', 'UI / UX Design' === $depois['footer']['col2_links'][1]['label'], $depois['footer']['col2_links'][1]['label']);

$GLOBALS['__o'] = [];
$pt = STCMS_Options::get('pt');
$formPt = $pt; $formPt['hero']['title_lines'] = implode("\n", $pt['hero']['title_lines']);
$formPt['footer']['col2_links'][0] = array('label'=>'Branding','url'=>'/servicos/branding-identidade-visual');
$GLOBALS['__o']['stcms_options'] = STCMS_Options::sanitize( $formPt );
$depoisPt = STCMS_Options::get('pt');
ok('PT: rótulo salvo', 'Branding' === $depoisPt['footer']['col2_links'][0]['label'], $depoisPt['footer']['col2_links'][0]['label']);
ok('PT: URL salva', '/servicos/branding-identidade-visual' === $depoisPt['footer']['col2_links'][0]['url'], $depoisPt['footer']['col2_links'][0]['url']);
ok('PT: coluna com 5 links', 5 === count($depoisPt['footer']['col2_links']), (string) count($depoisPt['footer']['col2_links']));

echo "\n== Newsletter editável nos dois idiomas ==\n";
$GLOBALS['__o'] = [];
$pt = STCMS_Options::get('pt'); $en = STCMS_Options::get('en');
ok('PT tem título da newsletter', 'Newsletter' === ($pt['footer']['newsletter_title'] ?? ''), $pt['footer']['newsletter_title'] ?? '(ausente)');
ok('PT tem texto do botão', 'Inscrever' === ($pt['footer']['newsletter_button'] ?? ''), $pt['footer']['newsletter_button'] ?? '(ausente)');
ok('EN tem texto do botão', 'Subscribe' === ($en['footer']['newsletter_button'] ?? ''), $en['footer']['newsletter_button'] ?? '(ausente)');
$formPt = $pt; $formPt['hero']['title_lines'] = implode("\n", $pt['hero']['title_lines']);
$formPt['footer']['newsletter_button'] = 'Quero receber';
$GLOBALS['__o']['stcms_options'] = STCMS_Options::sanitize( $formPt );
ok('PT: botão editado é salvo', 'Quero receber' === STCMS_Options::get('pt')['footer']['newsletter_button'], STCMS_Options::get('pt')['footer']['newsletter_button']);
$formEn = formulario_en(); $formEn['footer']['newsletter_button'] = 'Join the list';
$GLOBALS['__o']['stcms_options_en'] = STCMS_Options::sanitize_en( $formEn );
ok('EN: botão editado é salvo', 'Join the list' === STCMS_Options::get('en')['footer']['newsletter_button'], STCMS_Options::get('en')['footer']['newsletter_button']);
ok('PT não foi afetado', 'Quero receber' === STCMS_Options::get('pt')['footer']['newsletter_button']);

echo "\n== Links da última linha do rodapé ==\n";
$GLOBALS['__o'] = [];
$formPt = STCMS_Options::get('pt'); $formPt['hero']['title_lines'] = implode("\n", $formPt['hero']['title_lines']);
$formPt['footer']['legal'] = array(array('label'=>'Privacidade','url'=>'/p/privacidade'));
$GLOBALS['__o']['stcms_options'] = STCMS_Options::sanitize( $formPt );
$dep = STCMS_Options::get('pt');
ok('guarda exatamente 1 link', 1 === count($dep['footer']['legal']), (string) count($dep['footer']['legal']));
$formPt['footer']['legal'] = array();
$GLOBALS['__o']['stcms_options'] = STCMS_Options::sanitize( $formPt );
ok('lista vazia é aceita', 0 === count(STCMS_Options::get('pt')['footer']['legal'] ?? array()), (string) count(STCMS_Options::get('pt')['footer']['legal'] ?? array()));

echo "\n== Menu de destinos ==\n";
$grupos = STCMS_Options::destinos();
ok('lista as páginas do site', isset($grupos['Páginas do site']));
ok('inclui a home', '/' === ($grupos['Páginas do site'][0]['url'] ?? ''), $grupos['Páginas do site'][0]['url'] ?? '');
ok('inclui contato', '/contato' === ($grupos['Páginas do site'][5]['url'] ?? ''), $grupos['Páginas do site'][5]['url'] ?? '');
ok('etapas do processo entram', isset($grupos['Processo']) && 4 === count($grupos['Processo']), isset($grupos['Processo']) ? (string) count($grupos['Processo']) : 'ausente');
ok('etapa aponta para /processo/…', '/processo/diagnostico' === ($grupos['Processo'][0]['url'] ?? ''), $grupos['Processo'][0]['url'] ?? '');

echo "\n== O português nunca é afetado ==\n";
$pt = STCMS_Options::get('pt');
ok('hero PT intacto', 'TRANSFORMAMOS' === $pt['hero']['title_lines'][0], $pt['hero']['title_lines'][0]);
ok('etapa PT intacta', 'Design' === $pt['process'][2]['title'], $pt['process'][2]['title']);
ok('nav PT intacto', '/contato' === $pt['nav']['cta_url'], $pt['nav']['cta_url']);

echo "\n$ok passaram, $ko falharam\n";
exit($ko ? 1 : 0);
