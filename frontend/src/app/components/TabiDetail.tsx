import { TabiStroke } from "./TabiStroke"

/**
 * Assinatura 旅 discreta no canto da página — detalhe de marca, não elemento
 * de destaque. O traço se desenha ao entrar na viewport e fica em opacidade
 * baixa, para não competir com o conteúdo.
 *
 * Oculto no mobile: em telas pequenas não há margem sobrando para um
 * ornamento sem função.
 */
export function TabiDetail({
  corner = "bottom-right",
  size = "clamp(120px, 12vw, 180px)",
  opacity = 0.09,
  color = "#F20C25",
}: {
  corner?: "bottom-right" | "top-right" | "bottom-left"
  size?: string
  opacity?: number
  color?: string
}) {
  const pos: React.CSSProperties =
    corner === "top-right"
      ? { top: "12%", right: "4%" }
      : corner === "bottom-left"
        ? { bottom: "6%", left: "4%" }
        : { bottom: "6%", right: "4%" }

  return (
    <div
      aria-hidden="true"
      className="hidden md:block pointer-events-none"
      style={{ position: "absolute", width: size, opacity, zIndex: 0, ...pos }}
    >
      <TabiStroke width="100%" color={color} strokeWidth={1} duration={2.6} />
    </div>
  )
}
