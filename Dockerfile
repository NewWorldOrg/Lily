FROM composer:2.8.2 AS composer-base
FROM unit:php8.3 AS base

COPY --from=composer-base /usr/bin/composer /usr/bin/composer
WORKDIR /code
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY docker/unit/zz-custom.ini /usr/local/etc/php/conf.d/

RUN apt-get update && \
    apt-get install -y \
    g++ \
    zip \
    unzip \
    locales \
    libicu-dev \
    libmemcached-dev \
    unzip \
    zlib1g-dev \
    libonig-dev \
    default-mysql-client

RUN export MAKEFLAGS="-j$(nproc)" && \
    docker-php-ext-install -j$(nproc) bcmath intl pdo_mysql opcache bcmath

COPY docker/unit/config.json /docker-entrypoint.d/

## composer
FROM composer-base AS composer-cache

WORKDIR /code

# copy only composer files for caching
COPY composer.json composer.lock ./

# install deps(without generating autoloader)
RUN --mount=type=cache,target=/root/.cache/composer/ composer install --no-scripts --no-autoloader --ignore-platform-reqs

## appliation
FROM base

# clean cache
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# copy production config template
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# copy cached composer files
COPY --chown=unit:unit --from=composer-cache /code /code

# copy app sources
COPY --chown=unit:unit . ./

USER unit

# generate autoload files
RUN composer install

USER root
