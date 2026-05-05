#!/bin/sh
set -e

ROLE="${CONTAINER_ROLE:-app}"

echo "[entrypoint] Starting container with role: $ROLE"

case "$ROLE" in

    # -------------------------------------------------------------------------
    # app — PHP-FPM: run migrations, warm caches, start php-fpm
    # -------------------------------------------------------------------------
    app)
        # Ensure SQLite database file exists (first run)
        if [ ! -f /var/www/html/database/database.sqlite ]; then
            echo "[entrypoint] Creating SQLite database file..."
            touch /var/www/html/database/database.sqlite
            chown www-data:www-data /var/www/html/database/database.sqlite
        fi

        echo "[entrypoint] Running migrations..."
        php artisan migrate --force

        echo "[entrypoint] Linking storage..."
        php artisan storage:link --force

        if [ "${APP_ENV}" = "production" ]; then
            echo "[entrypoint] Caching config/routes/views/events..."
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            php artisan event:cache
        fi

        exec php-fpm
        ;;

    # -------------------------------------------------------------------------
    # horizon — queue worker
    # -------------------------------------------------------------------------
    horizon)
        echo "[entrypoint] Starting Laravel Horizon..."
        exec php artisan horizon
        ;;

    # -------------------------------------------------------------------------
    # reverb — WebSocket server
    # -------------------------------------------------------------------------
    reverb)
        HOST="${REVERB_SERVER_HOST:-0.0.0.0}"
        PORT="${REVERB_SERVER_PORT:-8080}"
        echo "[entrypoint] Starting Laravel Reverb on ${HOST}:${PORT}..."
        exec php artisan reverb:start --host="$HOST" --port="$PORT" --no-interaction
        ;;

    # -------------------------------------------------------------------------
    # Fallback — execute whatever was passed as CMD
    # -------------------------------------------------------------------------
    *)
        exec "$@"
        ;;
esac
