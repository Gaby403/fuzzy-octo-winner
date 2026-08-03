type Instancia = {
    scrollTo: (alvo: unknown, opcoes?: { immediate?: boolean; offset?: number }) => void;
} | null;

let lenis: Instancia = null;

export function registrarLenis(instancia: Instancia) {
    lenis = instancia;
}

export function scrollToTop(immediate = false) {
    if (lenis) {
        lenis.scrollTo(0, { immediate });
        return;
    }
    window.scrollTo({ top: 0, behavior: immediate ? "auto" : "smooth" });
}

export function scrollToEl(target: string | HTMLElement) {
    if (lenis) {
        lenis.scrollTo(target, { offset: -80 });
        return;
    }
    const el = typeof target === "string" ? document.querySelector(target) : target;
    if (el instanceof HTMLElement)
        el.scrollIntoView({ behavior: "smooth" });
}
