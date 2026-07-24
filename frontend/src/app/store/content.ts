import { createContext, useContext } from "react"

/**
 * Content model. This is the exact shape the WordPress REST endpoint
 * `studio-tabi/v1/content` returns — the WP plugin was built to mirror it —
 * so the site can be edited entirely from the WordPress admin and rendered
 * here without any transformation.
 */
/** A navigable link: a label plus a destination (anchor "#", page "/p/slug" or external URL). */
export interface MenuLink {
  label: string
  url: string
}

export interface SiteContent {
  site: {
    title: string
    tagline: string
    logoUrl: string
    faviconUrl: string
    heroImageUrl: string
  }
  /** Header navigation (brand, menu links, CTA). */
  nav: {
    brand: string
    links: MenuLink[]
    ctaLabel: string
  }
  hero: {
    eyebrow: string
    titleLines: string[]
    highlight: string
    description: string
  }
  about: {
    paragraph1: string
    paragraph2: string
    stats: { numeric: number; suffix: string; label: string }[]
    pillars: { title: string; body: string }[]
  }
  services: { num: string; title: string; body: string }[]
  projects: {
    id: string
    name: string
    category: string
    year: string
    bg: string
    accent: string
    featured: boolean
    imageUrl?: string
    /** Link externo para o site/projeto completo (abre em nova aba). */
    url?: string
    /** Galeria de imagens do projeto (URLs). */
    gallery?: string[]
    detail: {
      client: string
      scope: string[]
      duration: string
      challenge: string
      solution: string
      results: { label: string; value: string }[]
      mockupLines: string[]
    }
  }[]
  faq: { q: string; a: string }[]
  footer: {
    brand: string
    tagline: string
    ctaLabel: string
    columns: { title: string; links: MenuLink[] }[]
    contactTitle: string
    email: string
    phone: string
    city: string
    socialTitle: string
    social: MenuLink[]
    copyright: string
    madeIn: string
    legal: MenuLink[]
  }
  /** Dynamic pages created in WordPress, surfaced for navigation. */
  pages: { slug: string; title: string }[]
}

export const DEFAULT_CONTENT: SiteContent = {
  site: {
    title: "Studio Tabi",
    tagline: "Design e tecnologia que levam marcas até onde precisam chegar.",
    logoUrl: "",
    faviconUrl: "",
    heroImageUrl: "",
  },
  nav: {
    brand: "STUDIO TABI",
    links: [
      { label: "TRABALHOS", url: "#trabalhos" },
      { label: "SERVIÇOS", url: "#servicos" },
      { label: "SOBRE", url: "#sobre" },
      { label: "CONTATO", url: "#contato" },
    ],
    ctaLabel: "INICIAR PROJETO",
  },
  hero: {
    eyebrow: "STUDIO TABI — DIGITAL STUDIO",
    titleLines: ["TRANSFORMAMOS", "A SUA MARCA", "EM EXPERIÊNCIA"],
    highlight: "DIGITAL.",
    description: "Design, estratégia e desenvolvimento para transformar presença digital em percepção de valor, confiança e decisão.",
  },
  about: {
    paragraph1: 'O Studio Tabi nasceu da convicção de que presença digital é um ativo estratégico — não uma despesa de comunicação. Reunimos designers, estrategistas e engenheiros que recusam o medíocre do "bom o suficiente".',
    paragraph2: 'Cada projeto começa com uma pergunta simples: como esse negócio quer ser percebido daqui a cinco anos? A resposta guia cada decisão criativa, técnica e estratégica que tomamos.',
    stats: [
      { numeric: 7,   suffix: "+", label: "ANOS DE MERCADO" },
      { numeric: 120, suffix: "+", label: "PROJETOS ENTREGUES" },
      { numeric: 98,  suffix: "%", label: "TAXA DE RETENÇÃO" },
      { numeric: 3,   suffix: "×", label: "RETORNO MÉDIO EM 12M" },
    ],
    pillars: [
      { title: "Identidade que comunica",    body: "Marcas que carregam intenção em cada detalhe — do logotipo ao tom de voz. Construímos sistemas visuais que resistem ao tempo e crescem com o negócio." },
      { title: "Experiência que converte",   body: "Interface não é arte — é arquitetura de decisões. Cada tela, cada fluxo, cada micro-interação é desenhada para mover o usuário em direção ao objetivo." },
      { title: "Tecnologia que escala",      body: "Código sem dívida técnica. Estruturas que aguentam crescimento sem reescritas. Integrações que funcionam na primeira vez e continuam funcionando." },
      { title: "Estratégia que orienta",     body: "Dados, mercado e comportamento do usuário traduzidos em decisões claras. Sem achismos, sem modismos — só o que move o ponteiro." },
    ],
  },
  services: [
    { num: "01", title: "Branding & Identidade Visual", body: "Sistemas de marca que comunicam com precisão — do logotipo ao tom de voz. Identidades que crescem com o negócio e resistem ao tempo." },
    { num: "02", title: "Design de Interface (UI/UX)", body: "Interfaces construídas a partir do comportamento real do usuário. Cada pixel tem função. Cada fluxo tem intenção." },
    { num: "03", title: "Desenvolvimento Web", body: "Código limpo, performático e acessível. Sites e aplicações que carregam rápido, escalam com o negócio e integram com qualquer stack." },
    { num: "04", title: "Estratégia Digital", body: "Diagnóstico, posicionamento e roadmap para sua presença digital. Decisões com dados, não com suposições." },
    { num: "05", title: "Motion & Animação", body: "Movimento que conta histórias. Animações de interface e motion graphics que transformam conteúdo em experiência." },
    { num: "06", title: "Conteúdo & Copywriting", body: "Palavras que convertem. Narrativas que constroem autoridade, geram confiança e movem o usuário à ação." },
  ],
  projects: [
    {
      id: "01", name: "Nuvem Finance", category: "Branding & UI", year: "2025",
      bg: "linear-gradient(135deg,#1A0505 0%,#2D0A0A 50%,#1A0A14 100%)", accent: "#F20C25", featured: true,
      detail: {
        client: "Nuvem Finance", scope: ["Identidade Visual", "UI/UX Design", "Design System"], duration: "14 semanas",
        challenge: "A Nuvem Finance chegou até nós como mais uma fintech genérica em um mercado saturado — paleta azul corporativa, linguagem fria, zero diferenciação. O desafio era criar uma identidade que transmitisse solidez sem perder calor humano, e uma interface que tornasse conceitos financeiros complexos acessíveis para o usuário final.",
        solution: "Desenvolvemos uma identidade visual centrada no contraste: tipografia condensada e assertiva equilibrada com espaçamento generoso e tons terrosos que remetem a confiança sem o clichê do azul bancário. O design system foi construído para escalar com o produto, com 240+ componentes documentados e um guia de voz e tom integrado.",
        results: [{ label: "Aumento em conversão", value: "+38%" }, { label: "Redução em churn", value: "−22%" }, { label: "NPS pós-redesign", value: "72" }, { label: "Componentes no DS", value: "240+" }],
        mockupLines: ["DASHBOARD", "PORTFÓLIO", "ANÁLISE", "RELATÓRIOS"],
      },
    },
    {
      id: "02", name: "FlowDesk", category: "Produto SaaS", year: "2025",
      bg: "linear-gradient(135deg,#06061A 0%,#0A0A2D 50%,#060F1A 100%)", accent: "#5B7FFF", featured: true,
      detail: {
        client: "FlowDesk", scope: ["Produto SaaS", "UX Research", "Prototipação", "Dev Front-end"], duration: "22 semanas",
        challenge: "A FlowDesk tinha uma ideia sólida de produto mas o MVP inicial tinha uma curva de aprendizado altíssima. A taxa de abandono na primeira semana era de 67%. Precisávamos reconstruir a experiência do zero, sem perder os usuários existentes.",
        solution: "Realizamos 18 entrevistas em profundidade com usuários reais e mapeamos os principais pontos de atrito. Redesenhamos o onboarding com uma abordagem de 'progressive disclosure'. A nova arquitetura de informação reduziu os caminhos críticos de 7 para 3 cliques.",
        results: [{ label: "Redução de abandono", value: "−51%" }, { label: "Tempo médio no app", value: "+2.4×" }, { label: "Usuários ativos/mês", value: "12k+" }, { label: "Avaliação App Store", value: "4.8★" }],
        mockupLines: ["PROJETOS", "TAREFAS", "EQUIPE", "RELATÓRIO"],
      },
    },
    {
      id: "03", name: "Maison Lux", category: "E-commerce", year: "2024",
      bg: "linear-gradient(135deg,#0F0D08 0%,#1A1408 50%,#0D0B06 100%)", accent: "#C4A45A", featured: false,
      detail: {
        client: "Maison Lux", scope: ["E-commerce", "UI Design", "Motion Design"], duration: "10 semanas",
        challenge: "Uma marca de moda de luxo brasileira com atelier próprio mas presença digital completamente desalinhada com o posicionamento premium. O site anterior parecia uma loja de departamentos, não uma maison.",
        solution: "Criamos uma experiência editorial inspirada nas grandes maisons europeias: fotografia fullscreen, tipografia serif com muito espaço branco, e microinterações que reforçam a percepção de exclusividade. O checkout foi simplificado para 2 etapas.",
        results: [{ label: "Aumento no ticket médio", value: "+29%" }, { label: "Taxa de conversão", value: "+44%" }, { label: "Tempo na página produto", value: "+3.1min" }, { label: "Retorno de clientes", value: "+61%" }],
        mockupLines: ["COLEÇÃO", "ATELIÊ", "PEÇAS", "CONTATO"],
      },
    },
    {
      id: "04", name: "Vitalize App", category: "Mobile UI", year: "2024",
      bg: "linear-gradient(135deg,#060F08 0%,#081A0A 50%,#060D07 100%)", accent: "#3DBF72", featured: false,
      detail: {
        client: "Vitalize", scope: ["Mobile UI", "iOS & Android", "Ilustração"], duration: "8 semanas",
        challenge: "O app de saúde e bem-estar Vitalize enfrentava um paradoxo: usuários adoravam o conceito mas achavam o app 'pesado' e 'intimidador'. O design anterior usava verde clínico e iconografia médica que afastava justamente o público-alvo.",
        solution: "Redesenhamos com uma abordagem de 'saúde como estilo de vida': paleta orgânica, ilustrações feitas à mão que humanizam os dados, e um sistema de progresso gamificado que celebra pequenas vitórias.",
        results: [{ label: "Downloads no primeiro mês", value: "48k" }, { label: "Retenção em 30 dias", value: "71%" }, { label: "Avaliação nas stores", value: "4.9★" }, { label: "Menções espontâneas", value: "+180%" }],
        mockupLines: ["INÍCIO", "TREINOS", "NUTRIÇÃO", "PROGRESSO"],
      },
    },
  ],
  faq: [
    { q: "Como funciona o processo de trabalho?", a: "Iniciamos com um diagnóstico aprofundado do negócio, mercado e objetivos. Em seguida, criamos um roadmap claro com entregas, prazos e marcos de aprovação. Trabalhamos em sprints curtos com checkpoints semanais para garantir alinhamento contínuo — sem surpresas no final." },
    { q: "Quanto tempo leva um projeto?", a: "Depende do escopo. Um projeto de identidade visual leva de 3 a 6 semanas. Um site completo com design e desenvolvimento, de 6 a 12 semanas. Aplicações mais complexas podem levar de 3 a 6 meses. Sempre apresentamos um cronograma detalhado antes de iniciar." },
    { q: "Vocês trabalham com empresas de qual tamanho?", a: "Atendemos desde startups em fase de crescimento até empresas consolidadas que querem renovar sua presença digital. O que importa não é o tamanho, mas o comprometimento com qualidade e a disposição para construir algo duradouro." },
    { q: "Como é estruturada a precificação?", a: "Trabalhamos com projetos fechados (escopo e valor definidos no início) ou retainer mensal para empresas que precisam de parceria contínua. Não cobramos por hora — cobramos pelo resultado. O orçamento é apresentado de forma transparente, sem taxas ocultas." },
    { q: "Vocês oferecem suporte após a entrega?", a: "Sim. Todos os projetos incluem um período de garantia de 30 dias após o lançamento. Para clientes que desejam suporte contínuo, oferecemos planos de manutenção mensal que incluem atualizações, monitoramento e evolução do produto." },
    { q: "Como posso começar a trabalhar com vocês?", a: "Preencha o formulário de contato ou nos envie um e-mail com um breve contexto do seu projeto. Agendaremos uma chamada de diagnóstico gratuita de 30 minutos para entender suas necessidades e verificar se somos o parceiro certo para você." },
  ],
  footer: {
    brand: "STUDIO TABI",
    tagline: "Design e tecnologia que levam marcas até onde precisam chegar.",
    ctaLabel: "INICIAR PROJETO",
    columns: [
      {
        title: "Navegação",
        links: [
          { label: "Trabalhos", url: "#trabalhos" },
          { label: "Serviços", url: "#servicos" },
          { label: "Sobre", url: "#sobre" },
          { label: "Contato", url: "#contato" },
        ],
      },
      {
        title: "Serviços",
        links: [
          { label: "Branding", url: "#" },
          { label: "UI / UX Design", url: "#" },
          { label: "Desenvolvimento Web", url: "#" },
          { label: "Estratégia Digital", url: "#" },
          { label: "Motion & Animação", url: "#" },
        ],
      },
    ],
    contactTitle: "Contato",
    email: "oi@studiotabi.com.br",
    phone: "+55 11 9 9999-9999",
    city: "São Paulo, SP",
    socialTitle: "Social",
    social: [
      { label: "Instagram", url: "#" },
      { label: "LinkedIn", url: "#" },
      { label: "Behance", url: "#" },
      { label: "GitHub", url: "#" },
    ],
    copyright: "© 2026 Studio Tabi. Todos os direitos reservados.",
    madeIn: "Feito com precisão em São Paulo",
    legal: [
      { label: "Política de Privacidade", url: "#" },
      { label: "Termos de Uso", url: "#" },
    ],
  },
  pages: [],
}

/**
 * Base URL of the WordPress install, e.g. https://cms.studiotabi.com.br
 *
 * Resolution order:
 *   1. window.__STUDIO_TABI_API__  → runtime config (public/config.js), editável
 *      diretamente no servidor SEM recompilar. É o que o zip de deploy usa.
 *   2. VITE_WP_API                 → valor de build (dev local).
 *   3. ""                          → usa o conteúdo padrão embutido (offline).
 */
declare global {
  interface Window {
    __STUDIO_TABI_API__?: string
  }
}

function resolveApiBase(): string {
  const runtime = typeof window !== "undefined" ? window.__STUDIO_TABI_API__ : undefined
  const build = import.meta.env.VITE_WP_API as string | undefined
  return (runtime || build || "").replace(/\/$/, "")
}

export const WP_API: string = resolveApiBase()

const CONTENT_ENDPOINT = "/wp-json/studio-tabi/v1/content"

/** URL of the WordPress admin, for the "editar conteúdo" shortcut. */
export const WP_ADMIN_URL: string = WP_API ? `${WP_API}/wp-admin/` : "/wp-admin/"

/**
 * Deep-merge a partial API payload on top of the defaults so a missing or
 * malformed field never crashes the UI.
 */
function mergeContent(remote: Partial<SiteContent> | null | undefined): SiteContent {
  if (!remote) return DEFAULT_CONTENT
  return {
    site: { ...DEFAULT_CONTENT.site, ...(remote.site || {}) },
    nav: { ...DEFAULT_CONTENT.nav, ...(remote.nav || {}) },
    hero: { ...DEFAULT_CONTENT.hero, ...(remote.hero || {}) },
    about: { ...DEFAULT_CONTENT.about, ...(remote.about || {}) },
    services: remote.services?.length ? remote.services : DEFAULT_CONTENT.services,
    projects: remote.projects?.length ? remote.projects : DEFAULT_CONTENT.projects,
    faq: remote.faq?.length ? remote.faq : DEFAULT_CONTENT.faq,
    footer: { ...DEFAULT_CONTENT.footer, ...(remote.footer || {}) },
    pages: remote.pages || [],
  }
}

/**
 * Fetch the whole site content from WordPress. Falls back to DEFAULT_CONTENT
 * on any network/parse error so the site is never blank.
 */
export async function fetchContent(): Promise<SiteContent> {
  if (!WP_API) return DEFAULT_CONTENT
  try {
    const res = await fetch(`${WP_API}${CONTENT_ENDPOINT}`, { headers: { Accept: "application/json" } })
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    const data = (await res.json()) as Partial<SiteContent>
    return mergeContent(data)
  } catch (err) {
    console.warn("[Studio Tabi] Falha ao carregar conteúdo do WordPress, usando conteúdo padrão.", err)
    return DEFAULT_CONTENT
  }
}

export interface WpPage {
  slug: string
  title: string
  content: string
}

/** Fetch a single WordPress page by slug for the dynamic /p/:slug route. */
export async function fetchPage(slug: string): Promise<WpPage | null> {
  if (!WP_API) return null
  try {
    const res = await fetch(`${WP_API}/wp-json/studio-tabi/v1/page/${encodeURIComponent(slug)}`, {
      headers: { Accept: "application/json" },
    })
    if (!res.ok) return null
    return (await res.json()) as WpPage
  } catch {
    return null
  }
}

export interface ContentContextValue {
  content: SiteContent
  loading: boolean
}

export const ContentContext = createContext<ContentContextValue>({
  content: DEFAULT_CONTENT,
  loading: false,
})

export function useContent() {
  return useContext(ContentContext)
}
