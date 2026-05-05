# syntax=docker/dockerfile:1
# =============================================================================
# Stage 1: Install Composer dependencies (no dev)
# Must run first — Vite needs vendor/filament CSS at build time.
# =============================================================================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

# Mount auth.json as a BuildKit secret so the token is NEVER baked into any layer.
# Build with: docker compose build --secret id=composer_auth,src=auth.json
# auth.json should contain credentials for dikiakbarasyidiq.dev (or your Satis URL).
RUN --mount=type=secret,id=composer_auth,target=auth.json \
    composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --ignore-platform-reqs

# =============================================================================
# Stage 2: Build Vite frontend assets
# =============================================================================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .

# Filament CSS files are imported from vendor/ — copy them in from the vendor stage.
COPY --from=vendor /app/vendor ./vendor

# These get baked into the JS bundle at build time.
# Reverb is proxied via Nginx on port 80 — no separate port needed from the browser.
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
# Stage 3: PHP-FPM runtime (used by app, horizon, reverb services)
# =============================================================================
FROM php:8.3-fpm-bookworm AS runtime

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    # PHP extension dependencies
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libsqlite3-dev \
    libssl-dev \
    # Image optimization tools (for spatie/laravel-image-optimizer)
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    webp \
    # Utilities
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_sqlite \
        pdo_mysql \
        gd \
        zip \
        bcmath \
        intl \
        mbstring \
        pcntl \
        opcache \
        exif

# phpredis (required: REDIS_CLIENT=phpredis in .env)
RUN pecl install redis \
    && docker-php-ext-enable redis

COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini

WORKDIR /var/www/html

# Copy application files
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build

RUN mkdir -p /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache

COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]

# =============================================================================
# Stage 4: Nginx — serves static files + proxies PHP & Reverb WebSocket
# =============================================================================
FROM nginx:1.27-alpine AS webserver

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Bake public assets into the nginx image so it can serve them directly
COPY --from=runtime /var/www/html/public /var/www/html/public
