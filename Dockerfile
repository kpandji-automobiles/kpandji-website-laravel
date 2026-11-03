FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm install && npm run build

FROM php:8.4-cli-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip libpq-dev libzip-dev libicu-dev libpng-dev libsqlite3-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql pdo_sqlite zip intl gd bcmath opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
COPY --from=assets /app/public/build ./public/build

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chmod +x docker/start.sh \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 10000
CMD ["bash", "/var/www/html/docker/start.sh"]
