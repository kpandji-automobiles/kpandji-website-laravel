#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

php artisan config:cache
php artisan route:cache
php artisan view:cache

if [[ -n "${DATABASE_URL:-}" || -n "${DB_HOST:-}" ]]; then
    php artisan migrate --force
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
