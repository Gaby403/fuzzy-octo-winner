# Deploy na Hostinger — Studio Tabi (headless)

Este projeto tem **duas rotas** independentes que conversam por HTTP:

| Rota | O que é | Onde roda na Hostinger |
|------|---------|------------------------|
| **Back-end (CMS)** | WordPress + MySQL + plugin `studio-tabi-cms` | Hospedagem WordPress / PHP (ex.: `cms.seudominio.com.br`) |
| **Front-end (site)** | React/Vite compilado para arquivos estáticos | `public_html` do domínio principal (ex.: `seudominio.com.br`) |

Editar o conteúdo no WordPress reflete **imediatamente** no site, porque o
front-end lê a API REST do WordPress a cada carregamento.

```
Visitante ─▶ seudominio.com.br (React estático)
                  │  fetch  /wp-json/studio-tabi/v1/content
                  ▼
           cms.seudominio.com.br (WordPress + MySQL)  ◀── você edita aqui
```

---

## Parte 1 — Back-end WordPress (o CMS)

### 1.1 Criar o WordPress
Na hPanel da Hostinger:

1. **Sites → Criar site → WordPress** (ou instale o WordPress em um subdomínio).
2. Recomendado: use um **subdomínio** dedicado, por exemplo
   `cms.seudominio.com.br`. Crie-o em **Domínios → Subdomínios** e instale o
   WordPress nele. O MySQL é provisionado automaticamente pela Hostinger.

### 1.2 Instalar o plugin `studio-tabi-cms`
Gere o pacote (na sua máquina, dentro do repositório):

```bash
bash scripts/build-plugin-zip.sh      # cria studio-tabi-cms.zip
```

Depois, no WordPress:

1. **Plugins → Adicionar novo → Enviar plugin** → escolha `studio-tabi-cms.zip`.
2. Clique em **Instalar agora** e depois em **Ativar**.
3. Na ativação, o conteúdo padrão (serviços, projetos, FAQ, textos) é criado
   automaticamente. Você verá o menu **Studio Tabi** no admin.

> Alternativa sem zip: envie a pasta
> `wordpress/wp-content/plugins/studio-tabi-cms` para
> `public_html/wp-content/plugins/` pelo **Gerenciador de Arquivos** ou FTP e
> ative em **Plugins**.

### 1.3 Ativar links permanentes
**Configurações → Links permanentes → Nome do post → Salvar.**
Isso garante que a API responda em `/wp-json/...` (a Hostinger usa
Apache/LiteSpeed com `mod_rewrite`).

### 1.4 Restringir o CORS ao seu site (recomendado)
Por padrão a API aceita qualquer origem (`*`). Para limitar ao seu domínio,
edite o `wp-config.php` (via Gerenciador de Arquivos) e adicione **antes** de
`/* That's all, stop editing! */`:

```php
define( 'STCMS_CORS_ORIGIN', 'https://seudominio.com.br' );
```

### 1.5 Testar a API
Abra no navegador:

```
https://cms.seudominio.com.br/wp-json/studio-tabi/v1/content
```

Deve retornar um JSON com `site`, `hero`, `about`, `services`, `projects`,
`faq`, `footer` e `pages`.

### 1.6 Editar o conteúdo
No admin do WordPress:

- **Studio Tabi → Conteúdo** — textos do Hero, Sobre (estatísticas e pilares),
  Rodapé, título do site, **logo** e **favicon**.
- **Serviços / Projetos / FAQ** — cada um com seu próprio menu (adicionar,
  editar, reordenar por “ordem”).
- **Páginas** — crie páginas normalmente; elas aparecem no rodapé do site e em
  `https://seudominio.com.br/p/{slug}`.
- **Mídia** — envie imagens; use-as no logo, favicon, capa de projeto ou dentro
  do conteúdo das páginas.

---

## Parte 2 — Front-end React (o site)

### 2.1 Configurar a URL da API
No seu computador, dentro de `frontend/`:

```bash
cp .env.example .env
```

Edite `.env` e aponte para o WordPress publicado:

```
VITE_WP_API=https://cms.seudominio.com.br
```

### 2.2 Compilar
```bash
cd frontend
npm install
npm run build
```

Isso gera a pasta **`frontend/dist/`** com o site estático (HTML/CSS/JS) e o
`.htaccess` de roteamento SPA já incluído.

### 2.3 Enviar para a Hostinger
Envie **todo o conteúdo de `frontend/dist/`** (não a pasta em si, e sim os
arquivos de dentro, incluindo o `.htaccess`) para a pasta `public_html` do
domínio principal:

- **Gerenciador de Arquivos** (hPanel): entre em `public_html`, faça upload do
  zip do conteúdo de `dist/` e extraia; **ou**
- **FTP** (FileZilla): arraste os arquivos de `dist/` para `public_html`.

> O `.htaccess` incluído faz o roteamento do lado do cliente funcionar — assim
> `/admin` e `/p/{slug}` funcionam mesmo ao recarregar a página.

### 2.4 Pronto
Acesse `https://seudominio.com.br`. O site carrega e busca o conteúdo do
WordPress. Qualquer alteração feita no admin aparece ao recarregar.

---

## Resumo de portas / URLs

| Item | URL |
|------|-----|
| Site (front-end) | `https://seudominio.com.br` |
| WordPress (admin) | `https://cms.seudominio.com.br/wp-admin` |
| API de conteúdo | `https://cms.seudominio.com.br/wp-json/studio-tabi/v1/content` |
| Página no site | `https://seudominio.com.br/p/{slug}` |

Na Hostinger, HTTP/HTTPS (80/443) já são gerenciados pelo painel — não é
preciso abrir portas manualmente. Ative o **SSL grátis** para os dois domínios
em **Segurança → SSL**.

---

## Rodando tudo localmente antes de subir (opcional)

**Sem Docker** (baixa o WordPress do mirror do GitHub, não do wordpress.org):

```bash
WITH_MARIADB=1 scripts/setup-local-wordpress.sh   # banco + WP + plugin, tudo pronto
php -S 127.0.0.1:8080 scripts/wp-router.php       # inicia o CMS
```

**Com Docker:**

```bash
docker compose up -d db wordpress
docker compose run --rm wpcli /setup/wp-setup.sh   # instala o WP + ativa o plugin
```

- WordPress: <http://localhost:8080/wp-admin>  (admin / admin123)
- API: <http://localhost:8080/wp-json/studio-tabi/v1/content>

Front-end apontando para o WP local:

```bash
cd frontend
echo "VITE_WP_API=http://localhost:8080" > .env
npm install && npm run dev        # http://localhost:5173
```

## Sitemap (sem build)

O `sitemap.xml` é montado no próprio servidor pelo `sitemap.php`, que já vem no
ZIP do site. Ele busca as URLs no WordPress, guarda em cache por 6 horas e serve
o resultado — não é preciso rodar Node nem refazer o build quando você publica
um artigo, um serviço ou uma tradução.

Requisitos: subir o `sitemap.php`, o `sitemap-fallback.xml` e o `.htaccess` junto
com o resto do site (já estão no ZIP) e manter o endereço do WordPress correto no
`config.js`.

Para conferir qual origem respondeu:

    curl -sI https://studiotabi.com.br/sitemap.xml | grep -i x-sitemap-origem

- `cms` — recém-buscado no WordPress
- `cache` — cache de até 6 horas
- `cache-vencido` — WordPress fora do ar, servindo a última cópia boa
- `reserva` — sem cache e sem WordPress, servindo o arquivo estático do ZIP

Para forçar a atualização antes das 6 horas, apague o `sitemap-cache.xml` pelo
Gerenciador de Arquivos.
