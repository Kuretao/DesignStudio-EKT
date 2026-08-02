FROM php:8.4-cli-alpine
RUN sed -i 's|https://dl-cdn.alpinelinux.org/alpine|https://mirror.yandex.ru/mirrors/alpine|g' /etc/apk/repositories \
    && apk update \
    && apk add --no-cache bash curl icu-dev libzip-dev oniguruma-dev zip \
    && docker-php-ext-install intl zip mbstring pdo pdo_mysql
RUN apk add --no-cache bash curl icu-dev libzip-dev oniguruma-dev zip \
    && docker-php-ext-install intl mbstring pdo_mysql zip
	
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN { \
        echo 'upload_max_filesize=128M'; \
        echo 'post_max_size=128M'; \
        echo 'memory_limit=256M'; \
    } > /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts

COPY . .
RUN mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions storage/logs bootstrap/cache \
    && composer dump-autoload --optimize

COPY docker/entrypoint.sh /usr/local/bin/backend-entrypoint
RUN chmod +x /usr/local/bin/backend-entrypoint

EXPOSE 8000

ENTRYPOINT ["backend-entrypoint"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
