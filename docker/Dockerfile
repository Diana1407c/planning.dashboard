FROM php:8.1-apache-bullseye

RUN apt-get update && \
    apt-get install -y \
    git \
    curl \
    cron \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    supervisor \
    libx11-xcb1 libxss1 libgbm1 \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Workaround for write permission on write to MacOS X volumes
# See https://github.com/boot2docker/boot2docker/pull/534
RUN usermod -u 1000 www-data

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Enable Apache mod_rewrite
RUN a2enmod headers

# Enable Apache mod_rewrite
RUN a2enmod expires

WORKDIR /var/www/html
COPY . /var/www/html/

COPY ./docker/supervisor.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./docker/entrypoint.sh /usr/local/bin/dockerInit

#RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -
#RUN apt-get install -y nodejs=16.*
#
#RUN export COMPOSER_PROCESS_TIMEOUT=100000000
#
#RUN composer install
#RUN npm install
#RUN npm install vue@3.2.26
#RUN npm run prod
#RUN php artisan backpack:install --no-interaction

RUN chmod +x /usr/local/bin/dockerInit
CMD ["/usr/local/bin/dockerInit","apache2-foreground"]
