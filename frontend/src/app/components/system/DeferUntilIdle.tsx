import { useEffect, useState } from "react"

/**
 * Monta os filhos só depois que o navegador fica ocioso.
 *
 * Smooth scroll, cursor e transição de página são enfeites: se forem
 * inicializados junto com a primeira renderização, competem com ela pela
 * thread principal e inflam o TBT — justamente a métrica que mais pesa na
 * nota de performance no celular.
 */
export function DeferUntilIdle({ children, timeout = 2000 }: { children: React.ReactNode; timeout?: number }) {
  const [pronto, setPronto] = useState(false)

  useEffect(() => {
    let cancelado = false
    const liberar = () => { if (!cancelado) setPronto(true) }

    const ric = (window as unknown as {
      requestIdleCallback?: (cb: () => void, o?: { timeout: number }) => number
    }).requestIdleCallback

    // Espera a primeira pintura antes até de agendar o idle.
    const raf = requestAnimationFrame(() => {
      if (ric) ric(liberar, { timeout })
      else setTimeout(liberar, 200)
    })

    return () => { cancelado = true; cancelAnimationFrame(raf) }
  }, [timeout])

  return pronto ? <>{children}</> : null
}
