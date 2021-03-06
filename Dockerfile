FROM composer:2.1.10 as build
WORKDIR /app
COPY . /app
RUN composer install && composer dumpautoload


FROM php:8.1.0RC5-apache-buster
RUN apt-get update && apt-get install -y libssl-dev
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN pecl install mongodb && docker-php-ext-enable mongodb
RUN set libssl-dev

EXPOSE 8080
COPY --from=build /app /var/www/
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN chmod 777 -R /var/www/storage/ && \
  echo "Listen 8080">>/etc/apache2/ports.conf && \
  chown -R www-data:www-data /var/www/ && \
  a2enmod rewrite
