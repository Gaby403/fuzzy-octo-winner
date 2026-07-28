import { useEffect } from "react";
import { useLocation } from "react-router";
import Lenis from "lenis";
let lenis: Lenis | null = null;
const prefersReduced = () => typeof window !== "undefined" && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
export function scrollToTop(immediate = false) {
    if (lenis)
        lenis.scrollTo(0, { immediate });
    else
        window.scrollTo({ top: 0, behavior: immediate ? "auto" : "smooth" });
}
export function scrollToEl(target: string | HTMLElement) {
    if (lenis) {
        lenis.scrollTo(target as unknown as string, { offset: -80 });
        return;
    }
    const el = typeof target === "string" ? document.querySelector(target) : target;
    if (el instanceof HTMLElement)
        el.scrollIntoView({ behavior: "smooth" });
}
export function SmoothScroll() {
    const { pathname } = useLocation();
    useEffect(() => {
        if (prefersReduced())
            return;
        const instance = new Lenis({
            duration: 1.1,
            easing: (t: number) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            smoothWheel: true,
        });
        lenis = instance;
        let raf = 0;
        const loop = (time: number) => {
            instance.raf(time);
            raf = requestAnimationFrame(loop);
        };
        raf = requestAnimationFrame(loop);
        return () => {
            cancelAnimationFrame(raf);
            instance.destroy();
            lenis = null;
        };
    }, []);
    useEffect(() => {
        scrollToTop(true);
    }, [pathname]);
    return null;
}
