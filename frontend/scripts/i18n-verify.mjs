import { createServer } from "node:http";
import { readFile } from "node:fs/promises";
import { existsSync } from "node:fs";
import { join, extname, dirname } from "node:path";
import { fileURLToPath } from "node:url";
import puppeteer from "puppeteer-core";
const DIST = join(dirname(fileURLToPath(import.meta.url)), "..", "dist");
const PORTA = 4611;
const MIME = { ".html":"text/html; charset=utf-8", ".js":"text/javascript", ".css":"text/css", ".xml":"application/xml", ".woff2":"font/woff2", ".svg":"image/svg+xml", ".txt":"text/plain" };
const srv = createServer(async (req,res)=>{
  const u = decodeURIComponent((req.url||"/").split("?")[0]);
  let f = join(DIST,u);
  if(!extname(f) || !existsSync(f)) { const idx = join(DIST,u,"index.html"); f = existsSync(idx)?idx:join(DIST,"index.html"); }
  try{ const b = await readFile(f); res.writeHead(200,{"Content-Type":MIME[extname(f)]||"application/octet-stream"}); res.end(b); }
  catch{ res.writeHead(404).end("nf"); }
});
await new Promise(r=>srv.listen(PORTA,r));
const browser = await puppeteer.launch({ executablePath: process.env.CHROME_PATH || "/opt/pw-browsers/chromium-1194/chrome-linux/chrome", headless:"new", args:["--no-sandbox","--lang=en-US"] });
let ok=0, ko=0;
const check=(n,c,v="")=>{ c?ok++:ko++; console.log(`  ${c?"✓":"✗"}  ${n}${v!==""?": "+v:""}`); };

async function abrir(rota){
  const p = await browser.newPage();
  await p.setViewport({width:1440,height:900});
  await p.setRequestInterception(true);
  p.on("request", r => /wp-json|studio-tabi\/v1/.test(r.url()) ? r.abort() : r.continue());
  await p.goto(`http://localhost:${PORTA}${rota}`, {waitUntil:"networkidle0", timeout:60000});
  await new Promise(r=>setTimeout(r,1600));
  return p;
}

console.log("== EN: idioma, hreflang, canonical ==");
let p = await abrir("/en");
let d = await p.evaluate(()=>({
  lang: document.documentElement.lang,
  canon: document.querySelector('link[rel=canonical]')?.href,
  alts: [...document.querySelectorAll('link[rel=alternate][hreflang]')].map(e=>e.hreflang+"="+new URL(e.href).pathname),
  ogloc: document.querySelector('meta[property="og:locale"]')?.content,
  hero: document.querySelector("h1")?.innerText.replace(/\s+/g," ").trim().slice(0,40),
  inLang: JSON.parse(document.getElementById("ld-website")?.textContent||"{}").inLanguage,
}));
check("html lang", d.lang==="en", d.lang);
check("canonical /en", new URL(d.canon).pathname==="/en", new URL(d.canon).pathname);
check("hreflang pt-BR+en+x-default", d.alts.join(" ")==="pt-BR=/ en=/en x-default=/", d.alts.join(" "));
check("og:locale", d.ogloc==="en_US", d.ogloc);
check("JSON-LD inLanguage", d.inLang==="en", d.inLang);
check("hero em inglês", /WE TURN/i.test(d.hero||""), d.hero);

console.log("== EN: links internos permanecem em /en ==");
const links = await p.evaluate(()=>[...document.querySelectorAll('a[href^="/"]')]
  .filter(a=>!a.hasAttribute("hreflang"))
  .map(a=>a.getAttribute("href")+" « "+(a.innerText||a.getAttribute("aria-label")||"").replace(/\s+/g," ").trim().slice(0,24)));
const vazam = [...new Set(links)].filter(h=>!h.startsWith("/en"));
check("nenhum link vaza para o PT (fora do seletor de idioma)", vazam.length===0, vazam.join(" | ")||"—");
const troca = await p.evaluate(()=>[...document.querySelectorAll('a[hreflang]')].map(a=>a.getAttribute("hreflang")+"→"+a.getAttribute("href")).join(" "));
check("seletor de idioma aponta para os dois idiomas", troca.includes("pt→/")&&troca.includes("en→/en"), troca);

console.log("== EN: aviso de idioma (navegador en-US em /en → não aparece) ==");
let aviso = await p.evaluate(()=>!!document.querySelector('[data-no-prerender][role=region]'));
check("sem aviso em /en com navegador en", aviso===false);
await p.close();

console.log("== PT: navegador en-US em / → aviso aparece, sem redirecionar ==");
p = await abrir("/");
const r2 = await p.evaluate(()=>{
  const el = document.querySelector('[data-no-prerender][role=region]');
  return { visivel: !!el, texto: el?.innerText.replace(/\s+/g," ").trim(), destino: el?.querySelector("a")?.getAttribute("href"), url: location.pathname, lang: document.documentElement.lang };
});
check("não redirecionou", r2.url==="/", r2.url);
check("html lang pt-BR", r2.lang==="pt-BR", r2.lang);
check("aviso visível", r2.visivel===true);
check("aviso em inglês", /available in English/.test(r2.texto||""), (r2.texto||"").slice(0,60));
check("aviso leva para /en", r2.destino==="/en", r2.destino);
await p.close();

console.log("== Prerender não gravou o aviso no HTML ==");
const html = await readFile(join(DIST,"index.html"),"utf8");
check("data-no-prerender ausente do HTML estático", !html.includes("data-no-prerender"));

console.log("== EN: navegação para páginas internas ==");
for (const [rota, esperado] of [["/en/about","WE DON'T BUILD"],["/en/services","WHAT WE"],["/en/work","ALL OF OUR"],["/en/blog","BLOG"],["/en/contact",""]]) {
  const q = await abrir(rota);
  const t = await q.evaluate(()=>({h1:document.querySelector("h1")?.innerText.replace(/\s+/g," ").trim(), lang:document.documentElement.lang, canon:new URL(document.querySelector('link[rel=canonical]').href).pathname}));
  check(`${rota} lang+canonical`, t.lang==="en"&&t.canon===rota, `${t.lang} ${t.canon}`);
  if (esperado) check(`${rota} h1 traduzido`, (t.h1||"").toUpperCase().includes(esperado), (t.h1||"").slice(0,34));
  await q.close();
}

console.log("== Conteúdo das seções da home em inglês ==");
p = await abrir("/en");
const home = await p.evaluate(()=>{
  const txt = document.body.innerText;
  return {
    servicos: /WHAT WE\s+DELIVER/i.test(txt),
    faqTitulo: /FREQUENTLY\s+ASKED/i.test(txt),
    faqPergunta: /How does the process work\?/i.test(txt),
    projetos: /SELECTED\s+WORK/i.test(txt),
    sobre: /WE DON'T\s+BUILD WEBSITES/i.test(txt),
    servicoCard: /Branding & Visual Identity/i.test(txt),
    rodapeServicos: /Web Development/i.test(txt),
    rodapeNav: /Navigation/i.test(txt),
    resto: /ENTREGAMOS|PERGUNTAS|FREQUENTES|SELECIONADOS|Identidade Visual|Desenvolvimento Web|Quanto tempo/i.test(txt),
    faqLd: (JSON.parse(document.getElementById("ld-faq")?.textContent||"{}").mainEntity||[])[0]?.name || "",
  };
});
check("seção de serviços", home.servicos);
check("título do FAQ", home.faqTitulo);
check("perguntas do FAQ", home.faqPergunta);
check("seção de projetos", home.projetos);
check("seção sobre", home.sobre);
check("cards de serviço", home.servicoCard);
check("rodapé — coluna de serviços", home.rodapeServicos);
check("rodapé — coluna de navegação", home.rodapeNav);
check("JSON-LD do FAQ em inglês", /How does the process work/.test(home.faqLd), home.faqLd.slice(0,40));
check("nenhum resíduo em português", home.resto === false);
await p.close();

console.log("== PT continua intacto ==");
p = await abrir("/sobre");
const pt = await p.evaluate(()=>({h1:document.querySelector("h1")?.innerText.replace(/\s+/g," ").trim(), lang:document.documentElement.lang}));
check("PT /sobre", pt.lang==="pt-BR" && /NÃO FAZEMOS SITES/.test(pt.h1||""), `${pt.lang} — ${(pt.h1||"").slice(0,30)}`);
await p.close();
p = await abrir("/");
const ptHome = await p.evaluate(()=>{const t=document.body.innerText;return {
  servicos:/O QUE\s+ENTREGAMOS/i.test(t), faq:/PERGUNTAS\s+FREQUENTES/i.test(t),
  projetos:/TRABALHOS\s+SELECIONADOS/i.test(t), pergunta:/Como funciona o processo/i.test(t),
  rodape:/Desenvolvimento Web/i.test(t)};});
check("PT home — seções e FAQ intactos",
  ptHome.servicos && ptHome.faq && ptHome.projetos && ptHome.pergunta && ptHome.rodape,
  JSON.stringify(ptHome));
await p.close();

console.log("== sitemap.xml servido ==");
const sm = await (await fetch(`http://localhost:${PORTA}/sitemap.xml`)).text();
check("urlset com xhtml", sm.includes('xmlns:xhtml') && sm.includes('<xhtml:link'));
check("contém /en/work", sm.includes("/en/work"));

await browser.close(); srv.close();
console.log(`\n${ok} passaram, ${ko} falharam`);
process.exit(ko?1:0);
