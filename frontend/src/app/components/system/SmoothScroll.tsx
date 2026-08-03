import { useEffect } from "react";
import { useLocation } from "react-router";
import Lenis from "lenis";
import { registrarLenis, scrollToTop } from "./rolagem";

const prefersReduced = () => typeof window !== "undefined" && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

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
        registrarLenis(instance as never);
        let raf = 0;
        const loop = (time: number) => {
            instance.raf(time);
            raf = requestAnimationFrame(loop);
        };
        raf = requestAnimationFrame(loop);
        return () => {
            cancelAnimationFrame(raf);
            instance.destroy();
            registrarLenis(null);
        };
    }, []);
    useEffect(() => {
        scrollToTop(true);
    }, [pathname]);
    return null;
}
