import { useLocation } from "react-router";
import { localeFromPath, path, switchLocalePath, type Locale, type RouteKey } from "./locale";
import { traduzir, type ChaveTexto } from "./dicionario";

export function useLocale() {
    const { pathname } = useLocation();
    const locale = localeFromPath(pathname) as Locale;

    return {
        locale,
        t: (chave: ChaveTexto) => traduzir(locale, chave),
        rota: (chave: RouteKey, param?: string) => path(locale, chave, param),
        alternar: (alvo: Locale) => switchLocalePath(pathname, alvo),
        pathname,
    };
}
