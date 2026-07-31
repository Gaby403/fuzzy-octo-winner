import { createServer } from "node:http";
import { readFile } from "node:fs/promises";
import { existsSync } from "node:fs";
import { join, extname, dirname } from "node:path";
import { fileURLToPath } from "node:url";
import puppeteer from "puppeteer-core";

const DIST = join(dirname(fileURLToPath(import.meta.url)), "..", "dist");
const PORTA = 4620;
const MIME = { ".html": "text/html; charset=utf-8", ".js": "text/javascript", ".css": "text/css", ".xml": "application/xml", ".woff2": "font/woff2", ".svg": "image/svg+xml", ".txt": "text/plain", ".json": "application/json" };

const srv = createServer(async (req, res) => {
    const u = decodeURIComponent((req.url || "/").split("?")[0]);
    let f = join(DIST, u);
    if (!extname(f) || !existsSync(f)) {
        const idx = join(DIST, u, "index.html");
        f = existsSync(idx) ? idx : join(DIST, "index.html");
    }
    try {
        const b = await readFile(f);
        res.writeHead(200, { "Content-Type": MIME[extname(f)] || "application/octet-stream" });
        res.end(b);
    }
    catch {
        res.writeHead(404).end("nf");
    }
});
await new Promise(r => srv.listen(PORTA, r));

const browser = await puppeteer.launch({
    executablePath: process.env.CHROME_PATH || "/opt/pw-browsers/chromium-1194/chrome-linux/chrome",
    headless: "new",
    args: ["--no-sandbox", "--lang=en-US"],
});

const DETECTOR = `(() => {
    const DIACRITICO = /[ãõçáàâéêíóôúÃÕÇÁÀÂÉÊÍÓÔÚ]/;
    const PALAVRAS = new RegExp("\\\\b(" + [
        "de","da","do","das","dos","em","no","na","nos","nas","com","sem","para","pela","pelo",
        "que","quem","qual","quais","como","onde","quando","porque","por",
        "nao","sim","seu","sua","seus","suas","nosso","nossa","nossos","nossas","voce","voces",
        "mais","menos","muito","todos","todas","cada","outro","outra","ainda","depois","antes",
        "projeto","projetos","servico","servicos","pagina","paginas",
        "conteudo","equipe","empresa","negocio","marca","desenvolvimento",
        "entre","sobre","apos","atraves","ate","ja","aqui","agora","isso","este","esta","esse","essa",
        "fazer","ser","estar","ter","tem","foi","sao","vamos","quer","pode","deve","vai",
        "ver","ler","abrir","fechar","enviar","buscar","voltar","criar","falar","conheca",
        "trabalho","trabalhos","artigo","artigos","noticia","leitura","imagem","imagens",
        "anterior","proxima","proximo","todos","copiar","copiado","carregando","obrigado"
    ].join("|") + ")\\\\b", "i");

    // Nomes próprios, endereços e estrangeirismos do inglês carregam acento
    // sem serem português.
    const PERMITIDO = [/^https?:\\/\\//, /S\u00e3o Paulo/];
    const EMPRESTIMOS = /(clich\u00e9|caf\u00e9|r\u00e9sum\u00e9|na\u00efve|fa\u00e7ade|d\u00e9cor|expos\u00e9)/gi;

    function suspeito(txt) {
        const t = String(txt || "").trim();
        if (t.length < 3) return false;
        if (/^[\\d\\s\\p{P}\\p{S}]+$/u.test(t)) return false;
        if (PERMITIDO.some(r => r.test(t))) return false;
        const limpo = t.replace(EMPRESTIMOS, "");
        if (DIACRITICO.test(limpo)) return true;
        const semAcento = limpo.normalize("NFD").replace(/[\\u0300-\\u036f]/g, "");
        const achados = new Set();
        for (const m of semAcento.matchAll(new RegExp(PALAVRAS.source, "gi"))) achados.add(m[0].toLowerCase());
        return achados.size >= 2;
    }

    function coletar() {
        const achados = [];
        const visivel = el => {
            const s = getComputedStyle(el);
            return s.display !== "none" && s.visibility !== "hidden" && Number(s.opacity) > 0.01;
        };
        // Nós de texto realmente renderizados.
        const it = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        let no;
        while ((no = it.nextNode())) {
            const pai = no.parentElement;
            if (!pai || /^(SCRIPT|STYLE|NOSCRIPT)$/.test(pai.tagName)) continue;
            if (!visivel(pai)) continue;
            const t = no.nodeValue.trim();
            if (suspeito(t)) achados.push({ tipo: "texto", onde: pai.tagName.toLowerCase(), valor: t });
        }
        // Atributos que chegam ao usuário ou ao leitor de tela.
        const ATTRS = ["aria-label", "alt", "title", "placeholder", "aria-description", "aria-roledescription", "data-cursor-label", "value"];
        for (const el of document.querySelectorAll("*")) {
            for (const a of ATTRS) {
                const v = el.getAttribute && el.getAttribute(a);
                if (v && suspeito(v)) achados.push({ tipo: "@" + a, onde: el.tagName.toLowerCase(), valor: v });
            }
        }
        // Metadados indexáveis.
        for (const m of document.querySelectorAll('meta[name="description"],meta[property^="og:"],meta[name^="twitter:"]')) {
            const v = m.getAttribute("content");
            if (v && suspeito(v)) achados.push({ tipo: "meta", onde: m.getAttribute("name") || m.getAttribute("property"), valor: v });
        }
        if (suspeito(document.title)) achados.push({ tipo: "title", onde: "head", valor: document.title });
        for (const s of document.querySelectorAll('script[type="application/ld+json"]')) {
            const bruto = s.textContent || "";
            for (const m of bruto.matchAll(/"([^"\\\\]{6,})"/g)) {
                if (suspeito(m[1])) achados.push({ tipo: "json-ld", onde: s.id || "?", valor: m[1] });
            }
        }
        return achados;
    }
    return coletar();
})()`;

const ROTAS = ["/en", "/en/about", "/en/services", "/en/work", "/en/blog", "/en/contact", "/en/thank-you", "/en/nao-existe"];
const total = new Map();

function registrar(rota, estado, lista) {
    for (const a of lista) {
        const chave = `${a.tipo}|${a.valor}`;
        if (!total.has(chave)) total.set(chave, { ...a, onde: [] });
        total.get(chave).onde.push(`${rota}${estado ? " » " + estado : ""}`);
    }
}

for (const rota of ROTAS) {
    const p = await browser.newPage();
    await p.setViewport({ width: 1440, height: 900 });
    await p.setRequestInterception(true);
    p.on("request", r => (/wp-json|studio-tabi\/v1/.test(r.url()) ? r.abort() : r.continue()));
    await p.goto(`http://localhost:${PORTA}${rota}`, { waitUntil: "networkidle0", timeout: 60000 });
    await new Promise(r => setTimeout(r, 1800));
    registrar(rota, "", await p.evaluate(DETECTOR));

    const abrirMenu = await p.evaluate(() => {
        const b = document.querySelector('header button[aria-expanded]');
        if (!b) return false;
        b.click();
        return true;
    });
    if (abrirMenu) {
        await new Promise(r => setTimeout(r, 700));
        registrar(rota, "menu aberto", await p.evaluate(DETECTOR));
        await p.evaluate(() => document.querySelector('header button[aria-expanded]')?.click());
        await new Promise(r => setTimeout(r, 500));
    }

    if (rota === "/en") {
        const abriuFaq = await p.evaluate(() => {
            const alvos = [...document.querySelectorAll("button")].filter(b => /\?$/.test(b.innerText.trim()));
            alvos.slice(0, 3).forEach(b => b.click());
            return alvos.length > 0;
        });
        if (abriuFaq) {
            await new Promise(r => setTimeout(r, 700));
            registrar(rota, "FAQ aberto", await p.evaluate(DETECTOR));
        }
    }

    if (rota === "/en/work") {
        const abriu = await p.evaluate(() => {
            const card = document.querySelector("[data-cursor-label]");
            if (!card) return false;
            card.click();
            return true;
        });
        if (abriu) {
            await new Promise(r => setTimeout(r, 1500));
            registrar(rota, "modal de projeto", await p.evaluate(DETECTOR));
        }
    }

    if (rota === "/en/contact") {
        await p.evaluate(() => {
            const f = document.querySelector("form");
            if (f) f.dispatchEvent(new Event("submit", { bubbles: true, cancelable: true }));
        });
        await new Promise(r => setTimeout(r, 1200));
        registrar(rota, "formulário enviado", await p.evaluate(DETECTOR));
    }

    await p.close();
}

await browser.close();
srv.close();

const achados = [...total.values()];
if (!achados.length) {
    console.log("\n✓ Pente fino: nenhum texto em português no /en (8 rotas + estados interativos)\n");
    process.exit(0);
}
console.log(`\n${achados.length} trechos em português encontrados no /en:\n`);
for (const a of achados) {
    console.log(`  [${a.tipo}] <${a.onde}>`);
    console.log(`      "${a.valor.slice(0, 130)}"`);
    console.log(`      em: ${[...new Set(a.onde2 || a.onde)].toString().slice(0, 0)}${[...new Set(a.onde)].join(", ")}`);
}
console.log("");
process.exit(1);
