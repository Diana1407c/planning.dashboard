FROM php:8.1-fpm-bullseye

RUN apt-get update && \
    apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    redis \
    cron \
    supervisor \
    libx11-xcb1 libxss1 libgbm1 \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . /var/www/html/

COPY ./docker/php.ini /usr/local/etc/php/
COPY ./docker/entrypoint.sh /usr/local/bin/dockerInit


SHELL ["/bin/bash", "--login", "-i", "-c"]
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
RUN source /root/.bashrc && nvm install 16.16.0
SHELL ["/bin/bash", "--login", "-c"]
RUN apt-get install -y npm

RUN chmod +x /usr/local/bin/dockerInit
ENTRYPOINT dockerInit && php-fpm -F
