#!/usr/bin/env bash
# Entrypoint del contenedor en Render — corre en el web service.
# Render inyecta el puerto real en $PORT; lo escribimos en la config de Nginx.
set -e

PORT="${PORT:-10000}"
sed -i "s/__PORT__/${PORT}/g" /etc/nginx/http.d/default.conf

echo "==> Cacheando configuración de Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Ejecutando migraciones..."
php artisan migrate --force

echo "==> Iniciando Nginx + PHP-FPM en el puerto ${PORT}..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
