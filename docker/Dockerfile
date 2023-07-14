FROM php:8.1-apache-bullseye

RUN apt-get update && \
    apt-get install -y \
    git \
    curl \
    cron \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    redis \
    supervisor \
    libx11-xcb1 libxss1 libgbm1

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN usermod -u 1000 www-data

RUN a2enmod rewrite

RUN a2enmod headers

RUN a2enmod expires

WORKDIR /var/www/html
COPY . /var/www/html/

COPY ./docker/supervisor.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./docker/entrypoint.sh /usr/local/bin/dockerInit

RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y nodejs=16.*

RUN export COMPOSER_PROCESS_TIMEOUT=100000000

RUN chown $(whoami) .
RUN composer install
RUN npm install
RUN npm install vue@3.2.26
RUN npm run prod
RUN php artisan backpack:install --no-interaction

RUN chmod +x /usr/local/bin/dockerInit
CMD ["/usr/local/bin/dockerInit","apache2-foreground"]
