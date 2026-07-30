import type { Locale } from "./locale";

export const DICT = {
    "nav.abrir": { pt: "Abrir menu", en: "Open menu" },
    "nav.fechar": { pt: "Fechar menu", en: "Close menu" },
    "nav.cabecalho": { pt: "Cabeçalho principal", en: "Main header" },
    "nav.menu": { pt: "Menu principal", en: "Main menu" },
    "nav.pular": { pt: "Pular para o conteúdo", en: "Skip to content" },
    "nav.idioma": { pt: "Mudar idioma", en: "Change language" },

    "geral.home": { pt: "Home", en: "Home" },
    "geral.carregando": { pt: "Carregando…", en: "Loading…" },
    "geral.voltar": { pt: "Voltar", en: "Back" },
    "geral.trilha": { pt: "Trilha de navegação", en: "Breadcrumb" },

    "blog.titulo": { pt: "Blog", en: "Blog" },
    "blog.buscar": { pt: "Buscar…", en: "Search…" },
    "blog.vazio": { pt: "Ainda não há artigos publicados.", en: "No articles published yet." },
    "blog.semResultado": { pt: "Nenhum artigo encontrado.", en: "No articles found." },
    "blog.leitura": { pt: "min de leitura", en: "min read" },
    "blog.por": { pt: "por", en: "by" },
    "blog.neste": { pt: "Neste artigo", en: "In this article" },
    "blog.relacionados": { pt: "Artigos relacionados", en: "Related articles" },
    "blog.compartilhar": { pt: "Compartilhar", en: "Share" },
    "blog.copiar": { pt: "Copiar link", en: "Copy link" },
    "blog.copiado": { pt: "Copiado!", en: "Copied!" },
    "blog.naoEncontrado": { pt: "Artigo não encontrado", en: "Article not found" },
    "blog.voltarBlog": { pt: "← Voltar ao blog", en: "← Back to blog" },
    "blog.todas": { pt: "Todas", en: "All" },
    "blog.anterior": { pt: "Anterior", en: "Previous" },
    "blog.proxima": { pt: "Próxima", en: "Next" },

    "news.titulo": { pt: "Receba no seu e-mail", en: "Get it in your inbox" },
    "news.texto": { pt: "Novos artigos sobre design, estratégia e tecnologia. Sem spam.", en: "New articles on design, strategy and technology. No spam." },
    "news.email": { pt: "E-mail para a newsletter", en: "Newsletter email" },
    "news.inscrever": { pt: "Inscrever", en: "Subscribe" },
    "news.enviando": { pt: "Enviando…", en: "Sending…" },

    "form.nome": { pt: "Nome", en: "Name" },
    "form.email": { pt: "E-mail", en: "Email" },
    "form.assunto": { pt: "Assunto", en: "Subject" },
    "form.mensagem": { pt: "Mensagem", en: "Message" },
    "form.seuNome": { pt: "Seu nome", en: "Your name" },
    "form.sobreOQue": { pt: "Sobre o que quer falar?", en: "What would you like to discuss?" },
    "form.conte": { pt: "Conte sobre o seu projeto…", en: "Tell us about your project…" },
    "form.enviar": { pt: "ENVIAR MENSAGEM", en: "SEND MESSAGE" },
    "form.enviando": { pt: "ENVIANDO…", en: "SENDING…" },
    "form.telefone": { pt: "Telefone", en: "Phone" },
    "form.local": { pt: "Localização", en: "Location" },
    "form.recaptcha": { pt: "Protegido por reCAPTCHA — aplicam-se a", en: "Protected by reCAPTCHA — the Google" },
    "form.privacidade": { pt: "Política de Privacidade", en: "Privacy Policy" },
    "form.termos": { pt: "Termos de Serviço", en: "Terms of Service" },
    "form.doGoogle": { pt: "do Google.", en: "apply." },

    "servico.rotulo": { pt: "SERVIÇO", en: "SERVICE" },
    "servico.naoEncontrado": { pt: "Serviço não", en: "Service not" },
    "servico.encontrado": { pt: "encontrado", en: "found" },
    "servico.orcamento": { pt: "Solicitar orçamento", en: "Request a quote" },
    "servico.proximo": { pt: "Próximo:", en: "Next:" },
    "servico.todos": { pt: "Ver todos os serviços →", en: "See all services →" },

    "processo.etapa": { pt: "Etapa", en: "Step" },
    "processo.comoTrabalhamos": { pt: "Como Trabalhamos", en: "How We Work" },
    "processo.iniciar": { pt: "Iniciar projeto", en: "Start a project" },
    "processo.emBreve": { pt: "Conteúdo detalhado em breve.", en: "Detailed content coming soon." },
    "processo.naoEncontrada": { pt: "Etapa não", en: "Step not" },
    "processo.verProcesso": { pt: "← Ver o processo", en: "← See the process" },

    "erro.404": { pt: "Página não encontrada", en: "Page not found" },
    "erro.voltarHome": { pt: "← Voltar para a home", en: "← Back to home" },
} as const;

export type ChaveTexto = keyof typeof DICT;

export function traduzir(locale: Locale, chave: ChaveTexto): string {
    const entrada = DICT[chave];
    return entrada ? entrada[locale] : chave;
}
