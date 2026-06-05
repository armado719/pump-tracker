#!/usr/bin/env bash
# deploy.sh — Pump Tracker GRS
# Uso: bash deploy.sh
# Ejecutar como el usuario que tiene permisos sobre /var/www/pump-tracker

set -e

APP_DIR="/var/www/pump-tracker"
PHP="php8.3"

echo "==> Actualizando código..."
cd "$APP_DIR"
git pull origin main

echo "==> Instalando dependencias PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Instalando dependencias JS y compilando assets..."
npm ci --omit=dev
npm run build

echo "==> Modo mantenimiento ON..."
$PHP artisan down --retry=60 --secret=grs-deploy-secret

echo "==> Migraciones..."
$PHP artisan migrate --force

echo "==> Limpiando caché..."
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan event:cache

echo "==> Permisos de storage..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "==> Reiniciando workers..."
$PHP artisan queue:restart
supervisorctl restart pump-tracker-worker:*

echo "==> Modo mantenimiento OFF..."
$PHP artisan up

echo ""
echo "✓ Deploy completado: $(date '+%d/%m/%Y %H:%M')"
