import { useEffect } from "react"
import { Link, useParams } from "react-router"
import { m } from "motion/react"
import { useContent } from "../store/content"

const RED = "#F20C25"
const RED_BTN = "#DA0A20"
const RED_INK = "#FF3547"
const PURE_WHITE = "#FFFFFF"
const WHITE = "#EFEFEF"
const BLACK = "#111111"
const FONT_HEAD = '"Roboto Condensed", sans-serif'
const FONT_BODY = '"Be Vietnam Pro", sans-serif'
const EASE: [number, number, number, number] = [0.16, 1, 0.3, 1]

/**
 * Página interna de um serviço (/servicos/:slug). Usa o conteúdo (HTML)
 * escrito no WordPress; se não houver, mostra a descrição curta (body).
 */
export default function ServiceDetail() {
  const { content } = useContent()
  const { slug = "" } = useParams()
  const services = content.services
  const idx = services.findIndex((s) => s.slug === slug)
  const service = idx >= 0 ? services[idx] : null
  const next = services.length ? services[(Math.max(idx, 0) + 1) % services.length] : null
  const pad = "clamp(20px, 4vw, 82px)"

  useEffect(() => {
    document.title = service ? `${service.title} — ${content.site.title}` : `Serviço — ${content.site.title}`
    window.scrollTo(0, 0)
  }, [service, content.site.title])

  return (
    <div style={{ minHeight: "100svh", background: BLACK, color: WHITE, fontFamily: FONT_BODY }}>
      <header
        style={{ display: "flex", alignItems: "center", justifyContent: "space-between", padding: `22px ${pad}`, borderBottom: "1px solid rgba(239,239,239,0.08)", position: "sticky", top: 0, background: "rgba(17,17,17,0.86)", backdropFilter: "blur(10px)", zIndex: 20 }}
      >
        <Link to="/" aria-label="Voltar para a home" style={{ textDecoration: "none", color: WHITE, display: "inline-flex", alignItems: "center" }}>
          {content.site.logoUrl ? (
            <img src={content.site.logoUrl} alt={content.nav.brand || content.site.title} style={{ height: "clamp(26px, 3.4vw, 40px)", width: "auto", display: "block" }} />
          ) : (
            <span style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: 20, letterSpacing: "-0.04em", textTransform: "uppercase" }}>
              {content.site.title.split(" ")[0] || "STUDIO"} <span style={{ color: RED }}>{content.site.title.split(" ").slice(1).join(" ") || "TABI"}</span>
            </span>
          )}
        </Link>
        <Link to="/servicos" style={{ textDecoration: "none", color: "rgba(239,239,239,0.55)", fontSize: 11, fontWeight: 600, letterSpacing: "0.14em" }}>← SERVIÇOS</Link>
      </header>

      <main id="conteudo" style={{ maxWidth: 900, margin: "0 auto", padding: `clamp(44px,7vw,88px) ${pad} 120px` }}>
        {!service ? (
          <div>
            <h1 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(36px,7vw,64px)", letterSpacing: "-0.04em", textTransform: "uppercase", margin: "0 0 16px" }}>
              Serviço não <span style={{ color: RED }}>encontrado</span>
            </h1>
            <p style={{ color: "rgba(239,239,239,0.55)", lineHeight: 1.7, marginBottom: 28 }}>
              O serviço “{slug}” não existe ou foi removido.
            </p>
            <Link to="/servicos" style={{ color: RED_INK, textDecoration: "none", fontSize: 12, fontWeight: 600, letterSpacing: "0.13em", textTransform: "uppercase" }}>Ver todos os serviços →</Link>
          </div>
        ) : (
          <article>
            <m.p
              style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.16em", color: RED_INK, margin: "0 0 14px" }}
              initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.6, ease: EASE }}
            >
              SERVIÇO {service.num}
            </m.p>
            <m.h1
              style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(38px,6.5vw,84px)", letterSpacing: "-0.05em", textTransform: "uppercase", lineHeight: 0.92, margin: "0 0 28px" }}
              initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.8, ease: EASE, delay: 0.05 }}
            >
              {service.title}
            </m.h1>

            {service.image && (
              <img src={service.image} alt={service.title} style={{ width: "100%", borderRadius: 10, margin: "0 0 36px", display: "block" }} loading="lazy" />
            )}

            {service.content && service.content.trim() ? (
              <div
                className="wp-page-content"
                style={{ lineHeight: 1.8, fontSize: 16, color: "rgba(239,239,239,0.82)" }}
                dangerouslySetInnerHTML={{ __html: service.content }}
              />
            ) : (
              <p style={{ lineHeight: 1.8, fontSize: 17, color: "rgba(239,239,239,0.78)", margin: 0 }}>{service.body}</p>
            )}

            {/* CTA */}
            <div style={{ marginTop: "clamp(40px,6vw,64px)", paddingTop: "clamp(28px,4vw,40px)", borderTop: "1px solid rgba(239,239,239,0.10)", display: "flex", flexWrap: "wrap", gap: 16, alignItems: "center", justifyContent: "space-between" }}>
              <Link
                to="/contato"
                style={{ display: "inline-flex", alignItems: "center", gap: 12, padding: "14px 28px", borderRadius: 999, background: RED_BTN, color: PURE_WHITE, textDecoration: "none", fontSize: "10px", fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase" }}
              >
                Solicitar orçamento <span style={{ fontSize: 14 }}>→</span>
              </Link>
              {next && next.slug && next.slug !== service.slug && (
                <Link to={`/servicos/${next.slug}`} style={{ color: "rgba(239,239,239,0.6)", textDecoration: "none", fontSize: 11, fontWeight: 600, letterSpacing: "0.13em", textTransform: "uppercase" }}>
                  Próximo: {next.title} →
                </Link>
              )}
            </div>
          </article>
        )}
      </main>
    </div>
  )
}
