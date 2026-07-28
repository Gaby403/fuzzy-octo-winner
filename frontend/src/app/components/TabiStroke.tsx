import { m, useInView, type MotionValue } from "motion/react"
import { useRef } from "react"
import { TABI_PATH } from "./TabiMark"

/**
 * Assinatura 旅 desenhada a traço.
 *
 * O mesmo caminho da marca (TabiMark), mas contornado em vez de preenchido:
 * animar `pathLength` faz o kanji "se desenhar". Tabi significa jornada — o
 * traço se completa conforme o visitante avança, virando o fio condutor do
 * site em vez de um enfeite parado.
 *
 * Dois modos:
 *  - `progress`: o traço acompanha um MotionValue (0→1), normalmente de scroll;
 *  - sem `progress`: desenha uma vez ao entrar na viewport.
 */
export function TabiStroke({
  width = 200,
  color = "#F20C25",
  strokeWidth = 1.5,
  progress,
  duration = 2.4,
  opacity = 1,
  style,
}: {
  width?: number | string
  color?: string
  strokeWidth?: number
  /** Liga o traço ao scroll (0 = vazio, 1 = completo). */
  progress?: MotionValue<number>
  duration?: number
  opacity?: number
  style?: React.CSSProperties
}) {
  const ref = useRef<SVGSVGElement>(null)
  const inView = useInView(ref, { once: true, margin: "-15%" })
  const scrollDriven = !!progress

  return (
    <svg
      ref={ref}
      width={width}
      viewBox="0 0 224 220"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      aria-hidden="true"
      style={{ display: "block", flexShrink: 0, opacity, overflow: "visible", ...style }}
    >
      <m.path
        d={TABI_PATH}
        fill="none"
        stroke={color}
        strokeWidth={strokeWidth}
        strokeLinecap="round"
        strokeLinejoin="round"
        style={scrollDriven ? { pathLength: progress } : undefined}
        initial={scrollDriven ? undefined : { pathLength: 0, opacity: 0 }}
        animate={scrollDriven ? undefined : inView ? { pathLength: 1, opacity: 1 } : undefined}
        transition={scrollDriven ? undefined : { pathLength: { duration, ease: [0.16, 1, 0.3, 1] }, opacity: { duration: 0.3 } }}
      />
    </svg>
  )
}
