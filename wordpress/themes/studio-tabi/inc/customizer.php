<?php
/**
 * Personalizar (Aparência → Personalizar): todos os textos do site.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adiciona um campo de texto/área ao Personalizar de forma compacta.
 */
function tabi_add_field( $wp, $section, $id, $label, $default = '', $type = 'text', $description = '' ) {
	$sanitize = ( 'textarea' === $type ) ? 'sanitize_textarea_field' : 'sanitize_text_field';
	$wp->add_setting( $id, array(
		'default'           => $default,
		'sanitize_callback' => $sanitize,
		'transport'         => 'refresh',
	) );
	$wp->add_control( $id, array(
		'label'       => $label,
		'section'     => $section,
		'type'        => $type,
		'description' => $description,
	) );
}

add_action( 'customize_register', function ( $wp ) {

	$panel = 'tabi_content';
	$wp->add_panel( $panel, array(
		'title'    => __( 'Textos do site', 'studio-tabi' ),
		'priority' => 20,
	) );

	$section = function ( $id, $title ) use ( $wp, $panel ) {
		$wp->add_section( $id, array( 'title' => $title, 'panel' => 'tabi_content' ) );
	};

	// Dica sobre o destaque em vermelho.
	$hint = __( 'Uma linha por linha. Envolva uma linha com *asteriscos* para destacá-la em vermelho.', 'studio-tabi' );

	// ── Hero ──
	$section( 'tabi_hero', __( '1. Topo (Hero)', 'studio-tabi' ) );
	tabi_add_field( $wp, 'tabi_hero', 'tabi_hero_eyebrow', __( 'Tarja', 'studio-tabi' ), 'STUDIO TABI — DIGITAL STUDIO' );
	tabi_add_field( $wp, 'tabi_hero', 'tabi_hero_title', __( 'Título', 'studio-tabi' ), "TRANSFORMAMOS\nA SUA MARCA\nEM EXPERIÊNCIA\n*DIGITAL.*", 'textarea', $hint );
	tabi_add_field( $wp, 'tabi_hero', 'tabi_hero_desc', __( 'Descrição', 'studio-tabi' ), 'Design, estratégia e desenvolvimento para transformar presença digital em percepção de valor, confiança e decisão.', 'textarea' );
	tabi_add_field( $wp, 'tabi_hero', 'tabi_hero_cta1', __( 'Botão principal (texto)', 'studio-tabi' ), 'VER PORTFÓLIO' );
	tabi_add_field( $wp, 'tabi_hero', 'tabi_hero_cta1_url', __( 'Botão principal (link)', 'studio-tabi' ), '#trabalhos' );
	tabi_add_field( $wp, 'tabi_hero', 'tabi_hero_cta2', __( 'Botão secundário (texto)', 'studio-tabi' ), 'FALAR COM A EQUIPE' );
	tabi_add_field( $wp, 'tabi_hero', 'tabi_hero_cta2_url', __( 'Botão secundário (link)', 'studio-tabi' ), '#contato' );

	// ── Sobre ──
	$section( 'tabi_about', __( '2. Sobre', 'studio-tabi' ) );
	tabi_add_field( $wp, 'tabi_about', 'tabi_about_eyebrow', __( 'Tarja', 'studio-tabi' ), 'STUDIO TABI — SOBRE NÓS' );
	tabi_add_field( $wp, 'tabi_about', 'tabi_about_title', __( 'Título', 'studio-tabi' ), "NÃO FAZEMOS\nSITES.\n*CONSTRUÍMOS*\nPRESENÇA.", 'textarea', $hint );
	tabi_add_field( $wp, 'tabi_about', 'tabi_about_p1', __( 'Parágrafo 1', 'studio-tabi' ), 'O Studio Tabi nasceu da convicção de que presença digital é um ativo estratégico — não uma despesa de comunicação. Reunimos designers, estrategistas e engenheiros que recusam o medíocre do "bom o suficiente".', 'textarea' );
	tabi_add_field( $wp, 'tabi_about', 'tabi_about_p2', __( 'Parágrafo 2', 'studio-tabi' ), 'Cada projeto começa com uma pergunta simples: como esse negócio quer ser percebido daqui a cinco anos? A resposta guia cada decisão criativa, técnica e estratégica que tomamos.', 'textarea' );

	$stats = array(
		array( '7', '+', 'ANOS DE MERCADO' ),
		array( '120', '+', 'PROJETOS ENTREGUES' ),
		array( '98', '%', 'TAXA DE RETENÇÃO' ),
		array( '3', '×', 'RETORNO MÉDIO EM 12M' ),
	);
	foreach ( $stats as $i => $s ) {
		$n = $i + 1;
		tabi_add_field( $wp, 'tabi_about', "tabi_stat{$n}_num", sprintf( __( 'Número %d', 'studio-tabi' ), $n ), $s[0] . $s[1] );
		tabi_add_field( $wp, 'tabi_about', "tabi_stat{$n}_label", sprintf( __( 'Legenda %d', 'studio-tabi' ), $n ), $s[2] );
	}

	$pillars = array(
		array( 'Identidade que comunica', 'Marcas que carregam intenção em cada detalhe — do logotipo ao tom de voz. Construímos sistemas visuais que resistem ao tempo e crescem com o negócio.' ),
		array( 'Experiência que converte', 'Interface não é arte — é arquitetura de decisões. Cada tela, cada fluxo, cada micro-interação é desenhada para mover o usuário em direção ao objetivo.' ),
		array( 'Tecnologia que escala', 'Código sem dívida técnica. Estruturas que aguentam crescimento sem reescritas. Integrações que funcionam na primeira vez e continuam funcionando.' ),
		array( 'Estratégia que orienta', 'Dados, mercado e comportamento do usuário traduzidos em decisões claras. Sem achismos, sem modismos — só o que move o ponteiro.' ),
	);
	foreach ( $pillars as $i => $p ) {
		$n = $i + 1;
		tabi_add_field( $wp, 'tabi_about', "tabi_pillar{$n}_title", sprintf( __( 'Pilar %d — título', 'studio-tabi' ), $n ), $p[0] );
		tabi_add_field( $wp, 'tabi_about', "tabi_pillar{$n}_body", sprintf( __( 'Pilar %d — texto', 'studio-tabi' ), $n ), $p[1], 'textarea' );
	}

	// ── Serviços (cabeçalho) ──
	$section( 'tabi_services', __( '3. Serviços (cabeçalho)', 'studio-tabi' ) );
	tabi_add_field( $wp, 'tabi_services', 'tabi_services_eyebrow', __( 'Tarja', 'studio-tabi' ), 'STUDIO TABI — SERVIÇOS' );
	tabi_add_field( $wp, 'tabi_services', 'tabi_services_title', __( 'Título', 'studio-tabi' ), "O QUE\nENTREGAMOS.", 'textarea', $hint );
	tabi_add_field( $wp, 'tabi_services', 'tabi_services_intro', __( 'Introdução — os serviços em si são criados no menu “Serviços” do painel.', 'studio-tabi' ), 'Design, estratégia e tecnologia sob um mesmo teto. Cada serviço é pensado para mover o ponteiro do seu negócio.', 'textarea' );

	// ── Projetos (cabeçalho) ──
	$section( 'tabi_projects', __( '4. Projetos (cabeçalho)', 'studio-tabi' ) );
	tabi_add_field( $wp, 'tabi_projects', 'tabi_projects_eyebrow', __( 'Tarja', 'studio-tabi' ), 'STUDIO TABI — PROJETOS' );
	tabi_add_field( $wp, 'tabi_projects', 'tabi_projects_title', __( 'Título', 'studio-tabi' ), "TRABALHOS\n*SELECIONADOS.*", 'textarea', $hint );
	tabi_add_field( $wp, 'tabi_projects', 'tabi_projects_meta', __( 'Texto ao lado', 'studio-tabi' ), '120+ projetos entregues' );
	tabi_add_field( $wp, 'tabi_projects', 'tabi_project_challenge_label', __( 'Rótulo “Desafio”', 'studio-tabi' ), 'O DESAFIO' );
	tabi_add_field( $wp, 'tabi_projects', 'tabi_project_solution_label', __( 'Rótulo “Solução”', 'studio-tabi' ), 'A SOLUÇÃO' );
	tabi_add_field( $wp, 'tabi_projects', 'tabi_project_results_label', __( 'Rótulo “Resultados”', 'studio-tabi' ), 'RESULTADOS' );

	// ── FAQ ──
	$section( 'tabi_faq', __( '5. Perguntas frequentes', 'studio-tabi' ) );
	tabi_add_field( $wp, 'tabi_faq', 'tabi_faq_eyebrow', __( 'Tarja', 'studio-tabi' ), 'STUDIO TABI — FAQ' );
	tabi_add_field( $wp, 'tabi_faq', 'tabi_faq_title', __( 'Título', 'studio-tabi' ), "PERGUNTAS\nFREQUENTES\n*.*", 'textarea', $hint );
	tabi_add_field( $wp, 'tabi_faq', 'tabi_faq_intro', __( 'Introdução', 'studio-tabi' ), 'Não encontrou o que procura? Entre em contato diretamente com a equipe.', 'textarea' );
	$faqs = array(
		array( 'Como funciona o processo de trabalho?', 'Iniciamos com um diagnóstico aprofundado do negócio, mercado e objetivos. Em seguida, criamos um roadmap claro com entregas, prazos e marcos de aprovação. Trabalhamos em sprints curtos com checkpoints semanais para garantir alinhamento contínuo.' ),
		array( 'Quanto tempo leva um projeto?', 'Depende do escopo. Identidade visual leva de 3 a 6 semanas. Um site completo, de 6 a 12 semanas. Aplicações mais complexas podem levar de 3 a 6 meses. Sempre apresentamos um cronograma detalhado antes de iniciar.' ),
		array( 'Vocês trabalham com empresas de qual tamanho?', 'Atendemos desde startups em crescimento até empresas consolidadas. O que importa não é o tamanho, mas o comprometimento com qualidade e a disposição para construir algo duradouro.' ),
		array( 'Como é a precificação?', 'Trabalhamos com projetos fechados ou retainer mensal. Não cobramos por hora — cobramos pelo resultado. O orçamento é transparente, sem taxas ocultas.' ),
		array( 'Oferecem suporte após a entrega?', 'Sim. Todos os projetos incluem 30 dias de garantia após o lançamento. Também oferecemos planos de manutenção mensal com atualizações e evolução do produto.' ),
		array( 'Como começo a trabalhar com vocês?', 'Preencha o formulário de contato ou nos envie um e-mail com um breve contexto. Agendamos uma chamada de diagnóstico gratuita de 30 minutos.' ),
	);
	foreach ( $faqs as $i => $f ) {
		$n = $i + 1;
		tabi_add_field( $wp, 'tabi_faq', "tabi_faq{$n}_q", sprintf( __( 'Pergunta %d', 'studio-tabi' ), $n ), $f[0] );
		tabi_add_field( $wp, 'tabi_faq', "tabi_faq{$n}_a", sprintf( __( 'Resposta %d', 'studio-tabi' ), $n ), $f[1], 'textarea' );
	}

	// ── Rodapé ──
	$section( 'tabi_footer', __( '6. Rodapé e contato', 'studio-tabi' ) );
	tabi_add_field( $wp, 'tabi_footer', 'tabi_footer_tagline', __( 'Frase do rodapé', 'studio-tabi' ), 'Design e tecnologia que levam marcas até onde precisam chegar.', 'textarea' );
	tabi_add_field( $wp, 'tabi_footer', 'tabi_footer_cta', __( 'Botão de contato', 'studio-tabi' ), 'INICIAR PROJETO' );
	tabi_add_field( $wp, 'tabi_footer', 'tabi_footer_email', __( 'E-mail', 'studio-tabi' ), 'oi@studiotabi.com.br' );
	tabi_add_field( $wp, 'tabi_footer', 'tabi_footer_phone', __( 'Telefone', 'studio-tabi' ), '+55 11 9 9999-9999' );
	tabi_add_field( $wp, 'tabi_footer', 'tabi_footer_city', __( 'Cidade', 'studio-tabi' ), 'São Paulo, SP' );
	tabi_add_field( $wp, 'tabi_footer', 'tabi_footer_social', __( 'Redes sociais (uma por linha)', 'studio-tabi' ), "Instagram\nLinkedIn\nBehance\nGitHub", 'textarea' );
	tabi_add_field( $wp, 'tabi_footer', 'tabi_footer_copyright', __( 'Direitos autorais', 'studio-tabi' ), '© 2026 Studio Tabi. Todos os direitos reservados.' );
	tabi_add_field( $wp, 'tabi_footer', 'tabi_footer_signature', __( 'Assinatura', 'studio-tabi' ), 'Feito com precisão em São Paulo' );
} );
