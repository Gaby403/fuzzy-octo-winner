import { useEffect } from "react"
import { WP_ADMIN_URL } from "../store/content"

const RED = "#F20C25"
const WHITE = "#EFEFEF"
const BLACK = "#111111"

/**
 * The content is now managed in WordPress. This route simply forwards the
 * user to the WordPress admin (configured via VITE_WP_API), keeping the old
 * /admin link working.
 */
export default function Admin() {
  useEffect(() => {
    const t = setTimeout(() => {
      window.location.href = WP_ADMIN_URL
    }, 1200)
    return () => clearTimeout(t)
  }, [])

  return (
    <div
      style={{
        minHeight: "100svh",
        background: BLACK,
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        flexDirection: "column",
        gap: 18,
        fontFamily: '"Be Vietnam Pro", sans-serif',
        color: WHITE,
        padding: 24,
        textAlign: "center",
      }}
    >
      <div style={{ fontFamily: '"Roboto Condensed", sans-serif', fontWeight: 900, fontSize: 32, letterSpacing: "-0.05em", textTransform: "uppercase" }}>
        STUDIO <span style={{ color: RED }}>TABI</span>
      </div>
      <p style={{ fontSize: 13, letterSpacing: "0.06em", color: "rgba(239,239,239,0.55)", maxWidth: 420, lineHeight: 1.6, margin: 0 }}>
        O conteúdo deste site é gerenciado no <strong>WordPress</strong>. Você está sendo redirecionado para o painel administrativo…
      </p>
      <a
        href={WP_ADMIN_URL}
        style={{ background: RED, color: WHITE, textDecoration: "none", fontWeight: 700, fontSize: 12, letterSpacing: "0.12em", padding: "13px 22px", borderRadius: 8 }}
      >
        ABRIR PAINEL WORDPRESS →
      </a>
    </div>
  )
}
