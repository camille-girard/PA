ARG PHP_VERSION=8.4.1
ARG ALPINE_VERSION=""

FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION}

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

ARG WITH_MYSQL=0

RUN if [[ "${WITH_MYSQL}" -eq 1 ]]; then \
    apk add mysql_client &&  \
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
        ftp; \
fi

RUN if [[ "${WITH_MYSQL}" -eq 0 ]]; then \
    install-php-extensions pgsql  \
        pdo_pgsql  \
        pdo \
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
        ftp; \
fi

WORKDIR /srv

CMD ["php-fpm", "-R", "-F"]