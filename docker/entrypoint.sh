#!/bin/bash

cd /var/www/html/

cp tmp_env .env
rm tmp_env

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

php artisan optimize:cl

exec $@
