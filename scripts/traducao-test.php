<?php
error_reporting(E_ALL & ~E_DEPRECATED);
define('ABSPATH', __DIR__.'/'); define('HOUR_IN_SECONDS',3600);
$P = __DIR__.'/../wordpress/wp-content/plugins/studio-tabi-cms';

$GLOBALS['__o']     = ['stcms_site_origin' => 'https://studiotabi.com.br'];
$GLOBALS['__posts'] = [];   // id => objeto
$GLOBALS['__meta']  = [];   // id => [chave => valor]
$GLOBALS['__cats']  = [];   // id => [term_ids]
$GLOBALS['__capa']  = [];   // id => attachment_id
$GLOBALS['__id']    = 0;
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
function wp_strip_all_tags($s){return strip_tags((string)$s);} function apply_filters($t,$v){return $v;}
function esc_html($s){return $s;} function esc_attr($s){return $s;} function esc_textarea($s){return $s;}
function wp_get_attachment_image_url($i,$s=null){return $i ? "https://cms/img/$i.jpg" : '';}
function get_bloginfo($x=''){return 'Studio Tabi';} function wp_mail(...$a){return true;}
// Espelha o sanitize_title do WordPress: translitera acentos antes de trocar por hífen.
function sanitize_title($s){
    $s = strtr((string)$s, ['á'=>'a','à'=>'a','â'=>'a','ã'=>'a','ä'=>'a','é'=>'e','ê'=>'e','è'=>'e','í'=>'i','î'=>'i',
        'ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o','ú'=>'u','û'=>'u','ü'=>'u','ç'=>'c','ñ'=>'n',
        'Á'=>'A','À'=>'A','Â'=>'A','Ã'=>'A','É'=>'E','Ê'=>'E','Í'=>'I','Ó'=>'O','Ô'=>'O','Õ'=>'O','Ú'=>'U','Ç'=>'C']);
    return trim(preg_replace('/-+/','-', preg_replace('/[^a-z0-9]+/i','-', strtolower($s))), '-');
}
function checked($a,$b,$c=true){return '';} function mysql2date($f,$d,$t=true){return date($f, strtotime($d));}
function wpautop($s){return "<p>$s</p>";} function is_wp_error($x){return false;}
function current_user_can($c){return true;} function check_admin_referer($a){return true;}
function admin_url($p=''){return 'https://cms/wp-admin/'.$p;} function wp_nonce_url($u,$a){return $u;}
function get_edit_post_link($id){return "https://cms/wp-admin/post.php?post=$id";}
function get_page_by_path($slug,$o=null,$t=null){
    foreach ($GLOBALS['__posts'] as $p) { if ('page'===$p->post_type && $p->post_name===$slug) return $p; }
    return null;
}
function maybe_unserialize($v){return $v;}
class Redirecionou extends Exception {}
function wp_safe_redirect($u){$GLOBALS['__saida']=$u;throw new Redirecionou($u);}
if(!defined('OBJECT')) define('OBJECT','OBJECT');

function criar_post($tipo,$titulo,$conteudo='',$slug=null,$ordem=0,$metas=[],$lang=null){
    $p = new stdClass;
    $p->ID = ++$GLOBALS['__id'];
    $p->post_type = $tipo; $p->post_title = $titulo; $p->post_content = $conteudo;
    $p->post_excerpt = ''; $p->post_status = 'publish';
    $p->post_name = $slug ?? sanitize_title($titulo);
    $p->menu_order = $ordem;
    $p->post_date = '2026-01-01 10:00:00'; $p->post_modified_gmt = '2026-01-01 10:00:00';
    $GLOBALS['__posts'][$p->ID] = $p;
    foreach ($metas as $k=>$v) { $GLOBALS['__meta'][$p->ID][$k] = $v; }
    if ($lang) { $GLOBALS['__meta'][$p->ID]['stcms_lang'] = $lang; }
    return $p->ID;
}
function wp_insert_post($a){
    $id = criar_post($a['post_type'],$a['post_title'],$a['post_content']??'',$a['post_name']??null,$a['menu_order']??0);
    $GLOBALS['__posts'][$id]->post_excerpt = $a['post_excerpt'] ?? '';
    if (!empty($a['post_date'])) { $GLOBALS['__posts'][$id]->post_date = $a['post_date']; }
    return $id;
}
function update_post_meta($id,$k,$v){$GLOBALS['__meta'][$id][$k]=$v;return true;}
function get_post_meta($id,$k='',$s=true){
    if ('' === $k) { // formato do get_post_meta($id) sem chave: [chave => [valor]]
        $out=[]; foreach ($GLOBALS['__meta'][$id]??[] as $ck=>$cv) { $out[$ck]=[$cv]; } return $out;
    }
    return $GLOBALS['__meta'][$id][$k] ?? '';
}
function get_post_field($campo,$id){return $GLOBALS['__posts'][$id]->$campo ?? '';}
function get_post_thumbnail_id($id){return $GLOBALS['__capa'][$id] ?? 0;}
function set_post_thumbnail($id,$att){$GLOBALS['__capa'][$id]=$att;return true;}
function get_the_post_thumbnail_url($p,$s=null){$id=is_object($p)?$p->ID:$p;return ($GLOBALS['__capa'][$id]??0)?"https://cms/img/{$GLOBALS['__capa'][$id]}.jpg":'';}
function get_the_title($p){return is_object($p)?$p->post_title:($GLOBALS['__posts'][$p]->post_title??'');}
function wp_get_post_categories($id){return $GLOBALS['__cats'][$id] ?? [];}
function wp_set_post_categories($id,$c){$GLOBALS['__cats'][$id]=$c;return true;}
function wp_get_post_tags($id,$a=[]){return [];} function wp_set_post_tags($id,$t){return true;}
function get_the_category($id){return [];}

function get_posts($a){
    $tipo = $a['post_type'] ?? 'post';
    $mq   = $a['meta_query'] ?? [];
    $out  = [];
    foreach ($GLOBALS['__posts'] as $p) {
        if ('any' !== $tipo && $p->post_type !== $tipo) { continue; }
        if ($mq && ! meta_bate($p->ID, $mq)) { continue; }
        $out[] = $p;
    }
    usort($out, fn($a,$b) => [$a->menu_order,$a->ID] <=> [$b->menu_order,$b->ID]);
    if (!empty($a['numberposts']) && $a['numberposts'] > 0) { $out = array_slice($out, 0, $a['numberposts']); }
    if (($a['fields'] ?? '') === 'ids') { return array_map(fn($p)=>$p->ID, $out); }
    return $out;
}
function meta_bate($id, $mq){
    $relacao = strtoupper($mq['relation'] ?? 'AND');
    $clausulas = array_filter($mq, 'is_array');
    $resultados = [];
    foreach ($clausulas as $c) {
        $valor   = $GLOBALS['__meta'][$id][$c['key']] ?? null;
        $compare = $c['compare'] ?? '=';
        if ('NOT EXISTS' === $compare)      { $resultados[] = (null === $valor); }
        elseif ('!=' === $compare)          { $resultados[] = (null !== $valor && $valor !== $c['value']); }
        else                                { $resultados[] = ((string) $valor === (string) $c['value']); }
    }
    if (!$resultados) { return true; }
    return 'OR' === $relacao ? in_array(true, $resultados, true) : ! in_array(false, $resultados, true);
}
class WP_REST_Response{public $data;public $status;function __construct($d,$s=200){$this->data=$d;$this->status=$s;}}
class WP_REST_Request{private $p;function __construct($p=[]){$this->p=$p;} function get_json_params(){return $this->p;} function get_params(){return $this->p;} function get_param($k){return $this->p[$k]??null;}}

require_once "$P/includes/defaults.php"; require_once "$P/includes/class-options.php";
require_once "$P/includes/class-traducao.php"; require_once "$P/includes/class-rest.php";

$ok = 0; $ko = 0;
function ok($rotulo,$cond,$valor=''){global $ok,$ko;$cond?$ok++:$ko++;echo '  '.($cond?'✓':'✗')."  $rotulo".($valor!==''?": $valor":'')."\n";}
function achar($tipo,$titulo){foreach($GLOBALS['__posts'] as $p){if($p->post_type===$tipo && $p->post_title===$titulo) return $p;} return null;}

// Conteúdo de partida: o que o plugin instala + um serviço escrito pelo autor.
$ordem = 0;
foreach (stcms_default_services() as $s) {
    criar_post('st_service', $s['title'], $s['content'] ?? $s['body'], null, $ordem++, ['stcms_num'=>$s['num'], 'stcms_page_content'=>$s['content'] ?? '']);
}
$proprio = criar_post('st_service','Consultoria de SEO','<p>Auditoria técnica e plano de conteúdo.</p>',null,99,['stcms_num'=>'07','stcms_page_content'=>'<p>Auditoria técnica e plano de conteúdo.</p>']);
$GLOBALS['__capa'][$proprio] = 555;

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
$artigo = criar_post('post','Presença digital não é custo — é ativo estratégico','<p>Texto original.</p>',null,0);
$GLOBALS['__cats'][$artigo] = [7];
$GLOBALS['__capa'][$artigo]  = 900;

$antes = count($GLOBALS['__posts']);

echo "== Antes de traduzir ==\n";
$pend = STCMS_Traducao::pendentes();
ok('7 serviços pendentes', 7 === $pend['st_service'], (string) $pend['st_service']);
ok('6 FAQs pendentes', 6 === $pend['st_faq'], (string) $pend['st_faq']);
ok('4 projetos pendentes', 4 === $pend['st_project'], (string) $pend['st_project']);
ok('1 artigo pendente', 1 === $pend['post'], (string) $pend['post']);

echo "== Executa a criação ==\n";
$_GET = ['stcms_traduzir'=>'1'];
try { STCMS_Traducao::maybe_criar(); } catch (Redirecionou $e) {}
$_GET = [];
ok('18 itens criados', count($GLOBALS['__posts']) - $antes === 18, (string) (count($GLOBALS['__posts']) - $antes));
ok('volta para a aba en', str_contains((string) $GLOBALS['__saida'], 'stcms_lang=en&stcms_traduzidos=18'), (string) $GLOBALS['__saida']);

echo "== Conteúdo padrão sai traduzido ==\n";
$svc = achar('st_service','Branding & Visual Identity');
ok('serviço traduzido existe', null !== $svc);
ok('slug com -en', $svc && 'branding-identidade-visual-en' === $svc->post_name, $svc->post_name ?? '—');
ok('marcado como en', $svc && 'en' === get_post_meta($svc->ID,'stcms_lang'));
ok('texto da página interna traduzido', $svc && str_contains(get_post_meta($svc->ID,'stcms_page_content'), 'A brand is not a logo'));
ok('número preservado', $svc && '01' === get_post_meta($svc->ID,'stcms_num'), get_post_meta($svc->ID ?? 0,'stcms_num'));

$faq = achar('st_faq','How does the process work?');
ok('FAQ traduzido', null !== $faq && str_contains($faq->post_content, 'in-depth diagnosis'));

$proj = achar('st_project','Nuvem Finance');
$projs_en = array_values(array_filter($GLOBALS['__posts'], fn($p)=>'st_project'===$p->post_type && 'en'===get_post_meta($p->ID,'stcms_lang')));
$pen = $projs_en[0] ?? null;
ok('projeto: categoria traduzida', $pen && 'Branding & UI' === get_post_meta($pen->ID,'stcms_category'), get_post_meta($pen->ID ?? 0,'stcms_category'));
ok('projeto: duração traduzida', $pen && '14 weeks' === get_post_meta($pen->ID,'stcms_duration'), get_post_meta($pen->ID ?? 0,'stcms_duration'));
ok('projeto: desafio traduzido', $pen && str_contains(get_post_meta($pen->ID,'stcms_challenge'), 'yet another generic fintech'));
ok('projeto: escopo em formato de linhas', $pen && (get_post_meta($pen->ID,'stcms_scope')[0]['item'] ?? '') === 'Visual Identity', json_encode(get_post_meta($pen->ID ?? 0,'stcms_scope')[0] ?? null));
ok('projeto: cor preservada', $pen && '#F20C25' === get_post_meta($pen->ID,'stcms_accent'), get_post_meta($pen->ID ?? 0,'stcms_accent'));

$art = achar('post','Digital presence is not a cost — it is a strategic asset');
ok('artigo traduzido', null !== $art && str_contains($art->post_content, 'expensive mistake'));
ok('artigo mantém a categoria', $art && [7] === wp_get_post_categories($art->ID));
ok('artigo mantém a imagem de capa', $art && 900 === get_post_thumbnail_id($art->ID));

echo "== Conteúdo do autor é duplicado para tradução manual ==\n";
$meu = achar('st_service','Consultoria de SEO');
$meus = array_values(array_filter($GLOBALS['__posts'], fn($p)=>'st_service'===$p->post_type && 'Consultoria de SEO'===$p->post_title));
ok('gerou a cópia', 2 === count($meus), (string) count($meus));
$copia = $meus[1] ?? null;
ok('cópia marcada como en', $copia && 'en' === get_post_meta($copia->ID,'stcms_lang'));
ok('cópia mantém o texto original', $copia && str_contains($copia->post_content, 'Auditoria técnica'));
ok('cópia mantém a imagem de capa', $copia && 555 === get_post_thumbnail_id($copia->ID));
ok('cópia mantém o número', $copia && '07' === get_post_meta($copia->ID,'stcms_num'), get_post_meta($copia->ID ?? 0,'stcms_num'));

echo "== Idempotência ==\n";
$agora = count($GLOBALS['__posts']);
$_GET = ['stcms_traduzir'=>'1'];
try { STCMS_Traducao::maybe_criar(); } catch (Redirecionou $e) {}
$_GET = [];
ok('rodar de novo não cria nada', count($GLOBALS['__posts']) === $agora, (string) (count($GLOBALS['__posts']) - $agora));
$pend = STCMS_Traducao::pendentes();
ok('nada mais pendente', 0 === array_sum($pend), json_encode($pend));

echo "== A API separa os idiomas ==\n";
$pt = STCMS_Rest::get_content(new WP_REST_Request(['lang'=>'pt']))->data;
$en = STCMS_Rest::get_content(new WP_REST_Request(['lang'=>'en']))->data;
ok('PT com 7 serviços', 7 === count($pt['services']), (string) count($pt['services']));
ok('EN com 7 serviços', 7 === count($en['services']), (string) count($en['services']));
ok('PT em português', 'Branding & Identidade Visual' === $pt['services'][0]['title'], $pt['services'][0]['title']);
ok('EN em inglês', 'Branding & Visual Identity' === $en['services'][0]['title'], $en['services'][0]['title']);
ok('FAQ PT em português', str_starts_with($pt['faq'][0]['q'], 'Como funciona'), $pt['faq'][0]['q']);
ok('FAQ EN em inglês', str_starts_with($en['faq'][0]['q'], 'How does the process'), $en['faq'][0]['q']);
ok('projetos separados', 4 === count($pt['projects']) && 4 === count($en['projects']), count($pt['projects']).'/'.count($en['projects']));

echo "== Sitemap declara hreflang recíproco no conteúdo traduzido ==\n";
$xml = STCMS_Rest::get_sitemap()->data['xml'];
ok('serviço PT listado', str_contains($xml, '<loc>https://studiotabi.com.br/servicos/branding-identidade-visual</loc>'));
ok('serviço EN listado', str_contains($xml, '<loc>https://studiotabi.com.br/en/services/branding-identidade-visual-en</loc>'));
ok('alternate PT->EN', str_contains($xml, '<xhtml:link rel="alternate" hreflang="en" href="https://studiotabi.com.br/en/services/branding-identidade-visual-en"/>'));
ok('alternate EN->PT', str_contains($xml, '<xhtml:link rel="alternate" hreflang="pt-BR" href="https://studiotabi.com.br/servicos/branding-identidade-visual"/>'));
ok('x-default aponta para o PT', substr_count($xml, 'hreflang="x-default" href="https://studiotabi.com.br/servicos/branding-identidade-visual"') >= 2);
ok('artigo com alternate', str_contains($xml, 'hreflang="en" href="https://studiotabi.com.br/en/blog/presenca-digital-nao-e-custo-e-ativo-estrategico-en"'));
ok('XML bem formado', (bool) @simplexml_load_string($xml));

echo "== Item sem tradução não ganha alternate inventado ==\n";
$novo = criar_post('st_service','Serviço novo sem tradução','<p>x</p>',null,120,['stcms_num'=>'08']);
$xml = STCMS_Rest::get_sitemap()->data['xml'];
ok('listado só em PT', str_contains($xml, '<loc>https://studiotabi.com.br/servicos/servico-novo-sem-traducao</loc>'));
ok('sem alternate', ! str_contains($xml, 'href="https://studiotabi.com.br/en/services/servico-novo-sem-traducao'));
ok('volta a aparecer como pendente', 1 === STCMS_Traducao::pendentes()['st_service']);

echo "\n$ok passaram, $ko falharam\n";
exit($ko ? 1 : 0);
