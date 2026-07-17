# Studio Tabi — WordPress Headless

Site do Studio Tabi em arquitetura **headless**: o WordPress atua apenas como CMS (edição de conteúdo + REST API) e o front-end é um app React/Vite com a experiência visual completa (hero animado, sobre, serviços, projetos, FAQ e footer).

```
┌────────────────────┐         GET /wp-json/tabi/v1/content         ┌────────────────────┐
│  WordPress (CMS)   │ ───────────────────────────────────────────▶ │  Front-end React   │
│  tema headless     │ ◀─────────────────────────────────────────── │  (Vite + Motion)   │
│  /wordpress        │   POST (admin, via Application Password)     │  /frontend         │
└────────────────────┘                                              └────────────────────┘
```

## Estrutura do repositório

| Pasta | Descrição |
|---|---|
| `frontend/` | App React (Vite + Tailwind + Motion). É o site público. |
| `wordpress/themes/studio-tabi-headless/` | Tema WordPress headless: modelo de conteúdo, REST API, painel de edição e CORS. |
| `wordpress/elementor-exports/` | Exports Elementor de referência (versão anterior do site, opcional). |

## Como funciona

- O tema registra o endpoint **`GET /wp-json/tabi/v1/content`** (público), que devolve todo o conteúdo do site em JSON: `hero`, `about`, `services`, `projects`, `faq`, `footer`.
- O conteúdo é editado no painel do WP (menu **Conteúdo do Site**) ou pelo painel React em **`/admin`** do front-end, que publica via **`POST /wp-json/tabi/v1/content`** autenticado com [Application Password](https://make.wordpress.org/core/2020/11/05/application-passwords-integration-guide/).
- O front-end busca o conteúdo ao carregar e usa o conteúdo padrão embutido como fallback se o WP estiver fora do ar — o site nunca quebra.
- O CORS da API é liberado apenas para a origem do front-end configurada no painel.
- Visitantes que acessarem o WordPress diretamente são redirecionados para o front-end.

## Setup — WordPress

1. Copie `wordpress/themes/studio-tabi-headless` para `wp-content/themes/` e ative o tema.
2. No menu **Conteúdo do Site**, informe a **URL do front-end** (habilita o CORS e o redirect) e edite o conteúdo.
3. Em **Usuários → Perfil → Application Passwords**, gere uma senha de aplicação para publicar a partir do painel React.

## Setup — Front-end

```bash
cd frontend
cp .env.example .env.local   # defina VITE_WP_URL com a URL do seu WordPress
npm install
npm run dev                  # desenvolvimento
npm run build                # produção (deploy da pasta dist/ em qualquer host estático)
```

Sem `VITE_WP_URL`, o app roda em **modo standalone**: o conteúdo padrão é usado e o `/admin` salva no localStorage (senha local), como no protótipo original.
