import { useState, useRef, useEffect, useCallback } from "react";
import { m, AnimatePresence } from "motion/react";
import { type SiteContent } from "../store/content";
import { TabiMark } from "./TabiMark";
const RED = "#F20C25";
const RED_INK = "#FF3547";
const WHITE = "#EFEFEF";
const BLACK = "#111111";
const EASE_OUT_EXPO: [
    number,
    number,
    number,
    number
] = [0.16, 1, 0.3, 1];
type Project = SiteContent["projects"][0];
export default function ProjectDetail({ proj, onClose, onPrev, onNext }: {
    proj: Project;
    onClose: () => void;
    onPrev: () => void;
    onNext: () => void;
}) {
    const detail = proj.detail;
    const pad = "clamp(20px, 5vw, 90px)";
    const scrollRef = useRef<HTMLDivElement>(null);
    const gallery = proj.gallery || [];
    const documents = proj.documents || [];
    const [lightbox, setLightbox] = useState<number | null>(null);
    const [docPreview, setDocPreview] = useState<string | null>(null);
    const lightboxRef = useRef(false);
    lightboxRef.current = lightbox !== null || docPreview !== null;
    const lbPrev = useCallback(() => setLightbox(i => (i === null ? i : (i - 1 + gallery.length) % gallery.length)), [gallery.length]);
    const lbNext = useCallback(() => setLightbox(i => (i === null ? i : (i + 1) % gallery.length)), [gallery.length]);
    useEffect(() => {
        const handler = (e: KeyboardEvent) => {
            if (e.key === "Escape" && !lightboxRef.current)
                onClose();
        };
        window.addEventListener("keydown", handler);
        document.body.style.overflow = "hidden";
        return () => {
            window.removeEventListener("keydown", handler);
            document.body.style.overflow = "";
        };
    }, [onClose]);
    useEffect(() => {
        if (lightbox === null)
            return;
        const h = (e: KeyboardEvent) => {
            if (e.key === "Escape")
                setLightbox(null);
            else if (e.key === "ArrowLeft")
                lbPrev();
            else if (e.key === "ArrowRight")
                lbNext();
        };
        window.addEventListener("keydown", h);
        return () => window.removeEventListener("keydown", h);
    }, [lightbox, lbPrev, lbNext]);
    useEffect(() => {
        if (docPreview === null)
            return;
        const h = (e: KeyboardEvent) => { if (e.key === "Escape")
            setDocPreview(null); };
        window.addEventListener("keydown", h);
        return () => window.removeEventListener("keydown", h);
    }, [docPreview]);
    useEffect(() => { scrollRef.current?.scrollTo(0, 0); setLightbox(null); setDocPreview(null); }, [proj.id]);
    const stagger = (i: number) => ({ initial: { opacity: 0, y: 28 }, animate: { opacity: 1, y: 0 }, transition: { duration: 0.75, delay: 0.18 + i * 0.08, ease: EASE_OUT_EXPO } });
    return (<m.div ref={scrollRef} data-lenis-prevent style={{ position: "fixed", inset: 0, zIndex: 200, overflowY: "auto", backgroundColor: "#080808", fontFamily: '"Be Vietnam Pro", sans-serif' }} initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} transition={{ duration: 0.35 }}>
      
      <div style={{ position: "relative", height: "clamp(380px, 55vh, 620px)", overflow: "hidden" }}>
        
        <div style={{ position: "absolute", inset: 0, background: proj.bg }}/>
        {proj.imageUrl && (<img src={proj.imageUrl} alt={proj.name} loading="lazy" decoding="async" style={{ position: "absolute", inset: 0, width: "100%", height: "100%", objectFit: "cover", opacity: 0.6 }}/>)}
        <div style={{ position: "absolute", inset: 0, background: `radial-gradient(circle at 70% 50%, ${proj.accent}30 0%, transparent 65%)` }}/>
        
        <div aria-hidden="true" style={{ position: "absolute", inset: 0, backgroundImage: `linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px)`, backgroundSize: "60px 60px" }}/>
        
        <div aria-hidden="true" style={{ position: "absolute", right: "-2%", bottom: "-8%", fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(160px, 22vw, 340px)", lineHeight: 0.85, letterSpacing: "-0.08em", color: WHITE, opacity: 0.05, userSelect: "none" }}>
          {proj.id}
        </div>
        
        {!proj.imageUrl && (<div style={{ position: "absolute", right: pad, top: "50%", transform: "translateY(-50%)", width: "clamp(160px, 28vw, 360px)", background: "rgba(255,255,255,0.04)", border: `1px solid ${proj.accent}33`, borderRadius: 8, backdropFilter: "blur(12px)", overflow: "hidden" }}>
            <div style={{ padding: "12px 16px", borderBottom: `1px solid ${proj.accent}22`, display: "flex", alignItems: "center", gap: 8 }}>
              <span style={{ width: 7, height: 7, borderRadius: "50%", backgroundColor: proj.accent, display: "inline-block" }}/>
              <span style={{ fontSize: 9, letterSpacing: "0.14em", color: "rgba(239,239,239,0.5)", fontWeight: 600 }}>{proj.name.toUpperCase()}</span>
            </div>
            {detail.mockupLines.map((line, i) => (<div key={i} style={{ padding: "10px 16px", borderBottom: i < detail.mockupLines.length - 1 ? `1px solid rgba(255,255,255,0.04)` : "none", display: "flex", alignItems: "center", justifyContent: "space-between" }}>
                <span style={{ fontSize: 11, color: i === 0 ? WHITE : "rgba(239,239,239,0.35)", fontWeight: i === 0 ? 600 : 400, letterSpacing: "0.06em" }}>{line}</span>
                <span style={{ fontSize: 8, color: proj.accent, fontWeight: 600 }}>{i === 0 ? "ATIVO" : "—"}</span>
              </div>))}
          </div>)}
        
        <button onClick={onClose} style={{ position: "absolute", top: "clamp(18px, 3vw, 32px)", left: pad, background: "rgba(0,0,0,0.45)", border: "1px solid rgba(239,239,239,0.14)", borderRadius: 999, color: WHITE, fontSize: 9, fontWeight: 600, letterSpacing: "0.14em", padding: "10px 18px", cursor: "pointer", display: "flex", alignItems: "center", gap: 8, fontFamily: '"Be Vietnam Pro", sans-serif', backdropFilter: "blur(8px)" }}>
          ← VOLTAR
        </button>
        
        <div style={{ position: "absolute", bottom: "clamp(32px, 5vw, 56px)", left: pad }}>
          <m.p {...stagger(0)} style={{ margin: "0 0 8px", fontSize: 9, fontWeight: 600, letterSpacing: "0.16em", color: `${proj.accent}` }}>
            {proj.category.toUpperCase()} — {proj.year}
          </m.p>
          <m.h1 {...stagger(1)} style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(36px, 5.5vw, 88px)", letterSpacing: "-0.05em", textTransform: "uppercase", color: WHITE, margin: 0, lineHeight: 0.85 }}>
            {proj.name}
          </m.h1>
        </div>
        
        <div style={{ position: "absolute", bottom: 0, left: 0, right: 0, height: "40%", background: "linear-gradient(to top, #080808 0%, transparent 100%)" }}/>
      </div>

      
      <div style={{ maxWidth: 1200, margin: "0 auto", padding: `clamp(40px, 6vw, 80px) ${pad}` }}>

        
        <m.div {...stagger(2)} className="grid grid-cols-2 md:grid-cols-4" style={{ gap: "1px", backgroundColor: "rgba(239,239,239,0.08)", border: "1px solid rgba(239,239,239,0.08)", borderRadius: 6, overflow: "hidden", marginBottom: "clamp(48px, 7vw, 88px)" }}>
          {[
            { label: "Cliente", value: detail.client },
            { label: "Duração", value: detail.duration },
            { label: "Serviços", value: detail.scope.length + " áreas" },
            { label: "Ano", value: proj.year },
        ].map((item) => (<div key={item.label} style={{ padding: "clamp(18px,2.5vw,28px)", backgroundColor: "#0D0D0D" }}>
              <p style={{ margin: "0 0 6px", fontSize: 8, fontWeight: 600, letterSpacing: "0.16em", color: "rgba(239,239,239,0.30)" }}>{item.label.toUpperCase()}</p>
              <p style={{ margin: 0, fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 700, fontSize: "clamp(14px,1.4vw,20px)", letterSpacing: "-0.03em", color: WHITE }}>{item.value}</p>
            </div>))}
        </m.div>

        
        <m.div {...stagger(3)} style={{ display: "flex", flexWrap: "wrap", gap: 8, marginBottom: proj.url ? "clamp(28px, 4vw, 40px)" : "clamp(56px, 8vw, 100px)" }}>
          {detail.scope.map((tag) => (<span key={tag} style={{ fontSize: 9, fontWeight: 600, letterSpacing: "0.14em", color: proj.accent, border: `1px solid ${proj.accent}44`, borderRadius: 999, padding: "7px 14px" }}>
              {tag.toUpperCase()}
            </span>))}
        </m.div>

        
        {proj.url && (<m.a {...stagger(3)} href={proj.url} target="_blank" rel="noopener noreferrer" style={{ display: "inline-flex", alignItems: "center", gap: 12, background: proj.accent, color: WHITE, textDecoration: "none", fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: 11, fontWeight: 700, letterSpacing: "0.14em", padding: "15px 30px", borderRadius: 999, marginBottom: "clamp(56px, 8vw, 100px)" }} whileHover={{ scale: 1.03 } as any} transition={{ duration: 0.2 }}>
            VER PROJETO COMPLETO <span style={{ fontSize: 14 }}>↗</span>
          </m.a>)}

        
        <div className="grid grid-cols-1 md:grid-cols-2" style={{ gap: "clamp(32px, 5vw, 72px)", marginBottom: "clamp(56px, 8vw, 100px)" }}>
          <m.div {...stagger(4)}>
            <p style={{ margin: "0 0 20px", fontSize: 8, fontWeight: 600, letterSpacing: "0.18em", color: "rgba(239,239,239,0.30)" }}>O DESAFIO</p>
            <div style={{ width: 32, height: 2, backgroundColor: proj.accent, marginBottom: 24 }}/>
            <p style={{ fontSize: "clamp(14px, 1.1vw, 17px)", fontWeight: 400, lineHeight: 1.78, color: "rgba(239,239,239,0.65)", margin: 0 }}>
              {detail.challenge}
            </p>
          </m.div>
          <m.div {...stagger(5)}>
            <p style={{ margin: "0 0 20px", fontSize: 8, fontWeight: 600, letterSpacing: "0.18em", color: "rgba(239,239,239,0.30)" }}>A SOLUÇÃO</p>
            <div style={{ width: 32, height: 2, backgroundColor: "rgba(239,239,239,0.25)", marginBottom: 24 }}/>
            <p style={{ fontSize: "clamp(14px, 1.1vw, 17px)", fontWeight: 400, lineHeight: 1.78, color: "rgba(239,239,239,0.65)", margin: 0 }}>
              {detail.solution}
            </p>
          </m.div>
        </div>

        
        <m.div {...stagger(6)} style={{ marginBottom: "clamp(56px, 8vw, 100px)" }}>
          <p style={{ margin: "0 0 32px", fontSize: 8, fontWeight: 600, letterSpacing: "0.18em", color: "rgba(239,239,239,0.30)" }}>RESULTADOS</p>
          <div className="grid grid-cols-2 md:grid-cols-4" style={{ gap: "clamp(16px, 2vw, 24px)" }}>
            {detail.results.map((r, i) => (<m.div key={r.label} initial={{ opacity: 0, y: 20 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true }} transition={{ duration: 0.65, delay: i * 0.08, ease: EASE_OUT_EXPO }} style={{ padding: "clamp(20px,2.5vw,32px)", backgroundColor: "#0D0D0D", border: "1px solid rgba(239,239,239,0.07)", borderRadius: 6 }}>
                <p style={{ margin: "0 0 8px", fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(28px, 3.5vw, 48px)", letterSpacing: "-0.06em", color: proj.accent, lineHeight: 1 }}>
                  {r.value}
                </p>
                <p style={{ margin: 0, fontSize: 10, fontWeight: 500, color: "rgba(239,239,239,0.40)", lineHeight: 1.4 }}>
                  {r.label}
                </p>
              </m.div>))}
          </div>
        </m.div>

        
        {proj.gallery && proj.gallery.length > 0 ? (<m.div initial={{ opacity: 0, y: 20 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true }} transition={{ duration: 0.7, ease: EASE_OUT_EXPO }} style={{ marginBottom: "clamp(56px, 8vw, 100px)" }}>
            <p style={{ margin: "0 0 28px", fontSize: 8, fontWeight: 600, letterSpacing: "0.18em", color: "rgba(239,239,239,0.30)" }}>GALERIA</p>
            <div className="grid grid-cols-1 md:grid-cols-2" style={{ gap: "clamp(12px, 1.5vw, 20px)" }}>
              {proj.gallery.map((src, i) => (<m.button key={i} type="button" onClick={() => setLightbox(i)} aria-label={`Abrir imagem ${i + 1}`} initial={{ opacity: 0, y: 24 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true }} transition={{ duration: 0.6, delay: i * 0.06, ease: EASE_OUT_EXPO }} style={{ display: "block", padding: 0, cursor: "pointer", borderRadius: 8, overflow: "hidden", position: "relative", aspectRatio: "16 / 10", background: "#0D0D0D", border: "1px solid rgba(239,239,239,0.07)" }} whileHover={{ scale: 1.01 } as any}>
                  <img src={src} alt={`${proj.name} — imagem ${i + 1}`} loading="lazy" style={{ width: "100%", height: "100%", objectFit: "cover", display: "block" }}/>
                </m.button>))}
            </div>
          </m.div>) : (<m.div initial={{ opacity: 0, scaleX: 0.96 }} whileInView={{ opacity: 1, scaleX: 1 }} viewport={{ once: true }} transition={{ duration: 0.9, ease: EASE_OUT_EXPO }} style={{ height: "clamp(180px, 28vw, 340px)", borderRadius: 8, overflow: "hidden", position: "relative", marginBottom: "clamp(56px, 8vw, 100px)" }}>
            <div style={{ position: "absolute", inset: 0, background: proj.bg }}/>
            <div style={{ position: "absolute", inset: 0, background: `radial-gradient(circle at 30% 50%, ${proj.accent}28 0%, transparent 60%)` }}/>
            <div aria-hidden="true" style={{ position: "absolute", inset: 0, backgroundImage: `linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px)`, backgroundSize: "50px 50px" }}/>
            <div style={{ position: "absolute", inset: 0, display: "flex", alignItems: "center", justifyContent: "center" }}>
              <span style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: "clamp(48px, 8vw, 120px)", letterSpacing: "-0.06em", color: WHITE, opacity: 0.08, userSelect: "none", textTransform: "uppercase" }}>
                {proj.name}
              </span>
            </div>
          </m.div>)}

        
        {documents.length > 0 && (<m.div initial={{ opacity: 0, y: 20 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true }} transition={{ duration: 0.7, ease: EASE_OUT_EXPO }} style={{ marginBottom: "clamp(56px, 8vw, 100px)" }}>
            <p style={{ margin: "0 0 28px", fontSize: 8, fontWeight: 600, letterSpacing: "0.18em", color: "rgba(239,239,239,0.30)" }}>DOCUMENTOS</p>
            <div className="grid grid-cols-1 sm:grid-cols-2" style={{ gap: "clamp(12px, 1.5vw, 16px)" }}>
              {documents.map((doc, i) => (<m.button key={i} type="button" onClick={() => setDocPreview(doc.url)} initial={{ opacity: 0, y: 16 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true }} transition={{ duration: 0.5, delay: i * 0.05, ease: EASE_OUT_EXPO }} style={{ display: "flex", alignItems: "center", gap: 14, textAlign: "left", cursor: "pointer", padding: "16px 18px", background: "#0D0D0D", border: "1px solid rgba(239,239,239,0.08)", borderRadius: 10 }} whileHover={{ borderColor: `${proj.accent}66` } as any}>
                  <span style={{ flexShrink: 0, width: 40, height: 40, borderRadius: 8, background: `${proj.accent}1A`, color: proj.accent, display: "flex", alignItems: "center", justifyContent: "center", fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: 12, letterSpacing: "0.04em" }}>PDF</span>
                  <span style={{ flex: 1, minWidth: 0 }}>
                    <span style={{ display: "block", fontSize: 14, fontWeight: 600, color: WHITE, whiteSpace: "nowrap", overflow: "hidden", textOverflow: "ellipsis" }}>{doc.title}</span>
                    <span style={{ display: "block", fontSize: 10, fontWeight: 600, letterSpacing: "0.12em", color: "rgba(239,239,239,0.35)", marginTop: 3 }}>VISUALIZAR ↗</span>
                  </span>
                </m.button>))}
            </div>
          </m.div>)}

        
        <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", paddingTop: "clamp(24px,3vw,40px)", borderTop: "1px solid rgba(239,239,239,0.08)" }}>
          <button onClick={onPrev} style={{ background: "transparent", border: "none", cursor: "pointer", color: "rgba(239,239,239,0.40)", fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: 9, fontWeight: 600, letterSpacing: "0.14em", display: "flex", alignItems: "center", gap: 10, padding: 0, transition: "color 0.2s" }} onMouseEnter={e => (e.currentTarget.style.color = WHITE)} onMouseLeave={e => (e.currentTarget.style.color = "rgba(239,239,239,0.40)")}>
            ← PROJETO ANTERIOR
          </button>
          <button onClick={onClose} style={{ background: "transparent", border: "none", cursor: "pointer", color: "rgba(239,239,239,0.25)", fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: 9, fontWeight: 600, letterSpacing: "0.14em", padding: 0 }}>
            TODOS OS PROJETOS
          </button>
          <button onClick={onNext} style={{ background: "transparent", border: "none", cursor: "pointer", color: "rgba(239,239,239,0.40)", fontFamily: '"Be Vietnam Pro", sans-serif', fontSize: 9, fontWeight: 600, letterSpacing: "0.14em", display: "flex", alignItems: "center", gap: 10, padding: 0, transition: "color 0.2s" }} onMouseEnter={e => (e.currentTarget.style.color = WHITE)} onMouseLeave={e => (e.currentTarget.style.color = "rgba(239,239,239,0.40)")}>
            PRÓXIMO PROJETO →
          </button>
        </div>
      </div>

      
      <AnimatePresence>
        {lightbox !== null && gallery[lightbox] && (<m.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} transition={{ duration: 0.2 }} onClick={() => setLightbox(null)} style={{ position: "fixed", inset: 0, zIndex: 300, background: "rgba(0,0,0,0.93)", display: "flex", alignItems: "center", justifyContent: "center", padding: "clamp(16px, 4vw, 64px)" }}>
            <span style={{ position: "absolute", top: "clamp(18px,3vw,30px)", left: "clamp(16px,3vw,28px)", fontSize: 11, letterSpacing: "0.14em", color: "rgba(239,239,239,0.6)", fontWeight: 600 }}>
              {String(lightbox + 1).padStart(2, "0")} / {String(gallery.length).padStart(2, "0")}
            </span>
            <button onClick={e => { e.stopPropagation(); setLightbox(null); }} aria-label="Fechar" style={{ position: "absolute", top: "clamp(14px,3vw,26px)", right: "clamp(14px,3vw,26px)", width: 44, height: 44, borderRadius: "50%", background: "rgba(255,255,255,0.08)", border: "1px solid rgba(255,255,255,0.18)", color: WHITE, fontSize: 22, cursor: "pointer", lineHeight: 1 }}>
              ×
            </button>
            {gallery.length > 1 && (<button onClick={e => { e.stopPropagation(); lbPrev(); }} aria-label="Anterior" className="hero-cta-secondary" style={{ position: "absolute", left: "clamp(8px,2vw,28px)", top: "50%", transform: "translateY(-50%)", width: 50, height: 50, borderRadius: "50%", background: "rgba(255,255,255,0.08)", border: "1px solid rgba(255,255,255,0.18)", color: WHITE, fontSize: 24, cursor: "pointer", lineHeight: 1 }}>
                ‹
              </button>)}
            <m.img key={lightbox} src={gallery[lightbox]} alt={`${proj.name} — imagem ${lightbox + 1}`} onClick={e => e.stopPropagation()} initial={{ opacity: 0, scale: 0.98 }} animate={{ opacity: 1, scale: 1 }} transition={{ duration: 0.25, ease: EASE_OUT_EXPO }} style={{ maxWidth: "92vw", maxHeight: "86vh", objectFit: "contain", borderRadius: 6, boxShadow: "0 20px 80px rgba(0,0,0,0.6)" }}/>
            {gallery.length > 1 && (<button onClick={e => { e.stopPropagation(); lbNext(); }} aria-label="Próxima" style={{ position: "absolute", right: "clamp(8px,2vw,28px)", top: "50%", transform: "translateY(-50%)", width: 50, height: 50, borderRadius: "50%", background: "rgba(255,255,255,0.08)", border: "1px solid rgba(255,255,255,0.18)", color: WHITE, fontSize: 24, cursor: "pointer", lineHeight: 1 }}>
                ›
              </button>)}
          </m.div>)}
      </AnimatePresence>

      
      <AnimatePresence>
        {docPreview && (<m.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} transition={{ duration: 0.2 }} onClick={() => setDocPreview(null)} style={{ position: "fixed", inset: 0, zIndex: 300, background: "rgba(0,0,0,0.9)", display: "flex", flexDirection: "column", padding: "clamp(12px,3vw,40px)" }}>
            <div onClick={e => e.stopPropagation()} style={{ display: "flex", alignItems: "center", justifyContent: "flex-end", gap: 10, marginBottom: 12 }}>
              <a href={docPreview} target="_blank" rel="noopener noreferrer" style={{ display: "inline-flex", alignItems: "center", gap: 8, background: "rgba(255,255,255,0.08)", border: "1px solid rgba(255,255,255,0.18)", color: WHITE, textDecoration: "none", fontSize: 11, fontWeight: 600, letterSpacing: "0.12em", padding: "10px 16px", borderRadius: 999 }}>
                ABRIR EM NOVA ABA ↗
              </a>
              <button onClick={() => setDocPreview(null)} aria-label="Fechar" style={{ width: 44, height: 44, borderRadius: "50%", background: "rgba(255,255,255,0.08)", border: "1px solid rgba(255,255,255,0.18)", color: WHITE, fontSize: 22, cursor: "pointer", lineHeight: 1 }}>
                ×
              </button>
            </div>
            <m.iframe key={docPreview} src={docPreview} title="Documento" onClick={e => e.stopPropagation()} initial={{ opacity: 0, y: 8 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.25 }} style={{ flex: 1, width: "100%", border: "1px solid rgba(255,255,255,0.14)", borderRadius: 8, background: "#fff" }}/>
          </m.div>)}
      </AnimatePresence>
    </m.div>);
}
