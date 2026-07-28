import { useEffect, useState } from "react"
import { Link, useNavigate } from "react-router"
import { useContent, submitContact } from "../store/content"

const RED = "#F20C25"
const RED_BTN = "#DA0A20"
const RED_INK = "#FF3547"
const PURE_WHITE = "#FFFFFF"
const WHITE = "#EFEFEF"
const BLACK = "#111111"

const FONT_HEAD = '"Roboto Condensed", sans-serif'
const FONT_BODY = '"Be Vietnam Pro", sans-serif'

/**
 * Página de contato (/contato) com formulário que envia para o WordPress
 * (studio-tabi/v1/contact → e-mail do dono do site). As informações de
 * contato (e-mail, telefone, cidade) vêm do rodapé, editáveis no CMS.
 */
export default function Contact() {
  const { content } = useContent()
  const navigate = useNavigate()
  const f = content.footer

  const [form, setForm] = useState({ name: "", email: "", subject: "", message: "", website: "" })
  const [state, setState] = useState<"idle" | "sending" | "ok" | "error">("idle")
  const [feedback, setFeedback] = useState("")

  useEffect(() => {
    document.title = `Contato — ${content.site.title}`
    window.scrollTo(0, 0)
  }, [content.site.title])

  const set = (k: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) =>
    setForm(prev => ({ ...prev, [k]: e.target.value }))

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    if (state === "sending") return
    setState("sending")
    setFeedback("")
    const res = await submitContact(form)
    if (res.ok) {
      setState("ok")
      setForm({ name: "", email: "", subject: "", message: "", website: "" })
      // Redireciona para a página de agradecimento com a animação do kanji.
      navigate("/obrigado")
    } else {
      setState("error")
      setFeedback(res.message)
    }
  }

  const label: React.CSSProperties = {
    display: "block",
    fontFamily: FONT_BODY,
    fontSize: "9px",
    fontWeight: 600,
    letterSpacing: "0.17em",
    textTransform: "uppercase",
    color: "rgba(239,239,239,0.45)",
    marginBottom: 8,
  }
  const field: React.CSSProperties = {
    width: "100%",
    background: "rgba(239,239,239,0.04)",
    border: "1px solid rgba(239,239,239,0.14)",
    borderRadius: 8,
    color: WHITE,
    fontFamily: FONT_BODY,
    fontSize: 15,
    padding: "13px 15px",
    outline: "none",
  }

  return (
    <div style={{ minHeight: "100svh", background: BLACK, color: WHITE, fontFamily: FONT_BODY }}>
      {/* Header */}
      <header
        style={{ display: "flex", alignItems: "center", justifyContent: "space-between", padding: "22px clamp(20px,5vw,64px)", borderBottom: "1px solid rgba(239,239,239,0.08)", position: "sticky", top: 0, background: "rgba(17,17,17,0.86)", backdropFilter: "blur(10px)", zIndex: 20 }}
      >
        <Link to="/" style={{ textDecoration: "none", color: WHITE, display: "inline-flex", alignItems: "center" }}>
          {content.site.logoUrl ? (
            <img src={content.site.logoUrl} alt={content.nav.brand || content.site.title} style={{ height: "clamp(26px, 3.4vw, 40px)", width: "auto", display: "block" }} />
          ) : (
            <span style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: 20, letterSpacing: "-0.04em", textTransform: "uppercase" }}>
              {content.site.title.split(" ")[0] || "STUDIO"} <span style={{ color: RED }}>{content.site.title.split(" ").slice(1).join(" ") || "TABI"}</span>
            </span>
          )}
        </Link>
        <Link to="/" style={{ textDecoration: "none", color: "rgba(239,239,239,0.55)", fontSize: 11, fontWeight: 600, letterSpacing: "0.14em" }}>
          ← VOLTAR
        </Link>
      </header>

      <main id="conteudo"
        className="contact-grid"
        style={{ maxWidth: 1120, margin: "0 auto", padding: "clamp(44px,7vw,88px) clamp(20px,5vw,32px) 120px", display: "grid", gridTemplateColumns: "1fr", gap: "clamp(40px,6vw,72px)" }}
      >
        {/* Coluna esquerda — chamada + contatos */}
        <div>
          <div className="flex items-center gap-3" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.45)", marginBottom: 22 }}>
            <span className="block rounded-full" style={{ width: 7, height: 7, backgroundColor: RED }} />
            <span>{content.site.title.toUpperCase()} — CONTATO</span>
          </div>
          <h1 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(40px,6vw,76px)", letterSpacing: "-0.05em", textTransform: "uppercase", lineHeight: 0.9, margin: "0 0 24px" }}>
            {content.contact.title} <span style={{ color: RED }}>{content.contact.highlight}</span>
          </h1>
          <p style={{ fontSize: "clamp(13px,1vw,16px)", lineHeight: 1.7, color: "rgba(239,239,239,0.6)", maxWidth: 420, margin: "0 0 36px" }}>
            {content.contact.description}
          </p>

          <div style={{ display: "flex", flexDirection: "column", gap: 18 }}>
            {f.email && (
              <a href={`mailto:${f.email}`} style={{ textDecoration: "none", color: WHITE }}>
                <span style={label}>E-mail</span>
                <span style={{ fontSize: 16, color: "rgba(239,239,239,0.85)" }}>{f.email}</span>
              </a>
            )}
            {f.phone && (
              <a href={`tel:${f.phone.replace(/[^+\d]/g, "")}`} style={{ textDecoration: "none", color: WHITE }}>
                <span style={label}>Telefone</span>
                <span style={{ fontSize: 16, color: "rgba(239,239,239,0.85)" }}>{f.phone}</span>
              </a>
            )}
            {f.city && (
              <div>
                <span style={label}>Localização</span>
                <span style={{ fontSize: 16, color: "rgba(239,239,239,0.85)" }}>{f.city}</span>
              </div>
            )}
          </div>
        </div>

        {/* Coluna direita — formulário */}
        <form onSubmit={onSubmit} style={{ display: "flex", flexDirection: "column", gap: 20 }}>
          <div>
            <label style={label} htmlFor="c-name">Nome</label>
            <input id="c-name" style={field} type="text" value={form.name} onChange={set("name")} required placeholder="Seu nome" autoComplete="name" />
          </div>
          <div>
            <label style={label} htmlFor="c-email">E-mail</label>
            <input id="c-email" style={field} type="email" value={form.email} onChange={set("email")} required placeholder="voce@email.com" autoComplete="email" />
          </div>
          <div>
            <label style={label} htmlFor="c-subject">Assunto</label>
            <input id="c-subject" style={field} type="text" value={form.subject} onChange={set("subject")} placeholder="Sobre o que quer falar?" />
          </div>
          <div>
            <label style={label} htmlFor="c-message">Mensagem</label>
            <textarea id="c-message" style={{ ...field, minHeight: 140, resize: "vertical" }} value={form.message} onChange={set("message")} required placeholder="Conte sobre o seu projeto…" />
          </div>

          {/* Honeypot anti-spam (oculto) */}
          <input
            type="text"
            value={form.website}
            onChange={set("website")}
            tabIndex={-1}
            autoComplete="off"
            aria-hidden="true"
            style={{ position: "absolute", left: "-9999px", width: 1, height: 1, opacity: 0 }}
          />

          <button
            type="submit"
            disabled={state === "sending"}
            style={{
              marginTop: 4,
              display: "inline-flex",
              alignItems: "center",
              justifyContent: "center",
              gap: 12,
              padding: "15px 30px",
              borderRadius: 999,
              border: "none",
              background: RED_BTN,
              color: PURE_WHITE,
              fontFamily: FONT_BODY,
              fontSize: "10px",
              fontWeight: 700,
              letterSpacing: "0.16em",
              textTransform: "uppercase",
              cursor: state === "sending" ? "default" : "pointer",
              opacity: state === "sending" ? 0.6 : 1,
              transition: "opacity 0.2s",
            }}
          >
            {state === "sending" ? "ENVIANDO…" : "ENVIAR MENSAGEM"} <span style={{ fontSize: 14 }}>→</span>
          </button>

          {feedback && (
            <p
              role="status"
              style={{
                margin: 0,
                fontSize: 13,
                lineHeight: 1.6,
                color: state === "ok" ? "#3DBF72" : "#FF6B6B",
              }}
            >
              {feedback}
            </p>
          )}
        </form>
      </main>

      <style>{`
        @media (min-width: 860px) {
          .contact-grid { grid-template-columns: 1fr 1fr !important; align-items: start; }
        }
        .contact-grid input:focus, .contact-grid textarea:focus { border-color: ${RED} !important; }
        .contact-grid ::placeholder { color: rgba(239,239,239,0.30); }
      `}</style>
    </div>
  )
}
