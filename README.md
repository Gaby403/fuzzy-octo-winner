# Studio Tabi — Tema WordPress

Tema **WordPress nativo** (100% PHP, sem React) do site Studio Tabi. Feito para ser fácil de editar: os textos são alterados no **Personalizar** com pré-visualização ao vivo, e Serviços/Projetos são criados como itens no painel.

## Instalação

1. **Aparência → Temas → Adicionar novo → Enviar tema** → envie o zip → **Instalar** → **Ativar**.
2. **Configurações → Links Permanentes** → escolha **"Nome do post"** e salve (para as páginas de projeto e o blog terem endereços bonitos).
3. (Blog) **Configurações → Leitura**: crie uma Página chamada "Blog" e defina-a como *Página de posts*, ou deixe os posts na home.

## Como editar

| O quê | Onde |
|---|---|
| **Todos os textos** (hero, sobre, títulos, tarjas, botões, FAQ, rodapé) | **Aparência → Personalizar → Textos do site** (com pré-visualização ao vivo) |
| **Serviços** | Menu **Serviços** no painel — cada serviço é um item (título + descrição) |
| **Projetos** | Menu **Projetos** — imagem destacada + campos (categoria, ano, cliente, duração, escopo, resultados) + texto do case no editor |
| **Blog** | Menu **Posts** do WordPress |
| **Páginas** | Menu **Páginas** do WordPress |
| **Logo e menu** | Personalizar → Identidade do site / Menus |

Nos campos de **título**, coloque uma linha por linha e envolva uma linha com `*asteriscos*` para deixá-la **vermelha** (ex.: `TRABALHOS` / `*SELECIONADOS.*`).

## Por que mudou

A versão anterior embutia um app React e injetava o conteúdo no HTML, o que causava problemas de cache (as edições não apareciam). Esta versão renderiza tudo no servidor com PHP — como qualquer tema WordPress — então a edição funciona de forma previsível e o cache do servidor se comporta normalmente.

## Estrutura

```
studio-tabi/
├─ style.css, functions.php
├─ header.php, footer.php, front-page.php
├─ index.php (blog), single.php (post), page.php
├─ archive-projeto.php, single-projeto.php, 404.php
├─ inc/  → customizer.php, post-types.php, template-helpers.php
└─ assets/  → css/theme.css, js/theme.js
```

> A pasta `frontend/` contém o protótipo React original (Figma Make), mantido apenas como referência de design. Não é usada pelo tema.
