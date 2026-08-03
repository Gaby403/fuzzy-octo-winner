import { useEffect, useState } from "react";
import { useNavigate } from "react-router";
import { useContent, submitContact } from "../store/content";
import { getRecaptchaToken, prepararRecaptcha, trackEvent } from "../utils/analytics";
import { TabiDetail } from "../components/TabiDetail";
import { useLocale } from "../i18n/useLocale";
const RED = "#F20C25";
const RED_BTN = "#DA0A20";
const RED_INK = "#FF3547";
const PURE_WHITE = "#FFFFFF";
const WHITE = "#EFEFEF";
const BLACK = "#111111";
const FONT_HEAD = '"Roboto Condensed", "Roboto Condensed Fallback", sans-serif';
const FONT_BODY = '"Be Vietnam Pro", "Be Vietnam Pro Fallback", sans-serif';
export default function Contact() {
    const { content } = useContent();
    const navigate = useNavigate();
    const { t, rota, locale } = useLocale();
    const f = content.footer;
    const [form, setForm] = useState({ name: "", email: "", subject: "", message: "", website: "" });
    const [state, setState] = useState<"idle" | "sending" | "ok" | "error">("idle");
    const [feedback, setFeedback] = useState("");
    useEffect(() => {
        document.title = `${t("menu.contato")} — ${content.site.title}`;
        window.scrollTo(0, 0);
    }, [content.site.title, t]);
    const set = (k: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => setForm(prev => ({ ...prev, [k]: e.target.value }));
    const onSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (state === "sending")
            return;
        setState("sending");
        setFeedback("");
        const recaptchaToken = await getRecaptchaToken(content.site.recaptchaSite, "contact");
        const res = await submitContact({ ...form, recaptchaToken }, locale);
        if (res.ok) {
            setState("ok");
            setForm({ name: "", email: "", subject: "", message: "", website: "" });
            trackEvent("contact_submit");
            navigate(rota("obrigado"));
        }
        else {
            setState("error");
            setFeedback(res.message);
        }
    };
    const label: React.CSSProperties = {
        display: "block",
        fontFamily: FONT_BODY,
        fontSize: "9px",
        fontWeight: 600,
        letterSpacing: "0.17em",
        textTransform: "uppercase",
        color: "rgba(239,239,239,0.45)",
        marginBottom: 8,
    };
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
    };
    return (<div style={{ minHeight: "100svh", position: "relative", overflow: "hidden", background: BLACK, color: WHITE, fontFamily: FONT_BODY }}>
      <TabiDetail corner="top-right" opacity={0.16}/>

      <main id="conteudo" className="contact-grid" style={{ maxWidth: 1120, margin: "0 auto", padding: "clamp(44px,7vw,88px) clamp(20px,5vw,32px) 120px", display: "grid", gridTemplateColumns: "1fr", gap: "clamp(40px,6vw,72px)" }}>
        
        <div>
          <div className="flex items-center gap-3" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.45)", marginBottom: 22 }}>
            <span className="block rounded-full" style={{ width: 7, height: 7, backgroundColor: RED }}/>
            <span>{content.site.title.toUpperCase()} — {t("menu.contato").toUpperCase()}</span>
          </div>
          <h1 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(40px,6vw,76px)", letterSpacing: "-0.05em", textTransform: "uppercase", lineHeight: 0.9, margin: "0 0 24px" }}>
            {content.contact.title} <span style={{ color: RED }}>{content.contact.highlight}</span>
          </h1>
          <p style={{ fontSize: "clamp(13px,1vw,16px)", lineHeight: 1.7, color: "rgba(239,239,239,0.6)", maxWidth: 420, margin: "0 0 36px" }}>
            {content.contact.description}
          </p>

          <div style={{ display: "flex", flexDirection: "column", gap: 18 }}>
            {f.email && (<a href={`mailto:${f.email}`} style={{ textDecoration: "none", color: WHITE }}>
                <span style={label}>{t("form.email")}</span>
                <span style={{ fontSize: 16, color: "rgba(239,239,239,0.85)" }}>{f.email}</span>
              </a>)}
            {f.phone && (<a href={`tel:${f.phone.replace(/[^+\d]/g, "")}`} style={{ textDecoration: "none", color: WHITE }}>
                <span style={label}>{t("form.telefone")}</span>
                <span style={{ fontSize: 16, color: "rgba(239,239,239,0.85)" }}>{f.phone}</span>
              </a>)}
            {f.city && (<div>
                <span style={label}>{t("form.local")}</span>
                <span style={{ fontSize: 16, color: "rgba(239,239,239,0.85)" }}>{f.city}</span>
              </div>)}
          </div>
        </div>

        
        <form onSubmit={onSubmit} onFocus={() => prepararRecaptcha(content.site.recaptchaSite)} style={{ display: "flex", flexDirection: "column", gap: 20 }}>
          <div>
            <label style={label} htmlFor="c-name">{t("form.nome")}</label>
            <input id="c-name" style={field} type="text" value={form.name} onChange={set("name")} required placeholder={t("form.seuNome")} autoComplete="name"/>
          </div>
          <div>
            <label style={label} htmlFor="c-email">{t("form.email")}</label>
            <input id="c-email" style={field} type="email" value={form.email} onChange={set("email")} required placeholder={t("form.exemploEmail")} autoComplete="email"/>
          </div>
          <div>
            <label style={label} htmlFor="c-subject">{t("form.assunto")}</label>
            <input id="c-subject" style={field} type="text" value={form.subject} onChange={set("subject")} placeholder={t("form.sobreOQue")}/>
          </div>
          <div>
            <label style={label} htmlFor="c-message">{t("form.mensagem")}</label>
            <textarea id="c-message" style={{ ...field, minHeight: 140, resize: "vertical" }} value={form.message} onChange={set("message")} required placeholder={t("form.conte")}/>
          </div>

          
          <input type="text" value={form.website} onChange={set("website")} tabIndex={-1} autoComplete="off" aria-hidden="true" style={{ position: "absolute", left: "-9999px", width: 1, height: 1, opacity: 0 }}/>

          <button type="submit" disabled={state === "sending"} style={{
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
        }}>
            {state === "sending" ? t("form.enviando") : t("form.enviar")} <span style={{ fontSize: 14 }}>→</span>
          </button>

          
          {content.site.recaptchaSite && (<p style={{ margin: "14px 0 0", fontSize: 11, lineHeight: 1.6, color: "rgba(239,239,239,0.35)" }}>
              {t("form.recaptcha")}{" "}
              <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer" style={{ color: RED_INK }}>{t("form.privacidade")}</a>{" "}&{" "}
              <a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer" style={{ color: RED_INK }}>{t("form.termos")}</a> {t("form.doGoogle")}
            </p>)}

          {feedback && (<p role="status" style={{
                margin: 0,
                fontSize: 13,
                lineHeight: 1.6,
                color: state === "ok" ? "#3DBF72" : "#FF6B6B",
            }}>
              {feedback}
            </p>)}
        </form>
      </main>

      <style>{`
        @media (min-width: 860px) {
          .contact-grid { grid-template-columns: 1fr 1fr !important; align-items: start; }
        }
        .contact-grid input:focus, .contact-grid textarea:focus { border-color: ${RED} !important; }
        .contact-grid ::placeholder { color: rgba(239,239,239,0.30); }
      `}</style>
    </div>);
}
