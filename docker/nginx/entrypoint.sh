#!/bin/sh
set -e

DOMAIN="${NGINX_DOMAIN:-localhost}"
CERT_PATH="/etc/letsencrypt/live/${DOMAIN}/fullchain.pem"

# Upstream hostnames — override in Helipod via environment variables.
# If all services run in the same pod (single Helipod DNS), use localhost.
export NGINX_APP_HOST="${NGINX_APP_HOST:-app}"
export NGINX_REVERB_HOST="${NGINX_REVERB_HOST:-reverb}"
export NGINX_REVERB_PORT="${NGINX_REVERB_PORT:-8080}"

# Auto-detect DNS resolver from pod/container's resolv.conf.
# In Docker this is 127.0.0.11; in K3s/K8s this is the CoreDNS ClusterIP.
# Override via NGINX_RESOLVER env var if auto-detection fails.
export NGINX_RESOLVER="${NGINX_RESOLVER:-$(grep '^nameserver' /etc/resolv.conf | head -1 | awk '{print $2}')}"

echo "[nginx] Resolver: ${NGINX_RESOLVER}, App: ${NGINX_APP_HOST}, Reverb: ${NGINX_REVERB_HOST}:${NGINX_REVERB_PORT}"

if [ -f "$CERT_PATH" ]; then
    echo "[nginx] SSL certificate found for ${DOMAIN} — enabling HTTPS mode."
    envsubst '${NGINX_DOMAIN} ${NGINX_APP_HOST} ${NGINX_REVERB_HOST} ${NGINX_REVERB_PORT} ${NGINX_RESOLVER}' \
        < /etc/nginx/templates/ssl.conf.template \
        > /etc/nginx/conf.d/default.conf
else
    echo "[nginx] No SSL certificate found — starting in HTTP mode."
    envsubst '${NGINX_APP_HOST} ${NGINX_REVERB_HOST} ${NGINX_REVERB_PORT} ${NGINX_RESOLVER}' \
        < /etc/nginx/templates/http.conf \
        > /etc/nginx/conf.d/default.conf
fi

exec nginx -g "daemon off;"
