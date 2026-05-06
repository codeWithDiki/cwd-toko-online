# Deploying to Helipod

This guide is for contributors who want to deploy this project to [Helipod](https://helipod.io).

> **Important:** Helipod runs on single-node Kubernetes. Unlike Docker Compose which runs multiple services as separate containers, Helipod deploys a **single container** with all services managed internally by Supervisor (Nginx + PHP-FPM + Redis + Reverb + Horizon).

---

## Prerequisites

- A [Helipod](https://helipod.io) account
- Your project forked or pushed to GitHub

---

## 1. Create a New Service

1. Go to Helipod dashboard → **New Service**
2. Connect your GitHub repository
3. Helipod will detect the `Dockerfile` automatically

---

## 2. Configure Environment Variables

Go to **Variables** tab and add the following. All values are required unless marked optional.

### App

| Key | Example Value | Notes |
|-----|--------------|-------|
| `APP_NAME` | `Toko Online` | Your app name |
| `APP_ENV` | `production` | Must be `production` |
| `APP_KEY` | `base64:xxx...` | Generate with `php artisan key:generate --show` |
| `APP_URL` | `https://your-pod.helipod.app` | Your full Helipod URL with `https://` |
| `APP_DEBUG` | `false` | Never `true` in production |

### Database (SQLite)

| Key | Value | Notes |
|-----|-------|-------|
| `DB_CONNECTION` | `sqlite` | |
| `DB_DATABASE` | `/var/www/html/storage/sqlite/database.sqlite` | Must be inside `storage/` — writable at runtime |

> **Why not `database/database.sqlite`?** The `database/` directory is baked into the image and read-only at runtime. Only `storage/` is writable.

### Redis (internal — no external Redis needed)

| Key | Value | Notes |
|-----|-------|-------|
| `REDIS_HOST` | `127.0.0.1` | Redis runs inside the same container |
| `REDIS_PORT` | `6379` | Internal port, not exposed externally |
| `REDIS_PASSWORD` | _(empty)_ | Leave blank |
| `REDIS_CLIENT` | `phpredis` | |

### Cache / Session / Queue

| Key | Value |
|-----|-------|
| `CACHE_STORE` | `redis` |
| `SESSION_DRIVER` | `redis` |
| `QUEUE_CONNECTION` | `redis` |

### Laravel Reverb (WebSocket)

| Key | Example Value | Notes |
|-----|--------------|-------|
| `REVERB_APP_ID` | `my-app` | Any unique string |
| `REVERB_APP_KEY` | `my-key` | Any unique string — must match `VITE_REVERB_APP_KEY` |
| `REVERB_APP_SECRET` | `my-secret` | Any secret string |
| `REVERB_SERVER_HOST` | `0.0.0.0` | Listens on all interfaces inside the container |
| `REVERB_SERVER_PORT` | `8080` | Internal port, proxied by Nginx |
| `REVERB_HOST` | `your-pod.helipod.app` | Your public domain (no `https://`) |
| `REVERB_PORT` | `443` | Public port (Cloudflare terminates SSL) |
| `REVERB_SCHEME` | `https` | |

### Vite (Frontend build — baked at build time)

> These are **build-time** variables. Vite bakes them into the JS bundle during `docker build`. They must match your Reverb public settings above.

| Key | Example Value | Notes |
|-----|--------------|-------|
| `VITE_APP_NAME` | `Toko Online` | |
| `VITE_REVERB_APP_KEY` | `my-key` | Must match `REVERB_APP_KEY` exactly — literal value, no `${}` references |
| `VITE_REVERB_HOST` | `your-pod.helipod.app` | Your public domain |
| `VITE_REVERB_PORT` | `443` | |
| `VITE_REVERB_SCHEME` | `https` | |

### Mail (optional)

| Key | Example Value |
|-----|--------------|
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | `smtp.mailgun.org` |
| `MAIL_PORT` | `587` |
| `MAIL_USERNAME` | `your@email.com` |
| `MAIL_PASSWORD` | `your-password` |
| `MAIL_FROM_ADDRESS` | `no-reply@yourdomain.com` |
| `MAIL_FROM_NAME` | `Toko Online` |

---

## 3. Deploy

Click **Deploy** in Helipod. The build will:

1. Install Composer dependencies
2. Build Vite frontend assets (with your `VITE_*` variables baked in)
3. Build the runtime image with PHP-FPM, Nginx, Redis, Reverb, Horizon

On first start the container will:

1. Create the SQLite database file if it doesn't exist
2. Run `php artisan migrate --force`
3. Run `php artisan storage:link`
4. Cache config/routes/views (production only)
5. Configure Nginx
6. Start all services via Supervisor

---

## 4. Common Issues

### 502 Bad Gateway
Nginx can't reach PHP-FPM. Check that all services are running:
```sh
supervisorctl status
```

### WebSocket connects to `localhost` or wrong port
`VITE_REVERB_HOST` was not set correctly at build time. You must redeploy after fixing the variable — patching the JS file in the terminal only lasts until the next deploy.

### `attempt to write a readonly database`
`DB_DATABASE` is not set or points to `database/database.sqlite` (read-only). Set it to `/var/www/html/storage/sqlite/database.sqlite`.

### `Connection refused` on Redis during migration
Redis takes a moment to start. The entrypoint waits for Redis before running migrations — if this still fails, check `supervisorctl status redis`.

### Mixed Content errors (HTTP resources on HTTPS page)
`APP_URL` must start with `https://`. The app uses this to force HTTPS scheme on all generated URLs.

---

## 5. Differences from Docker Compose

| Docker Compose | Helipod (single container) |
|---------------|---------------------------|
| Separate `app`, `nginx`, `redis` services | All in one container via Supervisor |
| Services talk over Docker network by hostname | Services talk over `127.0.0.1` loopback |
| Redis on default port 6379, exposed | Redis on `127.0.0.1:6379`, not exposed |
| Volumes managed by Compose | Volumes managed by Helipod persistent storage |
| Multiple `ports:` mappings | Only port `80` exposed — Helipod/Cloudflare handles SSL |

---

## 6. Verifying the Deployment

Use the Helipod **Terminal** tab to run diagnostics:

```sh
# Check all services are running
supervisorctl status

# Check open ports (should see 80, 8080, 6379, 9000)
ss -tlnp

# Check Laravel can reach Redis
php artisan tinker --execute="var_dump(Illuminate\Support\Facades\Redis::ping());"

# Check migrations ran
php artisan migrate:status

# Tail Laravel logs
tail -f /var/www/html/storage/logs/laravel.log
```