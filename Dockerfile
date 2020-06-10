FROM php:7.2-apache
RUN docker-php-ext-install mysqli
RUN a2enmod rewrite
COPY . /var/www/html/
RUN mv /var/www/html/config.inc.docker.php /var/www/html/config.inc.php
