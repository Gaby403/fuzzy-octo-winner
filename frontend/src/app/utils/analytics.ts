declare global {
    interface Window {
        dataLayer?: unknown[];
        gtag?: (...args: unknown[]) => void;
        grecaptcha?: {
            ready: (cb: () => void) => void;
            execute: (siteKey: string, opts: {
                action: string;
            }) => Promise<string>;
        };
    }
}
let injectedGtm = "";
let injectedGa4 = "";
let injectedRecaptcha = "";
function loadScript(id: string, attrs: Partial<HTMLScriptElement>, inline?: string) {
    if (typeof document === "undefined" || document.getElementById(id))
        return;
    const s = document.createElement("script");
    s.id = id;
    Object.assign(s, attrs);
    if (inline)
        s.textContent = inline;
    document.head.appendChild(s);
}
function ensureDataLayer(): unknown[] {
    if (typeof window === "undefined")
        return [];
    window.dataLayer = window.dataLayer || [];
    return window.dataLayer;
}
export function initAnalytics(opts: {
    ga4Id?: string;
    gtmId?: string;
}) {
    if (typeof window === "undefined")
        return;
    const gtmId = (opts.gtmId || "").trim();
    const ga4Id = (opts.ga4Id || "").trim();
    if (gtmId && injectedGtm !== gtmId) {
        injectedGtm = gtmId;
        const dl = ensureDataLayer();
        dl.push({ "gtm.start": Date.now(), event: "gtm.js" });
        loadScript("gtm-loader", {
            async: true,
            src: `https://www.googletagmanager.com/gtm.js?id=${encodeURIComponent(gtmId)}`,
        });
        if (!document.getElementById("gtm-noscript")) {
            const ns = document.createElement("noscript");
            ns.id = "gtm-noscript";
            const iframe = document.createElement("iframe");
            iframe.src = `https://www.googletagmanager.com/ns.html?id=${encodeURIComponent(gtmId)}`;
            iframe.height = "0";
            iframe.width = "0";
            iframe.style.display = "none";
            iframe.style.visibility = "hidden";
            ns.appendChild(iframe);
            document.body.insertBefore(ns, document.body.firstChild);
        }
    }
    if (ga4Id && injectedGa4 !== ga4Id) {
        injectedGa4 = ga4Id;
        loadScript("ga4-loader", {
            async: true,
            src: `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(ga4Id)}`,
        });
        ensureDataLayer();
        window.gtag = window.gtag || function gtag() {
            window.dataLayer!.push(arguments);
        };
        window.gtag("js", new Date());
        window.gtag("config", ga4Id, { anonymize_ip: true });
    }
}
export function prepararRecaptcha(siteKey: string) {
    const key = (siteKey || "").trim();
    if (!key || typeof window === "undefined" || injectedRecaptcha === key)
        return;
    injectedRecaptcha = key;
    loadScript("recaptcha-v3", {
        async: true,
        defer: true,
        src: `https://www.google.com/recaptcha/api.js?render=${encodeURIComponent(key)}`,
    });
}
export function trackEvent(event: string, params: Record<string, unknown> = {}) {
    if (typeof window === "undefined")
        return;
    ensureDataLayer().push({ event, ...params });
    if (typeof window.gtag === "function") {
        window.gtag("event", event, params);
    }
}
export function getRecaptchaToken(siteKey: string, action: string): Promise<string> {
    const key = (siteKey || "").trim();
    if (!key || typeof window === "undefined")
        return Promise.resolve("");
    loadScript("recaptcha-v3", {
        async: true,
        defer: true,
        src: `https://www.google.com/recaptcha/api.js?render=${encodeURIComponent(key)}`,
    });
    injectedRecaptcha = key;
    const execute = (): Promise<string> => new Promise(resolve => {
        try {
            window.grecaptcha!.ready(() => {
                window.grecaptcha!
                    .execute(key, { action })
                    .then(token => resolve(token || ""))
                    .catch(() => resolve(""));
            });
        }
        catch {
            resolve("");
        }
    });
    if (window.grecaptcha)
        return execute();
    return new Promise(resolve => {
        const started = Date.now();
        const tick = window.setInterval(() => {
            if (window.grecaptcha) {
                window.clearInterval(tick);
                execute().then(resolve);
            }
            else if (Date.now() - started > 5000) {
                window.clearInterval(tick);
                resolve("");
            }
        }, 100);
    });
}
