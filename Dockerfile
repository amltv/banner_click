ARG PHP_VERSION="7.1.14"

#composer
FROM composer AS composer

#php builder
FROM php:${PHP_VERSION} AS php_builder
RUN apt-get update
RUN apt-get install -y zlib1g-dev
RUN docker-php-ext-install zip
RUN pecl install redis-3.1.2

#php
FROM php:${PHP_VERSION}-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=php_builder /usr/local/lib/php/extensions/no-debug-non-zts-20160303/zip.so /usr/local/lib/php/extensions/no-debug-non-zts-20160303/zip.so
COPY --from=php_builder /usr/local/lib/php/extensions/no-debug-non-zts-20160303/redis.so /usr/local/lib/php/extensions/no-debug-non-zts-20160303/redis.so
RUN docker-php-ext-enable zip redis

WORKDIR /var/www

EXPOSE 8080
ENTRYPOINT php -S 0.0.0.0:8080 public/index.php