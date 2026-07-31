import { useState } from "react";
import { subscribeNewsletter } from "../../store/blog";
import { useContent } from "../../store/content";
import { getRecaptchaToken, trackEvent } from "../../utils/analytics";
import { colors, fonts, radii } from "../../constants/theme";
import { useLocale } from "../../i18n/useLocale";
export function NewsletterForm({ compact = false }: {
    compact?: boolean;
}) {
    const { t, locale } = useLocale();
    const { content } = useContent();
    const [email, setEmail] = useState("");
    const [msg, setMsg] = useState("");
    const [ok, setOk] = useState(false);
    const [sending, setSending] = useState(false);
    const id = compact ? "nl-footer" : "nl-email";
    const submit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (sending)
            return;
        setSending(true);
        setMsg("");
        const token = await getRecaptchaToken(content.site.recaptchaSite, "newsletter");
        const r = await subscribeNewsletter(email, token, locale);
        setOk(r.ok);
        setMsg(r.message);
        setSending(false);
        if (r.ok) {
            setEmail("");
            trackEvent("newsletter_signup");
        }
    };
    return (<form onSubmit={submit} style={{ display: "flex", flexWrap: "wrap", gap: 10 }}>
      <label htmlFor={id} style={{ position: "absolute", width: 1, height: 1, overflow: "hidden", clip: "rect(0,0,0,0)" }}>{t("news.email")}</label>
      <input id={id} type="email" required value={email} onChange={e => setEmail(e.target.value)} placeholder={t("form.exemploEmail")} style={{ flex: "1 1 200px", minWidth: 0, background: colors.surface, border: `1px solid ${colors.borderStrong}`, borderRadius: radii.pill, color: colors.white, fontFamily: fonts.body, fontSize: 14, padding: "12px 18px", outline: "none" }}/>
      <button type="submit" disabled={sending} style={{ padding: "12px 22px", borderRadius: radii.pill, border: "none", background: colors.redBtn, color: colors.pureWhite, fontFamily: fonts.body, fontSize: 11, fontWeight: 700, letterSpacing: "0.12em", textTransform: "uppercase", cursor: sending ? "default" : "pointer", opacity: sending ? 0.6 : 1 }}>
        {sending ? t("news.enviando") : (content.footer.newsletterButton || t("news.inscrever"))}
      </button>
      {msg && <p role="status" style={{ margin: "4px 0 0", width: "100%", fontSize: 13, color: ok ? colors.ok : colors.error }}>{msg}</p>}
    </form>);
}
