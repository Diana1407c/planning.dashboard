#!/bin/bash

cd /var/www/html/

touch /var/log/cron.log
chmod 777 /var/log/cron.log
echo "SHELL=/bin/bash
*	*	*	*	*	/usr/local/bin/php /var/www/html/artisan schedule:run >> /var/log/cron.log 2>&1

# This extra line makes it a valid cron" > crons.txt

crontab crons.txt

php artisan migrate --force

service cron start
service supervisor start

chgrp -R www-data storage bootstrap/cache
chown www-data:www-data -R /var/www/html/storage/
chown www-data:www-data -R /var/www/html/bootstrap/
#chown www-data:www-data -R /var/www/html/public/uploads/
# chown www-data:www-data -R /var/www/html/docker/mysql_data

export COMPOSER_PROCESS_TIMEOUT=100000000
#
#
composer install
npm install
npm install vue@3.2.26
php artisan backpack:install --no-interaction

php artisan optimize:cl

exec $@
