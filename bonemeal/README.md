# BoNeMEAL (rewrite)

Laravel + Inertia + Vue rewrite of the legacy app in `../application`. The legacy folder is untouched.

## Two databases

| Connection | Env prefix | Purpose | Migrations |
|---|---|---|---|
| default | `DB_*` | App DB on DreamHost (users, roles, sessions...) | yes |
| `bandb` | `BANDB_*` | Remote Ban-Management plugin DB | **NEVER** |

The plugin owns its schema. `tests/Feature/BandbConnectionTest.php` fails if a migration references `bandb`.
Always run `php artisan migrate` without `--database=bandb`.

## Local development

Requires PHP >= 8.3 (target runtime is 8.5), Composer, Node 22.

    composer install && npm ci
    cp .env.example .env && php artisan key:generate
    composer run dev        # or Herd / Sail

To exercise `bandb`, point `BANDB_*` at a throwaway MariaDB loaded with the plugin schema, then:

    php artisan bandb:smoke   # read-only count of bm_players

## Tests

    php artisan test

## Deploy (DreamHost shared hosting)

    DEPLOY_HOST=user@host DEPLOY_PATH=/home/user/bonemeal.example.com ./deploy.sh

- Domain docroot must be `<DEPLOY_PATH>/public`.
- Select PHP 8.5 for the domain in the DreamHost panel (check with `php -v`/phpinfo; confirm `pdo_mysql`, `mbstring`, `intl`).
- Create `.env` on the server once (never uploaded). Set `APP_ENV=production`, `APP_DEBUG=false`, DB and `BANDB_*`.
- The plugin DB host must allow DreamHost's outbound IP.
- No cron or daemons required; use `SESSION_DRIVER=database` and `QUEUE_CONNECTION=sync`.
- Compiled assets are built locally and shipped; no Node on the host.
