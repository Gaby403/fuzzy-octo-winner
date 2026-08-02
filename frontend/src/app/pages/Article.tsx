import { useEffect, useMemo, useRef, useState } from "react";
import { Link, useParams } from "react-router";
import { m, useScroll, useSpring } from "motion/react";
import { useContent } from "../store/content";
import { fetchPost, PostFull } from "../store/blog";
import { Breadcrumbs } from "../components/Breadcrumbs";
import { subscribeNewsletter } from "../store/blog";
import { useLocale } from "../i18n/useLocale";
import { siteOrigin, formatarData } from "../i18n/locale";
import type { ChaveTexto } from "../i18n/dicionario";
const RED = "#F20C25";
const RED_INK = "#FF3547";
const RED_BTN = "#DA0A20";
const WHITE = "#EFEFEF";
const PURE_WHITE = "#FFFFFF";
const BLACK = "#111111";
const FONT_HEAD = '"Roboto Condensed", sans-serif';
const FONT_BODY = '"Be Vietnam Pro", sans-serif';
const pad = "clamp(20px, 4vw, 82px)";
interface Heading {
    id: string;
    text: string;
    level: number;
}
function processContent(html: string): {
    html: string;
    headings: Heading[];
} {
    if (typeof window === "undefined" || !html)
        return { html, headings: [] };
    const doc = new DOMParser().parseFromString(html, "text/html");
    const headings: Heading[] = [];
    doc.querySelectorAll("h2, h3").forEach((el, i) => {
        const text = el.textContent || "";
        const id = (text.toLowerCase().normalize("NFD").replace(/[̀-ͯ]/g, "").replace(/[^a-z0-9]+/g, "-").replace(/(^-|-$)/g, "") || `sec-${i}`);
        el.id = id;
        headings.push({ id, text, level: el.tagName === "H2" ? 2 : 3 });
    });
    return { html: doc.body.innerHTML, headings };
}
export default function Article() {
    const { content } = useContent();
    const { t, rota, locale } = useLocale();
    const { slug = "" } = useParams();
    const [post, setPost] = useState<PostFull | null>(null);
    const [status, setStatus] = useState<"loading" | "ready" | "missing">("loading");
    const { scrollYProgress } = useScroll();
    const progress = useSpring(scrollYProgress, { stiffness: 120, damping: 30, restDelta: 0.001 });
    useEffect(() => {
        let alive = true;
        setStatus("loading");
        fetchPost(slug, locale).then(p => {
            if (!alive)
                return;
            if (p) {
                setPost(p);
                setStatus("ready");
            }
            else {
                setStatus("missing");
            }
            window.scrollTo(0, 0);
        });
        return () => { alive = false; };
    }, [slug, locale]);
    const processed = useMemo(() => processContent(post?.content || ""), [post?.content]);
    const hasToc = processed.headings.length > 1;
    useEffect(() => {
        if (!post)
            return;
        document.title = `${post.title} — ${content.site.title}`;
        const origin = siteOrigin();
        const el = document.getElementById("ld-article") as HTMLScriptElement | null;
        const s = el || Object.assign(document.createElement("script"), { type: "application/ld+json", id: "ld-article" });
        s.textContent = JSON.stringify({
            "@context": "https://schema.org", "@type": "BlogPosting",
            headline: post.title, datePublished: post.dateISO, image: post.image || undefined,
            author: { "@type": "Person", name: post.author },
            publisher: { "@type": "Organization", name: content.site.title, ...(content.site.logoUrl ? { logo: { "@type": "ImageObject", url: content.site.logoUrl } } : {}) },
            mainEntityOfPage: `${origin}${rota("artigo", post.slug)}`,
            articleSection: post.categories[0]?.name,
            wordCount: (post.content || "").split(/\s+/).length,
        });
        if (!el)
            document.head.appendChild(s);
        return () => { const x = document.getElementById("ld-article"); if (x)
            x.remove(); };
    }, [post, content.site.title, content.site.logoUrl, rota]);
    const shareUrl = typeof window !== "undefined" ? window.location.href : "";
    if (status === "loading") {
        return <div style={{ minHeight: "100svh", background: BLACK, color: WHITE, fontFamily: FONT_BODY }}><main id="conteudo" style={{ padding: `60px ${pad}` }}><p style={{ color: "rgba(239,239,239,0.5)" }}>{t("geral.carregando")}</p></main></div>;
    }
    if (status === "missing" || !post) {
        return (<div style={{ minHeight: "100svh", background: BLACK, color: WHITE, fontFamily: FONT_BODY }}>
        <main id="conteudo" style={{ maxWidth: 760, margin: "0 auto", padding: `clamp(48px,8vw,96px) ${pad} 120px` }}>
          <h1 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(36px,7vw,64px)", textTransform: "uppercase", margin: "0 0 16px" }}>{t("blog.naoEncontrado")}</h1>
          <Link to={rota("blog")} style={{ color: RED_INK, textDecoration: "none", fontWeight: 600 }}>{t("blog.voltarBlog")}</Link>
        </main>
      </div>);
    }
    return (<div style={{ minHeight: "100svh", background: BLACK, color: WHITE, fontFamily: FONT_BODY }}>
      
      <m.div aria-hidden="true" style={{ position: "fixed", top: 0, left: 0, right: 0, height: 3, background: RED, transformOrigin: "left", scaleX: progress, zIndex: 60 }}/>

      <main id="conteudo" className={hasToc ? "article-page has-toc-page" : "article-page"} style={{ ["--pad" as string]: pad } as React.CSSProperties}>
        <div className="article-hero" style={{ paddingTop: "clamp(32px,5vw,56px)" }}>
          <Breadcrumbs items={[{ label: t("geral.home"), to: rota("home") }, { label: t("blog.titulo"), to: rota("blog") }, { label: post.title }]}/>
          <div style={{ display: "flex", flexWrap: "wrap", gap: 12, alignItems: "center", margin: "22px 0 14px", fontSize: 11, fontWeight: 600, letterSpacing: "0.08em", textTransform: "uppercase" }}>
            {post.categories[0]?.name && <span style={{ color: RED_INK }}>{post.categories[0].name}</span>}
            <span style={{ color: "rgba(239,239,239,0.5)" }}><time dateTime={post.dateISO}>{formatarData(post.dateISO, locale, post.date)}</time> · {post.readingTime} {t("blog.leitura")}</span>
          </div>
          <h1 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(34px,5vw,64px)", letterSpacing: "-0.04em", textTransform: "uppercase", lineHeight: 0.98, margin: "0 0 20px" }}>{post.title}</h1>
          <div style={{ display: "flex", alignItems: "center", gap: 12, marginBottom: 28 }}>
            {post.authorAvatar && <img src={post.authorAvatar} alt="" width={40} height={40} style={{ borderRadius: "50%" }} loading="lazy"/>}
            <span style={{ fontSize: 14, color: "rgba(239,239,239,0.75)" }}>{t("blog.por")} <strong>{post.author}</strong></span>
          </div>
        </div>

        {post.image && (<div style={{ maxWidth: 1100, margin: "0 auto", padding: `0 ${pad}` }}>
            <img src={post.image} alt={post.title} style={{ width: "100%", borderRadius: 14, display: "block", maxHeight: 520, objectFit: "cover" }}/>
          </div>)}

        
        <div style={{ maxWidth: hasToc ? 1100 : 820, margin: "0 auto", padding: `clamp(32px,5vw,56px) ${pad} 0`, display: "grid", gap: "clamp(24px,4vw,56px)" }} className={hasToc ? "article-grid has-toc" : "article-grid"}>
          
          {hasToc && (<aside aria-label={t("blog.indice")} className="article-toc" style={{ alignSelf: "start" }}>
              <p style={{ fontSize: 10, fontWeight: 700, letterSpacing: "0.14em", textTransform: "uppercase", color: "rgba(239,239,239,0.4)", margin: "0 0 12px" }}>{t("blog.neste")}</p>
              <ol style={{ listStyle: "none", margin: 0, padding: 0, display: "flex", flexDirection: "column", gap: 8 }}>
                {processed.headings.map(h => (<li key={h.id} style={{ paddingLeft: h.level === 3 ? 14 : 0 }}>
                    <a href={`#${h.id}`} style={{ color: "rgba(239,239,239,0.6)", textDecoration: "none", fontSize: 13, lineHeight: 1.4 }}>{h.text}</a>
                  </li>))}
              </ol>
            </aside>)}

          <article className="wp-page-content" style={{ lineHeight: 1.8, fontSize: 17, color: "rgba(239,239,239,0.85)", minWidth: 0 }} dangerouslySetInnerHTML={{ __html: processed.html }}/>
        </div>

        
        <div className="article-side" style={{ paddingTop: "clamp(32px,4vw,48px)" }}>
          <ShareBar url={shareUrl} title={post.title} t={t}/>
        </div>

        
        <div className="article-side" style={{ paddingTop: "clamp(32px,4vw,48px)" }}>
          <NewsletterBox t={t} locale={locale}/>
        </div>

        
        {post.related.length > 0 && (<section style={{ padding: `clamp(48px,7vw,96px) ${pad} clamp(64px,10vw,120px)` }} aria-label={t("blog.relacionados")}>
            <h2 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(24px,3vw,44px)", textTransform: "uppercase", letterSpacing: "-0.03em", margin: "0 0 clamp(20px,3vw,36px)" }}>{t("blog.leiaTambem")}</h2>
            <ul className="grid grid-cols-1 sm:grid-cols-3" style={{ listStyle: "none", margin: 0, padding: 0, gap: "clamp(16px,2vw,28px)" }}>
              {post.related.map(r => (<li key={r.id}>
                  <Link to={rota("artigo", r.slug)} style={{ textDecoration: "none", color: WHITE, display: "block", border: "1px solid rgba(239,239,239,0.1)", borderRadius: 12, overflow: "hidden" }}>
                    <div style={{ aspectRatio: "16/9", background: r.image ? `center/cover no-repeat url(${r.image})` : "linear-gradient(135deg,#1A0505,#2D0A0A)" }}/>
                    <div style={{ padding: 18 }}>
                      <h3 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: 18, textTransform: "uppercase", letterSpacing: "-0.02em", lineHeight: 1.1, margin: 0 }}>{r.title}</h3>
                    </div>
                  </Link>
                </li>))}
            </ul>
          </section>)}
      </main>

      <style>{`
        @media (min-width: 940px) {
          .article-grid.has-toc { grid-template-columns: 220px 1fr; }
          
          .has-toc-page .article-side {
            max-width: 1100px;
            padding-left: calc(var(--pad) + 220px + clamp(24px, 4vw, 56px));
          }
          .article-toc { position: sticky; top: 90px; }
        }
        @media (max-width: 939px) { .article-toc { order: -1; } }
        
        .article-hero {
          max-width: 1100px;
          margin: 0 auto;
          padding-left: var(--pad);
          padding-right: var(--pad);
        }
        .article-side {
          max-width: 820px;
          margin: 0 auto;
          padding-left: var(--pad);
          padding-right: var(--pad);
        }
      `}</style>
    </div>);
}
function ShareBar({ url, title, t }: {
    url: string;
    title: string;
    t: (chave: ChaveTexto) => string;
}) {
    const [copied, setCopied] = useState(false);
    const enc = encodeURIComponent;
    const links = [
        { label: "X (Twitter)", href: `https://twitter.com/intent/tweet?url=${enc(url)}&text=${enc(title)}` },
        { label: "LinkedIn", href: `https://www.linkedin.com/sharing/share-offsite/?url=${enc(url)}` },
        { label: "WhatsApp", href: `https://wa.me/?text=${enc(title + " " + url)}` },
    ];
    return (<div style={{ display: "flex", flexWrap: "wrap", gap: 10, alignItems: "center", borderTop: "1px solid rgba(239,239,239,0.1)", paddingTop: 24 }}>
      <span style={{ fontSize: 11, fontWeight: 700, letterSpacing: "0.12em", textTransform: "uppercase", color: "rgba(239,239,239,0.5)" }}>{t("blog.compartilhar")}</span>
      {links.map(l => (<a key={l.label} href={l.href} target="_blank" rel="noopener noreferrer" style={shareBtn}>{l.label}</a>))}
      <button onClick={() => { navigator.clipboard?.writeText(url); setCopied(true); setTimeout(() => setCopied(false), 1800); }} style={shareBtn}>
        {copied ? t("blog.copiado") : t("blog.copiar")}
      </button>
    </div>);
}
const shareBtn: React.CSSProperties = { fontFamily: FONT_BODY, fontSize: 12, fontWeight: 600, padding: "8px 14px", borderRadius: 999, border: "1px solid rgba(239,239,239,0.2)", color: "rgba(239,239,239,0.8)", background: "transparent", textDecoration: "none", cursor: "pointer" };
function NewsletterBox({ t, locale }: {
    t: (chave: ChaveTexto) => string;
    locale: "pt" | "en";
}) {
    const [email, setEmail] = useState("");
    const [msg, setMsg] = useState("");
    const [ok, setOk] = useState(false);
    const [sending, setSending] = useState(false);
    const submit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (sending)
            return;
        setSending(true);
        setMsg("");
        const r = await subscribeNewsletter(email, "", locale);
        setOk(r.ok);
        setMsg(r.message);
        setSending(false);
        if (r.ok)
            setEmail("");
    };
    return (<div style={{ border: "1px solid rgba(239,239,239,0.12)", borderRadius: 14, padding: "clamp(24px,3vw,36px)", background: "rgba(242,12,37,0.04)" }}>
      <h2 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(20px,2.4vw,32px)", textTransform: "uppercase", letterSpacing: "-0.03em", margin: "0 0 8px" }}>{t("news.titulo")}</h2>
      <p style={{ fontSize: 14, color: "rgba(239,239,239,0.6)", margin: "0 0 18px" }}>{t("news.texto")}</p>
      <form onSubmit={submit} style={{ display: "flex", flexWrap: "wrap", gap: 10 }}>
        <label htmlFor="nl-email" style={{ position: "absolute", width: 1, height: 1, overflow: "hidden", clip: "rect(0,0,0,0)" }}>{t("form.email")}</label>
        <input id="nl-email" type="email" required value={email} onChange={e => setEmail(e.target.value)} placeholder={t("form.exemploEmail")} style={{ flex: "1 1 220px", background: "rgba(239,239,239,0.06)", border: "1px solid rgba(239,239,239,0.16)", borderRadius: 999, color: WHITE, fontFamily: FONT_BODY, fontSize: 14, padding: "12px 18px", outline: "none" }}/>
        <button type="submit" disabled={sending} style={{ padding: "12px 24px", borderRadius: 999, border: "none", background: RED_BTN, color: PURE_WHITE, fontFamily: FONT_BODY, fontSize: 11, fontWeight: 700, letterSpacing: "0.12em", textTransform: "uppercase", cursor: "pointer", opacity: sending ? 0.6 : 1 }}>
          {sending ? t("news.enviando") : t("news.inscrever")}
        </button>
      </form>
      {msg && <p role="status" style={{ margin: "12px 0 0", fontSize: 13, color: ok ? "#3DBF72" : "#FF6B6B" }}>{msg}</p>}
    </div>);
}
