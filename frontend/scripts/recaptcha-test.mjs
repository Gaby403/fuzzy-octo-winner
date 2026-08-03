import { createServer } from "node:http";
import { readFile } from "node:fs/promises";
import { existsSync } from "node:fs";
import { join, extname, dirname } from "node:path";
import { fileURLToPath } from "node:url";
import puppeteer from "puppeteer-core";

const DIST = join(dirname(fileURLToPath(import.meta.url)), "..", "dist");
const CHAVE = "6LtesteChavePublica";
const MIME = {
  ".html": "text/html; charset=utf-8", ".js": "text/javascript", ".css": "text/css",
  ".woff2": "font/woff2", ".svg": "image/svg+xml", ".xml": "application/xml", ".txt": "text/plain",
};

const srv = createServer(async (req, res) => {
  const u = decodeURIComponent((req.url || "/").split("?")[0]);
  if (u.includes("/studio-tabi/v1/content")) {
    const corpo = JSON.stringify({ site: { title: "Studio Tabi", recaptchaSite: CHAVE, ga4Id: "", gtmId: "" } });
    res.writeHead(200, { "Content-Type": "application/json" }).end(corpo);
    return;
  }
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

async function abrir(rota) {
  const p = await browser.newPage();
  const pedidos = [];
  await p.setRequestInterception(true);
  p.on("request", (r) => {
    const url = r.url();
    if (/google\.com\/recaptcha/.test(url)) {
      pedidos.push(url);
      // Não temos rede: responde vazio para o teste não travar.
      r.respond({ status: 200, contentType: "text/javascript", body: "" });
      return;
    }
    r.continue();
  });
  await p.goto(`http://localhost:${PORTA}${rota}`, { waitUntil: "networkidle0", timeout: 60000 });
  await new Promise((r) => setTimeout(r, 5000));
  return { p, pedidos };
}

let falhas = 0;
const ok = (cond, msg) => {
  console.log(`${cond ? "  ok  " : "FALHA "} ${msg}`);
  if (!cond) falhas++;
};

{
  const { p, pedidos } = await abrir("/");
  ok(pedidos.length === 0, `home não carrega reCAPTCHA (pedidos: ${pedidos.length})`);
  await p.close();
}
{
  const { p, pedidos } = await abrir("/en");
  ok(pedidos.length === 0, `/en não carrega reCAPTCHA (pedidos: ${pedidos.length})`);
  await p.close();
}
{
  const { p, pedidos } = await abrir("/contato");
  ok(pedidos.length === 0, `/contato não carrega reCAPTCHA antes do foco (pedidos: ${pedidos.length})`);
  await p.focus("#c-name");
  await new Promise((r) => setTimeout(r, 1200));
  ok(pedidos.length === 1, `/contato carrega reCAPTCHA ao focar o formulário (pedidos: ${pedidos.length})`);
  ok(pedidos.some((u) => u.includes(CHAVE)), "usa a chave pública vinda do CMS");
  await p.close();
}
{
  const { p, pedidos } = await abrir("/blog");
  ok(pedidos.length === 0, `/blog não carrega reCAPTCHA antes do foco (pedidos: ${pedidos.length})`);
  await p.focus("#nl-footer");
  await new Promise((r) => setTimeout(r, 1200));
  ok(pedidos.length === 1, `newsletter do rodapé carrega ao focar (pedidos: ${pedidos.length})`);
  await p.close();
}

await browser.close();
srv.close();
console.log(falhas ? `\n${falhas} falha(s)` : "\nsem falhas");
process.exit(falhas ? 1 : 0);
