import { useEffect, useRef, useState } from "react"
import { useLocation } from "react-router"
import { m, AnimatePresence } from "motion/react"
import { TabiMark } from "../TabiMark"

const RED = "#F20C25"
const BLACK = "#0B0B0B"
const EASE: [number, number, number, number] = [0.76, 0, 0.24, 1]

/**
 * Transição entre rotas: um painel escuro sobe cobrindo a tela, o 旅 pulsa
 * no centro e o painel sai por cima, revelando a página nova. Dá a sensação
 * de aplicativo — e reforça a marca a cada navegação.
 *
 * Só a troca de rota dispara (não o primeiro carregamento) e nada é
 * bloqueado: o overlay é pointer-events:none e some sozinho. Em
 * prefers-reduced-motion a transição não roda.
 */
export function PageTransition() {
  const { pathname } = useLocation()
  const [covering, setCovering] = useState(false)
  const first = useRef(true)
  const timer = useRef<number | undefined>(undefined)

  useEffect(() => {
    // Não anima na primeira renderização (entrada direta na URL).
    if (first.current) {
      first.current = false
      return
    }
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return

    setCovering(true)
    window.clearTimeout(timer.current)
    timer.current = window.setTimeout(() => setCovering(false), 620)
    return () => window.clearTimeout(timer.current)
  }, [pathname])

  return (
    <AnimatePresence>
      {covering && (
        <m.div
          key="page-transition"
          aria-hidden="true"
          style={{
            position: "fixed", inset: 0, zIndex: 300, pointerEvents: "none",
            background: BLACK, display: "flex", alignItems: "center", justifyContent: "center",
          }}
          initial={{ clipPath: "inset(100% 0% 0% 0%)" }}
          animate={{ clipPath: "inset(0% 0% 0% 0%)" }}
          exit={{ clipPath: "inset(0% 0% 100% 0%)" }}
          transition={{ duration: 0.55, ease: EASE }}
        >
          <m.div
            initial={{ opacity: 0, scale: 0.86 }}
            animate={{ opacity: 1, scale: 1 }}
            exit={{ opacity: 0, scale: 1.08 }}
            transition={{ duration: 0.4, ease: [0.16, 1, 0.3, 1] }}
            style={{ width: "clamp(56px, 7vw, 96px)" }}
          >
            <TabiMark width="100%" color={RED} />
          </m.div>
        </m.div>
      )}
    </AnimatePresence>
  )
}
