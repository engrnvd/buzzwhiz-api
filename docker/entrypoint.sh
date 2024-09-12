#!/bin/bash

composer install --no-progress --no-interaction

if [ ! -f ".env" ]; then
    echo "Creating env file for env $APP_ENV"
    cp .env.example .env
else
    echo "env file exists."
fi

chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache

php artisan key:generate
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan route:clear
php artisan migrate --force
php artisan db:seed --force

echo 'running php-fpm'
php-fpm -D
echo 'running nginx'
service nginx start
echo 'running cron'
/usr/sbin/cron
echo 'running supervisord'
supervisord -c /etc/supervisor/conf.d/supervisord.conf
