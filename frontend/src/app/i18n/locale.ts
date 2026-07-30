export type Locale = "pt" | "en";

export const LOCALES: Locale[] = ["pt", "en"];
export const DEFAULT_LOCALE: Locale = "pt";
export const HTML_LANG: Record<Locale, string> = { pt: "pt-BR", en: "en" };

export type RouteKey =
    | "home" | "projetos" | "sobre" | "servicos" | "servico"
    | "processo" | "blog" | "artigo" | "contato" | "obrigado" | "pagina";

export const SLUGS: Record<RouteKey, Record<Locale, string>> = {
    home: { pt: "", en: "" },
    projetos: { pt: "projetos", en: "work" },
    sobre: { pt: "sobre", en: "about" },
    servicos: { pt: "servicos", en: "services" },
    servico: { pt: "servicos", en: "services" },
    processo: { pt: "processo", en: "process" },
    blog: { pt: "blog", en: "blog" },
    artigo: { pt: "blog", en: "blog" },
    contato: { pt: "contato", en: "contact" },
    obrigado: { pt: "obrigado", en: "thank-you" },
    pagina: { pt: "p", en: "p" },
};

export function localeFromPath(pathname: string): Locale {
    return /^\/en(\/|$)/.test(pathname) ? "en" : "pt";
}

export function stripLocale(pathname: string): string {
    const sem = pathname.replace(/^\/en(?=\/|$)/, "");
    return sem === "" ? "/" : sem;
}

export function path(locale: Locale, key: RouteKey, param?: string): string {
    const base = SLUGS[key][locale];
    const prefixo = locale === "pt" ? "" : "/en";
    const partes = [base, param].filter(Boolean).join("/");
    return partes ? `${prefixo}/${partes}` : prefixo || "/";
}

export function switchLocalePath(pathname: string, alvo: Locale): string {
    const atual = localeFromPath(pathname);
    if (atual === alvo) return pathname;
    const resto = stripLocale(pathname).replace(/^\//, "");
    if (!resto) return alvo === "pt" ? "/" : "/en";

    const [primeiro, ...cauda] = resto.split("/");
    const entrada = (Object.entries(SLUGS) as [RouteKey, Record<Locale, string>][])
        .find(([, s]) => s[atual] === primeiro);
    const traduzido = entrada ? entrada[1][alvo] : primeiro;
    const caminho = [traduzido, ...cauda].filter(Boolean).join("/");
    return alvo === "pt" ? `/${caminho}` : `/en/${caminho}`;
}
