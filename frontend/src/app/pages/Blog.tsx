import { useEffect, useState, useCallback } from "react"
import { Link, useSearchParams } from "react-router"
import { m } from "motion/react"
import { useContent } from "../store/content"
import { fetchPosts, fetchCategories, PostCard, BlogCategory } from "../store/blog"
import { Breadcrumbs } from "../components/Breadcrumbs"
import { TabiDetail } from "../components/TabiDetail"

const RED = "#F20C25"
const RED_INK = "#FF3547"
const WHITE = "#EFEFEF"
const BLACK = "#111111"
const FONT_HEAD = '"Roboto Condensed", sans-serif'
const FONT_BODY = '"Be Vietnam Pro", sans-serif'
const EASE: [number, number, number, number] = [0.16, 1, 0.3, 1]
const pad = "clamp(20px, 4vw, 82px)"

export default function Blog() {
  const { content } = useContent()
  const [params, setParams] = useSearchParams()
  const page = Math.max(1, parseInt(params.get("page") || "1", 10))
  const category = params.get("categoria") || ""
  const search = params.get("q") || ""

  const [data, setData] = useState<{ items: PostCard[]; totalPages: number; total: number } | null>(null)
  const [cats, setCats] = useState<BlogCategory[]>([])
  const [state, setState] = useState<"loading" | "ready" | "error">("loading")
  const [searchInput, setSearchInput] = useState(search)

  useEffect(() => {
    document.title = `Blog — ${content.site.title}`
    window.scrollTo(0, 0)
  }, [content.site.title, page, category, search])

  useEffect(() => { fetchCategories().then(setCats) }, [])

  useEffect(() => {
    let alive = true
    setState("loading")
    fetchPosts({ page, category, search, perPage: 9 }).then(res => {
      if (!alive) return
      setData({ items: res.items, totalPages: res.totalPages, total: res.total })
      setState("ready")
    }).catch(() => alive && setState("error"))
    return () => { alive = false }
  }, [page, category, search])

  const update = useCallback((next: Record<string, string | null>) => {
    setParams(prev => {
      const p = new URLSearchParams(prev)
      Object.entries(next).forEach(([k, v]) => { if (v) p.set(k, v); else p.delete(k) })
      p.delete("page")
      return p
    })
  }, [setParams])

  const onSearch = (e: React.FormEvent) => { e.preventDefault(); update({ q: searchInput || null }) }

  return (
    <div style={{ minHeight: "100svh", position: "relative", overflow: "hidden", background: BLACK, color: WHITE, fontFamily: FONT_BODY }}>
      <TabiDetail corner="bottom-right" />
      <main id="conteudo">
        <div style={{ padding: `clamp(40px, 6vw, 72px) ${pad} clamp(24px, 3vw, 40px)` }}>
          <Breadcrumbs items={[{ label: "Home", to: "/" }, { label: "Blog" }]} />
          <h1 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(44px, 7vw, 96px)", letterSpacing: "-0.05em", textTransform: "uppercase", margin: "20px 0 0", lineHeight: 0.92 }}>
            BLOG <span style={{ color: RED }}>&amp; IDEIAS.</span>
          </h1>
          <p style={{ fontSize: "clamp(13px,1vw,16px)", lineHeight: 1.7, color: "rgba(239,239,239,0.6)", maxWidth: 560, margin: "18px 0 0" }}>
            Estratégia, design e tecnologia — o que aprendemos construindo presença digital.
          </p>
        </div>

        {/* Filtros */}
        <div style={{ padding: `0 ${pad}`, display: "flex", flexWrap: "wrap", gap: 16, alignItems: "center", justifyContent: "space-between", marginBottom: "clamp(24px,3vw,40px)" }}>
          <div className="flex flex-wrap" style={{ gap: 8 }}>
            <button onClick={() => update({ categoria: null })} aria-pressed={!category}
              style={chip(!category)}>Todos</button>
            {cats.map(c => (
              <button key={c.slug} onClick={() => update({ categoria: c.slug })} aria-pressed={category === c.slug}
                style={chip(category === c.slug)}>{c.name}</button>
            ))}
          </div>
          <form onSubmit={onSearch} role="search" style={{ display: "flex", gap: 8 }}>
            <label htmlFor="blog-search" className="sr-only" style={srOnly}>Buscar no blog</label>
            <input id="blog-search" type="search" value={searchInput} onChange={e => setSearchInput(e.target.value)}
              placeholder="Buscar…" style={{ background: "rgba(239,239,239,0.05)", border: "1px solid rgba(239,239,239,0.16)", borderRadius: 999, color: WHITE, fontFamily: FONT_BODY, fontSize: 13, padding: "10px 16px", outline: "none", minWidth: 180 }} />
            <button type="submit" style={{ ...chip(false), borderColor: RED_INK, color: RED_INK }}>Buscar</button>
          </form>
        </div>

        {/* Conteúdo */}
        <div style={{ padding: `0 ${pad} clamp(64px, 10vw, 120px)` }}>
          {state === "loading" && <Skeletons />}
          {state === "error" && <Msg text="Não foi possível carregar o blog agora. Tente novamente em instantes." />}
          {state === "ready" && data && data.items.length === 0 && (
            <Msg text={search ? `Nenhum artigo encontrado para “${search}”.` : "Ainda não há artigos publicados."} />
          )}
          {state === "ready" && data && data.items.length > 0 && (
            <>
              <ul className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style={{ listStyle: "none", margin: 0, padding: 0, gap: "clamp(20px,2.5vw,32px)" }}>
                {data.items.map((p, i) => <li key={p.id}><ArticleCard p={p} index={i} /></li>)}
              </ul>
              {data.totalPages > 1 && (
                <nav aria-label="Paginação" style={{ display: "flex", gap: 8, justifyContent: "center", marginTop: "clamp(40px,5vw,64px)" }}>
                  <PageBtn disabled={page <= 1} onClick={() => setParams(p => { const n = new URLSearchParams(p); n.set("page", String(page - 1)); return n })}>Anterior</PageBtn>
                  <span style={{ alignSelf: "center", fontSize: 12, color: "rgba(239,239,239,0.6)" }}>Página {page} de {data.totalPages}</span>
                  <PageBtn disabled={page >= data.totalPages} onClick={() => setParams(p => { const n = new URLSearchParams(p); n.set("page", String(page + 1)); return n })}>Próxima</PageBtn>
                </nav>
              )}
            </>
          )}
        </div>
      </main>
    </div>
  )
}

function ArticleCard({ p, index }: { p: PostCard; index: number }) {
  return (
    <m.article initial={{ opacity: 0, y: 24 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true, margin: "-40px" }}
      transition={{ duration: 0.6, delay: (index % 3) * 0.06, ease: EASE }} style={{ height: "100%" }}>
      <Link to={`/blog/${p.slug}`} style={{ textDecoration: "none", color: WHITE, display: "flex", flexDirection: "column", height: "100%", border: "1px solid rgba(239,239,239,0.1)", borderRadius: 12, overflow: "hidden", background: "rgba(239,239,239,0.02)" }}>
        <div style={{ aspectRatio: "16/9", background: p.image ? `center/cover no-repeat url(${p.image})` : "linear-gradient(135deg,#1A0505,#2D0A0A)" }} aria-hidden={!p.image} />
        <div style={{ padding: "clamp(18px,2vw,26px)", display: "flex", flexDirection: "column", gap: 10, flex: 1 }}>
          <div style={{ display: "flex", gap: 10, alignItems: "center", fontSize: 10, fontWeight: 600, letterSpacing: "0.1em", color: RED_INK, textTransform: "uppercase" }}>
            {p.categories[0]?.name && <span>{p.categories[0].name}</span>}
            <span style={{ color: "rgba(239,239,239,0.4)" }}>{p.readingTime} min de leitura</span>
          </div>
          <h2 style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(18px,1.6vw,24px)", letterSpacing: "-0.03em", textTransform: "uppercase", lineHeight: 1.08, margin: 0 }}>{p.title}</h2>
          <p style={{ fontSize: 13.5, lineHeight: 1.6, color: "rgba(239,239,239,0.55)", margin: 0, flex: 1 }}>{p.excerpt}</p>
          <div style={{ fontSize: 11, color: "rgba(239,239,239,0.4)" }}>{p.author} · <time dateTime={p.dateISO}>{p.date}</time></div>
        </div>
      </Link>
    </m.article>
  )
}

const chip = (active: boolean): React.CSSProperties => ({
  fontFamily: FONT_BODY, fontSize: 12, fontWeight: 600, letterSpacing: "0.04em", padding: "8px 16px", borderRadius: 999, cursor: "pointer",
  border: `1px solid ${active ? RED_INK : "rgba(239,239,239,0.18)"}`, color: active ? "#111" : "rgba(239,239,239,0.75)", background: active ? RED_INK : "transparent",
})
const srOnly: React.CSSProperties = { position: "absolute", width: 1, height: 1, padding: 0, margin: -1, overflow: "hidden", clip: "rect(0,0,0,0)", whiteSpace: "nowrap", border: 0 }

function PageBtn({ children, disabled, onClick }: { children: React.ReactNode; disabled?: boolean; onClick: () => void }) {
  return <button onClick={onClick} disabled={disabled} style={{ ...chip(false), opacity: disabled ? 0.4 : 1, cursor: disabled ? "not-allowed" : "pointer" }}>{children}</button>
}
function Msg({ text }: { text: string }) {
  return <p style={{ color: "rgba(239,239,239,0.6)", fontSize: 15, padding: "40px 0" }}>{text}</p>
}
function Skeletons() {
  return (
    <ul className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style={{ listStyle: "none", margin: 0, padding: 0, gap: "clamp(20px,2.5vw,32px)" }}>
      {Array.from({ length: 6 }).map((_, i) => (
        <li key={i} style={{ border: "1px solid rgba(239,239,239,0.08)", borderRadius: 12, overflow: "hidden" }}>
          <div className="stcms-skeleton" style={{ aspectRatio: "16/9" }} />
          <div style={{ padding: 24, display: "flex", flexDirection: "column", gap: 12 }}>
            <div className="stcms-skeleton" style={{ height: 12, width: "40%", borderRadius: 4 }} />
            <div className="stcms-skeleton" style={{ height: 20, width: "90%", borderRadius: 4 }} />
            <div className="stcms-skeleton" style={{ height: 40, width: "100%", borderRadius: 4 }} />
          </div>
        </li>
      ))}
    </ul>
  )
}
