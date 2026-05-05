#!/bin/sh
set -e

DOMAIN="${NGINX_DOMAIN:-localhost}"
CERT_PATH="/etc/letsencrypt/live/${DOMAIN}/fullchain.pem"

if [ -f "$CERT_PATH" ]; then
    echo "[nginx] SSL certificate found for ${DOMAIN} — enabling HTTPS mode."
    envsubst '${NGINX_DOMAIN}' \
        < /etc/nginx/templates/ssl.conf.template \
        > /etc/nginx/conf.d/default.conf
else
    echo "[nginx] No SSL certificate found — starting in HTTP mode."
    cp /etc/nginx/templates/http.conf /etc/nginx/conf.d/default.conf
fi

exec nginx -g "daemon off;"
