FROM php:8.1-fpm

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

WORKDIR /var/www/html

COPY . /var/www/html/

COPY supervisor.conf /etc/supervisor/conf.d/supervisord.conf
COPY entrypoint.sh /usr/local/bin/dockerInit


RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y nodejs=16.*

RUN chmod +x /usr/local/bin/dockerInit
CMD ["/usr/local/bin/dockerInit","php-fpm","-F"]
