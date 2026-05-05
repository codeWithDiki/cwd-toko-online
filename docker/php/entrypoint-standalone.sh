#!/bin/sh
set -e

echo "[standalone] Starting setup..."

# ---------------------------------------------------------------------------
# Storage & bootstrap directories
# ---------------------------------------------------------------------------
mkdir -p \
    /var/www/html/storage/app/public \
    /var/www/html/storage/sqlite \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache

chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache 2>/dev/null || true

chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# ---------------------------------------------------------------------------
# SQLite: create database file if not exists
# ---------------------------------------------------------------------------
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    SQLITE_PATH="${DB_DATABASE:-/var/www/html/storage/sqlite/database.sqlite}"
    if [ ! -f "$SQLITE_PATH" ]; then
        echo "[standalone] Creating SQLite database at $SQLITE_PATH ..."
        mkdir -p "$(dirname "$SQLITE_PATH")"
        touch "$SQLITE_PATH"
        chown www-data:www-data "$SQLITE_PATH"
    fi
fi

# ---------------------------------------------------------------------------
# Laravel bootstrap
# ---------------------------------------------------------------------------
echo "[standalone] Running migrations..."
php artisan migrate --force

echo "[standalone] Linking storage..."
php artisan storage:link --force

if [ "${APP_ENV}" = "production" ]; then
    echo "[standalone] Caching config/routes/views/events..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
fi

# ---------------------------------------------------------------------------
# Nginx config generation
# In standalone mode, php-fpm and reverb are on localhost (same container).
# ---------------------------------------------------------------------------
DOMAIN="${NGINX_DOMAIN:-localhost}"
CERT_PATH="/etc/letsencrypt/live/${DOMAIN}/fullchain.pem"

export NGINX_APP_HOST="${NGINX_APP_HOST:-127.0.0.1}"
export NGINX_REVERB_HOST="${NGINX_REVERB_HOST:-127.0.0.1}"
export NGINX_REVERB_PORT="${NGINX_REVERB_PORT:-8080}"
export NGINX_RESOLVER="${NGINX_RESOLVER:-$(grep '^nameserver' /etc/resolv.conf | head -1 | awk '{print $2}')}"

echo "[standalone] Resolver: ${NGINX_RESOLVER}, FPM: ${NGINX_APP_HOST}, Reverb: ${NGINX_REVERB_HOST}:${NGINX_REVERB_PORT}"

if [ -f "$CERT_PATH" ]; then
    echo "[standalone] SSL certificate found — enabling HTTPS mode."
    envsubst '${NGINX_DOMAIN} ${NGINX_APP_HOST} ${NGINX_REVERB_HOST} ${NGINX_REVERB_PORT} ${NGINX_RESOLVER}' \
        < /etc/nginx/templates/ssl.conf.template \
        > /etc/nginx/conf.d/default.conf
else
    echo "[standalone] No SSL certificate — starting in HTTP mode."
    envsubst '${NGINX_APP_HOST} ${NGINX_REVERB_HOST} ${NGINX_REVERB_PORT} ${NGINX_RESOLVER}' \
        < /etc/nginx/templates/http.conf \
        > /etc/nginx/conf.d/default.conf
fi

# ---------------------------------------------------------------------------
# Start supervisord — manages php-fpm + nginx
# ---------------------------------------------------------------------------
echo "[standalone] Starting supervisord..."
exec supervisord -c /etc/supervisord.conf
