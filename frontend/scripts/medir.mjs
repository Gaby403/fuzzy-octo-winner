import { createServer } from "node:http";
import { readFile } from "node:fs/promises";
import { existsSync, statSync } from "node:fs";
import { join, extname, dirname } from "node:path";
import { fileURLToPath } from "node:url";
import { gzipSync } from "node:zlib";
import puppeteer from "puppeteer-core";

const DIST = join(dirname(fileURLToPath(import.meta.url)), "..", "dist");
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
    // Como a Hostinger serve: comprimido.
    const gz = gzipSync(b);
    res.writeHead(200, {
      "Content-Type": MIME[extname(f)] || "application/octet-stream",
      "Content-Encoding": "gzip",
      "Content-Length": gz.length,
    });
    res.end(gz);
  } catch {
    res.writeHead(404).end("nf");
  }
});
const PORTA = 4733;
await new Promise((r) => srv.listen(PORTA, r));

const browser = await puppeteer.launch({
  executablePath: process.env.CHROME_PATH || "/opt/pw-browsers/chromium-1194/chrome-linux/chrome",
  headless: "new",
  args: ["--no-sandbox"],
});

async function medir(rota) {
  const p = await browser.newPage();
  await p.setViewport({ width: 412, height: 915, deviceScaleFactor: 2, isMobile: true, hasTouch: true });
  await p.setRequestInterception(true);
  const bytes = { js: 0, css: 0, font: 0, html: 0, outros: 0 };
  let pedidos = 0;
  p.on("request", (r) => (/wp-json|studio-tabi\/v1/.test(r.url()) ? r.abort() : r.continue()));
  p.on("response", async (r) => {
    pedidos++;
    const tipo = r.request().resourceType();
    let n = 0;
    try { n = Number((await r.headers())["content-length"] || 0); } catch { n = 0; }
    const chave = tipo === "script" ? "js" : tipo === "stylesheet" ? "css"
      : tipo === "font" ? "font" : tipo === "document" ? "html" : "outros";
    bytes[chave] += n;
  });

  // Mesma desvantagem de um celular mediano em 4G.
  const cdp = await p.target().createCDPSession();
  await cdp.send("Network.emulateNetworkConditions", {
    offline: false, latency: 150, downloadThroughput: (1.6 * 1024 * 1024) / 8, uploadThroughput: (750 * 1024) / 8,
  });
  await cdp.send("Emulation.setCPUThrottlingRate", { rate: 4 });

  // Precisa observar antes do carregamento: getEntriesByType não devolve LCP
  // nem longtask depois do fato.
  await p.evaluateOnNewDocument(() => {
    window.__lcp = 0;
    window.__bloqueio = 0;
    window.__cand = [];
    new PerformanceObserver((l) => {
      for (const e of l.getEntries()) {
        window.__lcp = e.startTime;
        window.__cand.push({
          t: Math.round(e.startTime),
          tag: e.element ? e.element.tagName : "?",
          txt: ((e.element && e.element.innerText) || "").replace(/\s+/g, " ").slice(0, 46),
          size: e.size,
        });
      }
    }).observe({ type: "largest-contentful-paint", buffered: true });
    try {
      new PerformanceObserver((l) => {
        for (const e of l.getEntries()) window.__bloqueio += Math.max(0, e.duration - 50);
      }).observe({ type: "longtask", buffered: true });
    } catch {}
  });
  await p.goto(`http://localhost:${PORTA}${rota}`, { waitUntil: "load", timeout: 90000 });
  await new Promise((r) => setTimeout(r, 2500));

  const m = await p.evaluate(() => {
    const paint = performance.getEntriesByType("paint");
    const fcp = paint.find((e) => e.name === "first-contentful-paint")?.startTime || 0;
    const lcp = window.__lcp || 0;
    const nav = performance.getEntriesByType("navigation")[0] || {};

    return {
      fcp: Math.round(fcp),
      lcp: Math.round(lcp),
      dcl: Math.round(nav.domContentLoadedEventEnd || 0),
      bloqueio: Math.round(window.__bloqueio || 0),
      cand: window.__cand || [],
    };
  });
  await p.close();
  return { ...m, bytes, pedidos };
}

const rotas = process.argv.slice(2).length ? process.argv.slice(2) : ["/", "/blog", "/en"];
const kb = (n) => (n / 1024).toFixed(0).padStart(4) + " KB";

console.log("Celular mediano, 4G, CPU 4× mais lenta\n");
console.log("rota        FCP     LCP     bloqueio  reqs   js     css    fonte  html");
for (const rota of rotas) {
  const r = await medir(rota);
  console.log(
    rota.padEnd(11),
    `${r.fcp}ms`.padStart(6),
    `${r.lcp}ms`.padStart(7),
    `${r.bloqueio}ms`.padStart(8),
    String(r.pedidos).padStart(5),
    kb(r.bytes.js), kb(r.bytes.css), kb(r.bytes.font), kb(r.bytes.html),
  );
  if (process.env.LCP) {
    for (const c of r.cand) console.log(`      ${String(c.t).padStart(5)}ms  <${c.tag}> ${c.size}px²  "${c.txt}"`);
  }
}

await browser.close();
srv.close();
