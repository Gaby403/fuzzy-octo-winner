/**
 * Camada fina de analytics para o front-end headless.
 *
 * Toda a configuração (GA4, GTM, reCAPTCHA) é editável no CMS e chega via
 * content.site. Este módulo apenas injeta os scripts quando os IDs existem e
 * expõe helpers para disparar eventos — nada é hardcoded, e o site funciona
 * normalmente quando nenhuma integração está configurada.
 */

declare global {
  interface Window {
    dataLayer?: unknown[]
    gtag?: (...args: unknown[]) => void
    grecaptcha?: {
      ready: (cb: () => void) => void
      execute: (siteKey: string, opts: { action: string }) => Promise<string>
    }
  }
}

let injectedGtm = ""
let injectedGa4 = ""
let injectedRecaptcha = ""

/** Injeta um <script> uma única vez (idempotente por id). */
function loadScript(id: string, attrs: Partial<HTMLScriptElement>, inline?: string) {
  if (typeof document === "undefined" || document.getElementById(id)) return
  const s = document.createElement("script")
  s.id = id
  Object.assign(s, attrs)
  if (inline) s.textContent = inline
  document.head.appendChild(s)
}

/** Garante que window.dataLayer exista antes de qualquer push. */
function ensureDataLayer(): unknown[] {
  if (typeof window === "undefined") return []
  window.dataLayer = window.dataLayer || []
  return window.dataLayer
}

/**
 * Inicializa as integrações a partir do conteúdo do CMS. Deve ser chamada
 * quando o conteúdo carregar; é segura para chamar mais de uma vez.
 */
export function initAnalytics(opts: { ga4Id?: string; gtmId?: string; recaptchaSite?: string }) {
  if (typeof window === "undefined") return
  const gtmId = (opts.gtmId || "").trim()
  const ga4Id = (opts.ga4Id || "").trim()
  const recaptchaSite = (opts.recaptchaSite || "").trim()

  // Google Tag Manager (preferencial quando presente).
  if (gtmId && injectedGtm !== gtmId) {
    injectedGtm = gtmId
    const dl = ensureDataLayer()
    dl.push({ "gtm.start": Date.now(), event: "gtm.js" })
    loadScript("gtm-loader", {
      async: true,
      src: `https://www.googletagmanager.com/gtm.js?id=${encodeURIComponent(gtmId)}`,
    })
    // <noscript> fallback no <body>.
    if (!document.getElementById("gtm-noscript")) {
      const ns = document.createElement("noscript")
      ns.id = "gtm-noscript"
      const iframe = document.createElement("iframe")
      iframe.src = `https://www.googletagmanager.com/ns.html?id=${encodeURIComponent(gtmId)}`
      iframe.height = "0"
      iframe.width = "0"
      iframe.style.display = "none"
      iframe.style.visibility = "hidden"
      ns.appendChild(iframe)
      document.body.insertBefore(ns, document.body.firstChild)
    }
  }

  // GA4 direto (gtag.js) — quando não há GTM ou quando ambos são desejados.
  if (ga4Id && injectedGa4 !== ga4Id) {
    injectedGa4 = ga4Id
    loadScript("ga4-loader", {
      async: true,
      src: `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(ga4Id)}`,
    })
    ensureDataLayer()
    window.gtag = window.gtag || function gtag() {
      // eslint-disable-next-line prefer-rest-params
      window.dataLayer!.push(arguments)
    }
    window.gtag("js", new Date())
    window.gtag("config", ga4Id, { anonymize_ip: true })
  }

  // reCAPTCHA v3 — carrega o script assim que a chave pública existir.
  if (recaptchaSite && injectedRecaptcha !== recaptchaSite) {
    injectedRecaptcha = recaptchaSite
    loadScript("recaptcha-v3", {
      async: true,
      defer: true,
      src: `https://www.google.com/recaptcha/api.js?render=${encodeURIComponent(recaptchaSite)}`,
    })
  }
}

/**
 * Dispara um evento de analytics. Empurra para o dataLayer (GTM) e também para
 * o gtag (GA4 direto), de forma que funcione com qualquer uma das integrações.
 */
export function trackEvent(event: string, params: Record<string, unknown> = {}) {
  if (typeof window === "undefined") return
  ensureDataLayer().push({ event, ...params })
  if (typeof window.gtag === "function") {
    window.gtag("event", event, params)
  }
}

/**
 * Executa o reCAPTCHA v3 e devolve o token (ou "" se não estiver configurado).
 * Aguarda o grecaptcha ficar pronto; falha em silêncio devolvendo "".
 */
export function getRecaptchaToken(siteKey: string, action: string): Promise<string> {
  const key = (siteKey || "").trim()
  if (!key || typeof window === "undefined") return Promise.resolve("")

  // O script é carregado de forma adiada (idle) para não pesar no PageSpeed.
  // Se o visitante enviar o formulário antes disso, carregamos sob demanda e
  // aguardamos — assim o token nunca sai vazio por causa do defer.
  loadScript("recaptcha-v3", {
    async: true,
    defer: true,
    src: `https://www.google.com/recaptcha/api.js?render=${encodeURIComponent(key)}`,
  })
  injectedRecaptcha = key

  const execute = (): Promise<string> =>
    new Promise(resolve => {
      try {
        window.grecaptcha!.ready(() => {
          window.grecaptcha!
            .execute(key, { action })
            .then(token => resolve(token || ""))
            .catch(() => resolve(""))
        })
      } catch {
        resolve("")
      }
    })

  if (window.grecaptcha) return execute()

  // Aguarda o grecaptcha aparecer (máx. ~5s) antes de desistir.
  return new Promise(resolve => {
    const started = Date.now()
    const tick = window.setInterval(() => {
      if (window.grecaptcha) {
        window.clearInterval(tick)
        execute().then(resolve)
      } else if (Date.now() - started > 5000) {
        window.clearInterval(tick)
        resolve("")
      }
    }, 100)
  })
}
