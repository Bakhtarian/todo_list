FROM php:8.4-fpm-alpine

ARG DISPLAY_ERRORS=On
RUN echo "display_errors = $DISPLAY_ERRORS" >> /usr/local/etc/php/conf.d/display_errors.ini

RUN apk add --update --no-cache \
    --virtual .build-dependencies --no-cache \
    git \
    autoconf \
    g++ \
    make \
    linux-headers \
    libzip-dev \
    libxml2-dev \
    rabbitmq-c \
    rabbitmq-c-dev \
    curl-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    gettext-dev \
    icu-dev \
    postgresql-dev \
    libsodium-dev \
    libxslt-dev \
    zip \
    pcre-dev

RUN pecl install amqp
RUN pecl install xdebug
RUN pecl install apcu
RUN pecl install mongodb

RUN docker-php-ext-enable amqp
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-enable apcu
RUN docker-php-ext-enable mongodb

ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

RUN docker-php-ext-install \
    opcache \
    bcmath \
    calendar \
    dom \
    exif \
    fileinfo \
    filter \
    ftp \
    gd \
    gettext \
    pdo_pgsql \
    posix \
    sockets \
    xml \
    xsl \
    intl

RUN docker-php-ext-enable \
    apcu \
    bcmath \
    calendar \
    dom \
    exif \
    fileinfo \
    filter \
    ftp \
    gd \
    gettext \
    pdo_pgsql \
    posix \
    sockets \
    xml \
    xsl

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

RUN addgroup -S appgroup && adduser -S appuser -G appgroup -u 1000
USER appuser

WORKDIR /todo_list/
