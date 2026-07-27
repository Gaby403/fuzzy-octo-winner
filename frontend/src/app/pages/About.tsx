import { useEffect } from "react"
import { Link } from "react-router"
import { m } from "motion/react"
import { useContent } from "../store/content"
import { TabiMark } from "../components/TabiMark"

const RED = "#F20C25"
const WHITE = "#EFEFEF"
const BLACK = "#111111"
const FONT_HEAD = '"Roboto Condensed", sans-serif'
const FONT_BODY = '"Be Vietnam Pro", sans-serif'
const EASE: [number, number, number, number] = [0.16, 1, 0.3, 1]

/**
 * Página "Sobre" (/sobre) — versão completa, montada a partir do conteúdo
 * editável de "Sobre" no CMS (parágrafos, estatísticas e etapas).
 */
export default function About() {
  const { content } = useContent()
  const a = content.about
  const sec = content.sections.about
  const pad = "clamp(20px, 4vw, 82px)"

  useEffect(() => {
    document.title = `Sobre — ${content.site.title}`
    window.scrollTo(0, 0)
  }, [content.site.title])

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
        <Link to="/" style={{ textDecoration: "none", color: "rgba(239,239,239,0.55)", fontSize: 11, fontWeight: 600, letterSpacing: "0.14em" }}>← VOLTAR</Link>
      </header>

      <main id="conteudo" style={{ position: "relative", overflow: "hidden" }}>
        {/* Marca Tabi de fundo */}
        <div aria-hidden="true" style={{ position: "absolute", right: "-6%", top: "2%", width: "clamp(240px, 34vw, 560px)", opacity: 0.05, pointerEvents: "none" }}>
          <TabiMark width="100%" color={WHITE} />
        </div>

        {/* Intro */}
        <section style={{ padding: `clamp(48px, 8vw, 96px) ${pad} clamp(32px, 5vw, 64px)`, position: "relative", zIndex: 1 }}>
          <div className="flex items-center gap-3 mb-6" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
            <span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }} />
            <span>{sec.eyebrow}</span>
          </div>
          <m.h1
            style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(44px, 7vw, 100px)", letterSpacing: "-0.05em", textTransform: "uppercase", lineHeight: 0.9, margin: "0 0 36px", maxWidth: 1000 }}
            initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.8, ease: EASE }}
          >
            NÃO FAZEMOS SITES. <span style={{ color: RED }}>CONSTRUÍMOS PRESENÇA.</span>
          </m.h1>
          <div className="grid grid-cols-1 md:grid-cols-2" style={{ gap: "clamp(20px,4vw,60px)", maxWidth: 980 }}>
            <p style={{ fontSize: "clamp(13px,1vw,17px)", lineHeight: 1.72, color: "rgba(239,239,239,0.62)", margin: 0 }}>{a.paragraph1}</p>
            <p style={{ fontSize: "clamp(13px,1vw,17px)", lineHeight: 1.72, color: "rgba(239,239,239,0.62)", margin: 0 }}>{a.paragraph2}</p>
          </div>
        </section>

        {/* Stats */}
        <section className="grid grid-cols-2 md:grid-cols-4" style={{ padding: `clamp(32px,5vw,64px) ${pad}`, gap: "clamp(20px,3vw,48px)", borderTop: "1px solid rgba(239,239,239,0.08)", position: "relative", zIndex: 1 }}>
          {a.stats.map((s, i) => (
            <m.div key={s.label} initial={{ opacity: 0, y: 20 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true }} transition={{ duration: 0.6, delay: i * 0.08, ease: EASE }}>
              <div style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(36px,4.5vw,72px)", lineHeight: 0.9, letterSpacing: "-0.04em" }}>
                {s.numeric}<span style={{ color: RED }}>{s.suffix}</span>
              </div>
              <div style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.14em", color: "rgba(239,239,239,0.4)", marginTop: 10 }}>{s.label}</div>
            </m.div>
          ))}
        </section>

        {/* Etapas */}
        <section style={{ padding: `clamp(40px,6vw,80px) ${pad} clamp(56px,9vw,120px)`, position: "relative", zIndex: 1 }}>
          <div className="flex items-center justify-between" style={{ marginBottom: "clamp(16px,2vw,28px)" }}>
            <span style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.30)" }}>{sec.pillarsLabel}</span>
            <span style={{ fontSize: "9px", fontWeight: 500, letterSpacing: "0.10em", color: "rgba(239,239,239,0.20)" }}>{`0${a.pillars.length}`} ETAPAS</span>
          </div>
          {a.pillars.map((p, i) => (
            <div key={p.title} style={{ borderTop: "1px solid rgba(239,239,239,0.10)", display: "flex", alignItems: "flex-start", gap: "clamp(14px,3vw,40px)", padding: "clamp(22px,3vw,34px) 0" }}>
              <span style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(24px,3vw,48px)", lineHeight: 1, letterSpacing: "-0.05em", color: RED, flexShrink: 0, width: "clamp(44px,5vw,84px)" }}>{`0${i + 1}`}</span>
              <div>
                <h2 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(18px,2vw,32px)", letterSpacing: "-0.03em", textTransform: "uppercase", lineHeight: 1.05, margin: "0 0 8px" }}>{p.title}</h2>
                <p style={{ fontSize: "clamp(12px,0.9vw,15px)", lineHeight: 1.65, color: "rgba(239,239,239,0.5)", margin: 0, maxWidth: 620 }}>{p.body}</p>
              </div>
            </div>
          ))}

          <div style={{ marginTop: "clamp(40px,6vw,64px)" }}>
            <Link to="/contato" style={{ display: "inline-flex", alignItems: "center", gap: 12, padding: "15px 30px", borderRadius: 999, background: RED, color: WHITE, textDecoration: "none", fontSize: "10px", fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase" }}>
              Vamos conversar <span style={{ fontSize: 14 }}>→</span>
            </Link>
          </div>
        </section>
      </main>
    </div>
  )
}
