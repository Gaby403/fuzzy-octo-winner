# Studio Tabi — Tema WordPress

Site do Studio Tabi como um **tema WordPress unificado**: o próprio WordPress serve o site (interface React embutida) e todo o conteúdo é editado por campos amigáveis no painel. Sem API externa, sem CORS, sem hospedagem separada.

```
┌─────────────────────────────────────────────┐
│  WordPress (site.gabymuniz.me)               │
│                                              │
│  wp-admin → "Conteúdo do Site" (campos)      │
│      │ salva em option                       │
│      ▼                                        │
│  front-page.php injeta o conteúdo inline     │
│      │  window.__TABI_CONTENT__              │
│      ▼                                        │
│  App React embutido (assets/) renderiza      │
└─────────────────────────────────────────────┘
```

## Estrutura do repositório

| Pasta | Descrição |
|---|---|
| `wordpress/themes/studio-tabi/` | **O tema** — instale este no WordPress. Já inclui o app React compilado em `assets/`. |
| `frontend/` | Código-fonte React (Vite). Usado para gerar os `assets/` do tema. |
| `wordpress/elementor-exports/` | Exports Elementor de referência (opcional). |

## Como funciona

- O conteúdo é editado no painel do WP em **Conteúdo do Site** (campos por seção: hero, sobre, serviços, projetos, FAQ, rodapé) e salvo numa *option*.
  - Cada **projeto** tem página própria (`/projeto/slug`) com campos individuais, seletor de cor, **construtor de gradiente** visual e **galeria de imagens** (Biblioteca de Mídia).
- O **blog** usa os **Posts nativos do WordPress** (menu Posts), exibidos pelo app em `/blog` e `/blog/slug`.
- `front-page.php` monta o `#root` do app; `functions.php` enfileira o bundle (`assets/*.js` + `assets/*.css`), injeta o conteúdo inline em `window.__TABI_CONTENT__` e registra as rotas internas (`/servicos`, `/projeto/...`, `/blog`).
- O app React lê o conteúdo já na primeira renderização — sem chamada de rede para o conteúdo do site, então **não há problema de CORS**.
- Como o WordPress serve o próprio site, front e back moram no mesmo domínio.

> Uma página **Editor JSON (avançado)** continua disponível como submenu, para edição direta ou backup.

> **Links permanentes:** defina em Configurações → Links Permanentes a opção **"Nome do post"** para as rotas internas (serviços, projetos, blog) funcionarem. O tema avisa no painel se estiver pendente.

## Instalação

1. No WP admin: **Aparência → Temas → Adicionar novo → Enviar tema**, envie o zip do tema (`studio-tabi`) e ative.
2. Vá em **Conteúdo do Site** e edite os textos. Salve — o site já reflete as alterações.

## Desenvolvimento (regenerar o app)

Ao alterar o código React em `frontend/`, recompile e copie os assets para o tema:

```bash
cd frontend
npm install
npm run build
# copie o bundle gerado para dentro do tema:
rm -f ../wordpress/themes/studio-tabi/assets/*
cp dist/assets/*.js dist/assets/*.css ../wordpress/themes/studio-tabi/assets/
```

Para desenvolver o visual isoladamente (sem WordPress), `npm run dev` roda o app em modo standalone com o conteúdo padrão.
