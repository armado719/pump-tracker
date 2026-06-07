# ── Pump Tracker GRS — imagen Docker para Render ────────────────────────────
# Multi-stage: compila assets y dependencias, luego corre PHP-FPM + Nginx
# vía supervisord, escuchando en $PORT (Render lo inyecta en runtime).

# --- Etapa 1: dependencias PHP -----------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts

# --- Etapa 2: build de assets (Vite) ------------------------------------------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# --- Etapa 3: imagen final -----------------------------------------------------
FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx supervisor bash curl \
    libzip-dev libpng-dev oniguruma-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

WORKDIR /var/www/html

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN cp .env.example .env \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY deploy/render/nginx.conf       /etc/nginx/http.d/default.conf
COPY deploy/render/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY deploy/render/start.sh         /start.sh
RUN chmod +x /start.sh

EXPOSE 10000

CMD ["/start.sh"]
