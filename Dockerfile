FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

COPY . .

ENV WEBROOT /var/www/html/public
ENV APP_ENV production

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80