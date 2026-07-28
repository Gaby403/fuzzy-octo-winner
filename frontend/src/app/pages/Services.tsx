import { useEffect } from "react";
import { useNavigate } from "react-router";
import { m } from "motion/react";
import { useContent } from "../store/content";
const RED = "#F20C25";
const RED_BTN = "#DA0A20";
const RED_INK = "#FF3547";
const PURE_WHITE = "#FFFFFF";
const WHITE = "#EFEFEF";
const BLACK = "#111111";
const FONT_HEAD = '"Roboto Condensed", sans-serif';
const FONT_BODY = '"Be Vietnam Pro", sans-serif';
export default function Services() {
    const { content } = useContent();
    const navigate = useNavigate();
    const pad = "clamp(20px, 4vw, 82px)";
    useEffect(() => {
        document.title = `Serviços — ${content.site.title}`;
        window.scrollTo(0, 0);
    }, [content.site.title]);
    return (<div style={{ minHeight: "100svh", background: BLACK, color: WHITE, fontFamily: FONT_BODY }}>

      <main id="conteudo">
        <div style={{ padding: `clamp(48px, 8vw, 96px) ${pad} clamp(28px, 4vw, 48px)` }}>
          <div className="flex items-center gap-3 mb-6" style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: "rgba(239,239,239,0.40)" }}>
            <span className="block rounded-full flex-shrink-0" style={{ width: 7, height: 7, backgroundColor: RED }}/>
            <span>{content.sections.services.eyebrow}</span>
          </div>
          <h1 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(44px, 7vw, 96px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: 0, lineHeight: 0.92 }}>
            O QUE <span style={{ color: RED }}>ENTREGAMOS.</span>
          </h1>
        </div>

        <ul className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style={{ listStyle: "none", margin: 0, padding: `0 ${pad} clamp(64px, 10vw, 120px)`, gap: "0 clamp(24px, 3vw, 48px)" }}>
          {content.services.map((s, i) => {
            const to = s.slug ? `/servicos/${s.slug}` : undefined;
            return (<li key={s.num || s.title}>
                <m.a href={to} onClick={to ? (e) => { e.preventDefault(); navigate(to); } : undefined} aria-label={`Ver serviço: ${s.title}`} style={{ display: "block", textDecoration: "none", color: WHITE, borderTop: "1px solid rgba(239,239,239,0.10)", paddingTop: "clamp(24px,3vw,36px)", paddingBottom: "clamp(24px,3vw,36px)", cursor: to ? "pointer" : "default" }} initial={{ opacity: 0, y: 24 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true, margin: "-40px" }} transition={{ duration: 0.7, delay: (i % 3) * 0.06, ease: [0.16, 1, 0.3, 1] }}>
                  <span style={{ display: "block", fontSize: "9px", fontWeight: 600, letterSpacing: "0.16em", color: RED_INK, marginBottom: 16 }}>{s.num}</span>
                  <h2 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(18px, 1.7vw, 28px)", letterSpacing: "-0.04em", textTransform: "uppercase", lineHeight: 1.05, margin: "0 0 14px" }}>{s.title}</h2>
                  <p style={{ fontSize: "clamp(12px, 0.85vw, 14px)", lineHeight: 1.65, color: "rgba(239,239,239,0.45)", margin: 0 }}>{s.body}</p>
                  <span aria-hidden="true" style={{ display: "block", marginTop: 20, fontSize: 13, color: RED_INK }}>→</span>
                </m.a>
              </li>);
        })}
        </ul>
      </main>
    </div>);
}
