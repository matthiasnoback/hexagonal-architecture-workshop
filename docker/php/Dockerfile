FROM php:8.1-cli-alpine
RUN apk add icu-dev \
    && docker-php-ext-install intl
RUN docker-php-ext-install pcntl && php -m | grep pcntl
COPY php.ini ${PHP_INI_DIR}
