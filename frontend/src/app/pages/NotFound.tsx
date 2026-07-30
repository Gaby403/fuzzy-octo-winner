import { useEffect } from "react";
import { Link } from "react-router";
import { m } from "motion/react";
import { useContent } from "../store/content";
import { TabiMark } from "../components/TabiMark";
import { useLocale } from "../i18n/useLocale";
const RED = "#F20C25";
const RED_BTN = "#DA0A20";
const RED_INK = "#FF3547";
const PURE_WHITE = "#FFFFFF";
const WHITE = "#EFEFEF";
const BLACK = "#111111";
const FONT_HEAD = '"Roboto Condensed", sans-serif';
const FONT_BODY = '"Be Vietnam Pro", sans-serif';
const EASE: [
    number,
    number,
    number,
    number
] = [0.16, 1, 0.3, 1];
export default function NotFound() {
    const { content } = useContent();
    const { t, rota } = useLocale();
    useEffect(() => {
        document.title = `${t("erro.404")} — ${content.site.title}`;
        const meta = document.querySelector('meta[name="robots"]') || document.head.appendChild(Object.assign(document.createElement("meta"), { name: "robots" }));
        meta.setAttribute("content", "noindex, follow");
        return () => { meta.setAttribute("content", "index, follow"); };
    }, [content.site.title, t]);
    const links = [
        { to: rota("home"), label: t("menu.home") },
        { to: rota("servicos"), label: t("menu.servicos") },
        { to: rota("projetos"), label: t("menu.projetos") },
        { to: rota("blog"), label: t("menu.blog") },
        { to: rota("contato"), label: t("menu.contato") },
    ];
    return (<div style={{ minHeight: "100svh", background: BLACK, color: WHITE, fontFamily: FONT_BODY, display: "flex", flexDirection: "column" }}>
      <main id="conteudo" style={{ flex: 1, display: "flex", flexDirection: "column", alignItems: "center", justifyContent: "center", textAlign: "center", padding: "60px clamp(20px,5vw,32px)", position: "relative", overflow: "hidden" }}>
        <m.div style={{ position: "relative", width: "clamp(150px,26vw,240px)", height: "clamp(150px,26vw,240px)", display: "flex", alignItems: "center", justifyContent: "center", marginBottom: "clamp(24px,4vw,44px)" }} initial={{ scale: 0.6, opacity: 0 }} animate={{ scale: 1, opacity: 1 }} transition={{ duration: 1, ease: EASE }}>
          <m.div style={{ position: "absolute", inset: 0, borderRadius: "50%", background: RED }} animate={{ scale: [1, 1.05, 1] }} transition={{ duration: 4, repeat: Infinity, ease: "easeInOut" }}/>
          <div style={{ position: "relative", zIndex: 1, width: "clamp(80px,14vw,130px)" }}><TabiMark width="100%" color={WHITE}/></div>
        </m.div>
        <p style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(56px,12vw,120px)", lineHeight: 0.9, letterSpacing: "-0.05em", margin: 0 }}>4<span style={{ color: RED }}>0</span>4</p>
        <h1 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(22px,3vw,40px)", textTransform: "uppercase", letterSpacing: "-0.03em", margin: "8px 0 0" }}>{t("erro.404")}</h1>
        <p style={{ fontSize: "clamp(13px,1vw,16px)", color: "rgba(239,239,239,0.6)", maxWidth: 440, margin: "16px 0 0", lineHeight: 1.7 }}>
          {t("erro.texto")}
        </p>
        <Link to={rota("home")} style={{ marginTop: "clamp(28px,4vw,40px)", display: "inline-flex", alignItems: "center", gap: 12, padding: "15px 30px", borderRadius: 999, background: RED_BTN, color: PURE_WHITE, textDecoration: "none", fontSize: "10px", fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase" }}>
          {t("erro.voltar")} <span style={{ fontSize: 14 }}>→</span>
        </Link>
        <nav aria-label={t("erro.linksUteis")} style={{ marginTop: 28, display: "flex", flexWrap: "wrap", gap: 18, justifyContent: "center" }}>
          {links.map(l => (<Link key={l.to} to={l.to} style={{ color: RED_INK, textDecoration: "none", fontSize: 12, fontWeight: 600, letterSpacing: "0.06em" }}>{l.label}</Link>))}
        </nav>
      </main>
    </div>);
}
