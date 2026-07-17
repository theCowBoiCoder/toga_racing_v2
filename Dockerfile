FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --optimize-autoloader

FROM php:8.4-cli-alpine

RUN docker-php-ext-install pdo pdo_mysql
WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN chown -R www-data:www-data storage bootstrap/cache
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
