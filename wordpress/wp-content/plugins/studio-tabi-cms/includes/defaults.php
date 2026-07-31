<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function stcms_default_options() {
	return array(
		'site'   => array(
			'title'            => 'Studio Tabi',
			'meta_description' => 'Desenvolvemos cada projeto com intenção, cuidado e respeito à essência da marca. Criamos experiências digitais que unem estratégia, estética e tecnologia para transmitir confiança, gerar valor e criar presença.',
			'tagline'          => 'Desenvolvemos com propósito. Entregamos com honra.',
			'logo_id'          => 0,
			'favicon_id'       => 0,

			'ga4_id'           => '',
			'gtm_id'           => '',
			'recaptcha_site'   => '',
			'recaptcha_secret' => '',

			'form_email'       => '',
		),
		'nav'    => array(
			'brand'     => 'STUDIO TABI',
			'cta_label' => 'INICIAR PROJETO',
			'cta_url'   => '/contato',
			'links'     => array(
				array( 'label' => 'TRABALHOS', 'url' => '#trabalhos' ),
				array( 'label' => 'SERVIÇOS', 'url' => '/servicos' ),
				array( 'label' => 'SOBRE', 'url' => '/sobre' ),
				array( 'label' => 'BLOG', 'url' => '/blog' ),
				array( 'label' => 'CONTATO', 'url' => '/contato' ),
			),
		),
		'hero'   => array(
			'eyebrow'              => 'STUDIO TABI — DIGITAL STUDIO',
			'title_lines'         => array( 'SUA MARCA,', 'UMA EXPERIÊNCIA' ),
			'highlight'           => 'DIGITAL.',
			'description'         => 'Design, estratégia e desenvolvimento para transformar presença digital em percepção de valor, confiança e decisão.',
			'image_id'            => 0,
			'cta_primary_label'   => 'VER PORTFÓLIO',
			'cta_primary_url'     => '/projetos',
			'cta_secondary_label' => 'FALAR COM A EQUIPE',
			'cta_secondary_url'   => '/contato',
		),
		'projects_cta' => array(
			'label' => 'VER PORTFÓLIO',
			'url'   => '/projetos',
		),
		'thankyou' => array(
			'title'   => 'OBRIGADO',
			'message' => 'Recebemos a sua mensagem. Nossa equipe entra em contato em até 1 dia útil. Toda grande jornada — tabi — começa com um primeiro passo.',
		),
		'contact' => array(
			'title'       => 'VAMOS',
			'highlight'   => 'CONVERSAR.',
			'description' => 'Conte um pouco sobre o seu projeto. Respondemos em até 1 dia útil.',
		),

		'process' => array(
			array( 'title' => 'Diagnóstico',     'slug' => 'diagnostico',    'icon' => 'diagnostico', 'summary' => 'Mergulhamos no negócio, no mercado e nos objetivos para entender onde você está e onde precisa chegar.' ),
			array( 'title' => 'Narrativa',       'slug' => 'narrativa',      'icon' => 'narrativa',   'summary' => 'Definimos a história e o posicionamento da marca — a mensagem que guia cada decisão.' ),
			array( 'title' => 'Design',          'slug' => 'design',         'icon' => 'design',      'summary' => 'Traduzimos a estratégia em identidade e interface, com intenção, hierarquia e propósito.' ),
			array( 'title' => 'Desenvolvimento', 'slug' => 'desenvolvimento','icon' => 'desenvolvimento', 'summary' => 'Construímos com código limpo, rápido e escalável — da ideia ao ar, pronto para crescer.' ),
		),

		'sections' => array(
			'about_eyebrow'      => 'STUDIO TABI — SOBRE NÓS',
			'about_cta_label'    => 'CONHEÇA NOSSA HISTÓRIA',
			'about_cta_url'      => '/sobre',
			'about_pillars_label'=> 'COMO TRABALHAMOS',
			'services_eyebrow'   => 'STUDIO TABI — SERVIÇOS',
			'services_cta_label' => 'VER TODOS OS SERVIÇOS',
			'services_cta_url'   => '/servicos',
			'projects_eyebrow'   => 'STUDIO TABI — PROJETOS',
			'projects_note'      => '120+ projetos entregues',
			'projects_card_text' => 'Quer ver o portfólio completo com todos os nossos projetos?',
			'blog_eyebrow'       => 'STUDIO TABI — INSIGHTS',
			'blog_title'         => 'DO NOSSO',
			'blog_highlight'     => 'DIÁRIO.',
			'blog_note'          => 'Ideias sobre design, estratégia e tecnologia — direto de quem constrói.',
			'blog_cta_label'     => 'VER TODOS OS ARTIGOS',
			'faq_eyebrow'        => 'STUDIO TABI — FAQ',
			'faq_note'           => 'Não encontrou o que procura? Entre em contato diretamente com a equipe.',
			'faq_cta_label'      => 'FALAR COM A EQUIPE',
			'faq_cta_url'        => '/contato',
		),
		'about'  => array(
			'paragraph1' => 'O Studio Tabi nasceu da convicção de que presença digital é um ativo estratégico — não uma despesa de comunicação. Reunimos designers, estrategistas e engenheiros que recusam o medíocre do "bom o suficiente".',
			'paragraph2' => 'Cada projeto começa com uma pergunta simples: como esse negócio quer ser percebido daqui a cinco anos? A resposta guia cada decisão criativa, técnica e estratégica que tomamos.',
			'stats'      => array(
				array( 'numeric' => 7,   'suffix' => '+', 'label' => 'ANOS DE MERCADO' ),
				array( 'numeric' => 120, 'suffix' => '+', 'label' => 'PROJETOS ENTREGUES' ),
				array( 'numeric' => 98,  'suffix' => '%', 'label' => 'TAXA DE RETENÇÃO' ),
				array( 'numeric' => 3,   'suffix' => '×', 'label' => 'RETORNO MÉDIO EM 12M' ),
			),
			'pillars'    => array(
				array( 'title' => 'Diagnóstico',     'body' => 'Mergulhamos no negócio, no mercado e nos objetivos. Antes de qualquer pixel, entendemos onde você está e onde precisa chegar.' ),
				array( 'title' => 'Narrativa',       'body' => 'Definimos a história e o posicionamento da marca — a mensagem que guia cada decisão de design, conteúdo e produto.' ),
				array( 'title' => 'Design',          'body' => 'Traduzimos a estratégia em identidade e interface. Cada tela, cor e movimento com intenção, hierarquia e propósito.' ),
				array( 'title' => 'Desenvolvimento', 'body' => 'Construímos com código limpo, rápido e escalável. Da ideia ao ar, sem dívida técnica e pronto para crescer.' ),
			),
		),
		'footer' => array(
			'brand'         => 'STUDIO TABI',
			'tagline'       => 'Desenvolvemos com propósito. Entregamos com honra.',
			'cta_title'     => 'Vamos construir a sua',
			'cta_highlight' => 'presença digital.',
			'cta_label'     => 'INICIAR PROJETO',
			'cta_url'       => '/contato',
			'col1_title'    => 'Navegação',
			'col1_links'    => array(
				array( 'label' => 'Trabalhos', 'url' => '#trabalhos' ),
				array( 'label' => 'Serviços', 'url' => '#servicos' ),
				array( 'label' => 'Sobre', 'url' => '#sobre' ),
				array( 'label' => 'Contato', 'url' => '#contato' ),
			),
			'col2_title'    => 'Serviços',
			'col2_links'    => array(
				array( 'label' => 'Branding', 'url' => '/servicos/branding-identidade-visual' ),
				array( 'label' => 'UI / UX Design', 'url' => '/servicos/design-de-interface-ui-ux' ),
				array( 'label' => 'Desenvolvimento Web', 'url' => '/servicos/desenvolvimento-web' ),
				array( 'label' => 'Estratégia Digital', 'url' => '/servicos/estrategia-digital' ),
				array( 'label' => 'Motion & Animação', 'url' => '/servicos/motion-animacao' ),
			),
			'newsletter_title'  => 'Newsletter',
			'newsletter_text'   => 'E-mail para a newsletter',
			'newsletter_button' => 'Inscrever',
			'contact_title' => 'Contato',
			'email'         => 'contato@studiotabi.com.br',
			'phone'         => '+55 11 9 9999-9999',
			'city'          => 'Rio de Janeiro, RJ',
			'social_title'  => 'Social',
			'social'        => array(
				array( 'label' => 'Instagram', 'url' => '#' ),
				array( 'label' => 'LinkedIn', 'url' => '#' ),
				array( 'label' => 'Behance', 'url' => '#' ),
				array( 'label' => 'GitHub', 'url' => '#' ),
			),
			'copyright'     => '© 2026 Studio Tabi. Todos os direitos reservados.',
			'made_in'       => 'Feito com precisão no Rio de Janeiro',
			'legal'         => array(
				array( 'label' => 'Política de Privacidade', 'url' => '#' ),
				array( 'label' => 'Termos de Uso', 'url' => '#' ),
			),
		),
	);
}

function stcms_default_services() {
	return array(
		array( 'num' => '01', 'title' => 'Branding & Identidade Visual', 'body' => 'Sistemas de marca que comunicam com precisão — do logotipo ao tom de voz. Identidades que crescem com o negócio e resistem ao tempo.', 'content' => '<p>Marca não é logotipo — é a soma de todas as percepções que as pessoas têm do seu negócio. Construímos sistemas de identidade completos: logotipo, paleta, tipografia, grafismos, aplicações e um guia de uso que mantém tudo coerente onde quer que a marca apareça.</p><p>Começamos entendendo o posicionamento e a personalidade da marca, para que cada decisão visual tenha razão de existir. O resultado é uma identidade que transmite intenção, diferencia da concorrência e continua fazendo sentido daqui a cinco anos.</p>' ),
		array( 'num' => '02', 'title' => 'Design de Interface (UI/UX)', 'body' => 'Interfaces construídas a partir do comportamento real do usuário. Cada pixel tem função. Cada fluxo tem intenção.', 'content' => '<p>Interface boa é invisível: o usuário chega onde quer sem perceber o esforço por trás. Desenhamos produtos e sites partindo da pesquisa — quem usa, o que precisa e onde trava — e traduzimos isso em fluxos claros, hierarquia visual e micro-interações que guiam a decisão.</p><p>Entregamos do wireframe ao design system documentado, prontos para o time de desenvolvimento. Cada tela é pensada para reduzir atrito e aumentar conversão, sem abrir mão da estética.</p>' ),
		array( 'num' => '03', 'title' => 'Websites Imersivos', 'body' => 'Sites institucionais com direção visual forte, navegação fluida, responsividade e animações GSAP.', 'content' => '<p>Um site institucional é o cartão de visita mais importante da marca. Criamos experiências imersivas, com direção de arte forte, animações de scroll e transições que transformam a navegação em algo memorável — sem sacrificar performance nem acessibilidade.</p><p>Cada projeto é responsivo de verdade, rápido no celular e construído para ser encontrado no Google. Você recebe um site que impressiona e que também trabalha pelo seu negócio.</p>' ),
		array( 'num' => '04', 'title' => 'Landing Pages Conversivas', 'body' => 'Copy persuasiva, estrutura de oferta e interface pensada para campanhas, tráfego pago e captação de leads.', 'content' => '<p>Uma landing page tem um único objetivo: converter. Estruturamos cada seção — headline, prova social, oferta, objeções e CTA — para conduzir o visitante até a ação, seja um lead, uma venda ou um agendamento.</p><p>Unimos copy persuasiva, design orientado a conversão e testes A/B para extrair o máximo do seu investimento em tráfego pago. Páginas que carregam rápido e convertem mais.</p>' ),
		array( 'num' => '05', 'title' => 'Motion para Sites', 'body' => 'Movimento que conta histórias. Animações de interface e motion graphics que transformam conteúdo em experiência.', 'content' => '<p>Movimento é linguagem. Uma animação bem colocada guia o olhar, explica uma ideia e dá personalidade à marca. Produzimos motion para interfaces — transições, hover, scroll, loaders — e motion graphics para comunicação, sempre com propósito e performance.</p><p>Nada de animação por enfeite: cada movimento tem função, respeita quem prefere menos animação e roda liso em qualquer dispositivo.</p>' ),
		array( 'num' => '06', 'title' => 'Conteúdo & Copywriting', 'body' => 'Palavras que convertem. Narrativas que constroem autoridade, geram confiança e movem o usuário à ação.', 'content' => '<p>Design chama a atenção; a palavra certa fecha o negócio. Desenvolvemos a voz da sua marca e produzimos conteúdo que constrói autoridade, gera confiança e move o usuário à ação — do texto de um botão ao artigo que posiciona você como referência.</p><p>Trabalhamos copy de site, campanhas, e-mail e redes sociais, sempre alinhados à estratégia e ao tom de voz da marca.</p>' ),
	);
}

function stcms_default_faq() {
	return array(
		array( 'q' => 'Como funciona o processo de trabalho?', 'a' => 'Iniciamos com um diagnóstico aprofundado do negócio, mercado e objetivos. Em seguida, criamos um roadmap claro com entregas, prazos e marcos de aprovação. Trabalhamos em sprints curtos com checkpoints semanais para garantir alinhamento contínuo — sem surpresas no final.' ),
		array( 'q' => 'Quanto tempo leva um projeto?', 'a' => 'Depende do escopo. Um projeto de identidade visual leva de 3 a 6 semanas. Um site completo com design e desenvolvimento, de 6 a 12 semanas. Aplicações mais complexas podem levar de 3 a 6 meses. Sempre apresentamos um cronograma detalhado antes de iniciar.' ),
		array( 'q' => 'Vocês trabalham com empresas de qual tamanho?', 'a' => 'Atendemos desde startups em fase de crescimento até empresas consolidadas que querem renovar sua presença digital. O que importa não é o tamanho, mas o comprometimento com qualidade e a disposição para construir algo duradouro.' ),
		array( 'q' => 'Como é estruturada a precificação?', 'a' => 'Trabalhamos com projetos fechados (escopo e valor definidos no início) ou retainer mensal para empresas que precisam de parceria contínua. Não cobramos por hora — cobramos pelo resultado. O orçamento é apresentado de forma transparente, sem taxas ocultas.' ),
		array( 'q' => 'Vocês oferecem suporte após a entrega?', 'a' => 'Sim. Todos os projetos incluem um período de garantia de 30 dias após o lançamento. Para clientes que desejam suporte contínuo, oferecemos planos de manutenção mensal que incluem atualizações, monitoramento e evolução do produto.' ),
		array( 'q' => 'Como posso começar a trabalhar com vocês?', 'a' => 'Preencha o formulário de contato ou nos envie um e-mail com um breve contexto do seu projeto. Agendaremos uma chamada de diagnóstico gratuita de 30 minutos para entender suas necessidades e verificar se somos o parceiro certo para você.' ),
	);
}

function stcms_default_projects() {
	return array(
		array(
			'id' => '01', 'name' => 'Nuvem Finance', 'category' => 'Branding & UI', 'year' => '2025',
			'bg' => 'linear-gradient(135deg,#1A0505 0%,#2D0A0A 50%,#1A0A14 100%)', 'accent' => '#F20C25', 'featured' => true,
			'client' => 'Nuvem Finance', 'scope' => array( 'Identidade Visual', 'UI/UX Design', 'Design System' ), 'duration' => '14 semanas',
			'challenge' => 'A Nuvem Finance chegou até nós como mais uma fintech genérica em um mercado saturado — paleta azul corporativa, linguagem fria, zero diferenciação. O desafio era criar uma identidade que transmitisse solidez sem perder calor humano, e uma interface que tornasse conceitos financeiros complexos acessíveis para o usuário final.',
			'solution' => 'Desenvolvemos uma identidade visual centrada no contraste: tipografia condensada e assertiva equilibrada com espaçamento generoso e tons terrosos que remetem a confiança sem o clichê do azul bancário. O design system foi construído para escalar com o produto, com 240+ componentes documentados e um guia de voz e tom integrado.',
			'results' => array(
				array( 'label' => 'Aumento em conversão', 'value' => '+38%' ),
				array( 'label' => 'Redução em churn', 'value' => '−22%' ),
				array( 'label' => 'NPS pós-redesign', 'value' => '72' ),
				array( 'label' => 'Componentes no DS', 'value' => '240+' ),
			),
			'mockup_lines' => array( 'DASHBOARD', 'PORTFÓLIO', 'ANÁLISE', 'RELATÓRIOS' ),
		),
		array(
			'id' => '02', 'name' => 'FlowDesk', 'category' => 'Produto SaaS', 'year' => '2025',
			'bg' => 'linear-gradient(135deg,#06061A 0%,#0A0A2D 50%,#060F1A 100%)', 'accent' => '#5B7FFF', 'featured' => true,
			'client' => 'FlowDesk', 'scope' => array( 'Produto SaaS', 'UX Research', 'Prototipação', 'Dev Front-end' ), 'duration' => '22 semanas',
			'challenge' => 'A FlowDesk tinha uma ideia sólida de produto mas o MVP inicial tinha uma curva de aprendizado altíssima. A taxa de abandono na primeira semana era de 67%. Precisávamos reconstruir a experiência do zero, sem perder os usuários existentes.',
			'solution' => 'Realizamos 18 entrevistas em profundidade com usuários reais e mapeamos os principais pontos de atrito. Redesenhamos o onboarding com uma abordagem de \'progressive disclosure\'. A nova arquitetura de informação reduziu os caminhos críticos de 7 para 3 cliques.',
			'results' => array(
				array( 'label' => 'Redução de abandono', 'value' => '−51%' ),
				array( 'label' => 'Tempo médio no app', 'value' => '+2.4×' ),
				array( 'label' => 'Usuários ativos/mês', 'value' => '12k+' ),
				array( 'label' => 'Avaliação App Store', 'value' => '4.8★' ),
			),
			'mockup_lines' => array( 'PROJETOS', 'TAREFAS', 'EQUIPE', 'RELATÓRIO' ),
		),
		array(
			'id' => '03', 'name' => 'Maison Lux', 'category' => 'E-commerce', 'year' => '2024',
			'bg' => 'linear-gradient(135deg,#0F0D08 0%,#1A1408 50%,#0D0B06 100%)', 'accent' => '#C4A45A', 'featured' => false,
			'client' => 'Maison Lux', 'scope' => array( 'E-commerce', 'UI Design', 'Motion Design' ), 'duration' => '10 semanas',
			'challenge' => 'Uma marca de moda de luxo brasileira com atelier próprio mas presença digital completamente desalinhada com o posicionamento premium. O site anterior parecia uma loja de departamentos, não uma maison.',
			'solution' => 'Criamos uma experiência editorial inspirada nas grandes maisons europeias: fotografia fullscreen, tipografia serif com muito espaço branco, e microinterações que reforçam a percepção de exclusividade. O checkout foi simplificado para 2 etapas.',
			'results' => array(
				array( 'label' => 'Aumento no ticket médio', 'value' => '+29%' ),
				array( 'label' => 'Taxa de conversão', 'value' => '+44%' ),
				array( 'label' => 'Tempo na página produto', 'value' => '+3.1min' ),
				array( 'label' => 'Retorno de clientes', 'value' => '+61%' ),
			),
			'mockup_lines' => array( 'COLEÇÃO', 'ATELIÊ', 'PEÇAS', 'CONTATO' ),
		),
		array(
			'id' => '04', 'name' => 'Vitalize App', 'category' => 'Mobile UI', 'year' => '2024',
			'bg' => 'linear-gradient(135deg,#060F08 0%,#081A0A 50%,#060D07 100%)', 'accent' => '#3DBF72', 'featured' => false,
			'client' => 'Vitalize', 'scope' => array( 'Mobile UI', 'iOS & Android', 'Ilustração' ), 'duration' => '8 semanas',
			'challenge' => 'O app de saúde e bem-estar Vitalize enfrentava um paradoxo: usuários adoravam o conceito mas achavam o app \'pesado\' e \'intimidador\'. O design anterior usava verde clínico e iconografia médica que afastava justamente o público-alvo.',
			'solution' => 'Redesenhamos com uma abordagem de \'saúde como estilo de vida\': paleta orgânica, ilustrações feitas à mão que humanizam os dados, e um sistema de progresso gamificado que celebra pequenas vitórias.',
			'results' => array(
				array( 'label' => 'Downloads no primeiro mês', 'value' => '48k' ),
				array( 'label' => 'Retenção em 30 dias', 'value' => '71%' ),
				array( 'label' => 'Avaliação nas stores', 'value' => '4.9★' ),
				array( 'label' => 'Menções espontâneas', 'value' => '+180%' ),
			),
			'mockup_lines' => array( 'INÍCIO', 'TREINOS', 'NUTRIÇÃO', 'PROGRESSO' ),
		),
	);
}

function stcms_default_options_en() {
	return array(
		'site'   => array(
			'title'            => 'Studio Tabi',
			'meta_description' => "We build every project with intention, care, and respect for the brand's essence. We create digital experiences that blend strategy, aesthetics, and technology to build trust, generate value, and establish presence.",
			'tagline'          => 'We develop with honor, we deliver with purpose.',
		),
		'nav'    => array(
			'brand'     => 'STUDIO TABI',
			'cta_label' => 'START A PROJECT',
			'cta_url'   => '/en/contact',
			'links'     => array(
				array( 'label' => 'WORK', 'url' => '/en/work' ),
				array( 'label' => 'SERVICES', 'url' => '/en/services' ),
				array( 'label' => 'ABOUT', 'url' => '/en/about' ),
				array( 'label' => 'BLOG', 'url' => '/en/blog' ),
				array( 'label' => 'CONTACT', 'url' => '/en/contact' ),
			),
		),
		'hero'   => array(
			'eyebrow'              => 'STUDIO TABI — DIGITAL STUDIO',
			'title_lines'          => array( 'YOUR BRAND,', 'A DIGITAL' ),
			'highlight'            => 'EXPERIENCE.',
			'description'          => 'Design, strategy and engineering that turn digital presence into perceived value, trust and decision.',
			'cta_primary_label'    => 'VIEW PORTFOLIO',
			'cta_primary_url'      => '/en/work',
			'cta_secondary_label'  => 'TALK TO THE TEAM',
			'cta_secondary_url'    => '/en/contact',
		),
		'projects_cta' => array( 'label' => 'VIEW PORTFOLIO', 'url' => '/en/work' ),
		'thankyou' => array(
			'title'   => 'THANK YOU',
			'message' => 'We received your message. Our team will be in touch within one business day. Every great journey — tabi — begins with a first step.',
		),
		'contact' => array(
			'title'       => "LET'S",
			'highlight'   => 'TALK.',
			'description' => 'Tell us a bit about your project. We reply within one business day.',
		),
		'process' => array(
			array( 'title' => 'Diagnosis',   'slug' => 'diagnostico',    'icon' => 'diagnostico',     'summary' => 'We dive into the business, the market and the goals to understand where you are and where you need to go.' ),
			array( 'title' => 'Narrative',   'slug' => 'narrativa',      'icon' => 'narrativa',       'summary' => 'We define the brand story and positioning — the message that guides every decision.' ),
			array( 'title' => 'Design',      'slug' => 'design',         'icon' => 'design',          'summary' => 'We translate strategy into identity and interface, with intent, hierarchy and purpose.' ),
			array( 'title' => 'Engineering', 'slug' => 'desenvolvimento','icon' => 'desenvolvimento', 'summary' => 'We build with clean, fast, scalable code — from idea to launch, ready to grow.' ),
		),
		'sections' => array(
			'about_eyebrow'      => 'STUDIO TABI — ABOUT US',
			'about_cta_label'    => 'READ OUR STORY',
			'about_cta_url'      => '/en/about',
			'about_pillars_label'=> 'HOW WE WORK',
			'services_eyebrow'   => 'STUDIO TABI — SERVICES',
			'services_cta_label' => 'VIEW ALL SERVICES',
			'services_cta_url'   => '/en/services',
			'projects_eyebrow'   => 'STUDIO TABI — WORK',
			'projects_note'      => '120+ projects delivered',
			'projects_card_text' => 'Want to see the full portfolio with all of our work?',
			'blog_eyebrow'       => 'STUDIO TABI — INSIGHTS',
			'blog_title'         => 'FROM OUR',
			'blog_highlight'     => 'JOURNAL.',
			'blog_note'          => 'Ideas on design, strategy and technology — straight from the people building it.',
			'blog_cta_label'     => 'VIEW ALL ARTICLES',
			'faq_eyebrow'        => 'STUDIO TABI — FAQ',
			'faq_note'           => "Didn't find what you were looking for? Talk to the team directly.",
			'faq_cta_label'      => 'TALK TO THE TEAM',
			'faq_cta_url'        => '/en/contact',
		),
		'about'  => array(
			'paragraph1' => 'Studio Tabi was born from the conviction that digital presence is a strategic asset — not a communications expense. We bring together designers, strategists and engineers who refuse the mediocrity of "good enough".',
			'paragraph2' => 'Every project starts with a simple question: how does this business want to be perceived five years from now? The answer guides every creative, technical and strategic decision we make.',
			'stats'      => array(
				array( 'numeric' => 7,   'suffix' => '+', 'label' => 'YEARS IN THE MARKET' ),
				array( 'numeric' => 120, 'suffix' => '+', 'label' => 'PROJECTS DELIVERED' ),
				array( 'numeric' => 98,  'suffix' => '%', 'label' => 'RETENTION RATE' ),
				array( 'numeric' => 3,   'suffix' => '×', 'label' => 'AVERAGE 12-MONTH RETURN' ),
			),
		),
		'footer' => array(
			'brand'         => 'STUDIO TABI',
			'tagline'       => 'We develop with honor, we deliver with purpose.',
			'cta_title'     => "Let's build your",
			'cta_highlight' => 'digital presence.',
			'cta_label'     => 'START A PROJECT',
			'cta_url'       => '/en/contact',
			'col1_title'    => 'Navigation',
			'col1_links'    => array(
				array( 'label' => 'Work', 'url' => '/en/work' ),
				array( 'label' => 'Services', 'url' => '/en/services' ),
				array( 'label' => 'About', 'url' => '/en/about' ),
				array( 'label' => 'Contact', 'url' => '/en/contact' ),
			),
			'col2_title'    => 'Services',
			'col2_links'    => array(
				array( 'label' => 'Branding & Visual Identity', 'url' => '/en/services/branding-identidade-visual' ),
				array( 'label' => 'Interface Design (UI/UX)', 'url' => '/en/services/design-de-interface-ui-ux' ),
				array( 'label' => 'Immersive Websites', 'url' => '/en/services/desenvolvimento-web' ),
				array( 'label' => 'High-converting landing pages', 'url' => '/en/services/estrategia-digital' ),
				array( 'label' => 'Motion Design for Websites', 'url' => '/en/services/motion-animacao' ),
			),
			'newsletter_title'  => 'Newsletter',
			'newsletter_text'   => 'Newsletter email',
			'newsletter_button' => 'Subscribe',
			'contact_title' => 'Contact',
			'city'          => 'Rio de Janeiro, RJ',
			'social_title'  => 'Social',
			'copyright'     => '© 2026 Studio Tabi. All rights reserved.',
			'made_in'       => 'Crafted with precision in Rio de Janeiro.',
			'legal'         => array(
				array( 'label' => 'Privacy Policy', 'url' => '#' ),
				array( 'label' => 'Terms of Use', 'url' => '#' ),
			),
		),
	);
}

function stcms_traducoes_en() {
	return array(
		'servicos' => array(
			'Branding & Identidade Visual' => array(
				'title'   => 'Branding & Visual Identity',
				'body'    => 'Brand systems that communicate with precision — from the logo to the tone of voice. Identities that grow with the business and stand the test of time.',
				'page_content' => '<p>A brand is not a logo — it is the sum of every perception people hold of your business. We build complete identity systems: logo, palette, typography, graphic elements, applications and a usage guide that keeps everything coherent wherever the brand shows up.</p><p>We start by understanding the brand\'s positioning and personality, so every visual decision has a reason to exist. The result is an identity that conveys intent, sets you apart from the competition and still makes sense five years from now.</p><ul><li>Naming and verbal branding (tone of voice)</li><li>Logo, symbol and variations</li><li>Visual system: colour, typography and graphic elements</li><li>Brand manual and application kit</li></ul>',
			),
			'Design de Interface (UI/UX)' => array(
				'title'   => 'Interface Design (UI/UX)',
				'body'    => 'Interfaces built from real user behaviour. Every pixel has a function. Every flow has intent.',
				'page_content' => '<p>A good interface is invisible: users get where they want without noticing the effort behind it. We design products and websites starting from research — who uses it, what they need and where they get stuck — and translate that into clear flows, visual hierarchy and micro-interactions that guide the decision.</p><p>We deliver everything from wireframes to a documented design system, ready for the development team. Every screen is built to reduce friction and increase conversion, without giving up on aesthetics.</p><ul><li>UX research and information architecture</li><li>Wireframes and clickable prototypes</li><li>UI design and design system</li><li>Usability testing</li></ul>',
			),
			'Websites Imersivos' => array(
				'title'   => 'Immersive Websites',
				'body'    => 'Company websites with strong art direction, fluid navigation, responsiveness and animation.',
				'page_content' => '<p>A company website is the brand\'s most important calling card. We create immersive experiences, with strong art direction, scroll animations and transitions that turn browsing into something memorable — without sacrificing performance or accessibility.</p><p>Every project is genuinely responsive, fast on mobile and built to be found on Google. You get a site that impresses and that also works for your business.</p><ul><li>Art direction and visual storytelling</li><li>Scroll animations and micro-interactions</li><li>Responsiveness and performance (Core Web Vitals)</li><li>Technical SEO and CMS integration</li></ul>',
			),
			'Landing Pages Conversivas' => array(
				'title'   => 'High-Converting Landing Pages',
				'body'    => 'Persuasive copy, offer structure and an interface designed for campaigns, paid traffic and lead generation.',
				'page_content' => '<p>A landing page has a single goal: to convert. We structure every section — headline, social proof, offer, objections and CTA — to carry the visitor through to the action, be it a lead, a sale or a booking.</p><p>We combine persuasive copy, conversion-driven design and A/B testing to get the most out of your paid traffic investment. Pages that load fast and convert more.</p><ul><li>Copywriting and offer structure</li><li>Conversion-focused design</li><li>Integration with forms, pixel and analytics</li><li>A/B testing and continuous optimisation</li></ul>',
			),
			'Motion para Sites' => array(
				'title'   => 'Motion for Websites',
				'body'    => 'Movement that tells stories. Interface animation and motion graphics that turn content into experience.',
				'page_content' => '<p>Movement is a language. A well-placed animation guides the eye, explains an idea and gives the brand personality. We produce motion for interfaces — transitions, hover, scroll, loaders — and motion graphics for communication, always with purpose and performance in mind.</p><p>No animation for decoration\'s sake: every movement has a function, respects people who prefer reduced motion and runs smoothly on any device.</p><ul><li>Interface animation and transitions</li><li>Motion graphics and short videos</li><li>Scroll animation and storytelling</li><li>Performance and accessibility optimisation</li></ul>',
			),
			'Conteúdo & Copywriting' => array(
				'title'   => 'Content & Copywriting',
				'body'    => 'Words that convert. Narratives that build authority, earn trust and move the user to act.',
				'page_content' => '<p>Design gets attention; the right words close the deal. We develop your brand\'s voice and produce content that builds authority, earns trust and moves the user to act — from the text on a button to the article that positions you as a reference.</p><p>We work on website, campaign, email and social copy, always aligned with the brand\'s strategy and tone of voice.</p><ul><li>Tone of voice and key messaging</li><li>Copy for websites, landing pages and campaigns</li><li>Blog and social media content</li><li>Editing and editorial consistency</li></ul>',
			),
		),

		'faq' => array(
			'Como funciona o processo de trabalho?' => array(
				'title'   => 'How does the process work?',
				'body'    => 'We start with an in-depth diagnosis of the business, the market and the goals. Then we build a clear roadmap with deliverables, deadlines and approval milestones. We work in short sprints with weekly checkpoints to keep everyone aligned — no surprises at the end.',
			),
			'Quanto tempo leva um projeto?' => array(
				'title'   => 'How long does a project take?',
				'body'    => 'It depends on scope. A visual identity project takes 3 to 6 weeks. A full website with design and development, 6 to 12 weeks. More complex applications can take 3 to 6 months. We always present a detailed schedule before starting.',
			),
			'Vocês trabalham com empresas de qual tamanho?' => array(
				'title'   => 'What size of company do you work with?',
				'body'    => 'We work with everyone from growth-stage startups to established companies looking to renew their digital presence. What matters is not size, but the commitment to quality and the willingness to build something that lasts.',
			),
			'Como é estruturada a precificação?' => array(
				'title'   => 'How is pricing structured?',
				'body'    => 'We work with fixed-scope projects (scope and price agreed upfront) or a monthly retainer for companies that need an ongoing partner. We don\'t charge by the hour — we charge for the outcome. The quote is presented transparently, with no hidden fees.',
			),
			'Vocês oferecem suporte após a entrega?' => array(
				'title'   => 'Do you offer support after launch?',
				'body'    => 'Yes. Every project includes a 30-day warranty period after launch. For clients who want ongoing support, we offer monthly maintenance plans covering updates, monitoring and product evolution.',
			),
			'Como posso começar a trabalhar com vocês?' => array(
				'title'   => 'How do I start working with you?',
				'body'    => 'Fill in the contact form or send us an email with a brief outline of your project. We\'ll schedule a free 30-minute diagnosis call to understand your needs and check whether we\'re the right partner for you.',
			),
		),

		'projetos' => array(
			'Nuvem Finance' => array(
				'category'  => 'Branding & UI',
				'duration'  => '14 weeks',
				'challenge' => 'Nuvem Finance came to us as yet another generic fintech in a saturated market — corporate blue palette, cold language, zero differentiation. The challenge was to create an identity that conveyed solidity without losing human warmth, and an interface that made complex financial concepts accessible to the end user.',
				'solution'  => 'We developed a visual identity built on contrast: condensed, assertive typography balanced by generous spacing and earthy tones that suggest trust without the banking-blue cliché. The design system was built to scale with the product, with 240+ documented components and an integrated voice and tone guide.',
				'scope'     => array( 'Visual Identity', 'UI/UX Design', 'Design System' ),
				'mockup'    => array( 'DASHBOARD', 'PORTFOLIO', 'ANALYSIS', 'REPORTS' ),
				'results'   => array(
					'Increase in conversion',
					'Reduction in churn',
					'NPS after redesign',
					'Components in the DS',
				),
			),
			'FlowDesk' => array(
				'category'  => 'SaaS Product',
				'duration'  => '22 weeks',
				'challenge' => 'FlowDesk had a solid product idea but the initial MVP had a very steep learning curve. First-week drop-off was 67%. We needed to rebuild the experience from scratch without losing the existing users.',
				'solution'  => 'We ran 18 in-depth interviews with real users and mapped the main friction points. We redesigned onboarding with a progressive disclosure approach. The new information architecture cut the critical paths from 7 clicks to 3.',
				'scope'     => array( 'SaaS Product', 'UX Research', 'Prototyping', 'Front-end Dev' ),
				'mockup'    => array( 'PROJECTS', 'TASKS', 'TEAM', 'REPORT' ),
				'results'   => array(
					'Drop-off reduction',
					'Average time in app',
					'Monthly active users',
					'App Store rating',
				),
			),
			'Maison Lux' => array(
				'category'  => 'E-commerce',
				'duration'  => '10 weeks',
				'challenge' => 'A Brazilian luxury fashion brand with its own atelier, but a digital presence completely at odds with its premium positioning. The previous site looked like a department store, not a maison.',
				'solution'  => 'We created an editorial experience inspired by the great European maisons: fullscreen photography, serif typography with generous white space, and micro-interactions that reinforce the sense of exclusivity. Checkout was simplified to 2 steps.',
				'scope'     => array( 'E-commerce', 'UI Design', 'Motion Design' ),
				'mockup'    => array( 'COLLECTION', 'ATELIER', 'PIECES', 'CONTACT' ),
				'results'   => array(
					'Increase in average order',
					'Conversion rate',
					'Time on product page',
					'Returning customers',
				),
			),
			'Vitalize App' => array(
				'category'  => 'Mobile UI',
				'duration'  => '8 weeks',
				'challenge' => 'The Vitalize health and wellbeing app faced a paradox: users loved the concept but found the app \'heavy\' and \'intimidating\'. The previous design used clinical green and medical iconography that pushed away the very audience it targeted.',
				'solution'  => 'We redesigned it around \'health as a lifestyle\': an organic palette, hand-drawn illustrations that humanise the data, and a gamified progress system that celebrates small wins.',
				'scope'     => array( 'Mobile UI', 'iOS & Android', 'Illustration' ),
				'mockup'    => array( 'HOME', 'WORKOUTS', 'NUTRITION', 'PROGRESS' ),
				'results'   => array(
					'Downloads in the first month',
					'30-day retention',
					'Store rating',
					'Organic mentions',
				),
			),
		),

		'paginas' => array(
			'Diagnóstico' => array(
				'title' => 'Diagnosis',
				'body'  => "<p>No good solution is born from a guess. The diagnosis stage is where we listen, measure and understand the terrain before proposing any path.</p><h2>What we do</h2><p>Immersion in the business, competitive analysis, interviews with stakeholders and users, an audit of the current digital presence and a read of the data. We come out with clarity on where the brand is — and where it needs to go.</p><h2>Why it matters</h2><p>An honest diagnosis avoids rework, aligns expectations and turns hunches into decisions. It is the foundation for everything that follows.</p><h2>Deliverables</h2><ul><li>Diagnosis and opportunities report</li><li>Audience and journey map</li><li>Competitive benchmark</li><li>Goals and metrics definition</li></ul>",
			),
			'Narrativa' => array(
				'title' => 'Narrative',
				'body'  => "<p>Before the design exists, the story exists. The narrative defines what the brand says, to whom and why — the message that guides every creative and technical decision.</p><h2>What we do</h2><p>Positioning, value proposition, tone of voice and key messaging. We translate strategy into language that differentiates and connects.</p><h2>Why it matters</h2><p>Brands that know what they are saying convert more and compete on value, not on price. The narrative brings consistency to every touchpoint.</p><h2>Deliverables</h2><ul><li>Positioning and value proposition</li><li>Tone of voice and key messaging</li><li>Content architecture</li><li>Script for the main pages</li></ul>",
			),
			'Design' => array(
				'title' => 'Design',
				'body'  => "<p>Design is strategy made visible and usable. Every screen, colour and movement has intent, hierarchy and purpose.</p><h2>What we do</h2><p>Visual identity, interface design (UI/UX), design system and clickable prototypes. We combine aesthetics and function to create experiences that are memorable and that convert.</p><h2>Why it matters</h2><p>A good interface is invisible: it carries the user to the action without friction. Good design lowers support costs, increases conversion and raises the brand's value.</p><h2>Deliverables</h2><ul><li>Visual identity and design system</li><li>UI/UX for screens and flows</li><li>Clickable prototype</li><li>Specifications for development</li></ul>",
			),
			'Desenvolvimento' => array(
				'title' => 'Engineering',
				'body'  => "<p>This is where the idea becomes a live product. We build with clean, fast, scalable code — no technical debt and ready to grow.</p><h2>What we do</h2><p>Front-end development and integrations, performance (Core Web Vitals), accessibility, technical SEO and launch. All tested on desktop, tablet and mobile.</p><h2>Why it matters</h2><p>Speed is revenue and ranking. A fast, accessible, well-structured site works for your business 24 hours a day.</p><h2>Deliverables</h2><ul><li>Responsive, optimised site or application</li><li>CMS integration</li><li>Technical SEO and performance</li><li>Launch and follow-up</li></ul>",
			),
		),
		'posts' => array(
			'Presença digital não é custo — é ativo estratégico' => array(
				'title'   => 'Digital presence is not a cost — it is a strategic asset',
				'excerpt' => 'Why treating your site and brand as an investment changes what your business gets back.',
				'body'    => "<p>Plenty of companies still see their website as a marketing expense. It is an expensive mistake. A well-built digital presence is an <strong>asset</strong>: it raises the brand's value, lowers acquisition cost and sustains the trust that closes deals.</p><h2>What changes when you treat it as an asset</h2><p>You stop asking \"what does it cost\" and start asking \"what does it return\". The decision stops being aesthetic and becomes strategic.</p><h3>Perceived value</h3><p>A coherent brand signals solidity before the first conversation. That shortens the sales cycle.</p><h2>Where to start</h2><p>Start with the diagnosis: where the brand is, where it needs to go and what is blocking the path. Everything else follows from there.</p>",
			),
			'Design que converte: interface é arquitetura de decisões' => array(
				'title'   => 'Design that converts: an interface is decision architecture',
				'excerpt' => 'Every screen moves the user forward — or stops them. How design steers the action.',
				'body'    => "<p>A beautiful interface that doesn't convert is expensive decoration. Good design is invisible: it carries the user to the action without them noticing the effort.</p><h2>Visual hierarchy</h2><p>The eye follows contrast, size and space. Use that to highlight what matters and quiet what distracts.</p><h2>Reducing friction</h2><p>Every field, click and decision you remove increases conversion. Less is almost always more.</p>",
			),
			'Performance web: por que velocidade é receita' => array(
				'title'   => 'Web performance: why speed is revenue',
				'excerpt' => 'LCP, CLS and INP are not acronyms — they are money. Here is the real impact.',
				'body'    => "<p>Every extra second of load time drags down conversion and ranking. Performance is not technical vanity: it is revenue and SEO.</p><h2>The metrics that matter</h2><p>LCP (largest visible element), CLS (visual stability) and INP (interaction response) define the user's real experience.</p><h2>The basics, done well</h2><p>Local fonts, optimised images, split code and caching. The basics, done rigorously, already put you ahead of most.</p>",
			),
		),
	);
}
