<?php
error_reporting(E_ALL & ~E_DEPRECATED);
define('ABSPATH', __DIR__.'/');
$P = __DIR__.'/../wordpress/wp-content/plugins/studio-tabi-cms';

$GLOBALS['__posts'] = []; $GLOBALS['__meta'] = []; $GLOBALS['__id'] = 0;
$GLOBALS['__editores'] = [];

function get_option($k,$d=false){return $d;} function update_option($k,$v){return true;}
function add_option($k,$v){return true;} function get_transient($k){return false;}
function set_transient($k,$v,$t){return true;} function get_home_url(){return 'https://studiotabi.com.br';}
function untrailingslashit($s){return rtrim($s,'/');} function esc_url_raw($u,$p=null){return $u;}
function esc_url($u){return $u;} function sanitize_key($s){return strtolower(preg_replace('/[^a-z0-9_]/i','',$s));}
function wp_parse_url($u,$c=-1){return parse_url($u,$c);}
function esc_html($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function esc_attr($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function esc_textarea($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function checked($a,$b,$c=true){return '';} function apply_filters($t,$v){return $v;}
function wp_get_attachment_image_url($i,$s=null){return '';} function get_bloginfo($x=''){return 'Studio Tabi';}
function sanitize_title($s){return $s;} function mysql2date($f,$d,$t=true){return $d;}
function is_wp_error($x){return false;} function wpautop($s){return $s;}
function admin_url($p=''){return '/wp-admin/'.$p;} function wp_nonce_url($u,$a){return $u;}
function get_edit_post_link($id){return '#';} function get_post($id){return $GLOBALS['__posts'][$id]??null;}
function get_page_by_path($s,$o=null,$t=null){return null;} function get_posts($a){return [];}
function wp_safe_redirect($u){} function current_user_can($c,$id=null){return true;}
function check_admin_referer($a){return true;} function wp_trash_post($id){return true;}
function maybe_unserialize($v){return $v;}
function wp_strip_all_tags($s){return strip_tags((string)$s);}
if(!defined('OBJECT')) define('OBJECT','OBJECT');
if(!defined('DOING_AUTOSAVE')) { }

function wp_unslash($v){ return is_array($v) ? array_map('wp_unslash', $v) : stripslashes((string)$v); }
function sanitize_text_field($s){
    $s = strip_tags((string)$s);
    $s = preg_replace('/[\r\n\t ]+/', ' ', $s);
    return trim($s);
}
function sanitize_textarea_field($s){
    $s = strip_tags((string)$s);
    return trim($s);
}
function wp_kses_post($s){
    $s = preg_replace('#<script\b[^>]*>.*?</script>#is', '', (string)$s);
    $s = preg_replace('#<iframe\b[^>]*>.*?</iframe>#is', '', $s);
    $s = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $s);
    $s = preg_replace('#<a([^>]*)\shref\s*=\s*(["\'])\s*javascript:[^"\']*\2#i', '<a$1', $s);
    return $s;
}
function wp_verify_nonce($n,$a){ return $n === 'valido' ? 1 : false; }
function wp_nonce_field($a,$n,$r=true,$e=true){ echo '<input type="hidden" name="'.$n.'" value="valido" />'; }
function update_post_meta($id,$k,$v){ $GLOBALS['__meta'][$id][$k] = $v; return true; }
function delete_post_meta($id,$k){ unset($GLOBALS['__meta'][$id][$k]); return true; }
function get_post_meta($id,$k='',$s=true){
    if ('' === $k) { $o=[]; foreach ($GLOBALS['__meta'][$id]??[] as $ck=>$cv) $o[$ck]=[$cv]; return $o; }
    return $GLOBALS['__meta'][$id][$k] ?? '';
}
function add_meta_box($id,$t,$cb,$tipo,$ctx='advanced',$pri='default'){ return true; }
function add_action($h,$cb,$p=10,$n=1){ return true; }
function add_filter($h,$cb,$p=10,$n=1){ return true; }
function wp_editor($conteudo,$editor_id,$args=array()){
    $GLOBALS['__editores'][] = array('id'=>$editor_id, 'name'=>$args['textarea_name'] ?? $editor_id);
    echo '<textarea id="'.$editor_id.'" name="'.($args['textarea_name'] ?? $editor_id).'">'.esc_textarea($conteudo).'</textarea>';
}
function wp_is_post_revision($id){ return false; }

require_once "$P/includes/defaults.php";
require_once "$P/includes/class-options.php";
require_once "$P/includes/class-traducao.php";
require_once "$P/includes/class-meta.php";

$ok=0; $ko=0;
function ok($r,$c,$v=''){global $ok,$ko;$c?$ok++:$ko++;echo '  '.($c?'✓':'✗')."  $r".($v!==''?": $v":'')."\n";}

function criar($tipo,$titulo='Item'){
    $p = new stdClass; $p->ID = ++$GLOBALS['__id']; $p->post_type=$tipo;
    $p->post_title=$titulo; $p->post_content=''; $p->post_excerpt=''; $p->post_name='item';
    $GLOBALS['__posts'][$p->ID]=$p; return $p;
}

echo "== IDs dos editores ricos (o TinyMCE exige minúsculas, dígitos e hífen) ==\n";
foreach (array('st_service','post','page') as $tipo) {
    $GLOBALS['__editores'] = [];
    ob_start(); STCMS_Traducao::render(criar($tipo)); ob_end_clean();
    foreach ($GLOBALS['__editores'] as $e) {
        $valido = (bool) preg_match('/^[a-z][a-z0-9-]*$/', $e['id']);
        ok("$tipo → editor '{$e['id']}'", $valido, $valido ? 'ok' : 'ID inválido para o TinyMCE');
    }
}
$GLOBALS['__editores'] = [];
ob_start(); STCMS_Meta::render_service_page(criar('st_service')); ob_end_clean();
foreach ($GLOBALS['__editores'] as $e) {
    $valido = (bool) preg_match('/^[a-z][a-z0-9-]*$/', $e['id']);
    ok("st_service (página interna) → editor '{$e['id']}'", $valido, $valido ? 'ok' : 'ID inválido para o TinyMCE');
}

echo "\n== O que sobrevive ao save da caixa em inglês ==\n";
$svc = criar('st_service','Branding');
$_POST = array(
    'stcms_en_nonce'        => 'valido',
    'stcms_en_title'        => 'Branding & Visual Identity',
    'stcms_en_body'         => 'Brand systems that <strong>communicate</strong> with precision.',
    'stcms_en_page_content' => '<p>Real content</p><ul><li>One</li></ul><script>alert(1)</script>',
);
STCMS_Traducao::save($svc->ID, $svc);
ok('título salvo', 'Branding & Visual Identity' === get_post_meta($svc->ID,'stcms_en_title'), get_post_meta($svc->ID,'stcms_en_title'));
$body = get_post_meta($svc->ID,'stcms_en_body');
ok('descrição preserva o <strong>', false !== strpos($body,'<strong>'), $body);
$rico = get_post_meta($svc->ID,'stcms_en_page_content');
ok('página interna preserva HTML', false !== strpos($rico,'<li>'), substr($rico,0,50));
ok('página interna remove <script>', false === strpos($rico,'<script'), 'ok');

echo "\n== Resposta do FAQ (HTML precisa sobreviver) ==\n";
$faq = criar('st_faq','Como funciona?');
$_POST = array('stcms_en_nonce'=>'valido','stcms_en_title'=>'How does it work?',
    'stcms_en_body'=>"We start with a diagnosis.\n\nThen a <em>roadmap</em>.");
STCMS_Traducao::save($faq->ID,$faq);
$r = get_post_meta($faq->ID,'stcms_en_body');
ok('resposta preserva o <em>', false !== strpos($r,'<em>'), $r);
ok('resposta preserva a quebra de parágrafo', false !== strpos($r,"\n"), str_replace("\n",'\\n',$r));

echo "\n== Listas do projeto (uma por linha) ==\n";
$proj = criar('st_project','Nuvem Finance');
$_POST = array('stcms_en_nonce'=>'valido',
    'stcms_en_scope'=>"Visual Identity\nUI/UX Design\nDesign System",
    'stcms_en_results'=>"Increase in conversion\nReduction in churn");
STCMS_Traducao::save($proj->ID,$proj);
$sc = get_post_meta($proj->ID,'stcms_en_scope');
ok('escopo mantém as 3 linhas', 3 === count(array_filter(explode("\n",$sc),'strlen')), str_replace("\n",' | ',$sc));

echo "\n== Apóstrofo e acento (magic quotes do WordPress) ==\n";
$svc2 = criar('st_service','Conteúdo');
$_POST = array('stcms_en_nonce'=>'valido',
    'stcms_en_title'  => addslashes("Let's Talk — Conteúdo & Estratégia"),
    'stcms_en_body'   => addslashes('Uma "citação" com acento: ação.'));
STCMS_Traducao::save($svc2->ID,$svc2);
ok('apóstrofo sem barra invertida', "Let's Talk — Conteúdo & Estratégia" === get_post_meta($svc2->ID,'stcms_en_title'), get_post_meta($svc2->ID,'stcms_en_title'));
ok('aspas sem barra invertida', false === strpos(get_post_meta($svc2->ID,'stcms_en_body'),'\\"'), get_post_meta($svc2->ID,'stcms_en_body'));

echo "\n== A caixa tem abas Português / English ==\n";
$proj2 = criar('st_project','Nuvem Finance');
update_post_meta($proj2->ID,'stcms_category','Branding & UI');
update_post_meta($proj2->ID,'stcms_challenge','A Nuvem Finance chegou como mais uma fintech.');
update_post_meta($proj2->ID,'stcms_scope',array(array('item'=>'Identidade Visual'),array('item'=>'UI/UX Design')));
update_post_meta($proj2->ID,'stcms_en_category','Branding & UI');
ob_start(); STCMS_Traducao::render($proj2); $html = ob_get_clean();
ok('aba Português presente', false !== strpos($html,'data-aba="pt"'));
ok('aba English presente', false !== strpos($html,'data-aba="en"'));
ok('painel PT presente', false !== strpos($html,'data-painel="pt"'));
ok('painel EN presente', false !== strpos($html,'data-painel="en"'));
ok('painel PT mostra a categoria em português', false !== strpos($html,'Branding &amp; UI'));
ok('painel PT mostra o desafio em português', false !== strpos($html,'mais uma fintech'));
ok('painel PT converte a lista do repetidor', false !== strpos($html,'Identidade Visual'));
ok('aponta onde editar o português', false !== strpos($html,'Dados do projeto'));
ok('EN não duplica campo de título do projeto no PT', 1 === substr_count($html,'name="stcms_en_category"'));
ok('nenhum campo do painel PT é editável', false === strpos($html,'name="stcms_category"'));

echo "\n== Segurança do save ==\n";
$x = criar('st_service');
$_POST = array('stcms_en_title'=>'sem nonce');
STCMS_Traducao::save($x->ID,$x);
ok('sem nonce não grava', '' === get_post_meta($x->ID,'stcms_en_title'));
$_POST = array('stcms_en_nonce'=>'errado','stcms_en_title'=>'nonce invalido');
STCMS_Traducao::save($x->ID,$x);
ok('nonce inválido não grava', '' === get_post_meta($x->ID,'stcms_en_title'));

echo "\n== Campo esvaziado remove a tradução ==\n";
$_POST = array('stcms_en_nonce'=>'valido','stcms_en_title'=>'   ');
STCMS_Traducao::save($svc->ID,$svc);
ok('título em branco apaga a meta', '' === get_post_meta($svc->ID,'stcms_en_title'));
ok('os outros campos continuam', '' !== get_post_meta($svc->ID,'stcms_en_page_content'));

echo "\n$ok passaram, $ko falharam\n";
exit($ko ? 1 : 0);
