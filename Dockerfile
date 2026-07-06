FROM richarvey/nginx-php-fpm:3.1.6
WORKDIR /var/www/html
COPY . .
ENV WEBROOT /var/www/html/public
EXPOSE 80 