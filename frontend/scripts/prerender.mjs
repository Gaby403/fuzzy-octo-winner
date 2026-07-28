/**
 * Prerender do SPA.
 *
 * Depois do build, sobe um preview local, visita cada rota num navegador e
 * grava o HTML já renderizado em dist/<rota>/index.html. O visitante passa a
 * receber a página pintada de imediato, em vez de um <div id="root"> vazio
 * que só ganha conteúdo quando o JavaScript termina de executar — que era o
 * que segurava o LCP no celular.
 *
 * O React continua assumindo a página normalmente ao carregar; a marca
 * window.__PRERENDERED__ avisa a aplicação para não repetir as animações de
 * entrada sobre um conteúdo que já está visível.
 */
import { createServer } from "node:http"
import { readFile, writeFile, mkdir } from "node:fs/promises"
import { existsSync } from "node:fs"
import { join, extname, dirname } from "node:path"
import { fileURLToPath } from "node:url"
import puppeteer from "puppeteer-core"

const AQUI = dirname(fileURLToPath(import.meta.url))
const DIST = join(AQUI, "..", "dist")
const CHROME = process.env.CHROME_PATH || "/opt/pw-browsers/chromium-1194/chrome-linux/chrome"
const PORTA = 4599

// Rotas com conteúdo previsível no build. As dinâmicas (/blog/:slug,
// /processo/:slug, /servicos/:slug) continuam servidas pelo index.html.
const ROTAS = ["/", "/sobre", "/servicos", "/projetos", "/blog", "/contato", "/obrigado"]

const MIME = {
  ".html": "text/html; charset=utf-8", ".js": "text/javascript", ".css": "text/css",
  ".json": "application/json", ".svg": "image/svg+xml", ".woff2": "font/woff2",
  ".png": "image/png", ".jpg": "image/jpeg", ".webp": "image/webp", ".ico": "image/x-icon",
  ".txt": "text/plain; charset=utf-8", ".xml": "application/xml",
}

/** Servidor estático mínimo com fallback de SPA. */
function servir() {
  return new Promise(resolve => {
    const s = createServer(async (req, res) => {
      const url = decodeURIComponent((req.url || "/").split("?")[0])
      let arquivo = join(DIST, url)
      if (!extname(arquivo) || !existsSync(arquivo)) arquivo = join(DIST, "index.html")
      try {
        const buf = await readFile(arquivo)
        res.writeHead(200, { "Content-Type": MIME[extname(arquivo)] || "application/octet-stream" })
        res.end(buf)
      } catch {
        res.writeHead(404).end("não encontrado")
      }
    })
    s.listen(PORTA, () => resolve(s))
  })
}

const servidor = await servir()
const browser = await puppeteer.launch({ executablePath: CHROME, headless: "new", args: ["--no-sandbox"] })

let ok = 0
for (const rota of ROTAS) {
  const page = await browser.newPage()
  await page.setViewport({ width: 1440, height: 900 })

  // O CMS não é consultado no build: o snapshot usa o conteúdo padrão
  // embutido, exatamente o que a aplicação já mostra antes da API responder.
  await page.setRequestInterception(true)
  page.on("request", r => (/wp-json|\/studio-tabi\/v1\//.test(r.url()) ? r.abort() : r.continue()))

  await page.goto(`http://localhost:${PORTA}${rota}`, { waitUntil: "networkidle0", timeout: 60000 })
  // Espera as animações de entrada assentarem para gravar o estado final.
  await new Promise(r => setTimeout(r, 2200))

  const html = await page.evaluate(() => {
    // Elementos puramente decorativos e de estado não pertencem ao snapshot.
    document.querySelectorAll("[data-no-prerender]").forEach(el => el.remove())
    return "<!doctype html>\n" + document.documentElement.outerHTML
  })

  // Marca o documento e garante que o config.js continue antes do bundle.
  const marcado = html.replace(
    "<head>",
    '<head>\n  <script>window.__PRERENDERED__=1</script>'
  )

  const destino = rota === "/" ? join(DIST, "index.html") : join(DIST, rota, "index.html")
  await mkdir(dirname(destino), { recursive: true })
  await writeFile(destino, marcado, "utf8")
  console.log(`  ✓ ${rota.padEnd(12)} → ${(marcado.length / 1024).toFixed(0)} KB`)
  ok++
  await page.close()
}

await browser.close()
servidor.close()
console.log(`\n${ok}/${ROTAS.length} rotas pré-renderizadas`)
