import { useEffect, useState } from "react";
import { Link } from "react-router";
import { useLocale } from "../../i18n/useLocale";
import { LOCALES, type Locale } from "../../i18n/locale";

const CHAVE = "stcms-lang-notice";

function idiomaPreferido(): Locale | null {
    if (typeof navigator === "undefined")
        return null;
    const lista = navigator.languages && navigator.languages.length ? navigator.languages : [navigator.language];
    for (const tag of lista) {
        const base = String(tag || "").toLowerCase().split("-")[0];
        if ((LOCALES as string[]).includes(base))
            return base as Locale;
    }
    return null;
}

export function LanguageNotice() {
    const { locale, t, alternar } = useLocale();
    const [visivel, setVisivel] = useState(false);
    const outro: Locale = locale === "pt" ? "en" : "pt";

    useEffect(() => {
        if (typeof window === "undefined")
            return;
        try {
            if (window.localStorage.getItem(CHAVE))
                return;
        }
        catch {
            return;
        }
        setVisivel(idiomaPreferido() === outro);
    }, [outro]);

    const dispensar = () => {
        setVisivel(false);
        try {
            window.localStorage.setItem(CHAVE, "1");
        }
        catch {
        }
    };

    if (!visivel)
        return null;

    const rotulo = outro === "en" ? "This site is also available in English." : "Este site também está disponível em português.";
    const acao = outro === "en" ? "View in English" : "Ver em português";

    return (<div data-no-prerender role="region" aria-label={t("nav.idioma")} style={{
            position: "fixed",
            left: "50%",
            bottom: "clamp(16px, 3vw, 28px)",
            transform: "translateX(-50%)",
            zIndex: 70,
            display: "flex",
            alignItems: "center",
            gap: 14,
            maxWidth: "min(560px, calc(100vw - 32px))",
            padding: "12px 14px 12px 18px",
            borderRadius: 999,
            border: "1px solid rgba(239,239,239,0.16)",
            background: "rgba(17,17,17,0.92)",
            backdropFilter: "blur(10px)",
            fontFamily: '"Be Vietnam Pro", sans-serif',
            boxShadow: "0 10px 40px rgba(0,0,0,0.45)",
        }}>
      <span lang={outro} style={{ fontSize: 13, lineHeight: 1.4, color: "rgba(239,239,239,0.78)" }}>{rotulo}</span>
      <Link to={alternar(outro)} hrefLang={outro} onClick={dispensar} style={{
            flexShrink: 0,
            padding: "8px 16px",
            borderRadius: 999,
            background: "#DA0A20",
            color: "#FFFFFF",
            textDecoration: "none",
            fontSize: 10,
            fontWeight: 700,
            letterSpacing: "0.12em",
            textTransform: "uppercase",
            whiteSpace: "nowrap",
        }}>{acao}</Link>
      <button type="button" onClick={dispensar} aria-label={t("idioma.dispensar")} style={{
            flexShrink: 0,
            width: 26,
            height: 26,
            borderRadius: "50%",
            border: "none",
            background: "transparent",
            color: "rgba(239,239,239,0.5)",
            fontSize: 16,
            lineHeight: 1,
            cursor: "pointer",
        }}>×</button>
    </div>);
}
