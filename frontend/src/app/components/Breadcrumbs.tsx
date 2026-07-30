import { useEffect } from "react";
import { Link } from "react-router";
import { useLocale } from "../i18n/useLocale";
const RED_INK = "#FF3547";
export interface Crumb {
    label: string;
    to?: string;
}
export function Breadcrumbs({ items }: {
    items: Crumb[];
}) {
    const { t } = useLocale();
    useEffect(() => {
        if (typeof window === "undefined")
            return;
        const origin = window.location.origin;
        const el = document.getElementById("ld-breadcrumb") as HTMLScriptElement | null;
        const script = el || Object.assign(document.createElement("script"), { type: "application/ld+json", id: "ld-breadcrumb" });
        script.textContent = JSON.stringify({
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            itemListElement: items.map((c, i) => ({
                "@type": "ListItem",
                position: i + 1,
                name: c.label,
                ...(c.to ? { item: origin + c.to } : {}),
            })),
        });
        if (!el)
            document.head.appendChild(script);
        return () => {
            const s = document.getElementById("ld-breadcrumb");
            if (s)
                s.remove();
        };
    }, [items]);
    return (<nav aria-label={t("geral.trilha")} style={{ fontSize: 11, fontWeight: 600, letterSpacing: "0.08em", color: "rgba(239,239,239,0.5)" }}>
      <ol style={{ listStyle: "none", display: "flex", flexWrap: "wrap", alignItems: "center", gap: 8, margin: 0, padding: 0 }}>
        {items.map((c, i) => (<li key={c.label + i} style={{ display: "inline-flex", alignItems: "center", gap: 8 }}>
            {c.to && i < items.length - 1 ? (<Link to={c.to} style={{ color: "rgba(239,239,239,0.5)", textDecoration: "none" }}>{c.label}</Link>) : (<span aria-current="page" style={{ color: i === items.length - 1 ? RED_INK : "inherit" }}>{c.label}</span>)}
            {i < items.length - 1 && <span aria-hidden="true" style={{ opacity: 0.4 }}>/</span>}
          </li>))}
      </ol>
    </nav>);
}
