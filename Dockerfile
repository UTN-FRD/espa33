FROM php:8.0.28-apache
MAINTAINER Bruno Sagaste

COPY apache2conf/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

RUN docker-php-ext-install mysqli
RUN chown -R www-data:www-data /var/www