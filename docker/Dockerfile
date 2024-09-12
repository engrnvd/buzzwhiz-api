FROM php:8.3-fpm as php

ENV PHP_OPCACHE_ENABLE=1
ENV PHP_OPCACHE_ENABLE_CLI=0
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=1
ENV PHP_OPCACHE_REVALIDATE_FREQ=1

RUN usermod -u 1000 www-data

RUN apt-get update -y \
    && apt-get install -y unzip libpq-dev libcurl4-gnutls-dev nginx supervisor cron \
    && apt-get -y autoremove && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && docker-php-ext-install pdo pdo_mysql bcmath curl opcache exif \
    && pecl install -o -f redis && docker-php-ext-enable redis \
    &&  rm -rf /tmp/pear

WORKDIR /var/www

COPY --chown=www-data:www-data . .

COPY ./php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./nginx.conf /etc/nginx/nginx.conf
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY --from=composer:2.6.6 /usr/bin/composer /usr/bin/composer
COPY ./crontab /etc/cron.d/crontab
RUN chmod 0644 /etc/cron.d/crontab
RUN /usr/bin/crontab /etc/cron.d/crontab

RUN useradd -ms /bin/bash --no-user-group -g www-data -u 1337 sail

ENTRYPOINT [ "docker/entrypoint.sh" ]
