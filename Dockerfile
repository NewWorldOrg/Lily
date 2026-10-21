FROM composer:2.10.3 AS composer-base

## NGINX Unit 1.35.0 built from source on the PHP 8.4.27 image
FROM php:8.4.27-cli-bookworm AS unit

RUN set -ex \
    && apt-get update \
    && apt-get install --no-install-recommends --no-install-suggests -y \
         ca-certificates git build-essential libssl-dev libpcre2-dev zlib1g-dev pkg-config \
    && mkdir -p /usr/lib/unit/modules /usr/src/unit \
    && cd /usr/src/unit \
    && git clone --depth 1 -b 1.35.0 https://github.com/nginx/unit \
    && cd unit \
    && NCPU="$(getconf _NPROCESSORS_ONLN)" \
    && DEB_HOST_MULTIARCH="$(dpkg-architecture -q DEB_HOST_MULTIARCH)" \
    && CC_OPT="$(DEB_BUILD_MAINT_OPTIONS="hardening=+all,-pie" DEB_CFLAGS_MAINT_APPEND="-Wp,-D_FORTIFY_SOURCE=2 -fPIC" dpkg-buildflags --get CFLAGS)" \
    && LD_OPT="$(DEB_BUILD_MAINT_OPTIONS="hardening=+all,-pie" DEB_LDFLAGS_MAINT_APPEND="-Wl,--as-needed -pie" dpkg-buildflags --get LDFLAGS)" \
    && CONFIGURE_ARGS_MODULES="--prefix=/usr \
                --statedir=/var/lib/unit \
                --control=unix:/var/run/control.unit.sock \
                --runstatedir=/var/run \
                --pid=/var/run/unit.pid \
                --logdir=/var/log \
                --log=/var/log/unit.log \
                --tmpdir=/var/tmp \
                --user=unit \
                --group=unit \
                --openssl \
                --libdir=/usr/lib/$DEB_HOST_MULTIARCH" \
    && CONFIGURE_ARGS="$CONFIGURE_ARGS_MODULES --zlib" \
    && ./configure $CONFIGURE_ARGS --cc-opt="$CC_OPT" --ld-opt="$LD_OPT" --modulesdir=/usr/lib/unit/modules \
    && make -j $NCPU unitd \
    && install -pm755 build/sbin/unitd /usr/sbin/unitd \
    && make clean \
    && ./configure $CONFIGURE_ARGS_MODULES --cc-opt="$CC_OPT" --modulesdir=/usr/lib/unit/modules \
    && ./configure php \
    && make -j $NCPU php-install \
    && cd / \
    && rm -rf /usr/src/unit

## base: PHP 8.4.27 + Unit + extensions
FROM php:8.4.27-cli-bookworm AS base

COPY --from=composer-base /usr/bin/composer /usr/bin/composer
COPY --from=unit /usr/sbin/unitd /usr/sbin/unitd
COPY --from=unit /usr/lib/unit/modules/ /usr/lib/unit/modules/
COPY docker/unit/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN set -ex \
    && apt-get update \
    && apt-get install --no-install-recommends --no-install-suggests -y \
         curl ca-certificates libpcre2-8-0 \
    && apt-get install -y \
         g++ \
         zip \
         unzip \
         locales \
         libicu-dev \
         libmemcached-dev \
         zlib1g-dev \
         libonig-dev \
         default-mysql-client \
    && groupadd --gid 999 unit \
    && useradd --uid 999 --gid unit --no-create-home --home /nonexistent --comment "unit user" --shell /bin/false unit \
    && mkdir -p /var/lib/unit/ /docker-entrypoint.d/ \
    && ln -sf /dev/stderr /var/log/unit.log \
    && chmod +x /usr/local/bin/docker-entrypoint.sh \
    && ldconfig \
    && unitd --version

WORKDIR /code
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY docker/unit/zz-custom.ini /usr/local/etc/php/conf.d/

RUN export MAKEFLAGS="-j$(nproc)" && \
    docker-php-ext-install -j$(nproc) bcmath intl pdo_mysql opcache

COPY docker/unit/config.json /docker-entrypoint.d/

STOPSIGNAL SIGTERM
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
EXPOSE 80
CMD ["unitd", "--no-daemon", "--control", "unix:/var/run/control.unit.sock"]

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
