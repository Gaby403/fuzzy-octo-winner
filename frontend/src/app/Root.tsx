import { useEffect, useMemo, useState } from "react";
import { Outlet, useLocation } from "react-router";
import { ContentContext, fetchContent, DEFAULT_CONTENT, SiteContent } from "./store/content";
import { UIProvider } from "./contexts/UIContext";
import { Header, HEADER_HEIGHT } from "./components/layout/Header";
import { Footer } from "./components/layout/Footer";
import { SmoothScroll } from "./components/system/SmoothScroll";
import { CustomCursor } from "./components/system/CustomCursor";
import { PageTransition } from "./components/system/PageTransition";
import { DeferUntilIdle } from "./components/system/DeferUntilIdle";
import { initAnalytics, trackEvent } from "./utils/analytics";
import { localeFromPath, switchLocalePath, HTML_LANG } from "./i18n/locale";
function upsertMeta(attr: "name" | "property", key: string, value: string) {
    if (!value)
        return;
    let el = document.head.querySelector<HTMLMetaElement>(`meta[${attr}="${key}"]`);
    if (!el) {
        el = document.createElement("meta");
        el.setAttribute(attr, key);
        document.head.appendChild(el);
    }
    el.setAttribute("content", value);
}
function upsertLink(rel: string, href: string) {
    if (!href)
        return;
    let el = document.head.querySelector<HTMLLinkElement>(`link[rel="${rel}"]`);
    if (!el) {
        el = document.createElement("link");
        el.rel = rel;
        document.head.appendChild(el);
    }
    el.href = href;
}
function upsertJsonLd(id: string, data: unknown) {
    let el = document.getElementById(id) as HTMLScriptElement | null;
    if (!el) {
        el = document.createElement("script");
        el.type = "application/ld+json";
        el.id = id;
        document.head.appendChild(el);
    }
    el.textContent = JSON.stringify(data);
}
export default function Root() {
    const [content, setContent] = useState<SiteContent>(DEFAULT_CONTENT);
    const [loading, setLoading] = useState(true);
    const location = useLocation();
    useEffect(() => {
        let alive = true;
        fetchContent().then(c => {
            if (!alive)
                return;
            setContent(c);
            setLoading(false);
        });
        return () => {
            alive = false;
        };
    }, []);
    useEffect(() => {
        const opts = {
            ga4Id: content.site.ga4Id,
            gtmId: content.site.gtmId,
            recaptchaSite: content.site.recaptchaSite,
        };
        if (!opts.ga4Id && !opts.gtmId && !opts.recaptchaSite)
            return;
        const start = () => initAnalytics(opts);
        const ric = (window as unknown as {
            requestIdleCallback?: (cb: () => void, o?: {
                timeout: number;
            }) => number;
        }).requestIdleCallback;
        const id = ric ? ric(start, { timeout: 4000 }) : window.setTimeout(start, 2500);
        return () => {
            const cic = (window as unknown as {
                cancelIdleCallback?: (h: number) => void;
            }).cancelIdleCallback;
            if (ric && cic)
                cic(id);
            else
                window.clearTimeout(id);
        };
    }, [content.site.ga4Id, content.site.gtmId, content.site.recaptchaSite]);
    useEffect(() => {
        trackEvent("page_view", {
            page_path: location.pathname + location.search,
            page_title: document.title,
        });
    }, [location.pathname, location.search]);
    useEffect(() => {
        if (content.site.title)
            document.title = content.site.title;
    }, [content.site.title]);
    useEffect(() => {
        const url = content.site.faviconUrl;
        if (!url)
            return;
        let link = document.querySelector<HTMLLinkElement>('link[rel="icon"]');
        if (!link) {
            link = document.createElement("link");
            link.rel = "icon";
            document.head.appendChild(link);
        }
        link.href = url;
    }, [content.site.faviconUrl]);
    useEffect(() => {
        const { title, metaDescription, logoUrl } = content.site;
        upsertMeta("name", "description", metaDescription);
        upsertMeta("property", "og:title", title);
        upsertMeta("property", "og:description", metaDescription);
        upsertMeta("property", "og:type", "website");
        if (logoUrl)
            upsertMeta("property", "og:image", logoUrl);
        upsertMeta("name", "twitter:card", logoUrl ? "summary_large_image" : "summary");
        upsertMeta("name", "twitter:title", title);
        upsertMeta("name", "twitter:description", metaDescription);
        if (logoUrl)
            upsertMeta("name", "twitter:image", logoUrl);
    }, [content.site]);
    useEffect(() => {
        if (typeof window === "undefined")
            return;
        const origin = window.location.origin;
        const url = origin + location.pathname;
        upsertLink("canonical", url);
        upsertMeta("property", "og:url", url);

        const locale = localeFromPath(location.pathname);
        document.documentElement.lang = HTML_LANG[locale];
        upsertMeta("property", "og:locale", locale === "pt" ? "pt_BR" : "en_US");

        document.head.querySelectorAll('link[rel="alternate"][hreflang]').forEach(el => el.remove());
        const pt = origin + switchLocalePath(location.pathname, "pt");
        const en = origin + switchLocalePath(location.pathname, "en");
        for (const [lang, href] of [["pt-BR", pt], ["en", en], ["x-default", pt]] as const) {
            const el = document.createElement("link");
            el.rel = "alternate";
            el.hreflang = lang;
            el.href = href;
            document.head.appendChild(el);
        }
    }, [location.pathname]);
    useEffect(() => {
        if (typeof window === "undefined")
            return;
        const origin = window.location.origin;
        const { site, footer, services } = content;
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
        });
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
        });
    }, [content]);
    useEffect(() => {
        const existing = document.getElementById("ld-faq");
        if (location.pathname !== "/" || !content.faq?.length) {
            if (existing)
                existing.remove();
            return;
        }
        upsertJsonLd("ld-faq", {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            mainEntity: content.faq.map(f => ({
                "@type": "Question",
                name: f.q,
                acceptedAnswer: { "@type": "Answer", text: f.a },
            })),
        });
    }, [content.faq, location.pathname]);
    const ctx = useMemo(() => ({ content, loading }), [content, loading]);
    const isHome = location.pathname === "/";
    return (<ContentContext.Provider value={ctx}>
      <UIProvider>
        
        <DeferUntilIdle>
          <SmoothScroll />
          <CustomCursor />
          <PageTransition />
        </DeferUntilIdle>
        <a href="#conteudo" className="skip-link">Pular para o conteúdo</a>
        <Header />
        
        {!isHome && <div aria-hidden="true" style={{ height: HEADER_HEIGHT }}/>}
        <Outlet />
        <Footer />
      </UIProvider>
    </ContentContext.Provider>);
}
