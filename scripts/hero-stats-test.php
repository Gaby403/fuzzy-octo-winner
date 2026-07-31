<?php
error_reporting(E_ALL & ~E_DEPRECATED);
define('ABSPATH', __DIR__.'/');
$P = dirname(__DIR__).'/wordpress/wp-content/plugins/studio-tabi-cms';
$GLOBALS['__o']=[];
function get_option($k,$d=false){return $GLOBALS['__o'][$k]??$d;}
function update_option($k,$v){$GLOBALS['__o'][$k]=$v;return true;}
function add_action(...$a){} function add_filter(...$a){}
function sanitize_text_field($s){return trim((string)$s);}
function sanitize_textarea_field($s){return trim((string)$s);}
function sanitize_email($s){return $s;} function sanitize_key($s){return $s;}
function esc_attr($s){return $s;} function esc_html($s){return $s;} function esc_textarea($s){return $s;}
function esc_url($s){return $s;} function esc_url_raw($s){return $s;} function wp_unslash($v){return $v;}
function wp_kses_post($s){return $s;}
function sanitize_title($s){$s=strtolower(trim((string)$s));$s=strtr($s,['á'=>'a','à'=>'a','ã'=>'a','â'=>'a','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','õ'=>'o','ô'=>'o','ú'=>'u','ç'=>'c']);return preg_replace('/[^a-z0-9]+/','-',$s);}
function is_email($s){return (bool)filter_var($s,FILTER_VALIDATE_EMAIL);} function absint($n){return abs((int)$n);}
function wp_parse_url($u,$c=-1){return parse_url($u,$c);}
require_once "$P/includes/defaults.php"; require_once "$P/includes/class-options.php";

$ok=0;$fail=0;
function checa($n,$c,$d=''){global $ok,$fail; if($c){$ok++;echo "  ok  $n\n";}else{$fail++;echo "FAIL  $n".($d?" — $d":'')."\n";}}

echo "== Bug 1: hero title_lines virando \"Array\" ==\n";
// Primeira gravação: veio do textarea, string.
$r1 = STCMS_Options::sanitize_en(['hero'=>['title_lines'=>"YOUR BRAND,\nONE DIGITAL"]]);
checa('textarea vira lista', $r1['hero']['title_lines']===['YOUR BRAND,','ONE DIGITAL'], json_encode($r1['hero']['title_lines']));
// Segunda passada: o WordPress reexecuta o sanitize sobre o valor já gravado (array).
$r2 = STCMS_Options::sanitize_en($r1);
checa('segunda passada não vira "Array"', $r2['hero']['title_lines']===['YOUR BRAND,','ONE DIGITAL'], json_encode($r2['hero']['title_lines']));
checa('nenhuma linha literal "Array"', !in_array('Array',(array)$r2['hero']['title_lines'],true));

echo "\n== Bug 2: números da seção Sobre zerados em /en ==\n";
$GLOBALS['__o']['stcms_options'] = ['about'=>['stats'=>[
  ['numeric'=>7,'suffix'=>'+','label'=>'ANOS DE MERCADO'],
  ['numeric'=>120,'suffix'=>'+','label'=>'PROJETOS ENTREGUES'],
  ['numeric'=>98,'suffix'=>'%','label'=>'TAXA DE RETENÇÃO'],
  ['numeric'=>3,'suffix'=>'×','label'=>'RETORNO MÉDIO EM 12M'],
]]];
// Na aba English o usuário traduz só o rótulo e deixa o número em branco.
$GLOBALS['__o']['stcms_options_en'] = ['about'=>['stats'=>[
  ['numeric'=>'','suffix'=>'','label'=>'YEARS IN THE MARKET'],
  ['numeric'=>'','suffix'=>'','label'=>'PROJECTS DELIVERED'],
  ['numeric'=>'','suffix'=>'','label'=>'RETENTION RATE'],
  ['numeric'=>'','suffix'=>'','label'=>'AVERAGE 12-MONTH RETURN'],
]]];
$en = STCMS_Options::get('en');
$s = $en['about']['stats'];
checa('número herda do português', (int)$s[0]['numeric']===7, 'numeric='.var_export($s[0]['numeric'],true));
checa('120 herdado', (int)$s[1]['numeric']===120, 'numeric='.var_export($s[1]['numeric'],true));
checa('98 herdado', (int)$s[2]['numeric']===98);
checa('3 herdado', (int)$s[3]['numeric']===3);
checa('sufixo herdado', $s[2]['suffix']==='%', 'suffix='.var_export($s[2]['suffix'],true));
checa('rótulo em inglês preservado', $s[0]['label']==='YEARS IN THE MARKET');
checa('português intacto', STCMS_Options::get('pt')['about']['stats'][0]['label']==='ANOS DE MERCADO');

echo "\n== Inglês ainda pode sobrescrever quando preenche ==\n";
$GLOBALS['__o']['stcms_options_en']['about']['stats'][0] = ['numeric'=>10,'suffix'=>'++','label'=>'TEN YEARS'];
$s2 = STCMS_Options::get('en')['about']['stats'];
checa('número digitado no inglês vence', (int)$s2[0]['numeric']===10, var_export($s2[0]['numeric'],true));
checa('sufixo digitado vence', $s2[0]['suffix']==='++');

echo "\n$ok passaram, $fail falharam\n";
exit($fail?1:0);
