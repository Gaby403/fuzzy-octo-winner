import { useEffect, useRef, useState } from "react"
import { Link, useParams } from "react-router"
import { m, useScroll, useSpring, useTransform } from "motion/react"
import { useContent, fetchPage, WpPage } from "../store/content"
import { Breadcrumbs } from "../components/Breadcrumbs"
import { TabiStroke } from "../components/TabiStroke"
import { ProcessIcon } from "../components/ui/ProcessIcon"
import { colors, fonts, ease, PAGE_PAD } from "../constants/theme"

/**
 * Página de uma etapa do processo (/processo/:slug). Os metadados (título,
 * ícone, ordem) vêm de content.process; o texto rico vem da Página homônima
 * no WordPress (fetchPage), com fallback para o resumo.
 */
export default function ProcessStep() {
  const { content } = useContent()
  const { slug = "" } = useParams()
  const steps = content.process
  const idx = steps.findIndex(s => s.slug === slug)
  const step = idx >= 0 ? steps[idx] : null
  const prev = idx > 0 ? steps[idx - 1] : null
  const next = idx >= 0 && idx < steps.length - 1 ? steps[idx + 1] : null

  const [page, setPage] = useState<WpPage | null>(null)
  const [loaded, setLoaded] = useState(false)

  // O traço do 旅 acompanha a leitura da página (0 no topo, 1 no fim).
  const pageRef = useRef<HTMLDivElement>(null)
  const { scrollYProgress } = useScroll({ target: pageRef, offset: ["start start", "end end"] })
  const drawProgress = useSpring(scrollYProgress, { stiffness: 80, damping: 24, restDelta: 0.001 })
  // Some antes do fim para não pairar sobre o rodapé.
  const markOpacity = useTransform(scrollYProgress, [0, 0.06, 0.82, 0.95], [0, 0.18, 0.18, 0])

  useEffect(() => {
    let alive = true
    setLoaded(false)
    fetchPage(slug).then(p => { if (alive) { setPage(p); setLoaded(true) } })
    document.title = `${step ? step.title : "Processo"} — ${content.site.title}`
    window.scrollTo(0, 0)
    return () => { alive = false }
  }, [slug, step, content.site.title])

  return (
    <div ref={pageRef} style={{ minHeight: "100svh", background: colors.black, color: colors.white, fontFamily: fonts.body, position: "relative", overflow: "hidden" }}>
      {/* Assinatura tabi: o kanji da jornada se desenha conforme o visitante
          percorre a etapa — a leitura completa fecha o traço. */}
      <m.div aria-hidden="true" className="hidden md:block pointer-events-none"
        style={{ position: "fixed", right: "2%", top: "18%", width: "clamp(200px, 22vw, 360px)", opacity: markOpacity, zIndex: 0 }}>
        <TabiStroke key={slug} width="100%" color={colors.red} strokeWidth={0.8} progress={drawProgress} />
      </m.div>

      <main id="conteudo" style={{ position: "relative", zIndex: 1, maxWidth: 900, margin: "0 auto", padding: `clamp(40px,6vw,72px) ${PAGE_PAD} 120px` }}>
        <Breadcrumbs items={[{ label: "Home", to: "/" }, { label: "Como Trabalhamos", to: "/sobre" }, { label: step ? step.title : slug }]} />

        {!step ? (
          <div style={{ paddingTop: 40 }}>
            <h1 style={{ fontFamily: fonts.head, fontWeight: 900, fontSize: "clamp(36px,7vw,64px)", textTransform: "uppercase", margin: "0 0 16px" }}>Etapa não <span style={{ color: colors.red }}>encontrada</span></h1>
            <Link to="/sobre" style={{ color: colors.redInk, textDecoration: "none", fontWeight: 600 }}>← Ver o processo</Link>
          </div>
        ) : (
          <>
            <m.div style={{ display: "flex", alignItems: "center", gap: 14, margin: "22px 0 18px", color: colors.red }}
              initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.6, ease: ease.out }}>
              <ProcessIcon name={step.icon} size={34} />
              <span style={{ fontFamily: fonts.body, fontSize: 11, fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase", color: colors.redInk }}>Etapa 0{idx + 1}</span>
            </m.div>
            <m.h1 style={{ fontFamily: fonts.head, fontWeight: 900, fontSize: "clamp(40px,7vw,88px)", letterSpacing: "-0.05em", textTransform: "uppercase", lineHeight: 0.92, margin: "0 0 20px" }}
              initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.8, ease: ease.out, delay: 0.05 }}>
              {step.title}
            </m.h1>
            <p style={{ fontSize: "clamp(15px,1.3vw,20px)", lineHeight: 1.7, color: colors.textMuted, margin: "0 0 clamp(28px,4vw,44px)", maxWidth: 680 }}>{step.summary}</p>

            {loaded && page && page.content ? (
              <div className="wp-page-content" style={{ lineHeight: 1.8, fontSize: 17, color: colors.text }} dangerouslySetInnerHTML={{ __html: page.content }} />
            ) : loaded ? (
              <p style={{ color: colors.textFaint }}>Conteúdo detalhado em breve.</p>
            ) : (
              <div className="stcms-skeleton" style={{ height: 200, borderRadius: 12 }} />
            )}

            {/* Navegação entre etapas + CTA */}
            <div style={{ marginTop: "clamp(40px,6vw,64px)", paddingTop: "clamp(28px,4vw,40px)", borderTop: `1px solid ${colors.border}`, display: "flex", flexWrap: "wrap", gap: 16, alignItems: "center", justifyContent: "space-between" }}>
              <Link to="/contato" style={{ display: "inline-flex", alignItems: "center", gap: 12, padding: "14px 28px", borderRadius: 999, background: colors.redBtn, color: colors.pureWhite, textDecoration: "none", fontSize: "10px", fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase" }}>
                Iniciar projeto <span style={{ fontSize: 14 }}>→</span>
              </Link>
              <div style={{ display: "flex", gap: 20 }}>
                {prev && <Link to={`/processo/${prev.slug}`} style={{ color: colors.textMuted, textDecoration: "none", fontSize: 11, fontWeight: 600, letterSpacing: "0.1em", textTransform: "uppercase" }}>← {prev.title}</Link>}
                {next && <Link to={`/processo/${next.slug}`} style={{ color: colors.redInk, textDecoration: "none", fontSize: 11, fontWeight: 600, letterSpacing: "0.1em", textTransform: "uppercase" }}>{next.title} →</Link>}
              </div>
            </div>
          </>
        )}
      </main>
    </div>
  )
}
