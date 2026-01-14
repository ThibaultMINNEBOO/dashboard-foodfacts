# ---------------------------------------------
# Stage 0: build dependencies with Composer
# ---------------------------------------------
FROM composer:2 AS builder

WORKDIR /app

COPY ../composer.json composer.lock ./

RUN composer install --no-scripts --no-progress --optimize-autoloader

COPY .. /app

# ---------------------------------------------
# Stage 1: Runtime with PHP 8.5
# ---------------------------------------------
FROM dunglas/frankenphp:builder-php8.4-bookworm AS base

# Environements
ENV FRANKENPHP_CONFIG="worker ./public/index.php"
ENV SERVER_NAME=localhost
ENV TZ=Europe/Paris

# Prepare php.ini for set TZ
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Install packages
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y --no-install-recommends \
    lsb-release ca-certificates curl gnupg libpq-dev libicu-dev libzip-dev unzip supervisor

# Install php extensions
RUN install-php-extensions \
    pgsql \
    pdo_pgsql \
    gd \
    opcache \
    intl \
    zip \
    tokenizer \
    ctype \
    iconv

WORKDIR /app

COPY --from=builder /app/docker/messenger-worker.conf /etc/supervisor/conf.d/messenger-worker.conf

COPY --from=builder /app /app

# ---------------------------------------------
# Stage 2: dev server
# ---------------------------------------------
FROM base AS dev

ENTRYPOINT ["./docker/docker.sh"]

EXPOSE 80
EXPOSE 443
