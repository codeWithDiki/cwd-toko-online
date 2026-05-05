#!/bin/sh
set -e

ROLE="${CONTAINER_ROLE:-app}"

echo "[entrypoint] Starting container with role: $ROLE"

# Ensure the storage directory structure exists after volume mount.
# Docker volumes overlay the image directory, so subdirs may be missing on first run.
mkdir -p \
    /var/www/html/storage/app/public \
    /var/www/html/storage/sqlite \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache

# Seed the shared public/build volume from the baked-in init copy.
# This ensures nginx always reads the same build assets as this container,
# regardless of which Docker image version nginx was built from.
if [ ! -f /var/www/html/public/build/manifest.json ]; then
    echo "[entrypoint] Seeding public/build volume from image..."
    cp -a /var/www/html/public/build_init/. /var/www/html/public/build/
else
    # Always overwrite to ensure the volume matches the current image's build.
    echo "[entrypoint] Updating public/build volume from image..."
    cp -a /var/www/html/public/build_init/. /var/www/html/public/build/
fi

chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

case "$ROLE" in

    # -------------------------------------------------------------------------
    # app — PHP-FPM: run migrations, warm caches, start php-fpm
    # -------------------------------------------------------------------------
    app)
        # SQLite: pastikan file database-nya ada
        if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
            SQLITE_PATH="${DB_DATABASE:-/var/www/html/storage/sqlite/database.sqlite}"
            SQLITE_DIR="$(dirname "$SQLITE_PATH")"
            if [ ! -f "$SQLITE_PATH" ]; then
                echo "[entrypoint] Creating SQLite database file at $SQLITE_PATH ..."
                mkdir -p "$SQLITE_DIR"
                touch "$SQLITE_PATH"
                chown www-data:www-data "$SQLITE_PATH"
            fi
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
