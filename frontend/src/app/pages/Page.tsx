import { useEffect, useState } from "react"
import { useParams, Link } from "react-router"
import { fetchPage, useContent, WpPage } from "../store/content"

const RED = "#F20C25"
const WHITE = "#EFEFEF"
const BLACK = "#111111"

/**
 * Renders a page created in WordPress (Páginas), reached at /p/:slug.
 * The HTML comes straight from the WordPress editor so anything the user
 * writes there — text, images, embeds — shows up on the site.
 */
export default function Page() {
  const { slug = "" } = useParams()
  const { content } = useContent()
  const [page, setPage] = useState<WpPage | null>(null)
  const [state, setState] = useState<"loading" | "ready" | "missing">("loading")

  useEffect(() => {
    let alive = true
    setState("loading")
    fetchPage(slug).then(p => {
      if (!alive) return
      if (p) {
        setPage(p)
        setState("ready")
        if (p.title) document.title = `${p.title} — ${content.site.title}`
      } else {
        setState("missing")
      }
    })
    return () => {
      alive = false
    }
  }, [slug, content.site.title])

  return (
    <div style={{ minHeight: "100svh", background: BLACK, color: WHITE, fontFamily: '"Be Vietnam Pro", sans-serif' }}>
      <header
        style={{
          display: "flex",
          alignItems: "center",
          justifyContent: "space-between",
          padding: "22px clamp(20px,5vw,64px)",
          borderBottom: "1px solid rgba(239,239,239,0.08)",
        }}
      >
        <Link to="/" style={{ textDecoration: "none", color: WHITE, fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: 20, letterSpacing: "-0.04em", textTransform: "uppercase" }}>
          {content.site.title.split(" ")[0] || "STUDIO"} <span style={{ color: RED }}>{content.site.title.split(" ").slice(1).join(" ") || "TABI"}</span>
        </Link>
        <Link to="/" style={{ textDecoration: "none", color: "rgba(239,239,239,0.55)", fontSize: 11, fontWeight: 600, letterSpacing: "0.14em" }}>
          ← VOLTAR
        </Link>
      </header>

      <main id="conteudo" style={{ maxWidth: 760, margin: "0 auto", padding: "clamp(48px,8vw,96px) clamp(20px,5vw,32px) 120px" }}>
        {state === "loading" && (
          <p style={{ color: "rgba(239,239,239,0.4)", fontSize: 13, letterSpacing: "0.1em" }}>CARREGANDO…</p>
        )}

        {state === "missing" && (
          <div>
            <h1 style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(36px,7vw,64px)", letterSpacing: "-0.04em", textTransform: "uppercase", margin: "0 0 16px" }}>
              Página não <span style={{ color: RED }}>encontrada</span>
            </h1>
            <p style={{ color: "rgba(239,239,239,0.55)", lineHeight: 1.7 }}>
              A página “{slug}” não existe ou não está publicada no WordPress.
            </p>
          </div>
        )}

        {state === "ready" && page && (
          <article>
            <h1
              style={{
                fontFamily: '"Roboto Condensed", sans-serif',
                fontWeight: 900,
                fontSize: "clamp(36px,7vw,64px)",
                letterSpacing: "-0.04em",
                textTransform: "uppercase",
                lineHeight: 0.95,
                margin: "0 0 32px",
              }}
            >
              {page.title}
            </h1>
            <div
              className="wp-page-content"
              style={{ lineHeight: 1.8, fontSize: 16, color: "rgba(239,239,239,0.82)" }}
              // Content authored in WordPress by the site owner.
              dangerouslySetInnerHTML={{ __html: page.content }}
            />
          </article>
        )}
      </main>
    </div>
  )
}
