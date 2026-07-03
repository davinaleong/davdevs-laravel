# Dav/Devs — Deployment Guide (Laravel Cloud)

## Prerequisites

- Laravel Cloud account, project created for `davdevs`
- Cloudinary account (image storage) — set `CLOUDINARY_URL` in the
  Cloud environment config
- A MySQL 8 database provisioned by Laravel Cloud (or external)

## Environments

Create two Laravel Cloud environments:

| Environment | Branch | Purpose |
|---|---|---|
| `production` | `main` | Live site — davinaleong.com |
| `staging` | `staging` | Pre-release QA |

## Secrets (set in Laravel Cloud environment config, never in `.env`)

- `APP_KEY` — generate once with `php artisan key:generate --show`, set identically on both environments if sharing encrypted data, otherwise unique per environment
- `DB_*` — provided by Laravel Cloud's managed MySQL
- `CLOUDINARY_URL` — from the Cloudinary dashboard (API Environment variable)
- `CLOUDINARY_UPLOAD_PRESET` — if using unsigned uploads (not required for the current signed server-side upload flow)
- `CMS_IP_ALLOWLIST` — optional, comma-separated IPs to restrict `/panel/*`
- `MAIL_*` — a transactional mail provider (Postmark/Resend recommended over SMTP)

## Release/deploy steps

Laravel Cloud runs these automatically on deploy, but for manual/other hosts:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

## Queue worker

`QUEUE_CONNECTION=database` is used for: data exports (Phase 15), and
should also be used for AI calls if Phase 14 is implemented later.
Laravel Cloud provisions a queue worker process automatically when a
`queue.php`-style worker command is registered; otherwise run:

```bash
php artisan queue:work --tries=3 --max-time=3600
```

## Scheduler

Laravel Cloud runs `php artisan schedule:run` every minute
automatically. This project's schedule (`bootstrap/app.php`
`withSchedule()`) includes:

- `sitemap:generate` — daily
- `logs:prune` — daily (90-day retention)
- `exports:prune` — hourly (24h export expiry)

On non-Cloud hosts, add this cron entry:

```cron
* * * * * cd /path/to/davdevs && php artisan schedule:run >> /dev/null 2>&1
```

## Health check

`GET /up` is registered via `bootstrap/app.php` (`health: '/up'`) —
point Laravel Cloud's health check and any external uptime monitor at
this route.

## Zero-downtime deploys

Laravel Cloud's rolling deploy handles this natively. If deploying
elsewhere, use `php artisan down --refresh=15` before migrating and
`php artisan up` after, or use `octane:reload` if running Octane.

## Backups

- Laravel Cloud managed MySQL includes automated daily backups.
- Additionally schedule a weekly export via the CMS Data Export
  feature (Phase 15) to Cloudinary/R2 as an off-platform backup of
  content (not a full DB backup — covers entries, publications,
  images metadata, links, embeds, quips, settings, and 90 days of logs).

## First deploy checklist

1. Set all secrets above in the Laravel Cloud environment
2. Deploy — migrations run automatically via the release step
3. SSH/run-once: `php artisan db:seed --class=ContentTypeSeeder --class=LayoutSeeder --class=SettingSeeder` (idempotent — uses `firstOrCreate`)
4. Create the first admin user:
   ```bash
   php artisan tinker --execute="App\Models\User::create(['name' => 'Your Name', 'email' => 'you@example.com', 'password' => bcrypt('CHANGE_ME')]);"
   ```
5. Log in, complete 2FA setup (forced on first login by `TwoFactorMiddleware`)
6. Save the 8 recovery codes somewhere safe
7. Configure Settings (brand name, footer copyright, Lighthouse scores) in the CMS
8. Toggle "Enable public API" in Settings only if the headless REST API is needed
