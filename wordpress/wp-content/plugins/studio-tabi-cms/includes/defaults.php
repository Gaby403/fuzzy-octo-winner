<?php
/**
 * Default seed content for Studio Tabi CMS.
 * Mirrors the DEFAULT_CONTENT object of the React front-end so a fresh
 * WordPress install boots with the same content the design ships with.
 *
 * @package StudioTabiCMS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Singleton options defaults (hero / about / footer / site).
 *
 * @return array
 */
function stcms_default_options() {
	return array(
		'site'   => array(
			'title'            => 'Studio Tabi',
			'meta_description' => 'Studio Tabi — design, estratégia e desenvolvimento para transformar presença digital em valor, confiança e decisão.',
			'tagline'          => 'Design e tecnologia que levam marcas até onde precisam chegar.',
			'logo_id'          => 0,
			'favicon_id'       => 0,
		),
		'nav'    => array(
			'brand'     => 'STUDIO TABI',
			'cta_label' => 'INICIAR PROJETO',
			'cta_url'   => '/contato',
			'links'     => array(
				array( 'label' => 'TRABALHOS', 'url' => '#trabalhos' ),
				array( 'label' => 'SERVIÇOS', 'url' => '#servicos' ),
				array( 'label' => 'SOBRE', 'url' => '#sobre' ),
				array( 'label' => 'CONTATO', 'url' => '/contato' ),
			),
		),
		'hero'   => array(
			'eyebrow'              => 'STUDIO TABI — DIGITAL STUDIO',
			'title_lines'         => array( 'TRANSFORMAMOS', 'A SUA MARCA', 'EM EXPERIÊNCIA' ),
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
				array( 'title' => 'Identidade que comunica',  'body' => 'Marcas que carregam intenção em cada detalhe — do logotipo ao tom de voz. Construímos sistemas visuais que resistem ao tempo e crescem com o negócio.' ),
				array( 'title' => 'Experiência que converte', 'body' => 'Interface não é arte — é arquitetura de decisões. Cada tela, cada fluxo, cada micro-interação é desenhada para mover o usuário em direção ao objetivo.' ),
				array( 'title' => 'Tecnologia que escala',    'body' => 'Código sem dívida técnica. Estruturas que aguentam crescimento sem reescritas. Integrações que funcionam na primeira vez e continuam funcionando.' ),
				array( 'title' => 'Estratégia que orienta',   'body' => 'Dados, mercado e comportamento do usuário traduzidos em decisões claras. Sem achismos, sem modismos — só o que move o ponteiro.' ),
			),
		),
		'footer' => array(
			'brand'         => 'STUDIO TABI',
			'tagline'       => 'Design e tecnologia que levam marcas até onde precisam chegar.',
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
				array( 'label' => 'Branding', 'url' => '#' ),
				array( 'label' => 'UI / UX Design', 'url' => '#' ),
				array( 'label' => 'Desenvolvimento Web', 'url' => '#' ),
				array( 'label' => 'Estratégia Digital', 'url' => '#' ),
				array( 'label' => 'Motion & Animação', 'url' => '#' ),
			),
			'contact_title' => 'Contato',
			'email'         => 'oi@studiotabi.com.br',
			'phone'         => '+55 11 9 9999-9999',
			'city'          => 'São Paulo, SP',
			'social_title'  => 'Social',
			'social'        => array(
				array( 'label' => 'Instagram', 'url' => '#' ),
				array( 'label' => 'LinkedIn', 'url' => '#' ),
				array( 'label' => 'Behance', 'url' => '#' ),
				array( 'label' => 'GitHub', 'url' => '#' ),
			),
			'copyright'     => '© 2026 Studio Tabi. Todos os direitos reservados.',
			'made_in'       => 'Feito com precisão em São Paulo',
			'legal'         => array(
				array( 'label' => 'Política de Privacidade', 'url' => '#' ),
				array( 'label' => 'Termos de Uso', 'url' => '#' ),
			),
		),
	);
}

/**
 * Default services (seed into st_service CPT).
 *
 * @return array
 */
function stcms_default_services() {
	return array(
		array( 'num' => '01', 'title' => 'Branding & Identidade Visual', 'body' => 'Sistemas de marca que comunicam com precisão — do logotipo ao tom de voz. Identidades que crescem com o negócio e resistem ao tempo.' ),
		array( 'num' => '02', 'title' => 'Design de Interface (UI/UX)', 'body' => 'Interfaces construídas a partir do comportamento real do usuário. Cada pixel tem função. Cada fluxo tem intenção.' ),
		array( 'num' => '03', 'title' => 'Desenvolvimento Web', 'body' => 'Código limpo, performático e acessível. Sites e aplicações que carregam rápido, escalam com o negócio e integram com qualquer stack.' ),
		array( 'num' => '04', 'title' => 'Estratégia Digital', 'body' => 'Diagnóstico, posicionamento e roadmap para sua presença digital. Decisões com dados, não com suposições.' ),
		array( 'num' => '05', 'title' => 'Motion & Animação', 'body' => 'Movimento que conta histórias. Animações de interface e motion graphics que transformam conteúdo em experiência.' ),
		array( 'num' => '06', 'title' => 'Conteúdo & Copywriting', 'body' => 'Palavras que convertem. Narrativas que constroem autoridade, geram confiança e movem o usuário à ação.' ),
	);
}

/**
 * Default FAQ (seed into st_faq CPT).
 *
 * @return array
 */
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

/**
 * Default projects (seed into st_project CPT).
 *
 * @return array
 */
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
