import { Link } from "react-router"
import { colors, fonts, radii } from "../../constants/theme"

type Variant = "primary" | "outline" | "text"

interface BaseProps {
  variant?: Variant
  children: React.ReactNode
  arrow?: boolean
  className?: string
  style?: React.CSSProperties
}

function styleFor(variant: Variant): React.CSSProperties {
  const base: React.CSSProperties = {
    display: "inline-flex", alignItems: "center", justifyContent: "center", gap: 10,
    fontFamily: fonts.body, fontSize: "10px", fontWeight: 700, letterSpacing: "0.14em",
    textTransform: "uppercase", textDecoration: "none", cursor: "pointer", borderRadius: radii.pill,
    transition: "background-color .22s, color .22s, border-color .22s, gap .22s",
  }
  if (variant === "primary") return { ...base, padding: "14px 28px", border: "none", background: colors.redBtn, color: colors.pureWhite }
  if (variant === "outline") return { ...base, padding: "13px 26px", border: `1px solid ${colors.redInk}`, background: "transparent", color: colors.redInk }
  return { ...base, padding: 0, border: "none", background: "transparent", color: colors.redInk }
}

/** Botão/Link padronizado do Design System (acessível, contraste AA). */
export function Button({ variant = "primary", children, arrow, to, href, onClick, type, disabled, ariaLabel, className, style }: BaseProps & {
  to?: string; href?: string; onClick?: (e: React.MouseEvent) => void; type?: "button" | "submit"; disabled?: boolean; ariaLabel?: string
}) {
  const content = <>{children}{arrow && <span aria-hidden="true" style={{ fontSize: 14 }}>→</span>}</>
  const merged = { ...styleFor(variant), ...(disabled ? { opacity: 0.6, cursor: "not-allowed" } : {}), ...style }
  if (to) return <Link to={to} onClick={onClick} aria-label={ariaLabel} className={className} style={merged}>{content}</Link>
  if (href) return <a href={href} onClick={onClick} aria-label={ariaLabel} className={className} style={merged} target={/^https?:/.test(href) ? "_blank" : undefined} rel={/^https?:/.test(href) ? "noopener noreferrer" : undefined}>{content}</a>
  return <button type={type || "button"} onClick={onClick} disabled={disabled} aria-label={ariaLabel} className={className} style={merged}>{content}</button>
}
