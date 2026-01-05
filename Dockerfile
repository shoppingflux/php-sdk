FROM php:8.0-fpm

RUN apt-get update && \
    apt-get install --no-install-recommends -y \
    libcurl4-openssl-dev \
    libxml2-dev \
    libzip-dev \
    curl \
    git \
    zip

RUN pecl install xdebug && \
    docker-php-ext-enable xdebug

RUN docker-php-ext-install curl \
    && docker-php-ext-install opcache \
    && docker-php-ext-install zip \
    && docker-php-ext-install xml

RUN rm -R /usr/src && rm -R /usr/local/src

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN usermod -u 1000 www-data

RUN apt-get autoremove && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www
