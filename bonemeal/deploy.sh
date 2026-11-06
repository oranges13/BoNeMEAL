#!/usr/bin/env bash
# Deploy BoNeMEAL to DreamHost shared hosting.
# Builds assets locally (no Node on the host), uploads, migrates the APP database only.
#
# Required env: DEPLOY_HOST (ssh host), DEPLOY_PATH (app dir on the server; web docroot must be DEPLOY_PATH/public)
# Optional env: DEPLOY_PHP (php binary on the host, default: php)
set -euo pipefail

: "${DEPLOY_HOST:?set DEPLOY_HOST}"
: "${DEPLOY_PATH:?set DEPLOY_PATH}"
PHP="${DEPLOY_PHP:-php}"

cd "$(dirname "$0")"

echo "==> Installing production PHP dependencies locally"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Building frontend assets"
npm ci
npm run build

echo "==> Uploading"
rsync -az --delete \
  --exclude='.env' --exclude='.git' --exclude='node_modules' --exclude='tests' \
  --exclude='storage/logs/*' --exclude='storage/framework/cache/data/*' \
  --exclude='storage/framework/sessions/*' --exclude='storage/framework/views/*' \
  --exclude='database/*.sqlite' \
  ./ "$DEPLOY_HOST:$DEPLOY_PATH/"

echo "==> Migrating app DB (default connection only) and caching"
ssh "$DEPLOY_HOST" "cd '$DEPLOY_PATH' && \
  $PHP artisan migrate --force && \
  $PHP artisan config:cache && $PHP artisan route:cache && $PHP artisan view:cache"

echo "==> Done. Run '$PHP artisan bandb:smoke' on the host to verify the plugin DB."
