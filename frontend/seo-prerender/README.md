# SEO server-side (opcional)

O `index.php` deste diretório injeta as meta tags (título, descrição, Open
Graph/Twitter) diretamente no HTML inicial, buscando os valores no WordPress —
útil para buscadores/prévias que não executam JavaScript.

> **É opcional.** O site funciona sem ele: as meta tags também são aplicadas
> pelo próprio app (client-side). Só ative isto se tiver certeza de que o seu
> host executa PHP na pasta do site.

## Como ativar
1. Copie `index.php` para a `public_html` do domínio (junto do `index.html`).
2. No `.htaccess`, troque:
   - `DirectoryIndex index.html` → `DirectoryIndex index.php index.html`
   - a última regra `RewriteRule ^ index.html [L]` → `RewriteRule ^ index.php [L]`
3. Abra o site e veja o código-fonte (Ctrl+U): as meta tags devem aparecer.

Se a página ficar em branco, o host não está executando o `index.php` como
esperado — remova o `index.php` e reverta o `.htaccess`; o site volta ao normal.
