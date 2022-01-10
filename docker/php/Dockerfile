FROM php:8.1-cli-alpine
RUN apk add icu-dev \
    && docker-php-ext-install intl
COPY php.ini ${PHP_INI_DIR}
