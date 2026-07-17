import type { SiteContent } from "./content"

// URL do WordPress headless (ex.: https://cms.studiotabi.com.br), sem barra final.
// Definida em .env / .env.local como VITE_WP_URL. Sem ela, o app roda em modo
// standalone com conteúdo local (localStorage), como antes.
export const WP_URL = ((import.meta.env.VITE_WP_URL as string | undefined) ?? "").replace(/\/+$/, "")
export const isWpConfigured = WP_URL.length > 0

const CONTENT_ENDPOINT = `${WP_URL}/wp-json/tabi/v1/content`
const AUTH_KEY = "tabi_wp_auth"

export function getWpAuth(): string | null {
  return sessionStorage.getItem(AUTH_KEY)
}

export function clearWpAuth() {
  sessionStorage.removeItem(AUTH_KEY)
}

/** Valida usuário + Application Password contra o WP e guarda a credencial na sessão. */
export async function loginWp(user: string, appPassword: string): Promise<boolean> {
  const token = btoa(`${user}:${appPassword}`)
  try {
    const res = await fetch(`${WP_URL}/wp-json/wp/v2/users/me`, {
      headers: { Authorization: `Basic ${token}` },
    })
    if (!res.ok) return false
    sessionStorage.setItem(AUTH_KEY, token)
    return true
  } catch {
    return false
  }
}

/** Busca o conteúdo publicado no WP. Retorna null se indisponível (usa-se o fallback local). */
export async function fetchWpContent(): Promise<Partial<SiteContent> | null> {
  if (!isWpConfigured) return null
  try {
    const res = await fetch(CONTENT_ENDPOINT)
    if (!res.ok) return null
    return (await res.json()) as Partial<SiteContent>
  } catch {
    return null
  }
}

/** Publica o conteúdo no WP (requer login com Application Password). */
export async function saveWpContent(content: SiteContent): Promise<void> {
  const auth = getWpAuth()
  if (!auth) throw new Error("Sessão expirada — faça login novamente.")
  const res = await fetch(CONTENT_ENDPOINT, {
    method: "POST",
    headers: { "Content-Type": "application/json", Authorization: `Basic ${auth}` },
    body: JSON.stringify(content),
  })
  if (res.status === 401 || res.status === 403) {
    clearWpAuth()
    throw new Error("Credenciais recusadas pelo WordPress — faça login novamente.")
  }
  if (!res.ok) {
    throw new Error(`Falha ao salvar no WordPress (HTTP ${res.status}).`)
  }
}
