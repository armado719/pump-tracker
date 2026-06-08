#!/usr/bin/env bash
# Entrypoint del contenedor en Render — corre en el web service.
# Render inyecta el puerto real en $PORT; lo escribimos en la config de Nginx.
set -e

PORT="${PORT:-10000}"
sed -i "s/__PORT__/${PORT}/g" /etc/nginx/http.d/default.conf

# Validar que APP_KEY esté configurado (sin mostrarlo)
if [ -z "${APP_KEY}" ]; then
    echo "ERROR FATAL: APP_KEY no está configurado en las variables de entorno de Render."
    exit 1
fi
echo "==> APP_KEY: configurado correctamente."

# Garantizar que los directorios de storage existen y son escribibles por www-data
mkdir -p storage/framework/views \
         storage/framework/sessions \
         storage/framework/cache/data \
         storage/framework/testing \
         storage/logs \
         storage/app/private \
         storage/app/public \
         bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Copiar certificado Aiven y asegurar que sea legible por www-data (PHP-FPM)
if [ -f /etc/secrets/aiven-ca.pem ]; then
    chmod 644 /etc/secrets/aiven-ca.pem
    cp /etc/secrets/aiven-ca.pem /var/www/html/storage/aiven-ca.pem
    chmod 644 /var/www/html/storage/aiven-ca.pem
fi

echo "==> Cacheando configuración de Laravel..."
php artisan config:cache  || echo "WARN: config:cache falló, continuando sin caché de config"
php artisan route:cache   || echo "WARN: route:cache falló, continuando sin caché de rutas"
php artisan view:clear    2>/dev/null || true

echo "==> Ejecutando migraciones..."
php artisan migrate --force

echo "==> Iniciando Nginx + PHP-FPM en el puerto ${PORT}..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
