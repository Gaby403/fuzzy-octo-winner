import { createContext, useContext } from "react";
export interface MenuLink {
    label: string;
    url: string;
}
export interface SiteContent {
    site: {
        title: string;
        metaDescription: string;
        tagline: string;
        logoUrl: string;
        faviconUrl: string;
        heroImageUrl: string;
        ga4Id: string;
        gtmId: string;
        recaptchaSite: string;
    };
    nav: {
        brand: string;
        links: MenuLink[];
        ctaLabel: string;
        ctaUrl: string;
    };
    hero: {
        eyebrow: string;
        titleLines: string[];
        highlight: string;
        description: string;
        ctaPrimary: MenuLink;
        ctaSecondary: MenuLink;
    };
    projectsCta: MenuLink;
    thankYou: {
        title: string;
        message: string;
    };
    contact: {
        title: string;
        highlight: string;
        description: string;
    };
    process: {
        title: string;
        slug: string;
        icon: string;
        summary: string;
    }[];
    sections: {
        about: {
            eyebrow: string;
            pillarsLabel: string;
            ctaLabel: string;
            ctaUrl: string;
        };
        services: {
            eyebrow: string;
            ctaLabel: string;
            ctaUrl: string;
        };
        projects: {
            eyebrow: string;
            note: string;
            cardText: string;
        };
        blog: {
            eyebrow: string;
            title: string;
            highlight: string;
            note: string;
            ctaLabel: string;
        };
        faq: {
            eyebrow: string;
            note: string;
            ctaLabel: string;
            ctaUrl: string;
        };
    };
    about: {
        paragraph1: string;
        paragraph2: string;
        stats: {
            numeric: number;
            suffix: string;
            label: string;
        }[];
        pillars: {
            title: string;
            body: string;
        }[];
    };
    services: {
        num: string;
        title: string;
        body: string;
        slug?: string;
        content?: string;
        image?: string;
        showExcerpt?: boolean;
    }[];
    projects: {
        id: string;
        name: string;
        category: string;
        year: string;
        bg: string;
        accent: string;
        featured: boolean;
        home?: boolean;
        imageUrl?: string;
        url?: string;
        gallery?: string[];
        documents?: {
            url: string;
            title: string;
        }[];
        detail: {
            client: string;
            scope: string[];
            duration: string;
            challenge: string;
            solution: string;
            results: {
                label: string;
                value: string;
            }[];
            mockupLines: string[];
        };
    }[];
    faq: {
        q: string;
        a: string;
    }[];
    footer: {
        brand: string;
        tagline: string;
        ctaTitle: string;
        ctaHighlight: string;
        ctaLabel: string;
        ctaUrl: string;
        columns: {
            title: string;
            links: MenuLink[];
        }[];
        contactTitle: string;
        email: string;
        phone: string;
        city: string;
        socialTitle: string;
        social: MenuLink[];
        copyright: string;
        madeIn: string;
        legal: MenuLink[];
    };
    pages: {
        slug: string;
        title: string;
    }[];
}
export const DEFAULT_CONTENT: SiteContent = {
    site: {
        title: "Studio Tabi",
        metaDescription: "Studio Tabi — design, estratégia e desenvolvimento para transformar presença digital em valor, confiança e decisão.",
        tagline: "Design e tecnologia que levam marcas até onde precisam chegar.",
        logoUrl: "",
        faviconUrl: "",
        heroImageUrl: "",
        ga4Id: "",
        gtmId: "",
        recaptchaSite: "",
    },
    nav: {
        brand: "STUDIO TABI",
        links: [
            { label: "TRABALHOS", url: "#trabalhos" },
            { label: "SERVIÇOS", url: "/servicos" },
            { label: "SOBRE", url: "/sobre" },
            { label: "BLOG", url: "/blog" },
            { label: "CONTATO", url: "/contato" },
        ],
        ctaLabel: "INICIAR PROJETO",
        ctaUrl: "/contato",
    },
    hero: {
        eyebrow: "STUDIO TABI — DIGITAL STUDIO",
        titleLines: ["TRANSFORMAMOS", "A SUA MARCA", "EM EXPERIÊNCIA"],
        highlight: "DIGITAL.",
        description: "Design, estratégia e desenvolvimento para transformar presença digital em percepção de valor, confiança e decisão.",
        ctaPrimary: { label: "VER PORTFÓLIO", url: "/projetos" },
        ctaSecondary: { label: "FALAR COM A EQUIPE", url: "/contato" },
    },
    projectsCta: { label: "VER PORTFÓLIO", url: "/projetos" },
    thankYou: {
        title: "OBRIGADO",
        message: "Recebemos a sua mensagem. Nossa equipe entra em contato em até 1 dia útil. Toda grande jornada — tabi — começa com um primeiro passo.",
    },
    contact: {
        title: "VAMOS",
        highlight: "CONVERSAR.",
        description: "Conte um pouco sobre o seu projeto. Respondemos em até 1 dia útil.",
    },
    process: [
        { title: "Diagnóstico", slug: "diagnostico", icon: "diagnostico", summary: "Mergulhamos no negócio, no mercado e nos objetivos para entender onde você está e onde precisa chegar." },
        { title: "Narrativa", slug: "narrativa", icon: "narrativa", summary: "Definimos a história e o posicionamento da marca — a mensagem que guia cada decisão." },
        { title: "Design", slug: "design", icon: "design", summary: "Traduzimos a estratégia em identidade e interface, com intenção, hierarquia e propósito." },
        { title: "Desenvolvimento", slug: "desenvolvimento", icon: "desenvolvimento", summary: "Construímos com código limpo, rápido e escalável — da ideia ao ar, pronto para crescer." },
    ],
    sections: {
        about: { eyebrow: "STUDIO TABI — SOBRE NÓS", pillarsLabel: "COMO TRABALHAMOS", ctaLabel: "CONHEÇA NOSSA HISTÓRIA", ctaUrl: "/sobre" },
        services: { eyebrow: "STUDIO TABI — SERVIÇOS", ctaLabel: "VER TODOS OS SERVIÇOS", ctaUrl: "/servicos" },
        projects: { eyebrow: "STUDIO TABI — PROJETOS", note: "120+ projetos entregues", cardText: "Quer ver o portfólio completo com todos os nossos projetos?" },
        blog: { eyebrow: "STUDIO TABI — INSIGHTS", title: "DO NOSSO", highlight: "DIÁRIO.", note: "Ideias sobre design, estratégia e tecnologia — direto de quem constrói.", ctaLabel: "VER TODOS OS ARTIGOS" },
        faq: { eyebrow: "STUDIO TABI — FAQ", note: "Não encontrou o que procura? Entre em contato diretamente com a equipe.", ctaLabel: "FALAR COM A EQUIPE", ctaUrl: "/contato" },
    },
    about: {
        paragraph1: 'O Studio Tabi nasceu da convicção de que presença digital é um ativo estratégico — não uma despesa de comunicação. Reunimos designers, estrategistas e engenheiros que recusam o medíocre do "bom o suficiente".',
        paragraph2: 'Cada projeto começa com uma pergunta simples: como esse negócio quer ser percebido daqui a cinco anos? A resposta guia cada decisão criativa, técnica e estratégica que tomamos.',
        stats: [
            { numeric: 7, suffix: "+", label: "ANOS DE MERCADO" },
            { numeric: 120, suffix: "+", label: "PROJETOS ENTREGUES" },
            { numeric: 98, suffix: "%", label: "TAXA DE RETENÇÃO" },
            { numeric: 3, suffix: "×", label: "RETORNO MÉDIO EM 12M" },
        ],
        pillars: [
            { title: "Diagnóstico", body: "Mergulhamos no negócio, no mercado e nos objetivos. Antes de qualquer pixel, entendemos onde você está e onde precisa chegar." },
            { title: "Narrativa", body: "Definimos a história e o posicionamento da marca — a mensagem que guia cada decisão de design, conteúdo e produto." },
            { title: "Design", body: "Traduzimos a estratégia em identidade e interface. Cada tela, cor e movimento com intenção, hierarquia e propósito." },
            { title: "Desenvolvimento", body: "Construímos com código limpo, rápido e escalável. Da ideia ao ar, sem dívida técnica e pronto para crescer." },
        ],
    },
    services: [
        {
            num: "01", slug: "branding-identidade-visual", title: "Branding & Identidade Visual",
            body: "Sistemas de marca que comunicam com precisão — do logotipo ao tom de voz. Identidades que crescem com o negócio e resistem ao tempo.",
            content: "<p>Marca não é logotipo — é a soma de todas as percepções que as pessoas têm do seu negócio. Construímos sistemas de identidade completos: logotipo, paleta, tipografia, grafismos, aplicações e um guia de uso que mantém tudo coerente onde quer que a marca apareça.</p><p>Começamos entendendo o posicionamento e a personalidade da marca, para que cada decisão visual tenha razão de existir. O resultado é uma identidade que transmite intenção, diferencia da concorrência e continua fazendo sentido daqui a cinco anos.</p><ul><li>Naming e verbal branding (tom de voz)</li><li>Logotipo, símbolo e variações</li><li>Sistema visual: cores, tipografia e grafismos</li><li>Manual de marca e kit de aplicação</li></ul>",
        },
        {
            num: "02", slug: "design-de-interface-ui-ux", title: "Design de Interface (UI/UX)",
            body: "Interfaces construídas a partir do comportamento real do usuário. Cada pixel tem função. Cada fluxo tem intenção.",
            content: "<p>Interface boa é invisível: o usuário chega onde quer sem perceber o esforço por trás. Desenhamos produtos e sites partindo da pesquisa — quem usa, o que precisa e onde trava — e traduzimos isso em fluxos claros, hierarquia visual e micro-interações que guiam a decisão.</p><p>Entregamos do wireframe ao design system documentado, prontos para o time de desenvolvimento. Cada tela é pensada para reduzir atrito e aumentar conversão, sem abrir mão da estética.</p><ul><li>UX research e arquitetura de informação</li><li>Wireframes e protótipos navegáveis</li><li>UI design e design system</li><li>Testes de usabilidade</li></ul>",
        },
        {
            num: "03", slug: "websites-imersivos", title: "Websites Imersivos",
            body: "Sites institucionais com direção visual forte, navegação fluida, responsividade e animações.",
            content: "<p>Um site institucional é o cartão de visita mais importante da marca. Criamos experiências imersivas, com direção de arte forte, animações de scroll e transições que transformam a navegação em algo memorável — sem sacrificar performance nem acessibilidade.</p><p>Cada projeto é responsivo de verdade, rápido no celular e construído para ser encontrado no Google. Você recebe um site que impressiona e que também trabalha pelo seu negócio.</p><ul><li>Direção de arte e storytelling visual</li><li>Animações de scroll e micro-interações</li><li>Responsividade e performance (Core Web Vitals)</li><li>SEO técnico e integração com CMS</li></ul>",
        },
        {
            num: "04", slug: "landing-pages-conversivas", title: "Landing Pages Conversivas",
            body: "Copy persuasiva, estrutura de oferta e interface pensada para campanhas, tráfego pago e captação de leads.",
            content: "<p>Uma landing page tem um único objetivo: converter. Estruturamos cada seção — headline, prova social, oferta, objeções e CTA — para conduzir o visitante até a ação, seja um lead, uma venda ou um agendamento.</p><p>Unimos copy persuasiva, design orientado a conversão e testes A/B para extrair o máximo do seu investimento em tráfego pago. Páginas que carregam rápido e convertem mais.</p><ul><li>Copywriting e estrutura de oferta</li><li>Design focado em conversão</li><li>Integração com formulários, pixel e analytics</li><li>Testes A/B e otimização contínua</li></ul>",
        },
        {
            num: "05", slug: "motion-para-sites", title: "Motion para Sites",
            body: "Movimento que conta histórias. Animações de interface e motion graphics que transformam conteúdo em experiência.",
            content: "<p>Movimento é linguagem. Uma animação bem colocada guia o olhar, explica uma ideia e dá personalidade à marca. Produzimos motion para interfaces — transições, hover, scroll, loaders — e motion graphics para comunicação, sempre com propósito e performance.</p><p>Nada de animação por enfeite: cada movimento tem função, respeita quem prefere menos animação e roda liso em qualquer dispositivo.</p><ul><li>Animações de interface e transições</li><li>Motion graphics e vídeos curtos</li><li>Animações de scroll e storytelling</li><li>Otimização de performance e acessibilidade</li></ul>",
        },
        {
            num: "06", slug: "conteudo-copywriting", title: "Conteúdo & Copywriting",
            body: "Palavras que convertem. Narrativas que constroem autoridade, geram confiança e movem o usuário à ação.",
            content: "<p>Design chama a atenção; a palavra certa fecha o negócio. Desenvolvemos a voz da sua marca e produzimos conteúdo que constrói autoridade, gera confiança e move o usuário à ação — do texto de um botão ao artigo que posiciona você como referência.</p><p>Trabalhamos copy de site, campanhas, e-mail e redes sociais, sempre alinhados à estratégia e ao tom de voz da marca.</p><ul><li>Definição de tom de voz e mensagens-chave</li><li>Copy de sites, landing pages e campanhas</li><li>Conteúdo para blog e redes sociais</li><li>Revisão e consistência editorial</li></ul>",
        },
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
        ctaTitle: "Vamos construir a sua",
        ctaHighlight: "presença digital.",
        ctaLabel: "INICIAR PROJETO",
        ctaUrl: "/contato",
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
};
const DEFAULT_CONTENT_EN: SiteContent = {
    ...DEFAULT_CONTENT,
    site: {
        ...DEFAULT_CONTENT.site,
        metaDescription: "Studio Tabi — design, strategy and engineering that turn digital presence into value, trust and decision.",
        tagline: "Design and technology that take brands where they need to go.",
    },
    nav: {
        brand: "STUDIO TABI",
        links: [
            { label: "WORK", url: "/en/work" },
            { label: "SERVICES", url: "/en/services" },
            { label: "ABOUT", url: "/en/about" },
            { label: "BLOG", url: "/en/blog" },
            { label: "CONTACT", url: "/en/contact" },
        ],
        ctaLabel: "START A PROJECT",
        ctaUrl: "/en/contact",
    },
    hero: {
        eyebrow: "STUDIO TABI — DIGITAL STUDIO",
        titleLines: ["WE TURN", "YOUR BRAND", "INTO EXPERIENCE"],
        highlight: "DIGITAL.",
        description: "Design, strategy and engineering that turn digital presence into perceived value, trust and decision.",
        ctaPrimary: { label: "VIEW PORTFOLIO", url: "/en/work" },
        ctaSecondary: { label: "TALK TO THE TEAM", url: "/en/contact" },
    },
    projectsCta: { label: "VIEW PORTFOLIO", url: "/en/work" },
    thankYou: {
        title: "THANK YOU",
        message: "We received your message. Our team will be in touch within one business day. Every great journey — tabi — begins with a first step.",
    },
    contact: {
        title: "LET'S",
        highlight: "TALK.",
        description: "Tell us a bit about your project. We reply within one business day.",
    },
    process: [
        { title: "Diagnosis", slug: "diagnostico", icon: "diagnostico", summary: "We dive into the business, the market and the goals to understand where you are and where you need to go." },
        { title: "Narrative", slug: "narrativa", icon: "narrativa", summary: "We define the brand story and positioning — the message that guides every decision." },
        { title: "Design", slug: "design", icon: "design", summary: "We translate strategy into identity and interface, with intent, hierarchy and purpose." },
        { title: "Engineering", slug: "desenvolvimento", icon: "desenvolvimento", summary: "We build with clean, fast, scalable code — from idea to launch, ready to grow." },
    ],
    sections: {
        about: { eyebrow: "STUDIO TABI — ABOUT US", pillarsLabel: "HOW WE WORK", ctaLabel: "READ OUR STORY", ctaUrl: "/en/about" },
        services: { eyebrow: "STUDIO TABI — SERVICES", ctaLabel: "VIEW ALL SERVICES", ctaUrl: "/en/services" },
        projects: { eyebrow: "STUDIO TABI — WORK", note: "120+ projects delivered", cardText: "Want to see the full portfolio with all of our work?" },
        blog: { eyebrow: "STUDIO TABI — INSIGHTS", title: "FROM OUR", highlight: "JOURNAL.", note: "Ideas on design, strategy and technology — straight from the people building it.", ctaLabel: "VIEW ALL ARTICLES" },
        faq: { eyebrow: "STUDIO TABI — FAQ", note: "Didn't find what you were looking for? Talk to the team directly.", ctaLabel: "TALK TO THE TEAM", ctaUrl: "/en/contact" },
    },
    about: {
        ...DEFAULT_CONTENT.about,
        paragraph1: 'Studio Tabi was born from the conviction that digital presence is a strategic asset — not a communications expense. We bring together designers, strategists and engineers who refuse the mediocrity of "good enough".',
        paragraph2: "Every project starts with a simple question: how does this business want to be perceived five years from now? The answer guides every creative, technical and strategic decision we make.",
        stats: [
            { numeric: 7, suffix: "+", label: "YEARS IN THE MARKET" },
            { numeric: 120, suffix: "+", label: "PROJECTS DELIVERED" },
            { numeric: 98, suffix: "%", label: "RETENTION RATE" },
            { numeric: 3, suffix: "×", label: "AVERAGE 12-MONTH RETURN" },
        ],
    },
    footer: {
        ...DEFAULT_CONTENT.footer,
        tagline: "Design and technology that take brands where they need to go.",
        ctaTitle: "Let's build your",
        ctaHighlight: "digital presence.",
        ctaLabel: "START A PROJECT",
        ctaUrl: "/en/contact",
        columns: [
            {
                title: "Navigation",
                links: [
                    { label: "Work", url: "/en/work" },
                    { label: "Services", url: "/en/services" },
                    { label: "About", url: "/en/about" },
                    { label: "Contact", url: "/en/contact" },
                ],
            },
            { ...DEFAULT_CONTENT.footer.columns[1], title: "Services" },
        ],
        contactTitle: "Contact",
        city: "São Paulo, Brazil",
        socialTitle: "Social",
        copyright: "© 2026 Studio Tabi. All rights reserved.",
        madeIn: "Crafted with precision in São Paulo",
        legal: [
            { label: "Privacy Policy", url: "#" },
            { label: "Terms of Use", url: "#" },
        ],
    },
};
export function defaultContent(lang: "pt" | "en" = "pt"): SiteContent {
    return lang === "en" ? DEFAULT_CONTENT_EN : DEFAULT_CONTENT;
}
const FALLBACK_WP_API = "https://cms.studiotabi.com.br";
declare global {
    interface Window {
        __STUDIO_TABI_API__?: string;
    }
}
function resolveApiBase(): string {
    const runtime = typeof window !== "undefined" ? window.__STUDIO_TABI_API__ : undefined;
    const build = import.meta.env.VITE_WP_API as string | undefined;
    const resolved = (runtime || build || FALLBACK_WP_API).trim();
    if (!runtime && typeof console !== "undefined") {
        console.info(`[Studio Tabi] config.js não definiu a URL do WordPress; usando o padrão ${FALLBACK_WP_API}. ` +
            "Se o endereço do CMS for outro, atualize o config.js no servidor.");
    }
    return resolved.replace(/\/$/, "");
}
export const WP_API: string = resolveApiBase();
const CONTENT_ENDPOINT = "/wp-json/studio-tabi/v1/content";
export const WP_ADMIN_URL: string = WP_API ? `${WP_API}/wp-admin/` : "/wp-admin/";
function mergeContent(remote: Partial<SiteContent> | null | undefined, lang: "pt" | "en" = "pt"): SiteContent {
    const base = defaultContent(lang);
    if (!remote)
        return base;
    return {
        site: { ...base.site, ...(remote.site || {}) },
        nav: { ...base.nav, ...(remote.nav || {}) },
        hero: { ...base.hero, ...(remote.hero || {}) },
        projectsCta: { ...base.projectsCta, ...(remote.projectsCta || {}) },
        thankYou: { ...base.thankYou, ...(remote.thankYou || {}) },
        contact: { ...base.contact, ...(remote.contact || {}) },
        process: remote.process?.length ? remote.process : base.process,
        sections: remote.sections ? {
            about: { ...base.sections.about, ...(remote.sections.about || {}) },
            services: { ...base.sections.services, ...(remote.sections.services || {}) },
            projects: { ...base.sections.projects, ...(remote.sections.projects || {}) },
            blog: { ...base.sections.blog, ...(remote.sections.blog || {}) },
            faq: { ...base.sections.faq, ...(remote.sections.faq || {}) },
        } : base.sections,
        about: { ...base.about, ...(remote.about || {}) },
        services: remote.services?.length ? remote.services : base.services,
        projects: remote.projects?.length ? remote.projects : base.projects,
        faq: remote.faq?.length ? remote.faq : base.faq,
        footer: { ...base.footer, ...(remote.footer || {}) },
        pages: remote.pages || [],
    };
}
export async function fetchContent(lang: "pt" | "en" = "pt"): Promise<SiteContent> {
    if (!WP_API) {
        console.warn("[Studio Tabi] MODO OFFLINE: window.__STUDIO_TABI_API__ está vazio em config.js. " +
            "O site está mostrando o conteúdo padrão embutido e NÃO o conteúdo do CMS. " +
            'Defina a URL do WordPress em config.js, ex.: window.__STUDIO_TABI_API__ = "https://cms.studiotabi.com.br";');
        return defaultContent(lang);
    }
    try {
        const res = await fetch(`${WP_API}${CONTENT_ENDPOINT}${lang === "en" ? "?lang=en" : ""}`, { headers: { Accept: "application/json" } });
        if (!res.ok)
            throw new Error(`HTTP ${res.status}`);
        const data = (await res.json()) as Partial<SiteContent>;
        return mergeContent(data, lang);
    }
    catch (err) {
        console.warn(`[Studio Tabi] Falha ao carregar o conteúdo do WordPress em ${WP_API}${CONTENT_ENDPOINT} — ` +
            "usando o conteúdo padrão. Verifique: (1) o endpoint abre no navegador e devolve JSON; " +
            "(2) o CORS permite a origem do site; (3) os permalinks do WP não estão em 'Simples'.", err);
        return defaultContent(lang);
    }
}
export interface WpPage {
    slug: string;
    title: string;
    content: string;
}
export async function fetchPage(slug: string, lang: "pt" | "en" = "pt"): Promise<WpPage | null> {
    if (!WP_API)
        return null;
    const qs = lang === "en" ? "?lang=en" : "";
    try {
        const res = await fetch(`${WP_API}/wp-json/studio-tabi/v1/page/${encodeURIComponent(slug)}${qs}`, {
            headers: { Accept: "application/json" },
        });
        if (!res.ok)
            return null;
        return (await res.json()) as WpPage;
    }
    catch {
        return null;
    }
}
export interface ContactPayload {
    name: string;
    email: string;
    subject: string;
    message: string;
    website?: string;
    recaptchaToken?: string;
}
export interface ContactResult {
    ok: boolean;
    message: string;
}
export async function submitContact(payload: ContactPayload): Promise<ContactResult> {
    if (!WP_API) {
        return { ok: false, message: "Formulário indisponível: configure o endereço do WordPress em config.js." };
    }
    try {
        const res = await fetch(`${WP_API}/wp-json/studio-tabi/v1/contact`, {
            method: "POST",
            headers: { "Content-Type": "application/json", Accept: "application/json" },
            body: JSON.stringify(payload),
        });
        const data = (await res.json().catch(() => ({}))) as Partial<ContactResult>;
        if (!res.ok || !data.ok) {
            return { ok: false, message: data.message || "Não foi possível enviar. Tente novamente em instantes." };
        }
        return { ok: true, message: data.message || "Mensagem enviada! Em breve entraremos em contato." };
    }
    catch {
        return { ok: false, message: "Falha de conexão. Verifique sua internet e tente novamente." };
    }
}
export interface ContentContextValue {
    content: SiteContent;
    loading: boolean;
}
export const ContentContext = createContext<ContentContextValue>({
    content: DEFAULT_CONTENT,
    loading: false,
});
export function useContent() {
    return useContext(ContentContext);
}
