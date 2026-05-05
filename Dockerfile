# syntax=docker/dockerfile:1

# =============================================================================
# Stage 1 — Composer dependencies
# =============================================================================
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./

# COMPOSER_AUTH priority:
#   1. Build ARG (docker compose / CI pass via --build-arg)
#   2. build_env secret (Helipod auto-injects dashboard variables here)
#   3. composer_auth secret (local: reads ./auth.json)
ARG COMPOSER_AUTH=""
COPY docker/composer-install.sh /composer-install.sh
RUN chmod +x /composer-install.sh
RUN --mount=type=secret,id=composer_auth,dst=/run/secrets/composer_auth,required=false \
    --mount=type=secret,id=build_env,dst=/run/secrets/build_env,required=false \
    /composer-install.sh

# =============================================================================
# Stage 2 — Vite frontend assets
# =============================================================================
FROM node:22-alpine AS frontend

WORKDIR /app
COPY package*.json ./
RUN npm ci

COPY . .
COPY --from=vendor /app/vendor ./vendor

ARG VITE_APP_NAME="CodeWithDiki Toko Online"
ARG VITE_REVERB_APP_KEY=laravel-herd
ARG VITE_REVERB_HOST=localhost
ARG VITE_REVERB_PORT=80
ARG VITE_REVERB_SCHEME=http

ENV VITE_APP_NAME=$VITE_APP_NAME \
    VITE_REVERB_APP_KEY=$VITE_REVERB_APP_KEY \
    VITE_REVERB_HOST=$VITE_REVERB_HOST \
    VITE_REVERB_PORT=$VITE_REVERB_PORT \
    VITE_REVERB_SCHEME=$VITE_REVERB_SCHEME

RUN npm run build

# =============================================================================
# Stage 3 — PHP-FPM base (shared by docker-compose services)
# =============================================================================
FROM php:8.4-fpm-bookworm AS runtime

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev libzip-dev \
    libicu-dev libonig-dev libsqlite3-dev libssl-dev \
    jpegoptim optipng pngquant gifsicle webp \
    unzip curl \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_sqlite pdo_mysql gd zip bcmath intl mbstring pcntl opcache exif

RUN pecl install redis && docker-php-ext-enable redis

COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-docker-listen.conf

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build

RUN mkdir -p /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache

COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
EXPOSE 9000

# =============================================================================
# Stage 4 — Webserver (docker-compose only, target: webserver)
# Lightweight nginx:alpine — proxies to separate app/reverb containers.
# =============================================================================
FROM nginx:alpine AS webserver

RUN apk add --no-cache gettext

RUN rm -f /etc/nginx/conf.d/default.conf

COPY docker/nginx/templates /etc/nginx/templates
COPY docker/nginx/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
HEALTHCHECK --interval=10s --timeout=3s --start-period=10s --retries=3 \
    CMD wget -qO- http://localhost/healthz || exit 1
EXPOSE 80 443

# =============================================================================
# Stage 5 — Standalone (DEFAULT)
# All-in-one: Redis + PHP-FPM + Nginx + Reverb + Horizon via supervisord.
# Plain `docker build .` produces this image — no --target needed.
# =============================================================================
FROM runtime AS standalone

RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    gettext-base \
    redis-server \
    && rm -rf /var/lib/apt/lists/*

RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf

COPY docker/nginx/templates /etc/nginx/templates
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/php/entrypoint-standalone.sh /entrypoint-standalone.sh
RUN chmod +x /entrypoint-standalone.sh

ENTRYPOINT ["/entrypoint-standalone.sh"]
HEALTHCHECK --interval=15s --timeout=5s --start-period=90s --retries=5 \
    CMD curl -sf http://localhost/healthz || exit 1
EXPOSE 80
