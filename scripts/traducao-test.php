<?php
error_reporting(E_ALL & ~E_DEPRECATED);
define('ABSPATH', __DIR__.'/'); define('HOUR_IN_SECONDS',3600);
$P = __DIR__.'/../wordpress/wp-content/plugins/studio-tabi-cms';

$GLOBALS['__o']     = ['stcms_site_origin' => 'https://studiotabi.com.br'];
$GLOBALS['__posts'] = [];
$GLOBALS['__meta']  = [];
$GLOBALS['__cats']  = [];
$GLOBALS['__capa']  = [];
$GLOBALS['__id']    = 0;
$GLOBALS['__saida'] = null;
$GLOBALS['__lixo']  = [];

function get_option($k,$d=false){return $GLOBALS['__o'][$k]??$d;}
function update_option($k,$v){$GLOBALS['__o'][$k]=$v;return true;} function add_option($k,$v){return update_option($k,$v);}
function get_transient($k){return false;} function set_transient($k,$v,$t){return true;}
function get_home_url(){return 'https://studiotabi.com.br';} function untrailingslashit($s){return rtrim($s,'/');}
function esc_url_raw($u,$p=null){return $u;} function esc_url($u){return $u;} function wp_unslash($v){return $v;}
function sanitize_key($s){return strtolower(preg_replace('/[^a-z0-9_]/i','',$s));}
function wp_parse_url($u,$c=-1){return $c===PHP_URL_HOST?parse_url($u,PHP_URL_HOST):parse_url($u);}
function sanitize_text_field($s){return trim((string)$s);} function sanitize_textarea_field($s){return trim((string)$s);}
function sanitize_email($s){return $s;} function is_email($s){return (bool)filter_var($s,FILTER_VALIDATE_EMAIL);}
function wp_strip_all_tags($s){return strip_tags((string)$s);} function apply_filters($t,$v){return $v;}
function esc_html($s){return $s;} function esc_attr($s){return $s;} function esc_textarea($s){return $s;}
function wp_kses_post($s){return $s;}
function wp_get_attachment_image_url($i,$s=null){return $i ? "https://cms/img/$i.jpg" : '';}
function get_bloginfo($x=''){return 'Studio Tabi';} function wp_mail(...$a){return true;}
function sanitize_title($s){
    $s = strtr((string)$s, ['á'=>'a','à'=>'a','â'=>'a','ã'=>'a','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u','ç'=>'c']);
    return trim(preg_replace('/-+/','-', preg_replace('/[^a-z0-9]+/i','-', strtolower($s))), '-');
}
function checked($a,$b,$c=true){return '';} function mysql2date($f,$d,$t=true){return date($f, strtotime($d));}
function wpautop($s){return "<p>$s</p>";} function is_wp_error($x){return false;}
function current_user_can($c){return true;} function check_admin_referer($a){return true;}
function admin_url($p=''){return 'https://cms/wp-admin/'.$p;} function wp_nonce_url($u,$a){return $u;}
function get_edit_post_link($id){return "https://cms/wp-admin/post.php?post=$id";}
function maybe_unserialize($v){return $v;}
class Redirecionou extends Exception {}
function wp_safe_redirect($u){$GLOBALS['__saida']=$u;throw new Redirecionou($u);}
function wp_trash_post($id){$GLOBALS['__lixo'][]=$id; unset($GLOBALS['__posts'][$id]); return true;}
if(!defined('OBJECT')) define('OBJECT','OBJECT');

function criar_post($tipo,$titulo,$conteudo='',$slug=null,$ordem=0,$metas=[]){
    $p = new stdClass;
    $p->ID = ++$GLOBALS['__id'];
    $p->post_type=$tipo; $p->post_title=$titulo; $p->post_content=$conteudo; $p->post_excerpt='';
    $p->post_status='publish'; $p->post_name=$slug ?? sanitize_title($titulo); $p->menu_order=$ordem;
    $p->post_date='2026-01-01 10:00:00'; $p->post_modified_gmt='2026-01-01 10:00:00'; $p->post_author=1;
    $GLOBALS['__posts'][$p->ID]=$p;
    foreach ($metas as $k=>$v) { $GLOBALS['__meta'][$p->ID][$k]=$v; }
    return $p->ID;
}
function wp_insert_post($a){
    $id = criar_post($a['post_type'],$a['post_title'],$a['post_content']??'',$a['post_name']??null,$a['menu_order']??0);
    $GLOBALS['__posts'][$id]->post_excerpt = $a['post_excerpt'] ?? '';
    return $id;
}
function get_post($id){return $GLOBALS['__posts'][$id] ?? null;}
function get_page_by_path($slug,$o=null,$t=null){
    foreach ($GLOBALS['__posts'] as $p) { if ($p->post_name===$slug && (null===$t || $p->post_type===$t || 'page'===$p->post_type)) return $p; }
    return null;
}
function update_post_meta($id,$k,$v){$GLOBALS['__meta'][$id][$k]=$v;return true;}
function delete_post_meta($id,$k){unset($GLOBALS['__meta'][$id][$k]);return true;}
function get_post_meta($id,$k='',$s=true){
    if ('' === $k) { $out=[]; foreach ($GLOBALS['__meta'][$id]??[] as $ck=>$cv) { $out[$ck]=[$cv]; } return $out; }
    return $GLOBALS['__meta'][$id][$k] ?? '';
}
function get_post_field($campo,$id){return $GLOBALS['__posts'][$id]->$campo ?? '';}
function get_post_thumbnail_id($id){return $GLOBALS['__capa'][$id] ?? 0;}
function get_the_post_thumbnail_url($p,$s=null){$id=is_object($p)?$p->ID:$p;return ($GLOBALS['__capa'][$id]??0)?"https://cms/img/{$GLOBALS['__capa'][$id]}.jpg":'';}
function get_the_title($p){return is_object($p)?$p->post_title:($GLOBALS['__posts'][$p]->post_title??'');}
function get_the_category($id){return [];} function wp_get_post_categories($id){return $GLOBALS['__cats'][$id] ?? [];}
function has_excerpt($p){return false;} function get_the_excerpt($p){return '';}
function wp_trim_words($t,$n=55,$m='…'){$w=preg_split('/\s+/', strip_tags((string)$t)); return count($w)>$n ? implode(' ', array_slice($w,0,$n)).$m : implode(' ', $w);}
function get_the_date($f,$p){return '01 Jan 2026';} function get_the_author_meta($c,$u){return 'Studio Tabi';}
function get_avatar_url($u,$a=[]){return '';} function get_the_tags($id){return [];}

function get_posts($a){
    $tipos = (array) ($a['post_type'] ?? 'post');
    $mq    = $a['meta_query'] ?? [];
    $out   = [];
    foreach ($GLOBALS['__posts'] as $p) {
        if (!in_array($p->post_type, $tipos, true) && !in_array('any', $tipos, true)) { continue; }
        if ($mq && ! meta_bate($p->ID, $mq)) { continue; }
        $out[] = $p;
    }
    usort($out, fn($a,$b) => [$a->menu_order,$a->ID] <=> [$b->menu_order,$b->ID]);
    if (($a['fields'] ?? '') === 'ids') { return array_map(fn($p)=>$p->ID, $out); }
    return $out;
}
function meta_bate($id,$mq){
    foreach (array_filter($mq,'is_array') as $c) {
        $valor = $GLOBALS['__meta'][$id][$c['key']] ?? null;
        if (($c['compare'] ?? '=') === 'EXISTS') { if (null === $valor) return false; }
        elseif ((string)$valor !== (string)($c['value'] ?? '')) { return false; }
    }
    return true;
}
class WP_REST_Response{public $data;public $status;function __construct($d,$s=200){$this->data=$d;$this->status=$s;}}
class WP_REST_Request{private $p;function __construct($p=[]){$this->p=$p;} function get_json_params(){return $this->p;} function get_params(){return $this->p;} function get_param($k){return $this->p[$k]??null;}}
class WP_Query{public $posts=[];public $found_posts=0;public $max_num_pages=0;function __construct($a){$this->posts=get_posts($a+['numberposts'=>-1]);$this->found_posts=count($this->posts);$this->max_num_pages=1;}}
function wp_reset_postdata(){}

require_once "$P/includes/defaults.php"; require_once "$P/includes/class-options.php";
require_once "$P/includes/class-traducao.php"; require_once "$P/includes/class-rest.php";

$ok = 0; $ko = 0;
function ok($rotulo,$cond,$valor=''){global $ok,$ko;$cond?$ok++:$ko++;echo '  '.($cond?'✓':'✗')."  $rotulo".($valor!==''?": $valor":'')."\n";}
function achar($tipo,$titulo){foreach($GLOBALS['__posts'] as $p){if($p->post_type===$tipo && $p->post_title===$titulo) return $p;} return null;}

$ordem = 0;
foreach (stcms_default_services() as $s) {
    criar_post('st_service', $s['title'], $s['body'], null, $ordem++, ['stcms_num'=>$s['num'], 'stcms_page_content'=>$s['content'] ?? '']);
}
$meu = criar_post('st_service','Consultoria de SEO','Auditoria técnica e plano de conteúdo.',null,99,['stcms_num'=>'07']);
$GLOBALS['__capa'][$meu] = 555;

$ordem = 0;
foreach (stcms_default_faq() as $f) { criar_post('st_faq', $f['q'], $f['a'], null, $ordem++); }

$ordem = 0;
foreach (stcms_default_projects() as $p) {
    criar_post('st_project', $p['name'], '', null, $ordem++, [
        'stcms_num'=>$p['id'], 'stcms_name'=>$p['name'], 'stcms_category'=>$p['category'], 'stcms_year'=>$p['year'],
        'stcms_bg'=>$p['bg'], 'stcms_accent'=>$p['accent'], 'stcms_featured'=>$p['featured']?'1':'', 'stcms_home'=>'1',
        'stcms_client'=>$p['client'], 'stcms_duration'=>$p['duration'],
        'stcms_challenge'=>$p['challenge'], 'stcms_solution'=>$p['solution'],
        'stcms_scope'=>array_map(fn($v)=>['item'=>$v], $p['scope']),
        'stcms_mockup'=>array_map(fn($v)=>['item'=>$v], $p['mockup_lines']),
        'stcms_results'=>$p['results'],
    ]);
}
$artigo = criar_post('post','Presença digital não é custo — é ativo estratégico','<p>Texto original.</p>');
criar_post('page','Diagnóstico','<p>Mergulhamos no negócio.</p>','diagnostico');
$total_inicial = count($GLOBALS['__posts']);

echo "== O conteúdo é um só: nada é duplicado ==\n";
$_GET = ['stcms_preencher_en'=>'1'];
try { STCMS_Traducao::maybe_preencher(); } catch (Redirecionou $e) {}
$_GET = [];
ok('nenhum post criado', count($GLOBALS['__posts']) === $total_inicial, count($GLOBALS['__posts']).' de '.$total_inicial);
ok('volta para a aba en', str_contains((string) $GLOBALS['__saida'], 'stcms_lang=en&stcms_preenchidos='), (string) $GLOBALS['__saida']);

echo "== A tradução fica em metas do próprio item ==\n";
$svc = achar('st_service','Branding & Identidade Visual');
ok('título em inglês guardado no próprio serviço', 'Branding & Visual Identity' === get_post_meta($svc->ID,'stcms_en_title'), get_post_meta($svc->ID,'stcms_en_title'));
ok('página interna traduzida', str_contains((string) get_post_meta($svc->ID,'stcms_en_page_content'), 'A brand is not a logo'));
ok('título em português intacto', 'Branding & Identidade Visual' === $svc->post_title, $svc->post_title);
ok('slug intacto', 'branding-identidade-visual' === $svc->post_name, $svc->post_name);

$pag = achar('page','Diagnóstico');
ok('página de processo traduzida no lugar', 'Diagnosis' === get_post_meta($pag->ID,'stcms_en_title'), get_post_meta($pag->ID,'stcms_en_title'));

echo "== A API troca o texto e mantém a mesma quantidade de itens ==\n";
$pt = STCMS_Rest::get_content(new WP_REST_Request(['lang'=>'pt']))->data;
$en = STCMS_Rest::get_content(new WP_REST_Request(['lang'=>'en']))->data;
ok('mesma contagem de serviços', count($pt['services']) === count($en['services']) && 7 === count($pt['services']), count($pt['services']).'/'.count($en['services']));
ok('mesma contagem de FAQ', count($pt['faq']) === count($en['faq']) && 6 === count($pt['faq']), count($pt['faq']).'/'.count($en['faq']));
ok('mesma contagem de projetos', count($pt['projects']) === count($en['projects']) && 4 === count($pt['projects']));
ok('serviço PT', 'Branding & Identidade Visual' === $pt['services'][0]['title'], $pt['services'][0]['title']);
ok('serviço EN', 'Branding & Visual Identity' === $en['services'][0]['title'], $en['services'][0]['title']);
ok('mesmo slug nos dois idiomas', $pt['services'][0]['slug'] === $en['services'][0]['slug'], $en['services'][0]['slug']);
ok('FAQ PT', str_starts_with($pt['faq'][0]['q'], 'Como funciona'), $pt['faq'][0]['q']);
ok('FAQ EN', str_starts_with($en['faq'][0]['q'], 'How does the process'), $en['faq'][0]['q']);
ok('projeto: categoria EN', 'Branding & UI' === $en['projects'][0]['category'], $en['projects'][0]['category']);
ok('projeto: duração EN', '14 weeks' === $en['projects'][0]['detail']['duration'], $en['projects'][0]['detail']['duration']);
ok('projeto: escopo EN', 'Visual Identity' === $en['projects'][0]['detail']['scope'][0], $en['projects'][0]['detail']['scope'][0]);
ok('projeto: escopo mantém o tamanho', count($pt['projects'][0]['detail']['scope']) === count($en['projects'][0]['detail']['scope']));
ok('projeto: rótulo do resultado EN', 'Increase in conversion' === $en['projects'][0]['detail']['results'][0]['label'], $en['projects'][0]['detail']['results'][0]['label']);
ok('projeto: valor do resultado preservado', $pt['projects'][0]['detail']['results'][0]['value'] === $en['projects'][0]['detail']['results'][0]['value'], $en['projects'][0]['detail']['results'][0]['value']);
ok('projeto: nome não traduzido', $pt['projects'][0]['name'] === $en['projects'][0]['name'], $en['projects'][0]['name']);

echo "== Campo sem tradução cai no português ==\n";
$meuPt = null; $meuEn = null;
foreach ($pt['services'] as $s) { if ('consultoria-de-seo' === $s['slug']) $meuPt = $s; }
foreach ($en['services'] as $s) { if ('consultoria-de-seo' === $s['slug']) $meuEn = $s; }
ok('serviço do autor aparece nos dois', $meuPt && $meuEn);
ok('mostra o texto em português', $meuEn && 'Consultoria de SEO' === $meuEn['title'], $meuEn['title'] ?? '—');

echo "== Página e artigo servem os dois idiomas ==\n";
$pgPt = STCMS_Rest::get_page(new WP_REST_Request(['slug'=>'diagnostico','lang'=>'pt']))->data;
$pgEn = STCMS_Rest::get_page(new WP_REST_Request(['slug'=>'diagnostico','lang'=>'en']))->data;
ok('mesma URL, títulos diferentes', 'Diagnóstico' === $pgPt['title'] && 'Diagnosis' === $pgEn['title'], $pgPt['title'].' / '.$pgEn['title']);
ok('mesmo slug', $pgPt['slug'] === $pgEn['slug'], $pgEn['slug']);
$artPt = STCMS_Rest::get_single_post(new WP_REST_Request(['slug'=>$GLOBALS['__posts'][$artigo]->post_name,'lang'=>'pt']))->data;
$artEn = STCMS_Rest::get_single_post(new WP_REST_Request(['slug'=>$GLOBALS['__posts'][$artigo]->post_name,'lang'=>'en']))->data;
ok('artigo PT', str_contains($artPt['title'], 'Presença digital'), $artPt['title']);
ok('artigo EN', str_contains($artEn['title'], 'Digital presence'), $artEn['title']);

echo "== Idempotência e respeito ao que o autor escreveu ==\n";
update_post_meta($svc->ID, 'stcms_en_title', 'My own English title');
$_GET = ['stcms_preencher_en'=>'1'];
try { STCMS_Traducao::maybe_preencher(); } catch (Redirecionou $e) {}
$_GET = [];
ok('não sobrescreve tradução existente', 'My own English title' === get_post_meta($svc->ID,'stcms_en_title'), get_post_meta($svc->ID,'stcms_en_title'));
ok('continua sem criar posts', count($GLOBALS['__posts']) === $total_inicial, (string) count($GLOBALS['__posts']));
update_post_meta($svc->ID, 'stcms_en_title', 'Branding & Visual Identity');

echo "== Sitemap: hreflang recíproco só onde há tradução ==\n";
$xml = STCMS_Rest::get_sitemap()->data['xml'];
ok('serviço traduzido: as duas URLs', str_contains($xml,'<loc>https://studiotabi.com.br/servicos/branding-identidade-visual</loc>') && str_contains($xml,'<loc>https://studiotabi.com.br/en/services/branding-identidade-visual</loc>'));
ok('alternate recíproco', str_contains($xml,'hreflang="en" href="https://studiotabi.com.br/en/services/branding-identidade-visual"') && str_contains($xml,'hreflang="pt-BR" href="https://studiotabi.com.br/servicos/branding-identidade-visual"'));
ok('serviço sem tradução: só a URL em português', str_contains($xml,'<loc>https://studiotabi.com.br/servicos/consultoria-de-seo</loc>') && ! str_contains($xml,'/en/services/consultoria-de-seo'));
ok('etapa de processo traduzida entra no /en', str_contains($xml,'<loc>https://studiotabi.com.br/en/process/diagnostico</loc>'));
ok('artigo traduzido entra no /en', str_contains($xml,'/en/blog/presenca-digital-nao-e-custo-e-ativo-estrategico'));
ok('XML bem formado', (bool) @simplexml_load_string($xml));

echo "== Desfaz as duplicatas da versão anterior ==\n";
$copia = criar_post('st_service','Branding & Visual Identity','Brand systems that communicate.',null,0,[
    'stcms_traducao_de' => (string) $svc->ID, 'stcms_lang' => 'en', 'stcms_num' => '01',
]);
$copiaFaq = criar_post('st_faq','How does the process work?','We start with a diagnosis.',null,0,[
    'stcms_traducao_de' => (string) achar('st_faq','Como funciona o processo de trabalho?')->ID, 'stcms_lang' => 'en',
]);
delete_post_meta($svc->ID, 'stcms_en_title');
ok('2 duplicatas detectadas', 2 === count(STCMS_Traducao::duplicatas()), (string) count(STCMS_Traducao::duplicatas()));

$_GET = ['stcms_limpar_duplicatas'=>'1'];
try { STCMS_Traducao::maybe_limpar_duplicatas(); } catch (Redirecionou $e) {}
$_GET = [];
ok('duplicatas foram para a lixeira', 2 === count($GLOBALS['__lixo']), (string) count($GLOBALS['__lixo']));
ok('nenhuma duplicata restante', 0 === count(STCMS_Traducao::duplicatas()));
ok('texto da cópia foi aproveitado', 'Branding & Visual Identity' === get_post_meta($svc->ID,'stcms_en_title'), get_post_meta($svc->ID,'stcms_en_title'));
ok('meta de idioma antiga removida', '' === (string) get_post_meta($svc->ID,'stcms_lang'));
ok('total volta ao original', count($GLOBALS['__posts']) === $total_inicial, count($GLOBALS['__posts']).' de '.$total_inicial);

$en = STCMS_Rest::get_content(new WP_REST_Request(['lang'=>'en']))->data;
$pt = STCMS_Rest::get_content(new WP_REST_Request(['lang'=>'pt']))->data;
ok('API sem duplicatas depois da limpeza', 7 === count($pt['services']) && 7 === count($en['services']), count($pt['services']).'/'.count($en['services']));
ok('FAQ sem duplicatas', 6 === count($pt['faq']) && 6 === count($en['faq']), count($pt['faq']).'/'.count($en['faq']));

echo "== Páginas de processo órfãs da versão 1.19 ==\n";
$orfa = criar_post('page','Diagnosis','<p>We dive into the business.</p>','diagnostico-en',0,array('stcms_lang'=>'en'));
$ptPag = achar('page','Diagnóstico');
delete_post_meta($ptPag->ID,'stcms_en_title');
delete_post_meta($ptPag->ID,'stcms_en_body');

$antesOrfa = count($GLOBALS['__posts']);
ok('a órfã é detectada como duplicata', 1 === count(STCMS_Traducao::duplicatas()), (string) count(STCMS_Traducao::duplicatas()));

$pgEnAntes = STCMS_Rest::get_page(new WP_REST_Request(['slug'=>'diagnostico','lang'=>'en']))->data;
ok('antes da limpeza o /en mostra português', 'Diagnóstico' === $pgEnAntes['title'], $pgEnAntes['title']);

$_GET = ['stcms_limpar_duplicatas'=>'1'];
try { STCMS_Traducao::maybe_limpar_duplicatas(); } catch (Redirecionou $e) {}
$_GET = [];

ok('a órfã foi para a lixeira', count($GLOBALS['__posts']) === $antesOrfa - 1, (string) count($GLOBALS['__posts']));
ok('título em inglês aproveitado', 'Diagnosis' === get_post_meta($ptPag->ID,'stcms_en_title'), get_post_meta($ptPag->ID,'stcms_en_title'));
ok('texto em inglês aproveitado', str_contains((string) get_post_meta($ptPag->ID,'stcms_en_body'), 'We dive into the business'));

$pgPt = STCMS_Rest::get_page(new WP_REST_Request(['slug'=>'diagnostico','lang'=>'pt']))->data;
$pgEn = STCMS_Rest::get_page(new WP_REST_Request(['slug'=>'diagnostico','lang'=>'en']))->data;
ok('/processo/diagnostico segue em português', 'Diagnóstico' === $pgPt['title'], $pgPt['title']);
ok('/en/process/diagnostico agora em inglês', 'Diagnosis' === $pgEn['title'], $pgEn['title']);
ok('o corpo também troca', str_contains($pgEn['content'],'We dive into the business') && str_contains($pgPt['content'],'Mergulhamos'), 'ok');
ok('não sobrou duplicata', 0 === count(STCMS_Traducao::duplicatas()));

echo "\n$ok passaram, $ko falharam\n";
exit($ko ? 1 : 0);
