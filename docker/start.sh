#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache database
chmod -R ug+rwx storage bootstrap/cache database

if [[ -z "${APP_KEY:-}" ]]; then
    echo "APP_KEY is missing; generating an ephemeral key for this boot." >&2
    export APP_KEY="$(php artisan key:generate --show --no-interaction)"
fi

if [[ -n "${DATABASE_URL:-}" ]]; then
    export DB_CONNECTION="${DB_CONNECTION:-pgsql}"
else
    export DB_CONNECTION=sqlite
    export DB_DATABASE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    touch "${DB_DATABASE}"
fi

php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction
php artisan db:seed --force --no-interaction --class=CmsSeeder || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
