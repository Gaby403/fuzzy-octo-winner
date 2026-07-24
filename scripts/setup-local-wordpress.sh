#!/usr/bin/env bash
#
# Cria TODO o ambiente WordPress headless localmente, sem depender do
# wordpress.org (o core é baixado do mirror oficial no GitHub) e sem Docker.
#
#   - Baixa o WordPress (mirror github.com/WordPress/WordPress)
#   - Baixa o WP-CLI
#   - (Opcional) sobe um MariaDB local e cria o banco/usuário
#   - Gera o wp-config.php, instala o WordPress e ativa o plugin studio-tabi-cms
#   - Deixa o site + API prontos em http://localhost:$WP_PORT
#
# Uso:
#   scripts/setup-local-wordpress.sh              # usa um MySQL/MariaDB já rodando
#   WITH_MARIADB=1 scripts/setup-local-wordpress.sh   # sobe um MariaDB local também
#
# Variáveis (com padrões):
#   WP_VERSION=6.7  WP_PORT=8080
#   DB_HOST=127.0.0.1:3306  DB_NAME=wordpress  DB_USER=wordpress  DB_PASS=wordpress
#   WP_ADMIN_USER=admin  WP_ADMIN_PASS=admin123  WP_ADMIN_EMAIL=admin@studiotabi.local
#
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
RUNTIME="$ROOT/runtime"
WP_DIR="$RUNTIME/wordpress"
WPCLI_PHAR="$ROOT/bin/wp-cli.phar"

WP_VERSION="${WP_VERSION:-6.7}"
WP_PORT="${WP_PORT:-8080}"
WP_URL="${WP_URL:-http://localhost:$WP_PORT}"
DB_HOST="${DB_HOST:-127.0.0.1:3306}"
DB_NAME="${DB_NAME:-wordpress}"
DB_USER="${DB_USER:-wordpress}"
DB_PASS="${DB_PASS:-wordpress}"
WP_ADMIN_USER="${WP_ADMIN_USER:-admin}"
WP_ADMIN_PASS="${WP_ADMIN_PASS:-admin123}"
WP_ADMIN_EMAIL="${WP_ADMIN_EMAIL:-admin@studiotabi.local}"
WITH_MARIADB="${WITH_MARIADB:-0}"

log() { printf '\033[1;36m→ %s\033[0m\n' "$*"; }

WP_FLAGS=""
if [ "$(id -u)" = "0" ]; then WP_FLAGS="--allow-root"; fi
wp() { php "$WPCLI_PHAR" $WP_FLAGS --path="$WP_DIR" "$@"; }

mkdir -p "$RUNTIME" "$ROOT/bin"

# ------------------------------------------------------------ 1. MariaDB local
if [ "$WITH_MARIADB" = "1" ]; then
	log "Preparando MariaDB local..."
	if ! command -v mariadbd >/dev/null 2>&1; then
		echo "   Instalando mariadb-server (requer sudo/apt)..."
		export DEBIAN_FRONTEND=noninteractive
		apt-get install -y --no-install-recommends mariadb-server mariadb-client
	fi
	SOCK=/run/mysqld/mysqld.sock
	mkdir -p /run/mysqld && chown mysql:mysql /run/mysqld || true
	[ -f /var/lib/mysql/ibdata1 ] || mariadb-install-db --user=mysql --datadir=/var/lib/mysql >/dev/null 2>&1 || true
	if ! mariadb --socket="$SOCK" -e "SELECT 1" >/dev/null 2>&1; then
		nohup /usr/sbin/mariadbd --user=mysql --datadir=/var/lib/mysql --socket="$SOCK" >/tmp/mariadb.log 2>&1 &
		for _ in $(seq 1 30); do mariadb --socket="$SOCK" -e "SELECT 1" >/dev/null 2>&1 && break; sleep 1; done
	fi
	mariadb --socket="$SOCK" <<-SQL
		CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
		CREATE USER IF NOT EXISTS '$DB_USER'@'127.0.0.1' IDENTIFIED BY '$DB_PASS';
		CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
		GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'127.0.0.1';
		GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
		FLUSH PRIVILEGES;
	SQL
	log "MariaDB pronto (banco: $DB_NAME)."
fi

# ------------------------------------------------------------ 2. WordPress core
if [ ! -f "$WP_DIR/wp-load.php" ]; then
	log "Baixando o WordPress $WP_VERSION (mirror do GitHub)..."
	git clone --depth 1 --branch "$WP_VERSION" https://github.com/WordPress/WordPress.git "$WP_DIR"
	rm -rf "$WP_DIR/.git"
else
	log "WordPress já presente em runtime/wordpress."
fi

# ------------------------------------------------------------ 3. WP-CLI
if [ ! -f "$WPCLI_PHAR" ]; then
	log "Baixando o WP-CLI..."
	curl -sSL https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o "$WPCLI_PHAR"
fi

# ------------------------------------------------------------ 4. Plugin (link)
log "Vinculando o plugin studio-tabi-cms..."
ln -sfn "$ROOT/wordpress/wp-content/plugins/studio-tabi-cms" "$WP_DIR/wp-content/plugins/studio-tabi-cms"

# ------------------------------------------------------------ 5. Config + install
if [ ! -f "$WP_DIR/wp-config.php" ]; then
	log "Gerando wp-config.php..."
	wp config create --dbname="$DB_NAME" --dbuser="$DB_USER" --dbpass="$DB_PASS" \
		--dbhost="$DB_HOST" --dbprefix=wp_ --locale=pt_BR --skip-check
fi

if ! wp core is-installed >/dev/null 2>&1; then
	log "Instalando o WordPress..."
	wp core install --url="$WP_URL" --title="Studio Tabi CMS" \
		--admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASS" \
		--admin_email="$WP_ADMIN_EMAIL" --skip-email
else
	log "WordPress já instalado."
fi

log "Ativando o plugin e ajustando permalinks..."
wp plugin activate studio-tabi-cms
wp rewrite structure '/%postname%/' >/dev/null
wp rewrite flush --hard >/dev/null

cat <<EOF

╭──────────────────────────────────────────────────────────────╮
│  Ambiente pronto ✔                                           │
╰──────────────────────────────────────────────────────────────╯
  Admin WordPress : $WP_URL/wp-admin   ($WP_ADMIN_USER / $WP_ADMIN_PASS)
  API de conteúdo : $WP_URL/wp-json/studio-tabi/v1/content

  Para iniciar o servidor do CMS:
     php -S 127.0.0.1:$WP_PORT scripts/wp-router.php

  Para o front-end (noutro terminal):
     cd frontend && echo "VITE_WP_API=$WP_URL" > .env && npm install && npm run dev
EOF
