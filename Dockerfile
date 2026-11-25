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

# Installer updates require a reviewed checksum update.
COPY composer-setup.php.sha512 /tmp/composer-setup.php.sha512
RUN curl -fsSL --proto '=https' --proto-redir '=https' https://getcomposer.org/installer -o /tmp/composer-setup.php && \
    sha512sum --check --strict /tmp/composer-setup.php.sha512 && \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    rm /tmp/composer-setup.php /tmp/composer-setup.php.sha512 && \
    usermod -u 1000 www-data && \
    apt-get autoremove && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www
