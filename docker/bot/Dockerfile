## composer
FROM composer:2.10.3 AS composer-cache
WORKDIR /code
# copy only composer files for caching
COPY composer.json composer.lock /code/

# install deps(without generating autoloader)
RUN composer install --no-scripts --no-autoloader --ignore-platform-reqs

FROM php:8.5.12-bookworm

COPY --from=composer-cache /usr/bin/composer /usr/bin/composer

WORKDIR /code

RUN apt update && \
    apt full-upgrade -y && \
    apt autoremove -y && \
    apt autoclean -y && \
    apt install -y \
    g++ \
    zip \
    unzip \
    locales \
    libicu-dev \
    unzip \
    zlib1g-dev \
    libonig-dev \
    default-mysql-client

# opcache is compiled into PHP 8.5 and already enabled, so it is no longer installed as a shared extension
RUN docker-php-ext-install bcmath intl pdo_mysql

RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/*

CMD ["php", "artisan", "discord-bot:run"]
