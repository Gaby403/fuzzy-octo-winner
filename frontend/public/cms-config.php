<?php
/**
 * Endereço do WordPress — usado só pelo servidor.
 *
 * Fica em PHP de propósito: o Apache executa o arquivo em vez de mostrá-lo,
 * então o endereço do CMS não chega ao navegador. O site conversa apenas com
 * o próprio domínio (api.php faz o repasse).
 *
 * Edite as duas linhas abaixo pelo Gerenciador de Arquivos da Hostinger.
 */

return array(

	// URL do WordPress, sem barra no final.
	'url' => 'https://cms.studiotabi.com.br',

	/**
	 * Chave compartilhada com o plugin (Studio Tabi → Integrações → "Chave do
	 * proxy"). Serve para o WordPress reconhecer que a chamada veio do site e
	 * poder aplicar o limite de envios por visitante, e não por servidor.
	 * Deixe vazio para não usar — o proxy continua funcionando.
	 */
	'token' => '',

);
