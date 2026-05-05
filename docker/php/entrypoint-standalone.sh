#!/bin/sh
set -e

echo "[standalone] Starting all-in-one container (nginx + php-fpm + redis + reverb + horizon)..."

# ---------------------------------------------------------------------------
# Defaults for standalone mode — can be overridden via platform env vars.
# If an external Redis is set in the platform, those values take precedence.
# ---------------------------------------------------------------------------
export REDIS_HOST="${REDIS_HOST:-127.0.0.1}"
export REDIS_PORT="${REDIS_PORT:-6379}"
# In standalone, Reverb always binds to loopback — nginx proxies it internally.
export REVERB_SERVER_HOST="127.0.0.1"
export REVERB_SERVER_PORT="${REVERB_SERVER_PORT:-8080}"

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
# NOTE: No bootstrap Redis needed — artisan migrate/cache/link commands
# do not connect to Redis. Supervisord will start Redis (priority=10)
# before PHP-FPM (priority=20) and other services.
# ---------------------------------------------------------------------------

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
# Nginx config generation — all upstreams are localhost in standalone mode
# ---------------------------------------------------------------------------
DOMAIN="${NGINX_DOMAIN:-localhost}"
CERT_PATH="/etc/letsencrypt/live/${DOMAIN}/fullchain.pem"

export NGINX_APP_HOST="127.0.0.1"
export NGINX_REVERB_HOST="127.0.0.1"
export NGINX_REVERB_PORT="${REVERB_SERVER_PORT}"
export NGINX_RESOLVER="${NGINX_RESOLVER:-$(grep '^nameserver' /etc/resolv.conf | head -1 | awk '{print $2}')}"

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
# Hand off to supervisord — manages redis, php-fpm, nginx, reverb, horizon
# ---------------------------------------------------------------------------
echo "[standalone] Handing off to supervisord..."
exec supervisord -c /etc/supervisord.conf
