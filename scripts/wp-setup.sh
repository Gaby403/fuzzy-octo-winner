#!/bin/sh
# Instala o WordPress e ativa o plugin Studio Tabi CMS de forma automática.
# Uso:  docker compose run --rm wpcli /setup/wp-setup.sh
set -e

WP_URL="${WP_URL:-http://localhost:8080}"
WP_TITLE="${WP_TITLE:-Studio Tabi CMS}"
WP_ADMIN_USER="${WP_ADMIN_USER:-admin}"
WP_ADMIN_PASS="${WP_ADMIN_PASS:-admin123}"
WP_ADMIN_EMAIL="${WP_ADMIN_EMAIL:-admin@studiotabi.local}"

echo "→ Aguardando o banco de dados..."
until wp db check --path=/var/www/html >/dev/null 2>&1; do
  # Antes do core estar instalado, 'db check' falha; testamos a conexão via config.
  if wp config path --path=/var/www/html >/dev/null 2>&1; then
    break
  fi
  sleep 3
done

if ! wp core is-installed --path=/var/www/html >/dev/null 2>&1; then
  echo "→ Instalando o WordPress..."
  wp core install \
    --path=/var/www/html \
    --url="$WP_URL" \
    --title="$WP_TITLE" \
    --admin_user="$WP_ADMIN_USER" \
    --admin_password="$WP_ADMIN_PASS" \
    --admin_email="$WP_ADMIN_EMAIL" \
    --skip-email
else
  echo "→ WordPress já instalado."
fi

echo "→ Ativando o plugin Studio Tabi CMS..."
wp plugin activate studio-tabi-cms --path=/var/www/html

# Permalinks amigáveis (necessário para a REST API responder em /wp-json).
wp rewrite structure '/%postname%/' --path=/var/www/html
wp rewrite flush --hard --path=/var/www/html

echo ""
echo "✓ Pronto!"
echo "  Site/Admin : $WP_URL/wp-admin  (usuário: $WP_ADMIN_USER / senha: $WP_ADMIN_PASS)"
echo "  API        : $WP_URL/wp-json/studio-tabi/v1/content"
