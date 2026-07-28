import { useRef } from "react";
import { Link } from "react-router";
import { m, useInView } from "motion/react";
import { useContent } from "../../store/content";
import { useGoTo } from "../../hooks/useGoTo";
import { TabiMark } from "../TabiMark";
import { NewsletterForm } from "../ui/NewsletterForm";
import { colors, fonts, ease, PAGE_PAD } from "../../constants/theme";
export function Footer() {
    const { content } = useContent();
    const goTo = useGoTo();
    const ref = useRef<HTMLElement>(null);
    const inView = useInView(ref, { once: true, margin: "-60px" });
    const f = content.footer;
    const colTitle: React.CSSProperties = { display: "block", fontSize: "9px", fontWeight: 600, letterSpacing: "0.17em", color: colors.textFaint, marginBottom: 20, textTransform: "uppercase" };
    const linkStyle: React.CSSProperties = { fontFamily: fonts.body, fontSize: "clamp(12px, 0.85vw, 14px)", fontWeight: 400, color: colors.textMuted, textDecoration: "none", display: "block" };
    return (<footer id="contato" ref={ref} style={{ backgroundColor: colors.black, fontFamily: fonts.body, position: "relative", overflow: "hidden" }}>
      <div aria-hidden="true" style={{ position: "absolute", left: "-4%", bottom: "-10%", width: "clamp(240px, 34vw, 580px)", opacity: 0.04, pointerEvents: "none" }}>
        <TabiMark width="100%" color={colors.white}/>
      </div>

      
      <div style={{ borderTop: `1px solid ${colors.border}`, borderBottom: `1px solid ${colors.border}`, position: "relative", zIndex: 1 }}>
        <m.div className="flex flex-col md:flex-row md:items-center md:justify-between gap-6" style={{ padding: `clamp(48px,7vw,88px) ${PAGE_PAD}` }} initial={{ opacity: 0, y: 20 }} animate={inView ? { opacity: 1, y: 0 } : {}} transition={{ duration: 0.7, ease: ease.out }}>
          <h2 style={{ fontFamily: fonts.head, fontWeight: 900, fontSize: "clamp(28px,4vw,56px)", letterSpacing: "-0.04em", textTransform: "uppercase", lineHeight: 0.95, margin: 0, maxWidth: 720 }}>
            {f.ctaTitle} <span style={{ color: colors.red }}>{f.ctaHighlight}</span>
          </h2>
          <button onClick={(e) => goTo(f.ctaUrl || "/contato", e)} style={{ flexShrink: 0, display: "inline-flex", alignItems: "center", gap: 12, padding: "16px 32px", borderRadius: 999, border: "none", background: colors.redBtn, color: colors.pureWhite, fontFamily: fonts.body, fontSize: "11px", fontWeight: 700, letterSpacing: "0.14em", textTransform: "uppercase", cursor: "pointer" }}>
            {f.ctaLabel} <span style={{ fontSize: 14 }}>→</span>
          </button>
        </m.div>
      </div>

      
      <div style={{ padding: `clamp(48px, 8vw, 88px) ${PAGE_PAD} clamp(32px, 5vw, 56px)`, position: "relative", zIndex: 1 }}>
        <div className="grid grid-cols-1 md:grid-cols-12" style={{ gap: "clamp(36px, 5vw, 56px)" }}>
          
          <div className="md:col-span-4">
            <div style={{ marginBottom: 18 }}>
              {content.site.logoUrl ? (<img src={content.site.logoUrl} alt={f.brand || content.site.title} loading="lazy" decoding="async" style={{ height: "clamp(30px, 3.4vw, 44px)", width: "auto", display: "block" }}/>) : (<span style={{ fontFamily: fonts.head, fontWeight: 900, fontSize: "clamp(18px, 1.6vw, 26px)", letterSpacing: "-0.05em", textTransform: "uppercase", color: colors.white }}>{f.brand}</span>)}
              <span style={{ display: "block", width: 32, height: 2, backgroundColor: colors.red, marginTop: 10 }}/>
            </div>
            <p style={{ fontSize: "clamp(12px, 0.85vw, 14px)", lineHeight: 1.72, color: colors.textFaint, maxWidth: 300, marginBottom: 22 }}>{f.tagline}</p>
            <span style={colTitle}>Newsletter</span>
            <NewsletterForm compact/>
          </div>

          
          {f.columns.map((col, ci) => (<div key={col.title + ci} className="md:col-span-2" style={{ minWidth: 0 }}>
              <span style={colTitle}>{col.title}</span>
              <ul style={{ listStyle: "none", padding: 0, margin: 0, display: "flex", flexDirection: "column", gap: 12 }}>
                {col.links.map((link, li) => (<li key={link.label + li}>
                    <a href={link.url || "#"} onClick={(e) => goTo(link.url, e)} style={linkStyle}>{link.label}</a>
                  </li>))}
              </ul>
            </div>))}

          
          <div className="md:col-span-2" style={{ minWidth: 0 }}>
            <span style={colTitle}>{f.contactTitle}</span>
            <ul style={{ listStyle: "none", padding: 0, margin: 0, display: "flex", flexDirection: "column", gap: 12 }}>
              {f.email && <li><a href={`mailto:${f.email}`} style={linkStyle}>{f.email}</a></li>}
              {f.phone && <li><a href={`tel:${f.phone.replace(/[^+\d]/g, "")}`} style={linkStyle}>{f.phone}</a></li>}
              {f.city && <li><span style={{ ...linkStyle, cursor: "default" }}>{f.city}</span></li>}
            </ul>
          </div>

          
          <div className="md:col-span-2">
            <span style={colTitle}>{f.socialTitle}</span>
            <ul style={{ listStyle: "none", padding: 0, margin: 0, display: "flex", flexDirection: "column", gap: 12 }}>
              {f.social.map((s, si) => (<li key={s.label + si}>
                  <a href={s.url || "#"} target={s.url && s.url.startsWith("http") ? "_blank" : undefined} rel={s.url && s.url.startsWith("http") ? "noopener noreferrer" : undefined} style={{ ...linkStyle, display: "flex", alignItems: "center", gap: 8 }}>
                    {s.label} <span aria-hidden="true" style={{ fontSize: 10, opacity: 0.4 }}>↗</span>
                  </a>
                </li>))}
            </ul>
          </div>
        </div>
      </div>

      
      <div style={{ borderTop: `1px solid ${colors.border}`, padding: `20px ${PAGE_PAD}`, position: "relative", zIndex: 1 }}>
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
          <span style={{ fontSize: "9px", fontWeight: 500, letterSpacing: "0.10em", color: "rgba(239,239,239,0.3)" }}>{f.copyright}</span>
          <div className="flex flex-wrap items-center gap-6">
            {f.legal.map((item, i) => (<a key={item.label + i} href={item.url || "#"} onClick={(e) => goTo(item.url, e)} style={{ fontSize: "9px", fontWeight: 500, letterSpacing: "0.10em", color: "rgba(239,239,239,0.3)", textDecoration: "none" }}>{item.label}</a>))}
            {content.pages.map(pg => (<Link key={pg.slug} to={`/p/${pg.slug}`} style={{ fontSize: "9px", fontWeight: 500, letterSpacing: "0.10em", color: "rgba(239,239,239,0.3)", textDecoration: "none" }}>{pg.title}</Link>))}
            {f.madeIn && <span style={{ fontSize: "9px", fontWeight: 500, letterSpacing: "0.08em", color: "rgba(239,239,239,0.18)" }}>{f.madeIn}</span>}
          </div>
        </div>
      </div>
    </footer>);
}
