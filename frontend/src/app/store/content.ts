import { createContext, useContext } from "react";
import { traduzir } from "../i18n/dicionario";
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
        newsletterTitle: string;
        newsletterText: string;
        newsletterButton: string;
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
        metaDescription: "Desenvolvemos cada projeto com intenção, cuidado e respeito à essência da marca. Criamos experiências digitais que unem estratégia, estética e tecnologia para transmitir confiança, gerar valor e criar presença.",
        tagline: "Desenvolvemos com propósito. Entregamos com honra.",
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
        titleLines: ["SUA MARCA,", "UMA EXPERIÊNCIA"],
        highlight: "DIGITAL.",
        description: "Desenvolvemos cada projeto com intenção, cuidado e respeito à essência da marca. Criamos experiências digitais que unem estratégia, estética e tecnologia para transmitir confiança, gerar valor e criar presença.",
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
        paragraph1: "Desenvolvemos cada projeto com intenção, cuidado e profundo respeito à essência de cada marca, valorizando sua identidade, sua história e seus diferenciais em cada detalhe do processo.",
        paragraph2: "Criamos experiências digitais que unem estratégia, estética e tecnologia para transmitir confiança, gerar valor real e construir uma presença marcante, consistente e duradoura.",
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
            num: "03", slug: "desenvolvimento-web", title: "Websites Imersivos",
            body: "Sites institucionais com direção visual forte, navegação fluida, responsividade e animações GSAP.",
            content: "<p>Um site institucional é o cartão de visita mais importante da marca. Criamos experiências imersivas, com direção de arte forte, animações de scroll e transições que transformam a navegação em algo memorável — sem sacrificar performance nem acessibilidade.</p><p>Cada projeto é responsivo de verdade, rápido no celular e construído para ser encontrado no Google. Você recebe um site que impressiona e que também trabalha pelo seu negócio.</p><ul><li>Direção de arte e storytelling visual</li><li>Animações de scroll e micro-interações</li><li>Responsividade e performance (Core Web Vitals)</li><li>SEO técnico e integração com CMS</li></ul>",
        },
        {
            num: "04", slug: "estrategia-digital", title: "Landing pages conversivas",
            body: "Copy persuasiva, estrutura de oferta e interface pensada para campanhas, tráfego pago e captação de leads.",
            content: "<p>Uma landing page tem um único objetivo: converter. Estruturamos cada seção — headline, prova social, oferta, objeções e CTA — para conduzir o visitante até a ação, seja um lead, uma venda ou um agendamento.</p><p>Unimos copy persuasiva, design orientado a conversão e testes A/B para extrair o máximo do seu investimento em tráfego pago. Páginas que carregam rápido e convertem mais.</p><ul><li>Copywriting e estrutura de oferta</li><li>Design focado em conversão</li><li>Integração com formulários, pixel e analytics</li><li>Testes A/B e otimização contínua</li></ul>",
        },
        {
            num: "05", slug: "motion-animacao", title: "Motion para sites",
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
            id: "01", name: "MCCO - Construtora em Ipatinga", category: "Site", year: "2025",
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
            id: "02", name: "Arbor craft", category: "E-commerce", year: "2025",
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
            id: "03", name: "Modu", category: "E-Learning App", year: "2026",
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
            id: "04", name: "Andrea & Suzy", category: "Landing page", year: "2026",
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
        tagline: "Desenvolvemos com propósito. Entregamos com honra.",
        ctaTitle: "Vamos construir a sua",
        ctaHighlight: "presença digital.",
        ctaLabel: "INICIAR PROJETO",
        ctaUrl: "/contato",
        columns: [
            {
                title: "Navegação",
                links: [
                    { label: "Trabalhos", url: "/projetos" },
                    { label: "Serviços", url: "/servicos" },
                    { label: "Sobre", url: "/sobre" },
                    { label: "Contato", url: "/contato" },
                ],
            },
            {
                title: "Serviços",
                links: [
                    { label: "Branding", url: "/servicos/branding-identidade-visual" },
                    { label: "UI / UX Design", url: "/servicos/design-de-interface-ui-ux" },
                    { label: "Desenvolvimento Web", url: "/servicos/desenvolvimento-web" },
                    { label: "Estratégia Digital", url: "/servicos/estrategia-digital" },
                    { label: "Motion & Animação", url: "/servicos/motion-animacao" },
                ],
            },
        ],
        newsletterTitle: "Newsletter",
        newsletterText: "E-mail para a newsletter",
        newsletterButton: "Inscrever",
        contactTitle: "Contato",
        email: "contato@studiotabi.com.br",
        phone: "",
        city: "Rio de Janeiro, RJ",
        socialTitle: "Social",
        social: [
            { label: "Instagram", url: "https://www.instagram.com/tabi.std/" },
            { label: "Behance", url: "https://www.behance.net/tabistudio" },
            { label: "X", url: "https://x.com/tabistd" },
            { label: "Threads", url: "https://www.threads.com/@tabi.std" },
            { label: "Linkedin", url: "https://www.linkedin.com/company/tabi-std/?viewAsMember=true" },
            { label: "Facebook", url: "https://www.facebook.com/profile.php?id=61577186025731&locale=pt_BR" },
        ],
        copyright: "© 2026 Studio Tabi. Todos os direitos reservados.",
        madeIn: "Feito com precisão no Rio de Janeiro",
        legal: [
            { label: "Política de Privacidade", url: "#" },
            { label: "Termos de Uso", url: "#" },
        ],
    },
    pages: [],
};
type DetalheProjeto = SiteContent["projects"][number]["detail"];
const PROJECT_DETAIL_EN: Partial<DetalheProjeto>[] = [
    {
        scope: ["Visual Identity", "UI/UX Design", "Design System"], duration: "14 weeks",
        challenge: "Nuvem Finance came to us as yet another generic fintech in a saturated market — corporate blue palette, cold language, zero differentiation. The challenge was to create an identity that conveyed solidity without losing human warmth, and an interface that made complex financial concepts accessible to the end user.",
        solution: "We developed a visual identity built on contrast: condensed, assertive typography balanced by generous spacing and earthy tones that suggest trust without the banking-blue cliché. The design system was built to scale with the product, with 240+ documented components and an integrated voice and tone guide.",
        results: [{ label: "Increase in conversion", value: "+38%" }, { label: "Reduction in churn", value: "−22%" }, { label: "NPS after redesign", value: "72" }, { label: "Components in the DS", value: "240+" }],
        mockupLines: ["DASHBOARD", "PORTFOLIO", "ANALYSIS", "REPORTS"],
    },
    {
        scope: ["SaaS Product", "UX Research", "Prototyping", "Front-end Dev"], duration: "22 weeks",
        challenge: "FlowDesk had a solid product idea but the initial MVP had a very steep learning curve. First-week drop-off was 67%. We needed to rebuild the experience from scratch without losing the existing users.",
        solution: "We ran 18 in-depth interviews with real users and mapped the main friction points. We redesigned onboarding with a progressive disclosure approach. The new information architecture cut the critical paths from 7 clicks to 3.",
        results: [{ label: "Drop-off reduction", value: "−51%" }, { label: "Average time in app", value: "+2.4×" }, { label: "Monthly active users", value: "12k+" }, { label: "App Store rating", value: "4.8★" }],
        mockupLines: ["PROJECTS", "TASKS", "TEAM", "REPORT"],
    },
    {
        scope: ["E-commerce", "UI Design", "Motion Design"], duration: "10 weeks",
        challenge: "A Brazilian luxury fashion brand with its own atelier, but a digital presence completely at odds with its premium positioning. The previous site looked like a department store, not a maison.",
        solution: "We created an editorial experience inspired by the great European maisons: fullscreen photography, serif typography with generous white space, and micro-interactions that reinforce the sense of exclusivity. Checkout was simplified to 2 steps.",
        results: [{ label: "Increase in average order", value: "+29%" }, { label: "Conversion rate", value: "+44%" }, { label: "Time on product page", value: "+3.1min" }, { label: "Returning customers", value: "+61%" }],
        mockupLines: ["COLLECTION", "ATELIER", "PIECES", "CONTACT"],
    },
    {
        scope: ["Mobile UI", "iOS & Android", "Illustration"], duration: "8 weeks",
        challenge: "The Vitalize health and wellbeing app faced a paradox: users loved the concept but found the app 'heavy' and 'intimidating'. The previous design used clinical green and medical iconography that pushed away the very audience it targeted.",
        solution: "We redesigned it around 'health as a lifestyle': an organic palette, hand-drawn illustrations that humanise the data, and a gamified progress system that celebrates small wins.",
        results: [{ label: "Downloads in the first month", value: "48k" }, { label: "30-day retention", value: "71%" }, { label: "Store rating", value: "4.9★" }, { label: "Organic mentions", value: "+180%" }],
        mockupLines: ["HOME", "WORKOUTS", "NUTRITION", "PROGRESS"],
    },
];
const SERVICES_EN: SiteContent["services"] = [

        {
            num: "01", slug: "branding-identidade-visual", title: "Branding & Visual Identity",
            body: "Brand systems that communicate with precision — from the logo to the tone of voice. Identities that grow with the business and stand the test of time.",
            content: "<p>A brand is not a logo — it is the sum of every perception people hold of your business. We build complete identity systems: logo, palette, typography, graphic elements, applications and a usage guide that keeps everything coherent wherever the brand shows up.</p><p>We start by understanding the brand's positioning and personality, so every visual decision has a reason to exist. The result is an identity that conveys intent, sets you apart from the competition and still makes sense five years from now.</p><ul><li>Naming and verbal branding (tone of voice)</li><li>Logo, symbol and variations</li><li>Visual system: colour, typography and graphic elements</li><li>Brand manual and application kit</li></ul>",
        },
        {
            num: "02", slug: "design-de-interface-ui-ux", title: "Interface Design (UI/UX)",
            body: "Interfaces built around real user behavior. Every pixel has a purpose. Every flow has intention.",
            content: "<p>A good interface is invisible: users get where they want without noticing the effort behind it. We design products and websites starting from research — who uses it, what they need and where they get stuck — and translate that into clear flows, visual hierarchy and micro-interactions that guide the decision.</p><p>We deliver everything from wireframes to a documented design system, ready for the development team. Every screen is built to reduce friction and increase conversion, without giving up on aesthetics.</p><ul><li>UX research and information architecture</li><li>Wireframes and clickable prototypes</li><li>UI design and design system</li><li>Usability testing</li></ul>",
        },
        {
            num: "03", slug: "desenvolvimento-web", title: "Immersive Websites",
            body: "Institutional websites with strong visual direction, seamless navigation, responsive design, and GSAP-powered animations.",
            content: "<p>A company website is the brand's most important calling card. We create immersive experiences, with strong art direction, scroll animations and transitions that turn browsing into something memorable — without sacrificing performance or accessibility.</p><p>Every project is genuinely responsive, fast on mobile and built to be found on Google. You get a site that impresses and that also works for your business.</p><ul><li>Art direction and visual storytelling</li><li>Scroll animations and micro-interactions</li><li>Responsiveness and performance (Core Web Vitals)</li><li>Technical SEO and CMS integration</li></ul>",
        },
        {
            num: "04", slug: "estrategia-digital", title: "High-converting landing pages",
            body: "Persuasive copy, offer structure, and interfaces designed for campaigns, paid traffic, and lead generation.",
            content: "<p>A landing page has a single goal: to convert. We structure every section — headline, social proof, offer, objections and CTA — to carry the visitor through to the action, be it a lead, a sale or a booking.</p><p>We combine persuasive copy, conversion-driven design and A/B testing to get the most out of your paid traffic investment. Pages that load fast and convert more.</p><ul><li>Copywriting and offer structure</li><li>Conversion-focused design</li><li>Integration with forms, pixel and analytics</li><li>A/B testing and continuous optimisation</li></ul>",
        },
        {
            num: "05", slug: "motion-animacao", title: "Motion Design for Websites",
            body: "Motion that tells stories. Interface animations and motion graphics that transform content into experiences.",
            content: "<p>Movement is a language. A well-placed animation guides the eye, explains an idea and gives the brand personality. We produce motion for interfaces — transitions, hover, scroll, loaders — and motion graphics for communication, always with purpose and performance in mind.</p><p>No animation for decoration's sake: every movement has a function, respects people who prefer reduced motion and runs smoothly on any device.</p><ul><li>Interface animation and transitions</li><li>Motion graphics and short videos</li><li>Scroll animation and storytelling</li><li>Performance and accessibility optimisation</li></ul>",
        },
        {
            num: "06", slug: "conteudo-copywriting", title: "Content & Copywriting",
            body: "Words that convert. Narratives that build authority, earn trust and move the user to act.",
            content: "<p>Design gets attention; the right words close the deal. We develop your brand's voice and produce content that builds authority, earns trust and moves the user to act — from the text on a button to the article that positions you as a reference.</p><p>We work on website, campaign, email and social copy, always aligned with the brand's strategy and tone of voice.</p><ul><li>Tone of voice and key messaging</li><li>Copy for websites, landing pages and campaigns</li><li>Blog and social media content</li><li>Editing and editorial consistency</li></ul>",
        },
    ];
const FAQ_EN: SiteContent["faq"] = [

        { q: "How does the process work?", a: "We start with an in-depth diagnosis of the business, the market and the goals. Then we build a clear roadmap with deliverables, deadlines and approval milestones. We work in short sprints with weekly checkpoints to keep everyone aligned — no surprises at the end." },
        { q: "How long does a project take?", a: "It depends on scope. A visual identity project takes 3 to 6 weeks. A full website with design and development, 6 to 12 weeks. More complex applications can take 3 to 6 months. We always present a detailed schedule before starting." },
        { q: "What size of company do you work with?", a: "We work with everyone from growth-stage startups to established companies looking to renew their digital presence. What matters is not size, but the commitment to quality and the willingness to build something that lasts." },
        { q: "How is pricing structured?", a: "We work with fixed-scope projects (scope and price agreed upfront) or a monthly retainer for companies that need an ongoing partner. We don't charge by the hour — we charge for the outcome. The quote is presented transparently, with no hidden fees." },
        { q: "Do you offer support after launch?", a: "Yes. Every project includes a 30-day warranty period after launch. For clients who want ongoing support, we offer monthly maintenance plans covering updates, monitoring and product evolution." },
        { q: "How do I start working with you?", a: "Fill in the contact form or send us an email with a brief outline of your project. We'll schedule a free 30-minute diagnosis call to understand your needs and check whether we're the right partner for you." },
    ];
const DEFAULT_CONTENT_EN: SiteContent = {
    ...DEFAULT_CONTENT,
    site: {
        ...DEFAULT_CONTENT.site,
        metaDescription: "We build every project with intention, care, and respect for the brand's essence. We create digital experiences that blend strategy, aesthetics, and technology to build trust, generate value, and establish presence.",
        tagline: "We develop with honor, we deliver with purpose.",
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
        titleLines: ["YOUR BRAND,", "A DIGITAL"],
        highlight: "EXPERIENCE.",
        description: "We build every project with intention, care, and respect for the brand's essence. We create digital experiences that blend strategy, aesthetics, and technology to build trust, generate value, and establish presence.",
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
        paragraph1: "We develop every project with intention, care, and deep respect for the essence of each brand, honoring its identity, story, and unique strengths throughout every stage of the process.",
        paragraph2: "Every project starts with a simple question: how does this business want to be perceived five years from now? The answer guides every creative, technical and strategic decision we make.",
        stats: [
            { numeric: 7, suffix: "+", label: "YEARS IN THE MARKET" },
            { numeric: 120, suffix: "+", label: "PROJECTS DELIVERED" },
            { numeric: 98, suffix: "%", label: "RETENTION RATE" },
            { numeric: 3, suffix: "×", label: "AVERAGE 12-MONTH RETURN" },
        ],
    },
    services: SERVICES_EN,
    faq: FAQ_EN,
    projects: DEFAULT_CONTENT.projects.map((p, i) => ({
        ...p,
        category: ["Branding & UI", "SaaS Product", "E-commerce", "Mobile UI"][i] ?? p.category,
        detail: { ...p.detail, ...(PROJECT_DETAIL_EN[i] ?? {}) },
    })),
    footer: {
        ...DEFAULT_CONTENT.footer,
        tagline: "We develop with honor, we deliver with purpose.",
        ctaTitle: "Let's build your",
        ctaHighlight: "digital presence.",
        ctaLabel: "START A PROJECT",
        ctaUrl: "/en/contact",
        columns: [
            {
                title: "Navigation",
                links: [
                    { label: "Projects", url: "/en/work" },
                    { label: "Services", url: "/en/services" },
                    { label: "About", url: "/en/about" },
                    { label: "Contact", url: "/en/contact" },
                ],
            },
            {
                title: "Services",
                links: [
                    { label: "Branding & Visual Identity", url: "/en/services/branding-identidade-visual" },
                    { label: "Interface Design (UI/UX)", url: "/en/services/design-de-interface-ui-ux" },
                    { label: "Immersive Websites", url: "/en/services/desenvolvimento-web" },
                    { label: "High-converting landing pages", url: "/en/services/estrategia-digital" },
                    { label: "Motion Design for Websites", url: "/en/services/motion-animacao" },
                    { label: "Content & Copywriting", url: "/en/services/conteudo-copywriting" },
                ],
            },
        ],
        newsletterTitle: "Newsletter",
        newsletterText: "Newsletter email",
        newsletterButton: "Subscribe",
        contactTitle: "Contact",
        city: "Rio de Janeiro, RJ",
        socialTitle: "Social",
        copyright: "© 2026 Studio Tabi. All rights reserved.",
        madeIn: "Crafted with precision in Rio de Janeiro.",
        legal: [
            { label: "Privacy Policy", url: "/en/p/politica-de-privacidade" },
            { label: "Terms of Use", url: "/en/p/termos-de-uso" },
        ],
    },
};
export function defaultContent(lang: "pt" | "en" = "pt"): SiteContent {
    return lang === "en" ? DEFAULT_CONTENT_EN : DEFAULT_CONTENT;
}
const FALLBACK_WP_API = "";
declare global {
    interface Window {
        __STUDIO_TABI_API__?: string;
    }
}
function resolveApiBase(): string {
    const runtime = typeof window !== "undefined" ? window.__STUDIO_TABI_API__ : undefined;
    const build = import.meta.env.VITE_WP_API as string | undefined;
    const resolved = (runtime || build || FALLBACK_WP_API).trim();
    return resolved.replace(/\/$/, "");
}
export const WP_API: string = resolveApiBase();
const CONTENT_ENDPOINT = "/wp-json/studio-tabi/v1/content";
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
export async function submitContact(payload: ContactPayload, lang: "pt" | "en" = "pt"): Promise<ContactResult> {
    try {
        const res = await fetch(`${WP_API}/wp-json/studio-tabi/v1/contact?lang=${lang}`, {
            method: "POST",
            headers: { "Content-Type": "application/json", Accept: "application/json" },
            body: JSON.stringify({ ...payload, lang }),
        });
        const data = (await res.json().catch(() => ({}))) as Partial<ContactResult>;
        if (!res.ok || !data.ok) {
            return { ok: false, message: data.message || traduzir(lang, "form.naoEnviou") };
        }
        return { ok: true, message: data.message || "Mensagem enviada! Em breve entraremos em contato." };
    }
    catch {
        return { ok: false, message: traduzir(lang, "form.semConexao") };
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
