/**
 * Design System — tokens centrais do Studio Tabi.
 * Fonte única de verdade para cores, tipografia, espaçamento, raios e sombras.
 * Componentes novos devem consumir estes tokens (evita valores mágicos espalhados).
 */

export const colors = {
  // Marca — grafismos e textos grandes.
  red: "#F20C25",
  // Botões preenchidos (fundo) com texto branco — contraste AA.
  redBtn: "#DA0A20",
  // Texto/links vermelhos pequenos em fundo escuro — contraste AA.
  redInk: "#FF3547",
  white: "#EFEFEF",
  pureWhite: "#FFFFFF",
  black: "#111111",
  bgDeep: "#0D0D0D",
  // Tons de texto sobre fundo escuro.
  text: "rgba(239,239,239,0.85)",
  textMuted: "rgba(239,239,239,0.6)",
  textFaint: "rgba(239,239,239,0.4)",
  // Bordas e superfícies.
  border: "rgba(239,239,239,0.1)",
  borderStrong: "rgba(239,239,239,0.18)",
  surface: "rgba(239,239,239,0.04)",
  ok: "#3DBF72",
  error: "#FF6B6B",
} as const

export const fonts = {
  head: '"Roboto Condensed", sans-serif',
  body: '"Be Vietnam Pro", sans-serif',
} as const

/** Escala de espaçamento (px) — base 4. */
export const space = {
  xs: 4, sm: 8, md: 16, lg: 24, xl: 40, xxl: 64, xxxl: 96,
} as const

export const radii = { sm: 8, md: 12, lg: 16, pill: 999 } as const

export const ease = {
  out: [0.16, 1, 0.3, 1] as [number, number, number, number],
}

/** Padding lateral responsivo padrão das seções. */
export const PAGE_PAD = "clamp(20px, 4vw, 82px)"

/** Easing como string CSS para transições. */
export const EASE_CSS = "cubic-bezier(0.16, 1, 0.3, 1)"
