import { createServer } from "node:http";
import { readFile } from "node:fs/promises";
import { existsSync } from "node:fs";
import { join, extname, dirname } from "node:path";
import { fileURLToPath } from "node:url";
import puppeteer from "puppeteer-core";

const RAIZ = join(dirname(fileURLToPath(import.meta.url)), "..");
const DIST = join(RAIZ, "dist");
const MIME = {
  ".html": "text/html; charset=utf-8", ".js": "text/javascript", ".css": "text/css",
  ".woff2": "font/woff2", ".svg": "image/svg+xml", ".xml": "application/xml", ".txt": "text/plain",
};

const srv = createServer(async (req, res) => {
  const u = decodeURIComponent((req.url || "/").split("?")[0]);
  let f = join(DIST, u);
  if (!extname(f) || !existsSync(f)) {
    const idx = join(DIST, u, "index.html");
    f = existsSync(idx) ? idx : join(DIST, "index.html");
  }
  try {
    const b = await readFile(f);
    res.writeHead(200, { "Content-Type": MIME[extname(f)] || "application/octet-stream" }).end(b);
  } catch {
    res.writeHead(404).end("nf");
  }
});
await new Promise((r) => srv.listen(0, r));
const PORTA = srv.address().port;

const browser = await puppeteer.launch({
  executablePath: process.env.CHROME_PATH || "/opt/pw-browsers/chromium-1194/chrome-linux/chrome",
  headless: "new",
  args: ["--no-sandbox"],
});

let ok = 0, ko = 0;
const checar = (cond, msg, valor = "") => {
  console.log(`  ${cond ? "✓" : "✗"}  ${msg}${valor !== "" ? `: ${valor}` : ""}`);
  cond ? ok++ : ko++;
};

const TELAS = {
  celular: { width: 412, height: 915, isMobile: true, hasTouch: true },
  desktop: { width: 1440, height: 900, isMobile: false, hasTouch: false },
};

async function estado(p) {
  return p.evaluate(() => {
    const ancoras = document.querySelectorAll(".hero-sun-anchor");
    const hero = ancoras[0]?.closest("section");
    const cs = (el) => (el ? getComputedStyle(el) : null);
    const op = (el) => (el ? Math.round(parseFloat(cs(el).opacity) * 100) / 100 : -1);
    // São três âncoras — brilho, sol e lua — e a ordem não diz qual é qual.
    // Acha pela cor do disco.
    const discos = [...document.querySelectorAll(".hero-sun-anchor .rounded-full")];
    const porCor = (cor) => discos.find((d) => cs(d).backgroundColor === cor);
    return {
      fundo: cs(hero)?.backgroundColor || "",
      solVermelho: op(porCor("rgb(242, 12, 37)")),
      luaBranca: op(porCor("rgb(239, 239, 239)")),
      kanjiOp: op(document.querySelector(".hero-kanji")),
      frenteT: cs(document.querySelector(".hero-mountain-front"))?.transform || "",
      // A cor animada está na linha do título, não no <h1> que a contém.
      tituloCor: cs(document.querySelector(".hero-title-line"))?.color || "",
      docAltura: document.documentElement.scrollHeight,
    };
  });
}

async function rolarFracao(p, f) {
  await p.evaluate((frac) => {
    const zona = document.querySelector(".hero-scroll-zone");
    const alvo = zona ? zona.getBoundingClientRect().height : window.innerHeight;
    window.scrollTo(0, alvo * frac);
  }, f);
  await new Promise((r) => setTimeout(r, 1200));
}

for (const [nome, tela] of Object.entries(TELAS)) {
  console.log(`\n== ${nome} (${tela.width}×${tela.height}) ==`);
  const p = await browser.newPage();
  await p.setViewport({ ...tela, deviceScaleFactor: 1 });
  await p.goto(`http://localhost:${PORTA}/`, { waitUntil: "networkidle0", timeout: 60000 });
  await new Promise((r) => setTimeout(r, 2000));

  const inicio = await estado(p);
  checar(inicio.docAltura > tela.height * 2, "a página tem zona de rolagem", `${inicio.docAltura}px`);
  checar(/255|239/.test(inicio.fundo), "hero começa claro (dia)", inicio.fundo);
  checar(inicio.kanjiOp > 0.9, "kanji começa visível", String(inicio.kanjiOp));
  checar(inicio.solVermelho > 0.9, "sol vermelho começa aceso", String(inicio.solVermelho));
  checar(inicio.luaBranca < 0.1, "lua branca começa apagada", String(inicio.luaBranca));

  await rolarFracao(p, 0.25);
  const meio = await estado(p);
  checar(meio.kanjiOp < inicio.kanjiOp, "o kanji some ao rolar", `${inicio.kanjiOp} → ${meio.kanjiOp}`);
  checar(meio.frenteT !== inicio.frenteT, "a montanha da frente se desloca", "transform mudou");
  checar(meio.tituloCor !== inicio.tituloCor, "o título inverte de cor", `${inicio.tituloCor} → ${meio.tituloCor}`);

  await rolarFracao(p, 0.6);
  const noite = await estado(p);
  checar(/rgb\(0, 0, 0\)|rgb\(17, 17, 17\)/.test(noite.fundo), "o hero vira noite", noite.fundo);
  checar(noite.solVermelho < inicio.solVermelho, "o sol vermelho se põe", `${inicio.solVermelho} → ${noite.solVermelho}`);
  checar(noite.luaBranca > inicio.luaBranca, "a lua branca nasce", `${inicio.luaBranca} → ${noite.luaBranca}`);

  // E o resto da página continua rolando normalmente.
  await p.evaluate(() => window.scrollTo(0, document.documentElement.scrollHeight));
  await new Promise((r) => setTimeout(r, 900));
  const fim = await p.evaluate(() => Math.round(window.scrollY));
  checar(fim > tela.height * 2, "a página rola até o fim", `${fim}px`);

  const rodape = await p.evaluate(() => !!document.querySelector("footer"));
  checar(rodape, "o rodapé é alcançado");

  await p.close();
}

await browser.close();
srv.close();
console.log(`\n${ok} passaram, ${ko} falharam`);
process.exit(ko ? 1 : 0);
