FROM php:8.1-apache

RUN apt-get update --fix-missing
RUN apt-get install -y curl
RUN apt-get install -y build-essential libssl-dev zlib1g-dev libxml2-dev libicu-dev zip
RUN docker-php-ext-install pdo mysqli pdo_mysql
RUN docker-php-ext-configure intl && docker-php-ext-install intl

COPY docker/web/apache/000-default.conf /etc/apache2/sites-enabled/000-default.conf
COPY docker/web/php/php.ini /usr/local/etc/php/php.ini
COPY . /var/www/

# Install Composer and dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /var/www/
RUN composer install -n --no-scripts --prefer-dist
RUN chmod 777 /var/www/vendor -R
RUN a2enmod rewrite

EXPOSE 80