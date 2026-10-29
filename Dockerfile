# DutyBoard development image.
#
# PHP with the extensions DutyBoard needs, plus Composer. Your copy of the
# code is mounted into the container by compose.yaml, so edits show up
# without rebuilding. The production image (PHP-FPM behind Caddy, HTTPS)
# is added in a later change.

FROM composer:2 AS composer

FROM php:8.4-cli-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev libicu-dev libzip-dev unzip \
    && docker-php-ext-install pdo_pgsql intl zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY docker/entrypoint.sh /usr/local/bin/dutyboard-entrypoint

WORKDIR /var/www/html

# Composer and PHP write caches here, which works for any user ID.
ENV HOME=/tmp

ENTRYPOINT ["dutyboard-entrypoint"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
