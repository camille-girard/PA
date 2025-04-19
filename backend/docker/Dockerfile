ARG PHP_VERSION=8.4.1
ARG ALPINE_VERSION=""

FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION} AS base

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apk update && apk upgrade

RUN apk add --update --no-cache \
    acl \
    bash \
    ca-certificates \
    git \
    openssh \
    rsync \
    patch

RUN apk add mysql-client && \
    install-php-extensions pdo_mysql \
        intl \
        curl \
        dom \
        fileinfo \
        mbstring \
        zip \
        opcache \
        exif \
        apcu \
        ctype \
        gd \
        tokenizer \
        xml \
        session \
        ftp

WORKDIR /app

FROM base AS dev

RUN apk add --update --no-cache \
    vim \
    nano \
    htop \
    sudo

RUN install-php-extensions xdebug

RUN { \
    echo 'display_errors=1'; \
    echo 'error_reporting=E_ALL'; \
    echo 'opcache.validate_timestamps=1'; \
} > /usr/local/etc/php/conf.d/dev.ini

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader

COPY . .
RUN composer dump-autoload --optimize

CMD ["php-fpm", "-R", "-F"]

FROM base AS prod

RUN { \
    echo 'display_errors=0'; \
    echo 'error_reporting=E_ERROR | E_WARNING | E_PARSE'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.preload_user=www-data'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.interned_strings_buffer=16'; \
} > /usr/local/etc/php/conf.d/prod.ini

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY . .
RUN composer dump-autoload --no-dev --optimize

RUN rm -rf /tmp/* /var/cache/apk/*

CMD ["php-fpm", "-R", "-F"]
