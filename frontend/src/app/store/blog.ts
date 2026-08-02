import { WP_API } from "./content";
import { traduzir } from "../i18n/dicionario";
export interface BlogCategory {
    name: string;
    slug: string;
    count?: number;
}
export interface PostCard {
    id: number;
    slug: string;
    title: string;
    excerpt: string;
    date: string;
    dateISO: string;
    author: string;
    image: string;
    categories: BlogCategory[];
    readingTime: number;
}
export interface PostFull extends PostCard {
    content: string;
    tags: BlogCategory[];
    authorBio: string;
    authorAvatar: string;
    related: PostCard[];
}
export interface PostsPage {
    items: PostCard[];
    total: number;
    totalPages: number;
    page: number;
    perPage: number;
}
const NS = "/wp-json/studio-tabi/v1";
export async function fetchPosts(opts: {
    page?: number;
    perPage?: number;
    search?: string;
    category?: string;
    lang?: "pt" | "en";
} = {}): Promise<PostsPage> {
    const empty: PostsPage = { items: [], total: 0, totalPages: 0, page: 1, perPage: opts.perPage ?? 9 };
    const qs = new URLSearchParams();
    qs.set("page", String(opts.page ?? 1));
    qs.set("per_page", String(opts.perPage ?? 9));
    if (opts.search)
        qs.set("search", opts.search);
    if (opts.category)
        qs.set("category", opts.category);
    if (opts.lang === "en")
        qs.set("lang", "en");
    try {
        const res = await fetch(`${WP_API}${NS}/posts?${qs.toString()}`, { headers: { Accept: "application/json" } });
        if (!res.ok)
            return empty;
        return (await res.json()) as PostsPage;
    }
    catch {
        return empty;
    }
}
export async function fetchPost(slug: string, lang: "pt" | "en" = "pt"): Promise<PostFull | null> {
    const qs = lang === "en" ? "?lang=en" : "";
    try {
        const res = await fetch(`${WP_API}${NS}/post/${encodeURIComponent(slug)}${qs}`, { headers: { Accept: "application/json" } });
        if (!res.ok)
            return null;
        return (await res.json()) as PostFull;
    }
    catch {
        return null;
    }
}
export async function fetchCategories(lang: "pt" | "en" = "pt"): Promise<BlogCategory[]> {
    const qs = lang === "en" ? "?lang=en" : "";
    try {
        const res = await fetch(`${WP_API}${NS}/categories${qs}`, { headers: { Accept: "application/json" } });
        if (!res.ok)
            return [];
        return (await res.json()) as BlogCategory[];
    }
    catch {
        return [];
    }
}
export async function subscribeNewsletter(email: string, recaptchaToken = "", lang: "pt" | "en" = "pt"): Promise<{
    ok: boolean;
    message: string;
}> {
    try {
        const res = await fetch(`${WP_API}${NS}/subscribe`, {
            method: "POST",
            headers: { "Content-Type": "application/json", Accept: "application/json" },
            body: JSON.stringify({ email, recaptchaToken }),
        });
        const data = (await res.json().catch(() => ({}))) as {
            ok?: boolean;
            message?: string;
        };
        if (!res.ok || !data.ok)
            return { ok: false, message: data.message || traduzir(lang, "news.naoInscreveu") };
        return { ok: true, message: data.message || traduzir(lang, "news.confirmado") };
    }
    catch {
        return { ok: false, message: traduzir(lang, "news.semConexao") };
    }
}
