#!/bin/bash

cd /var/www/html/

php artisan migrate --force
php artisan optimize

service redis-server start

chgrp -R www-data storage bootstrap/cache
chown www-data:www-data -R /var/www/html/storage/
chown www-data:www-data -R /var/www/html/bootstrap/

composer install

exec $@
