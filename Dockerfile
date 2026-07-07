FROM richarvey/nginx-php-fpm:3.1.6
WORKDIR /var/www/html
COPY . .

RUN mkdir -p vendor/composer && echo "<?php // ignored" > vendor/composer/platform_check.php

ENV WEBROOT /var/www/html/public
EXPOSE 80