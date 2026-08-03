import { createServer } from "node:http";
import { readFile } from "node:fs/promises";
import { existsSync, mkdirSync } from "node:fs";
import { join, extname, dirname } from "node:path";
import { fileURLToPath } from "node:url";
import puppeteer from "puppeteer-core";

const RAIZ = join(dirname(fileURLToPath(import.meta.url)), "..");
const DIST = join(RAIZ, "dist");
mkdirSync(join(RAIZ, ".fotos"), { recursive: true });
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
  args: ["--no-sandbox", "--force-device-scale-factor=1"],
});

const TELAS = {
  celular: { width: 412, height: 915, isMobile: true, hasTouch: true },
  pequeno: { width: 360, height: 780, isMobile: true, hasTouch: true },
  mini: { width: 320, height: 568, isMobile: true, hasTouch: true },
  iphone: { width: 390, height: 844, isMobile: true, hasTouch: true },
  grande: { width: 430, height: 932, isMobile: true, hasTouch: true },
  deitado: { width: 844, height: 390, isMobile: true, hasTouch: true },
  deitadoP: { width: 740, height: 360, isMobile: true, hasTouch: true },
  tablet: { width: 834, height: 1112, isMobile: true, hasTouch: true },
  desktop: { width: 1440, height: 900, isMobile: false, hasTouch: false },
};

const rota = process.env.ROTA || "/";
const alvos = (process.argv.slice(2).length ? process.argv.slice(2) : ["celular"]);

for (const nome of alvos) {
  const tela = TELAS[nome];
  if (!tela) { console.log(`tela desconhecida: ${nome}`); continue; }
  const p = await browser.newPage();
  await p.setViewport({ ...tela, deviceScaleFactor: 2 });
  p.on("request", (r) => {});
  await p.goto(`http://localhost:${PORTA}${rota}`, { waitUntil: "networkidle0", timeout: 60000 });
  await new Promise((r) => setTimeout(r, 2500));

  // Pergunta ao próprio desenho, não à caixa: a silhueta da montanha cobre
  // algum pedaço do glifo? isPointInFill responde no espaço do path.
  const caixas = await p.evaluate(() => {
    const hero = document.querySelector(".hero-sun-anchor")?.closest("section");
    const kanji = document.querySelector(".hero-kanji");
    if (!hero || !kanji) return null;
    const h = hero.getBoundingClientRect();
    const pct = (v) => Math.round(((v - h.top) / h.height) * 1000) / 10;

    // O kanji é um SVG, não texto: mede o desenho, senão o font-size do
    // wrapper engana e a conta sai muito menor que a marca de verdade.
    const marca = kanji.querySelector("svg") || kanji;
    const r = marca.getBoundingClientRect();
    const glifo = { esq: r.left, dir: r.right, topo: r.top, base: r.bottom };

    const camadas = ["haze", "far", "middle", "front"];
    const invasao = {};
    let pior = null;
    for (const nome of camadas) {
      const path = document.querySelector(`.hero-mountain-${nome} path`);
      if (!path) continue;
      const ctm = path.getScreenCTM();
      if (!ctm) continue;
      const inv = ctm.inverse();
      const svg = path.ownerSVGElement;
      let maisAlto = null;
      for (let i = 0; i <= 40; i++) {
        const x = glifo.esq + ((glifo.dir - glifo.esq) * i) / 40;
        for (let j = 0; j <= 40; j++) {
          const y = glifo.topo + ((glifo.base - glifo.topo) * j) / 40;
          const pt = svg.createSVGPoint();
          pt.x = x; pt.y = y;
          const local = pt.matrixTransform(inv);
          if (path.isPointInFill(local)) {
            if (maisAlto === null || y < maisAlto) maisAlto = y;
            break;
          }
        }
      }
      if (maisAlto !== null) {
        invasao[nome] = pct(maisAlto);
        if (pior === null || maisAlto < pior) pior = maisAlto;
      }
    }

    // O sol subiu demais? Encostar no botão é tão errado quanto a montanha
    // cortar o kanji, e só se enxerga olhando.
    const sol = document.querySelector(".hero-sun-anchor").getBoundingClientRect();
    const botao = document.querySelector(".hero-content button");
    let colisao = null;
    if (botao) {
      const b = botao.getBoundingClientRect();
      const cx = sol.left + sol.width / 2;
      const cy = sol.top + sol.height / 2;
      const raio = sol.width / 2;
      const px = Math.max(b.left, Math.min(cx, b.right));
      const py = Math.max(b.top, Math.min(cy, b.bottom));
      const d = Math.hypot(cx - px, cy - py);
      colisao = d < raio ? Math.round((raio - d) * 10) / 10 : null;
    }

    return {
      alturaHero: Math.round(h.height),
      kanji: { topo: pct(glifo.topo), base: pct(glifo.base), fonte: Math.round(glifo.base - glifo.topo) },
      invasao,
      pior: pior === null ? null : pct(pior),
      sobra: pior === null ? null : Math.round((glifo.base - pior) * 10) / 10,
      colisao,
    };
  });

  const arq = join(RAIZ, ".fotos", `hero-${nome}.png`);
  await p.screenshot({ path: arq, clip: { x: 0, y: 0, width: tela.width, height: tela.height } });
  console.log(`\n${nome} (${tela.width}×${tela.height}) → ${arq}`);
  if (caixas) {
    console.log(`  hero ${caixas.alturaHero}px · kanji ${caixas.kanji.fonte}px de alto, de ${caixas.kanji.topo}% a ${caixas.kanji.base}%`);
    if (caixas.pior === null) {
      console.log("  → nenhuma montanha toca o glifo ✓ LIVRE");
    } else {
      for (const [k, v] of Object.entries(caixas.invasao)) console.log(`  ${k.padEnd(7)} invade a partir de ${v}%`);
      console.log(`  → cobre ${caixas.sobra}px do glifo ✗ COBERTO`);
    }
    if (caixas.colisao !== null) console.log(`  → o sol invade ${caixas.colisao}px do botão ✗ COLIDE`);
  }
  await p.close();
}

await browser.close();
srv.close();
