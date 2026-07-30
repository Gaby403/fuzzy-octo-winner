import { useEffect, useRef, useState } from "react";
import { Link, useLocation, useNavigate } from "react-router";
import { m, AnimatePresence } from "motion/react";
import { useContent } from "../../store/content";
import { useUI } from "../../contexts/UIContext";
import { useLocale } from "../../i18n/useLocale";
import { LOCALES, stripLocale } from "../../i18n/locale";
const RED = "#F20C25";
const RED_BTN = "#DA0A20";
const RED_INK = "#FF3547";
const WHITE = "#EFEFEF";
const PURE_WHITE = "#FFFFFF";
const BLACK = "#111111";
const FONT_HEAD = '"Roboto Condensed", sans-serif';
const FONT_BODY = '"Be Vietnam Pro", sans-serif';
export const HEADER_HEIGHT = 68;
function isActive(url: string, pathname: string): boolean {
    if (!url || url.startsWith("#") || /^https?:\/\//.test(url))
        return false;
    const clean = url.split("#")[0].split("?")[0];
    if (clean === "/")
        return pathname === "/";
    return pathname === clean || pathname.startsWith(clean + "/");
}
export function Header() {
    const { content } = useContent();
    const { menuOpen, toggleMenu, closeMenu } = useUI();
    const { locale, t, alternar, rota } = useLocale();
    const location = useLocation();
    const navigate = useNavigate();
    const isHome = stripLocale(location.pathname) === "/";
    const [scrolled, setScrolled] = useState(false);
    const solid = !isHome || scrolled;
    const iconColor = solid ? WHITE : BLACK;
    const hamburgerRef = useRef<HTMLButtonElement>(null);
    const panelRef = useRef<HTMLDivElement>(null);
    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 40);
        onScroll();
        window.addEventListener("scroll", onScroll, { passive: true });
        return () => window.removeEventListener("scroll", onScroll);
    }, []);
    useEffect(() => { closeMenu(); }, [location.pathname, closeMenu]);
    useEffect(() => {
        if (!menuOpen)
            return;
        const prev = document.body.style.overflow;
        document.body.style.overflow = "hidden";
        const onKey = (e: KeyboardEvent) => { if (e.key === "Escape")
            closeMenu(); };
        document.addEventListener("keydown", onKey);
        const first = panelRef.current?.querySelector<HTMLElement>("a, button");
        first?.focus();
        return () => {
            document.body.style.overflow = prev;
            document.removeEventListener("keydown", onKey);
            hamburgerRef.current?.focus();
        };
    }, [menuOpen, closeMenu]);
    const go = (url: string, e: React.MouseEvent) => {
        if (!url)
            return;
        if (/^https?:\/\//i.test(url) || url.startsWith("mailto:") || url.startsWith("tel:"))
            return;
        e.preventDefault();
        closeMenu();
        const prefixo = locale === "en" ? "/en" : "";
        if (url.startsWith("#")) {
            if (stripLocale(location.pathname) !== "/") {
                navigate(`${prefixo}/${url}`);
                return;
            }
            const el = document.querySelector(url);
            if (el)
                el.scrollIntoView({ behavior: "smooth" });
        }
        else {
            navigate(url.startsWith("/") ? `${prefixo}${url}` : url);
        }
    };
    const brand = (<Link to={rota("home")} aria-label={`${content.site.title} — ${t("menu.home")}`} style={{ display: "inline-flex", alignItems: "center", color: iconColor, textDecoration: "none", pointerEvents: "auto" }}>
      {content.site.logoUrl ? (<img src={content.site.logoUrl} alt={content.nav.brand || content.site.title} fetchPriority="high" decoding="async" style={{ height: "clamp(24px, 3vw, 40px)", width: "auto", display: "block" }}/>) : (<span style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(14px, 1.3vw, 20px)", letterSpacing: "-0.04em", textTransform: "uppercase" }}>{content.nav.brand}</span>)}
    </Link>);
    return (<>
      <m.header aria-label={t("nav.cabecalho")} style={{
            position: "fixed", top: 0, left: 0, width: "100%", zIndex: 50,
            display: "flex", alignItems: "center", justifyContent: "space-between",
            padding: "clamp(12px, 1.6vw, 18px) clamp(20px, 4vw, 82px)", minHeight: HEADER_HEIGHT,
            color: iconColor, borderBottom: "1px solid transparent",
        }} initial={false} animate={{
            backgroundColor: solid ? "rgba(17,17,17,0.82)" : "rgba(17,17,17,0)",
            backdropFilter: solid ? "blur(12px)" : "blur(0px)",
            borderBottomColor: solid ? "rgba(239,239,239,0.08)" : "rgba(239,239,239,0)",
        }} transition={{ duration: 0.35, ease: [0.16, 1, 0.3, 1] }}>
        {brand}
        <div className="flex items-center" style={{ gap: 14 }}>
        <nav aria-label={t("nav.idioma")} className="flex items-center" style={{ gap: 6, fontFamily: FONT_BODY, fontSize: 10, fontWeight: 700, letterSpacing: "0.12em" }}>
          {LOCALES.map((l, i) => (<span key={l} className="flex items-center" style={{ gap: 6 }}>
              {i > 0 && <span aria-hidden="true" style={{ opacity: 0.3 }}>/</span>}
              <a href={alternar(l)} hrefLang={l} aria-current={locale === l ? "true" : undefined} style={{ color: locale === l ? RED_INK : iconColor, opacity: locale === l ? 1 : 0.55, textDecoration: "none", textTransform: "uppercase" }}>{l}</a>
            </span>))}
        </nav>
        <button ref={hamburgerRef} type="button" onClick={toggleMenu} aria-label={menuOpen ? t("nav.fechar") : t("nav.abrir")} aria-expanded={menuOpen} aria-controls="menu-drawer" className="flex flex-col justify-center items-end gap-[5px] cursor-pointer bg-transparent border-none p-2 -mr-2" style={{ color: iconColor }}>
          <m.span className="block h-px bg-current" animate={{ width: 22, rotate: menuOpen ? 45 : 0, y: menuOpen ? 6 : 0 }} transition={{ duration: 0.28, ease: [0.16, 1, 0.3, 1] }}/>
          <m.span className="block h-px bg-current" animate={{ width: menuOpen ? 22 : 14, rotate: menuOpen ? -45 : 0, y: menuOpen ? -1 : 0 }} transition={{ duration: 0.28, ease: [0.16, 1, 0.3, 1] }}/>
        </button>
        </div>
      </m.header>

      
      <AnimatePresence>
        {menuOpen && (<m.div className="fixed inset-0" style={{ zIndex: 55 }} initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} transition={{ duration: 0.2 }}>
            <div aria-hidden="true" onClick={closeMenu} style={{ position: "absolute", inset: 0, background: "rgba(0,0,0,0.55)", backdropFilter: "blur(4px)" }}/>
            <m.div id="menu-drawer" ref={panelRef} role="dialog" aria-modal="true" aria-label={t("nav.menuNav")} className="absolute top-0 right-0 h-full flex flex-col" style={{ width: "min(400px, 88vw)", background: BLACK, paddingTop: "clamp(72px, 12svh, 104px)", paddingBottom: 40, paddingLeft: 32, paddingRight: 32 }} initial={{ x: "100%" }} animate={{ x: "0%" }} exit={{ x: "100%" }} transition={{ duration: 0.38, ease: [0.16, 1, 0.3, 1] }}>
              <button type="button" onClick={closeMenu} aria-label={t("nav.fechar")} style={{ position: "absolute", top: 24, right: 28, background: "transparent", border: "none", color: WHITE, fontSize: 26, lineHeight: 1, cursor: "pointer" }}>×</button>

              <nav aria-label={t("nav.menu")} className="flex flex-col flex-1">
                {content.nav.links.map((item, i) => {
                const active = isActive(item.url, location.pathname);
                return (<m.a key={item.label + i} href={item.url || "#"} onClick={(e) => go(item.url, e)} aria-current={active ? "page" : undefined} className="group flex items-center gap-3 py-4 border-b" style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: "clamp(22px, 6vw, 32px)", letterSpacing: "-0.04em", textTransform: "uppercase", color: active ? RED_INK : WHITE, textDecoration: "none", borderColor: "rgba(239,239,239,0.08)" }} initial={{ x: 24, opacity: 0 }} animate={{ x: 0, opacity: 1 }} transition={{ duration: 0.4, delay: 0.1 + i * 0.05, ease: [0.16, 1, 0.3, 1] }} whileHover={{ x: 6 } as any}>
                      <span style={{ fontSize: 9, color: active ? RED_INK : RED, fontFamily: FONT_BODY, fontWeight: 600, letterSpacing: "0.1em" }}>0{i + 1}</span>
                      {item.label}
                    </m.a>);
            })}
              </nav>

              <div style={{ marginTop: 24 }}>
                {content.nav.ctaLabel && (<a href={content.nav.ctaUrl || "/contato"} onClick={(e) => go(content.nav.ctaUrl || "/contato", e)} data-cursor style={{ display: "inline-flex", alignItems: "center", gap: 10, padding: "13px 24px", borderRadius: 999, background: RED_BTN, color: PURE_WHITE, fontFamily: FONT_BODY, fontSize: 11, fontWeight: 700, letterSpacing: "0.14em", textTransform: "uppercase", textDecoration: "none" }}>
                    {content.nav.ctaLabel} <span aria-hidden="true" style={{ fontSize: 14 }}>→</span>
                  </a>)}
                {content.footer.email && (<a href={`mailto:${content.footer.email}`} style={{ display: "block", marginTop: 16, fontSize: 14, color: RED_INK, fontFamily: FONT_BODY, fontWeight: 600, textDecoration: "none" }}>{content.footer.email}</a>)}
              </div>
            </m.div>
          </m.div>)}
      </AnimatePresence>
    </>);
}
