#!/bin/sh
set -e

DOMAIN="${NGINX_DOMAIN:-localhost}"
CERT_PATH="/etc/letsencrypt/live/${DOMAIN}/fullchain.pem"

# Upstream hostnames — override in Helipod via environment variables
export NGINX_APP_HOST="${NGINX_APP_HOST:-app}"
export NGINX_REVERB_HOST="${NGINX_REVERB_HOST:-reverb}"
export NGINX_REVERB_PORT="${NGINX_REVERB_PORT:-8080}"

if [ -f "$CERT_PATH" ]; then
    echo "[nginx] SSL certificate found for ${DOMAIN} — enabling HTTPS mode."
    envsubst '${NGINX_DOMAIN} ${NGINX_APP_HOST} ${NGINX_REVERB_HOST} ${NGINX_REVERB_PORT}' \
        < /etc/nginx/templates/ssl.conf.template \
        > /etc/nginx/conf.d/default.conf
else
    echo "[nginx] No SSL certificate found — starting in HTTP mode."
    envsubst '${NGINX_APP_HOST} ${NGINX_REVERB_HOST} ${NGINX_REVERB_PORT}' \
        < /etc/nginx/templates/http.conf \
        > /etc/nginx/conf.d/default.conf
fi

exec nginx -g "daemon off;"
