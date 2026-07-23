<?php
/**
 * Valores padrão de TODOS os textos do site — fonte única, usada tanto pelo
 * Personalizar (ao registrar os campos) quanto pelo front-end (get_theme_mod).
 */

defined( 'ABSPATH' ) || exit;

function tabi_defaults() {
	static $d = null;
	if ( null !== $d ) { return $d; }

	$d = array(
		// Hero
		'tabi_hero_eyebrow'  => 'STUDIO TABI — DIGITAL STUDIO',
		'tabi_hero_title'    => "TRANSFORMAMOS\nA SUA MARCA\nEM EXPERIÊNCIA\n*DIGITAL.*",
		'tabi_hero_desc'     => 'Design, estratégia e desenvolvimento para transformar presença digital em percepção de valor, confiança e decisão.',
		'tabi_hero_cta1'     => 'VER PORTFÓLIO',
		'tabi_hero_cta1_url' => '#trabalhos',
		'tabi_hero_cta2'     => 'FALAR COM A EQUIPE',
		'tabi_hero_cta2_url' => '#contato',

		// Sobre
		'tabi_about_eyebrow' => 'STUDIO TABI — SOBRE NÓS',
		'tabi_about_title'   => "NÃO FAZEMOS\nSITES.\n*CONSTRUÍMOS*\nPRESENÇA.",
		'tabi_about_p1'      => 'O Studio Tabi nasceu da convicção de que presença digital é um ativo estratégico — não uma despesa de comunicação. Reunimos designers, estrategistas e engenheiros que recusam o medíocre do "bom o suficiente".',
		'tabi_about_p2'      => 'Cada projeto começa com uma pergunta simples: como esse negócio quer ser percebido daqui a cinco anos? A resposta guia cada decisão criativa, técnica e estratégica que tomamos.',

		'tabi_stat1_num'   => '7+',  'tabi_stat1_label' => 'ANOS DE MERCADO',
		'tabi_stat2_num'   => '120+', 'tabi_stat2_label' => 'PROJETOS ENTREGUES',
		'tabi_stat3_num'   => '98%', 'tabi_stat3_label' => 'TAXA DE RETENÇÃO',
		'tabi_stat4_num'   => '3×',  'tabi_stat4_label' => 'RETORNO MÉDIO EM 12M',

		'tabi_pillar1_title' => 'Identidade que comunica',
		'tabi_pillar1_body'  => 'Marcas que carregam intenção em cada detalhe — do logotipo ao tom de voz. Construímos sistemas visuais que resistem ao tempo e crescem com o negócio.',
		'tabi_pillar2_title' => 'Experiência que converte',
		'tabi_pillar2_body'  => 'Interface não é arte — é arquitetura de decisões. Cada tela, cada fluxo, cada micro-interação é desenhada para mover o usuário em direção ao objetivo.',
		'tabi_pillar3_title' => 'Tecnologia que escala',
		'tabi_pillar3_body'  => 'Código sem dívida técnica. Estruturas que aguentam crescimento sem reescritas. Integrações que funcionam na primeira vez e continuam funcionando.',
		'tabi_pillar4_title' => 'Estratégia que orienta',
		'tabi_pillar4_body'  => 'Dados, mercado e comportamento do usuário traduzidos em decisões claras. Sem achismos, sem modismos — só o que move o ponteiro.',

		// Serviços
		'tabi_services_eyebrow' => 'STUDIO TABI — SERVIÇOS',
		'tabi_services_title'   => "O QUE\nENTREGAMOS.",
		'tabi_services_intro'   => 'Design, estratégia e tecnologia sob um mesmo teto. Cada serviço é pensado para mover o ponteiro do seu negócio.',

		// Projetos
		'tabi_projects_eyebrow'        => 'STUDIO TABI — PROJETOS',
		'tabi_projects_title'          => "TRABALHOS\n*SELECIONADOS.*",
		'tabi_projects_meta'           => '120+ projetos entregues',
		'tabi_project_challenge_label' => 'O DESAFIO',
		'tabi_project_solution_label'  => 'A SOLUÇÃO',
		'tabi_project_results_label'   => 'RESULTADOS',

		// FAQ
		'tabi_faq_eyebrow' => 'STUDIO TABI — FAQ',
		'tabi_faq_title'   => "PERGUNTAS\nFREQUENTES\n*.*",
		'tabi_faq_intro'   => 'Não encontrou o que procura? Entre em contato diretamente com a equipe.',
		'tabi_faq1_q' => 'Como funciona o processo de trabalho?',
		'tabi_faq1_a' => 'Iniciamos com um diagnóstico aprofundado do negócio, mercado e objetivos. Em seguida, criamos um roadmap claro com entregas, prazos e marcos de aprovação. Trabalhamos em sprints curtos com checkpoints semanais para garantir alinhamento contínuo.',
		'tabi_faq2_q' => 'Quanto tempo leva um projeto?',
		'tabi_faq2_a' => 'Depende do escopo. Identidade visual leva de 3 a 6 semanas. Um site completo, de 6 a 12 semanas. Aplicações mais complexas podem levar de 3 a 6 meses. Sempre apresentamos um cronograma detalhado antes de iniciar.',
		'tabi_faq3_q' => 'Vocês trabalham com empresas de qual tamanho?',
		'tabi_faq3_a' => 'Atendemos desde startups em crescimento até empresas consolidadas. O que importa não é o tamanho, mas o comprometimento com qualidade e a disposição para construir algo duradouro.',
		'tabi_faq4_q' => 'Como é a precificação?',
		'tabi_faq4_a' => 'Trabalhamos com projetos fechados ou retainer mensal. Não cobramos por hora — cobramos pelo resultado. O orçamento é transparente, sem taxas ocultas.',
		'tabi_faq5_q' => 'Oferecem suporte após a entrega?',
		'tabi_faq5_a' => 'Sim. Todos os projetos incluem 30 dias de garantia após o lançamento. Também oferecemos planos de manutenção mensal com atualizações e evolução do produto.',
		'tabi_faq6_q' => 'Como começo a trabalhar com vocês?',
		'tabi_faq6_a' => 'Preencha o formulário de contato ou nos envie um e-mail com um breve contexto. Agendamos uma chamada de diagnóstico gratuita de 30 minutos.',

		// Rodapé
		'tabi_footer_tagline'   => 'Design e tecnologia que levam marcas até onde precisam chegar.',
		'tabi_footer_cta'       => 'INICIAR PROJETO',
		'tabi_footer_email'     => 'oi@studiotabi.com.br',
		'tabi_footer_phone'     => '+55 11 9 9999-9999',
		'tabi_footer_city'      => 'São Paulo, SP',
		'tabi_footer_social'    => "Instagram\nLinkedIn\nBehance\nGitHub",
		'tabi_footer_copyright' => '© 2026 Studio Tabi. Todos os direitos reservados.',
		'tabi_footer_signature' => 'Feito com precisão em São Paulo',
	);
	return $d;
}

/** Padrão de um campo específico. */
function tabi_default( $id ) {
	$d = tabi_defaults();
	return isset( $d[ $id ] ) ? $d[ $id ] : '';
}
