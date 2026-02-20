# PHP-FPM for Yii1 on Alpine + PHP 8.3
FROM composer:2 AS composer

FROM php:8.3-fpm-alpine

# Install system deps and PHP extensions commonly required by Yii1
RUN set -eux \
    && apk add --no-cache \
        bash \
        curl \
        git \
        icu \
        libzip \
        oniguruma \
        freetype \
        libjpeg-turbo \
        libpng \
        libwebp \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mysqli \
        intl \
        mbstring \
        gd \
        zip \
        opcache \
        exif \
        bcmath \
    && apk add --no-cache php83-pecl-redis \
    && apk del --no-cache .build-deps \
    && rm -rf /tmp/* /var/cache/apk/*

# Add Composer binary from the composer stage
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# Session storage for PHP (matches php.ini)
RUN mkdir -p /tmp/sessions && chown www-data:www-data /tmp/sessions

# Expose php-fpm port
EXPOSE 9000

CMD ["php-fpm"]
