import { useEffect } from "react"
import { useLocation } from "react-router"
import Lenis from "lenis"

/**
 * Instância única do Lenis, acessível pelos helpers de rolagem abaixo. Fica
 * em nível de módulo para que âncoras (useGoTo) usem a mesma rolagem suave.
 */
let lenis: Lenis | null = null

const prefersReduced = () =>
  typeof window !== "undefined" && window.matchMedia("(prefers-reduced-motion: reduce)").matches

/** Rola suavemente até o topo (imediato na troca de rota). */
export function scrollToTop(immediate = false) {
  if (lenis) lenis.scrollTo(0, { immediate })
  else window.scrollTo({ top: 0, behavior: immediate ? "auto" : "smooth" })
}

/** Rola suavemente até um seletor/elemento, com folga para o header fixo. */
export function scrollToEl(target: string | HTMLElement) {
  if (lenis) {
    lenis.scrollTo(target as unknown as string, { offset: -80 })
    return
  }
  const el = typeof target === "string" ? document.querySelector(target) : target
  if (el instanceof HTMLElement) el.scrollIntoView({ behavior: "smooth" })
}

/**
 * Ativa o smooth scroll (Lenis) em todo o site. Não renderiza nada.
 * É desligado automaticamente quando o usuário pede menos movimento
 * (prefers-reduced-motion), preservando a acessibilidade.
 */
export function SmoothScroll() {
  const { pathname } = useLocation()

  useEffect(() => {
    if (prefersReduced()) return
    const instance = new Lenis({
      duration: 1.1,
      easing: (t: number) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      smoothWheel: true,
    })
    lenis = instance

    let raf = 0
    const loop = (time: number) => {
      instance.raf(time)
      raf = requestAnimationFrame(loop)
    }
    raf = requestAnimationFrame(loop)

    return () => {
      cancelAnimationFrame(raf)
      instance.destroy()
      lenis = null
    }
  }, [])

  // Volta ao topo (sem animar a página inteira) a cada troca de rota.
  useEffect(() => {
    scrollToTop(true)
  }, [pathname])

  return null
}
