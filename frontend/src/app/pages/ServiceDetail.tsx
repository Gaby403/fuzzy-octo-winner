import { useEffect, useRef } from "react";
import { Link, useParams } from "react-router";
import { m, useScroll, useSpring, useTransform } from "motion/react";
import { useContent } from "../store/content";
import { Breadcrumbs } from "../components/Breadcrumbs";
import { TabiStroke } from "../components/TabiStroke";
import { TextReveal } from "../components/ui/TextReveal";
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
export default function ServiceDetail() {
    const { content } = useContent();
    const { t, rota } = useLocale();
    const { slug = "" } = useParams();
    const services = content.services;
    const idx = services.findIndex((s) => s.slug === slug);
    const service = idx >= 0 ? services[idx] : null;
    const next = services.length ? services[(Math.max(idx, 0) + 1) % services.length] : null;
    const pad = "clamp(20px, 4vw, 82px)";
    const pageRef = useRef<HTMLDivElement>(null);
    const { scrollYProgress } = useScroll({ target: pageRef, offset: ["start start", "end end"] });
    const drawProgress = useSpring(scrollYProgress, { stiffness: 80, damping: 24, restDelta: 0.001 });
    const markOpacity = useTransform(scrollYProgress, [0, 0.06, 0.82, 0.95], [0, 0.18, 0.18, 0]);
    useEffect(() => {
        document.title = service ? `${service.title} — ${content.site.title}` : `${t("servico.rotulo")} — ${content.site.title}`;
        window.scrollTo(0, 0);
    }, [service, content.site.title, t]);
    return (<div ref={pageRef} style={{ minHeight: "100svh", background: BLACK, color: WHITE, fontFamily: FONT_BODY, position: "relative", overflow: "hidden" }}>
      <m.div aria-hidden="true" className="hidden md:block pointer-events-none" style={{ position: "fixed", right: "2%", top: "18%", width: "clamp(200px, 22vw, 360px)", opacity: markOpacity, zIndex: 0 }}>
        <TabiStroke key={slug} width="100%" color={RED} strokeWidth={0.8} progress={drawProgress}/>
      </m.div>

      <main id="conteudo" style={{ position: "relative", zIndex: 1, maxWidth: 900, margin: "0 auto", padding: `clamp(44px,7vw,88px) ${pad} 120px` }}>
        {!service ? (<div>
            <h1 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(36px,7vw,64px)", letterSpacing: "-0.04em", textTransform: "uppercase", margin: "0 0 16px" }}>
              {t("servico.naoEncontrado")} <span style={{ color: RED }}>{t("servico.encontrado")}</span>
            </h1>
            <p style={{ color: "rgba(239,239,239,0.55)", lineHeight: 1.7, marginBottom: 28 }}>
              {t("servico.inexistente")} “{slug}”.
            </p>
            <Link to={rota("servicos")} style={{ color: RED_INK, textDecoration: "none", fontSize: 12, fontWeight: 600, letterSpacing: "0.13em", textTransform: "uppercase" }}>{t("servico.todos")}</Link>
          </div>) : (<article>
            <Breadcrumbs items={[{ label: t("geral.home"), to: rota("home") }, { label: t("menu.servicos"), to: rota("servicos") }, { label: service.title }]}/>
            <m.p style={{ fontSize: "9px", fontWeight: 600, letterSpacing: "0.16em", color: RED_INK, margin: "22px 0 14px" }} initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.6, ease: EASE }}>
              {t("servico.rotulo")} {service.num}
            </m.p>
            <m.h1 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(38px,6.5vw,84px)", letterSpacing: "-0.05em", textTransform: "uppercase", lineHeight: 0.92, margin: "0 0 28px" }} initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.8, ease: EASE, delay: 0.05 }}>
              {service.title}
            </m.h1>

            {service.image && (<img src={service.image} alt={service.title} style={{ width: "100%", borderRadius: 10, margin: "0 0 36px", display: "block" }} loading="lazy"/>)}

            {service.content && service.content.trim() ? (<>
                
                {service.showExcerpt && service.body && (<TextReveal text={service.body} style={{ fontSize: "clamp(15px,1.3vw,20px)", lineHeight: 1.7, color: "rgba(239,239,239,0.7)", margin: "0 0 clamp(24px,3vw,36px)", maxWidth: 680 }}/>)}
                <div className="wp-page-content" style={{ lineHeight: 1.8, fontSize: 16, color: "rgba(239,239,239,0.82)" }} dangerouslySetInnerHTML={{ __html: service.content }}/>
              </>) : (<TextReveal text={service.body} style={{ lineHeight: 1.8, fontSize: 17, color: "rgba(239,239,239,0.78)", margin: 0 }}/>)}

            
            <div style={{ marginTop: "clamp(40px,6vw,64px)", paddingTop: "clamp(28px,4vw,40px)", borderTop: "1px solid rgba(239,239,239,0.10)", display: "flex", flexWrap: "wrap", gap: 16, alignItems: "center", justifyContent: "space-between" }}>
              <Link to={rota("contato")} style={{ display: "inline-flex", alignItems: "center", gap: 12, padding: "14px 28px", borderRadius: 999, background: RED_BTN, color: PURE_WHITE, textDecoration: "none", fontSize: "10px", fontWeight: 700, letterSpacing: "0.16em", textTransform: "uppercase" }}>
                {t("servico.orcamento")} <span style={{ fontSize: 14 }}>→</span>
              </Link>
              {next && next.slug && next.slug !== service.slug && (<Link to={rota("servico", next.slug)} style={{ color: "rgba(239,239,239,0.6)", textDecoration: "none", fontSize: 11, fontWeight: 600, letterSpacing: "0.13em", textTransform: "uppercase" }}>
                  {t("servico.proximo")} {next.title} →
                </Link>)}
            </div>
          </article>)}
      </main>
    </div>);
}
