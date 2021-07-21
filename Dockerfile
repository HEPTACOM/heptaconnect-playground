FROM php:7.4-apache

RUN set -ex; \
	apt-get update && apt-get install -y \
		libmcrypt-dev libxml2-dev libzip-dev libbz2-dev \
	    zlib1g-dev libpng-dev libjpeg-dev \
		zip unzip git mariadb-client nano;

RUN set -ex; echo "memory_limit = 1024M\n" >> /usr/local/etc/php/php.ini
RUN set -ex; echo "max_execution_time=3600" >> /usr/local/etc/php/php.ini

RUN docker-php-ext-install gd intl pdo_mysql zip
RUN docker-php-ext-enable gd intl pdo_mysql zip

RUN a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY --chown=www-data:www-data . /var/www/html
COPY dev-ops/docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY dev-ops/docker/entrypoint.sh /var/www/html/entrypoint.sh

RUN runuser -u www-data -- make --directory=/var/www/html shopware-platform-files

RUN pecl install xdebug-2.9.8;\
    docker-php-ext-enable xdebug

CMD ["/var/www/html/entrypoint.sh"]
