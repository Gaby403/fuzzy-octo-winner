=== Studio Tabi CMS (Headless) ===
Contributors: studiotabi
Tags: headless, rest-api, cms, decoupled
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later

CMS nativo headless para o site Studio Tabi. Gerencia todo o conteúdo no
WordPress e expõe via API REST para o front-end React desacoplado.

== Descrição ==

Este plugin transforma o WordPress no back-end (CMS) de um site headless.
Ele adiciona:

* Tipos de conteúdo: **Serviços**, **Projetos** e **FAQ**.
* Uma página de configurações **Studio Tabi → Conteúdo** para os blocos fixos:
  Site (título, logo, **favicon**), Hero, Sobre (parágrafos, estatísticas,
  pilares) e Rodapé.
* Suporte às **Páginas** nativas do WordPress (crie páginas normalmente; elas
  aparecem no front-end em /p/{slug}).
* Uma API REST em `studio-tabi/v1` que entrega todo o conteúdo em um único
  JSON, no formato exato que o front-end consome.
* Cabeçalhos CORS para permitir o consumo por um front-end em outro domínio.

== Endpoints ==

* `GET /wp-json/studio-tabi/v1/content`     — Conteúdo completo do site.
* `GET /wp-json/studio-tabi/v1/pages`       — Lista de páginas publicadas.
* `GET /wp-json/studio-tabi/v1/page/{slug}` — Uma página (título + HTML).

== Instalação ==

1. Envie a pasta `studio-tabi-cms` para `wp-content/plugins/` (ou faça upload
   do .zip em Plugins → Adicionar novo → Enviar plugin).
2. Ative o plugin. Na ativação, o conteúdo padrão é criado automaticamente.
3. (Opcional) Restrinja o CORS ao domínio do front-end definindo, no
   `wp-config.php`:  `define( 'STCMS_CORS_ORIGIN', 'https://seudominio.com.br' );`

== Changelog ==

= 1.0.0 =
* Versão inicial: CPTs, opções, páginas, API REST e CORS.
