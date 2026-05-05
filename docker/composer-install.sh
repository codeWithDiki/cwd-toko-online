#!/bin/sh
set -e

# Resolve COMPOSER_AUTH from multiple sources (checked in order):
#   1. COMPOSER_AUTH build ARG — already exported into env by Dockerfile ARG
#   2. build_env secret — Helipod injects all dashboard variables here
#   3. composer_auth secret — local docker compose (./auth.json)
#   4. auth.json in build context — fallback for plain docker build

if [ -z "$COMPOSER_AUTH" ] && [ -f /run/secrets/build_env ]; then
    _val=$(grep -m1 '^COMPOSER_AUTH=' /run/secrets/build_env | cut -d= -f2-)
    # Strip surrounding single or double quotes if present
    _val=$(printf '%s' "$_val" | sed "s/^'//;s/'$//;s/^\x22//;s/\x22$//")
    [ -n "$_val" ] && COMPOSER_AUTH="$_val"
fi

if [ -z "$COMPOSER_AUTH" ] && [ -s /run/secrets/composer_auth ]; then
    COMPOSER_AUTH=$(cat /run/secrets/composer_auth)
fi

if [ -z "$COMPOSER_AUTH" ] && [ -s auth.json ]; then
    COMPOSER_AUTH=$(cat auth.json)
fi

export COMPOSER_AUTH

exec composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --ignore-platform-reqs
