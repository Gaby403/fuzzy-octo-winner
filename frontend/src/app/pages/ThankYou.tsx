import { useEffect } from "react"
import { Link } from "react-router"
import { m } from "motion/react"
import { useContent } from "../store/content"
import { TabiMark } from "../components/TabiMark"

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
 * Página de agradecimento (/obrigado) exibida após o envio do formulário de
 * contato. Traz o kanji Tabi (旅) animado — surge com o círculo vermelho,
 * gira levemente ao entrar e flutua em loop.
 */
export default function ThankYou() {
  const { content } = useContent()
  const t = content.thankYou
  // Acrescenta um ponto final vermelho, a menos que o título já termine em pontuação.
  const title = (t.title || "OBRIGADO").trim()
  const needsDot = !/[.!?…]$/.test(title)

  useEffect(() => {
    document.title = `Obrigado — ${content.site.title}`
    window.scrollTo(0, 0)
  }, [content.site.title])

  return (
    <div
      id="conteudo"
      style={{
        minHeight: "100svh",
        background: BLACK,
        color: WHITE,
        fontFamily: FONT_BODY,
        display: "flex",
        flexDirection: "column",
        alignItems: "center",
        justifyContent: "center",
        textAlign: "center",
        padding: "48px clamp(20px,5vw,32px)",
        position: "relative",
        overflow: "hidden",
      }}
    >
      {/* Kanji Tabi animado dentro do círculo vermelho (como o "sol" do hero) */}
      <m.div
        style={{ position: "relative", width: "clamp(180px, 34vw, 300px)", height: "clamp(180px, 34vw, 300px)", display: "flex", alignItems: "center", justifyContent: "center", marginBottom: "clamp(32px, 5vw, 56px)" }}
        initial={{ scale: 0.5, opacity: 0 }}
        animate={{ scale: 1, opacity: 1 }}
        transition={{ duration: 1.1, ease: EASE }}
      >
        {/* Halo pulsante */}
        <m.div
          aria-hidden="true"
          style={{ position: "absolute", inset: "-8%", borderRadius: "50%", background: `radial-gradient(circle, ${RED}44 0%, transparent 68%)` }}
          animate={{ scale: [1, 1.12, 1], opacity: [0.55, 0.85, 0.55] }}
          transition={{ duration: 3.4, ease: "easeInOut", repeat: Infinity }}
        />
        {/* Círculo vermelho */}
        <m.div
          style={{ position: "absolute", inset: 0, borderRadius: "50%", background: RED }}
          initial={{ scale: 0 }}
          animate={{ scale: 1 }}
          transition={{ duration: 0.9, ease: EASE, delay: 0.05 }}
        />
        {/* Kanji Tabi flutuando */}
        <m.div
          style={{ position: "relative", zIndex: 1, display: "flex", alignItems: "center", justifyContent: "center" }}
          initial={{ rotate: -14, scale: 0.6, opacity: 0 }}
          animate={{ rotate: 0, scale: 1, opacity: 1 }}
          transition={{ duration: 1.2, ease: EASE, delay: 0.25 }}
        >
          <m.div
            animate={{ y: ["0%", "-6%", "0%"] }}
            transition={{ duration: 4.2, ease: "easeInOut", repeat: Infinity }}
          >
            <TabiMark width="58%" color={WHITE} style={{ width: "clamp(96px, 18vw, 170px)" }} />
          </m.div>
        </m.div>
      </m.div>

      {/* Título */}
      <m.h1
        style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(44px, 8vw, 104px)", letterSpacing: "-0.05em", textTransform: "uppercase", lineHeight: 0.9, margin: 0 }}
        initial={{ y: 24, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ duration: 0.9, ease: EASE, delay: 0.5 }}
      >
        {title}{needsDot && <span style={{ color: RED }}>.</span>}
      </m.h1>

      <m.p
        style={{ fontSize: "clamp(13px, 1.1vw, 17px)", lineHeight: 1.7, color: "rgba(239,239,239,0.62)", maxWidth: 460, margin: "22px 0 0" }}
        initial={{ y: 18, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ duration: 0.9, ease: EASE, delay: 0.62 }}
      >
        {t.message}
      </m.p>

      <m.div
        initial={{ y: 16, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ duration: 0.9, ease: EASE, delay: 0.74 }}
        style={{ marginTop: "clamp(30px, 4vw, 44px)", display: "flex", flexWrap: "wrap", gap: 16, justifyContent: "center" }}
      >
        <Link
          to="/"
          style={{ display: "inline-flex", alignItems: "center", gap: 12, padding: "14px 28px", borderRadius: 999, background: RED_BTN, color: PURE_WHITE, textDecoration: "none", fontFamily: FONT_BODY, fontSize: "10px", fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase" }}
        >
          VOLTAR AO INÍCIO <span style={{ fontSize: 14 }}>→</span>
        </Link>
        <Link
          to="/projetos"
          style={{ display: "inline-flex", alignItems: "center", gap: 10, padding: "14px 28px", borderRadius: 999, border: "1px solid rgba(239,239,239,0.22)", color: WHITE, textDecoration: "none", fontFamily: FONT_BODY, fontSize: "10px", fontWeight: 600, letterSpacing: "0.16em", textTransform: "uppercase" }}
        >
          VER PROJETOS
        </Link>
      </m.div>
    </div>
  )
}
