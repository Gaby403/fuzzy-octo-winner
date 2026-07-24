# Studio Tabi — Site headless (WordPress + React)

Site institucional do **Studio Tabi** implementado no padrão **WordPress
headless**: o conteúdo é gerenciado num CMS (WordPress) e exibido por um
front-end React desacoplado que preserva 100% das animações do layout original.

São **duas rotas** independentes, ambas prontas para deploy na Hostinger:

```
┌─────────────────────────────┐        REST/JSON        ┌────────────────────────────┐
│  FRONT-END  (React + Vite)  │  ───────────────────▶   │  BACK-END  (WordPress + DB) │
│  site estático, animado     │   /wp-json/studio-tabi  │  CMS nativo + painel admin  │
│  seudominio.com.br          │        /v1/content      │  cms.seudominio.com.br      │
└─────────────────────────────┘                         └────────────────────────────┘
        exibe o conteúdo                                     você edita aqui
```

> **Editar no WordPress reflete direto no site.** O front-end lê a API do
> WordPress a cada carregamento — textos, imagens, serviços, projetos, FAQ,
> favicon e páginas.

## Estrutura do repositório

```
.
├── frontend/                         # Site React/Vite (rota do front-end)
│   ├── src/app/store/content.ts      #   cliente da API + fallback offline
│   ├── src/app/Root.tsx              #   carrega conteúdo + favicon/título do WP
│   ├── src/app/pages/Page.tsx        #   renderiza páginas criadas no WP (/p/:slug)
│   ├── public/.htaccess              #   roteamento SPA para Apache/LiteSpeed
│   └── .env.example                  #   VITE_WP_API=...
│
├── wordpress/wp-content/plugins/
│   └── studio-tabi-cms/              # Plugin do CMS (rota do back-end)
│       ├── studio-tabi-cms.php       #   bootstrap
│       └── includes/                 #   CPTs, opções, meta boxes, REST, seed
│
├── docker-compose.yml                # WordPress + MySQL local (opção com Docker)
├── scripts/
│   ├── setup-local-wordpress.sh      #   ambiente completo SEM Docker (WP via GitHub)
│   ├── wp-router.php                 #   router do php -S para servir o WordPress
│   ├── wp-setup.sh                   #   instala WP + ativa o plugin (via WP-CLI/Docker)
│   ├── build-plugin-zip.sh           #   empacota o plugin para upload
│   └── contract-test.php             #   testa o contrato back-end → front-end
└── DEPLOY.md                         # Passo a passo de deploy na Hostinger
```

## O que dá para gerenciar no WordPress

| No admin do WordPress | Aparece no site |
|-----------------------|-----------------|
| **Studio Tabi → Conteúdo** | Hero, Sobre (parágrafos, estatísticas, pilares), Rodapé |
| **Studio Tabi → Conteúdo** (Site) | Título, **logo** e **favicon** |
| **Serviços** (CPT) | Seção de serviços |
| **Projetos** (CPT) | Portfólio (com desafio, solução, resultados, capa) |
| **FAQ** (CPT) | Perguntas frequentes |
| **Páginas** (nativo) | Páginas novas em `/p/{slug}`, listadas no rodapé |
| **Mídia** | Imagens usadas em qualquer campo acima |

## Início rápido (local)

Pré-requisitos: Node 18+, PHP 8+ e um MySQL/MariaDB (o script pode subir um).

### Opção A — sem Docker (baixa o WordPress do mirror do GitHub)

Não depende do wordpress.org. Um comando prepara banco + WordPress + plugin:

```bash
# Sobe um MariaDB local, baixa o WordPress, instala e ativa o plugin
WITH_MARIADB=1 scripts/setup-local-wordpress.sh
#   → WP admin:  http://localhost:8080/wp-admin  (admin / admin123)
#   → API:       http://localhost:8080/wp-json/studio-tabi/v1/content

# Inicia o servidor do CMS
php -S 127.0.0.1:8080 scripts/wp-router.php
```

> Já tem um MySQL rodando? Rode sem `WITH_MARIADB=1` e informe as credenciais
> por variáveis (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).

### Opção B — com Docker (WordPress + MySQL em contêineres)

```bash
docker compose up -d db wordpress
docker compose run --rm wpcli /setup/wp-setup.sh
```

### Front-end (em qualquer uma das opções)

```bash
cd frontend
cp .env.example .env          # VITE_WP_API=http://localhost:8080
npm install
npm run dev                   # http://localhost:5173
```

Sem back-end configurado (`VITE_WP_API` vazio), o site roda com o **conteúdo
padrão embutido** — útil para preview/offline.

## Testes

O contrato de dados entre back-end e front-end é verificado sem precisar subir
o WordPress:

```bash
php scripts/contract-test.php     # valida o JSON da API contra o modelo SiteContent
```

## Deploy na Hostinger

Guia completo em **[DEPLOY.md](DEPLOY.md)** — publica o WordPress (CMS) num
subdomínio e o front-end estático no domínio principal, com CORS e SSL.

## API

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/wp-json/studio-tabi/v1/content` | Todo o conteúdo do site (um JSON) |
| GET | `/wp-json/studio-tabi/v1/pages` | Lista de páginas publicadas |
| GET | `/wp-json/studio-tabi/v1/page/{slug}` | Uma página (título + HTML) |

## Créditos

Layout original “Studio Tabi” (Figma Make) — React 18, Vite 6, Tailwind v4,
Motion. Camada headless (plugin WordPress + integração REST) adicionada sobre
esse layout.
