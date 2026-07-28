import { useEffect, useState } from "react"
import { Outlet, useLocation } from "react-router"
import { ContentContext, fetchContent, DEFAULT_CONTENT, SiteContent } from "./store/content"
import { UIProvider } from "./contexts/UIContext"
import { Header, HEADER_HEIGHT } from "./components/layout/Header"

/** Cria/atualiza uma <meta> no <head> pelo atributo-chave (name ou property). */
function upsertMeta(attr: "name" | "property", key: string, value: string) {
  if (!value) return
  let el = document.head.querySelector<HTMLMetaElement>(`meta[${attr}="${key}"]`)
  if (!el) {
    el = document.createElement("meta")
    el.setAttribute(attr, key)
    document.head.appendChild(el)
  }
  el.setAttribute("content", value)
}

/** Cria/atualiza um <link rel> único no <head>. */
function upsertLink(rel: string, href: string) {
  if (!href) return
  let el = document.head.querySelector<HTMLLinkElement>(`link[rel="${rel}"]`)
  if (!el) {
    el = document.createElement("link")
    el.rel = rel
    document.head.appendChild(el)
  }
  el.href = href
}

/** Injeta/atualiza um bloco JSON-LD (dados estruturados) para SEO e agentes. */
function upsertJsonLd(id: string, data: unknown) {
  let el = document.getElementById(id) as HTMLScriptElement | null
  if (!el) {
    el = document.createElement("script")
    el.type = "application/ld+json"
    el.id = id
    document.head.appendChild(el)
  }
  el.textContent = JSON.stringify(data)
}

/**
 * Loads the site content from WordPress once, then provides it to the whole
 * app through context. Also wires the WordPress-managed favicon and site
 * title into the document head so both are editable from the CMS.
 */
export default function Root() {
  const [content, setContent] = useState<SiteContent>(DEFAULT_CONTENT)
  const [loading, setLoading] = useState(true)
  const location = useLocation()

  useEffect(() => {
    let alive = true
    fetchContent().then(c => {
      if (!alive) return
      setContent(c)
      setLoading(false)
    })
    return () => {
      alive = false
    }
  }, [])

  // Document title from the CMS.
  useEffect(() => {
    if (content.site.title) document.title = content.site.title
  }, [content.site.title])

  // Favicon managed from the CMS.
  useEffect(() => {
    const url = content.site.faviconUrl
    if (!url) return
    let link = document.querySelector<HTMLLinkElement>('link[rel="icon"]')
    if (!link) {
      link = document.createElement("link")
      link.rel = "icon"
      document.head.appendChild(link)
    }
    link.href = url
  }, [content.site.faviconUrl])

  // Meta tags (SEO + compartilhamento) geridas pelo CMS.
  useEffect(() => {
    const { title, metaDescription, logoUrl } = content.site
    upsertMeta("name", "description", metaDescription)
    // Open Graph
    upsertMeta("property", "og:title", title)
    upsertMeta("property", "og:description", metaDescription)
    upsertMeta("property", "og:type", "website")
    if (logoUrl) upsertMeta("property", "og:image", logoUrl)
    // Twitter
    upsertMeta("name", "twitter:card", logoUrl ? "summary_large_image" : "summary")
    upsertMeta("name", "twitter:title", title)
    upsertMeta("name", "twitter:description", metaDescription)
    if (logoUrl) upsertMeta("name", "twitter:image", logoUrl)
  }, [content.site])

  // URL canônica + og:url por rota (bom para SEO e para agentes).
  useEffect(() => {
    if (typeof window === "undefined") return
    const url = window.location.origin + location.pathname
    upsertLink("canonical", url)
    upsertMeta("property", "og:url", url)
  }, [location.pathname])

  // Dados estruturados (JSON-LD): Organization + WebSite. Ajuda SEO e a
  // "navegação agêntica" — IAs entendem quem é a marca, contato e serviços.
  useEffect(() => {
    if (typeof window === "undefined") return
    const origin = window.location.origin
    const { site, footer, services } = content
    upsertJsonLd("ld-org", {
      "@context": "https://schema.org",
      "@type": "Organization",
      name: site.title,
      url: origin,
      description: site.metaDescription,
      ...(site.logoUrl ? { logo: site.logoUrl } : {}),
      ...(footer.email ? { email: footer.email } : {}),
      ...(footer.phone ? { telephone: footer.phone } : {}),
      ...(footer.city ? { address: { "@type": "PostalAddress", addressLocality: footer.city } } : {}),
      sameAs: (footer.social || []).map(s => s.url).filter(u => u && u.startsWith("http")),
      makesOffer: (services || []).map(s => ({
        "@type": "Offer",
        itemOffered: { "@type": "Service", name: s.title, description: s.body, ...(s.slug ? { url: `${origin}/servicos/${s.slug}` } : {}) },
      })),
    })
    upsertJsonLd("ld-website", {
      "@context": "https://schema.org",
      "@type": "WebSite",
      name: site.title,
      url: origin,
      inLanguage: "pt-BR",
      potentialAction: {
        "@type": "SearchAction",
        target: `${origin}/blog?q={search_term_string}`,
        "query-input": "required name=search_term_string",
      },
    })
  }, [content])

  // FAQPage schema — apenas na home (onde a seção de FAQ é exibida).
  useEffect(() => {
    const existing = document.getElementById("ld-faq")
    if (location.pathname !== "/" || !content.faq?.length) {
      if (existing) existing.remove()
      return
    }
    upsertJsonLd("ld-faq", {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      mainEntity: content.faq.map(f => ({
        "@type": "Question",
        name: f.q,
        acceptedAnswer: { "@type": "Answer", text: f.a },
      })),
    })
  }, [content.faq, location.pathname])

  const isHome = location.pathname === "/"

  return (
    <ContentContext.Provider value={{ content, loading }}>
      <UIProvider>
        <a href="#conteudo" className="skip-link">Pular para o conteúdo</a>
        <Header />
        {/* Espaçador nas páginas internas para o conteúdo não ficar sob o header fixo.
            Na home, o header sobrepõe a Hero (fundo claro no topo). */}
        {!isHome && <div aria-hidden="true" style={{ height: HEADER_HEIGHT }} />}
        <Outlet />
      </UIProvider>
    </ContentContext.Provider>
  )
}
