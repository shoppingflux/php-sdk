FROM php:5.6-apache

ARG MOUNTPOINT=/var/www
ARG PHPUNIT_VERSION=5
ARG COMPOSER_BIN_DIR=/usr/local/bin

RUN apt-get update -yqq \
    && apt-get install git zip zlib1g-dev libxml2-dev -yqq \
    && docker-php-ext-install zip \
    && docker-php-ext-install xml

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=$COMPOSER_BIN_DIR --filename=composer \
    && php -r "unlink('composer-setup.php');"

ADD . $MOUNTPOINT
WORKDIR $MOUNTPOINT
