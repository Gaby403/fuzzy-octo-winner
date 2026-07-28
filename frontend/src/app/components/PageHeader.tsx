import { Link } from "react-router"
import { useContent } from "../store/content"

const RED = "#F20C25"
const WHITE = "#EFEFEF"
const FONT_HEAD = '"Roboto Condensed", sans-serif'

/**
 * Cabeçalho fixo padrão das páginas internas (logo do CMS + link de volta).
 * Centraliza o header para manter consistência e garantir a logo em todas as
 * páginas (corrige "logo ausente" e "header inconsistente").
 */
export function PageHeader({ backTo = "/", backLabel = "← VOLTAR" }: { backTo?: string; backLabel?: string }) {
  const { content } = useContent()
  const pad = "clamp(20px, 4vw, 82px)"
  return (
    <header
      style={{ display: "flex", alignItems: "center", justifyContent: "space-between", padding: `22px ${pad}`, borderBottom: "1px solid rgba(239,239,239,0.08)", position: "sticky", top: 0, background: "rgba(17,17,17,0.86)", backdropFilter: "blur(10px)", zIndex: 20 }}
    >
      <Link to="/" aria-label="Voltar para a home" style={{ textDecoration: "none", color: WHITE, display: "inline-flex", alignItems: "center" }}>
        {content.site.logoUrl ? (
          <img src={content.site.logoUrl} alt={content.nav.brand || content.site.title} style={{ height: "clamp(26px, 3.4vw, 40px)", width: "auto", display: "block" }} />
        ) : (
          <span style={{ fontFamily: FONT_HEAD, fontWeight: 900, fontSize: 20, letterSpacing: "-0.04em", textTransform: "uppercase" }}>
            {content.site.title.split(" ")[0] || "STUDIO"} <span style={{ color: RED }}>{content.site.title.split(" ").slice(1).join(" ") || "TABI"}</span>
          </span>
        )}
      </Link>
      <Link to={backTo} style={{ textDecoration: "none", color: "rgba(239,239,239,0.55)", fontSize: 11, fontWeight: 600, letterSpacing: "0.14em" }}>{backLabel}</Link>
    </header>
  )
}
