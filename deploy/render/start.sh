#!/usr/bin/env bash
# Entrypoint del contenedor en Render — corre en el web service.
# Render inyecta el puerto real en $PORT; lo escribimos en la config de Nginx.
set -e

PORT="${PORT:-10000}"
sed -i "s/__PORT__/${PORT}/g" /etc/nginx/http.d/default.conf

# Copiar certificado Aiven al path que espera Laravel
if [ -f /etc/secrets/aiven-ca.pem ]; then
    cp /etc/secrets/aiven-ca.pem /var/www/html/storage/aiven-ca.pem
fi

echo "==> Cacheando configuración de Laravel..."
php artisan config:cache  || echo "WARN: config:cache falló, continuando sin caché de config"
php artisan route:cache   || echo "WARN: route:cache falló, continuando sin caché de rutas"
php artisan view:clear    2>/dev/null || true
php artisan view:cache    || echo "WARN: view:cache falló, continuando sin caché de vistas"

echo "==> Ejecutando migraciones..."
php artisan migrate --force

echo "==> Iniciando Nginx + PHP-FPM en el puerto ${PORT}..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
