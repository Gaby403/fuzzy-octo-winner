import { readFile, writeFile } from "node:fs/promises";
import { existsSync } from "node:fs";
import { join, dirname } from "node:path";
import { fileURLToPath } from "node:url";

const AQUI = dirname(fileURLToPath(import.meta.url));
const DIST = join(AQUI, "..", "dist");
const PUBLIC = join(AQUI, "..", "public");
const DESTINO = join(DIST, "sitemap.xml");

async function apiBase() {
    if (process.env.STCMS_API)
        return process.env.STCMS_API.replace(/\/+$/, "");
    try {
        const cfg = await readFile(join(PUBLIC, "config.js"), "utf8");
        const linhas = cfg.split(/\r?\n/).filter(l => /^\s*window\.__STUDIO_TABI_API__\s*=/.test(l));
        const m = linhas.length ? linhas[linhas.length - 1].match(/["']([^"']+)["']/) : null;
        if (m && m[1])
            return m[1].replace(/\/+$/, "");
    }
    catch {
    }
    return "";
}

const base = await apiBase();
if (!base) {
    console.log("  sitemap: sem URL do WordPress; mantendo o sitemap.xml existente");
    process.exit(0);
}

const url = `${base}/wp-json/studio-tabi/v1/sitemap`;
let dados;
try {
    const res = await fetch(url, { headers: { Accept: "application/json" } });
    if (!res.ok)
        throw new Error(`HTTP ${res.status}`);
    dados = await res.json();
}
catch (err) {
    console.log(`  sitemap: ${url} indisponível (${err.message}); mantendo o sitemap.xml existente`);
    if (!existsSync(DESTINO))
        console.log("  sitemap: ATENÇÃO — dist/sitemap.xml não existe");
    process.exit(0);
}

if (!dados || typeof dados.xml !== "string" || !dados.xml.includes("<urlset")) {
    console.log("  sitemap: resposta inesperada do CMS; mantendo o sitemap.xml existente");
    process.exit(0);
}

await writeFile(DESTINO, dados.xml, "utf8");
console.log(`  ✓ sitemap.xml — ${dados.total} URLs (PT + EN) a partir de ${dados.base}`);
