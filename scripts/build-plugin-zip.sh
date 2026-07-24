#!/usr/bin/env bash
# Gera o pacote studio-tabi-cms.zip pronto para upload no WordPress
# (Plugins → Adicionar novo → Enviar plugin).
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SRC="$ROOT/wordpress/wp-content/plugins/studio-tabi-cms"
OUT="$ROOT/studio-tabi-cms.zip"

cd "$ROOT/wordpress/wp-content/plugins"
rm -f "$OUT"
zip -r "$OUT" studio-tabi-cms \
  -x '*/.DS_Store' -x '*/node_modules/*' >/dev/null

echo "✓ Plugin empacotado em: $OUT"
