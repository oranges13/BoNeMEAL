FROM php:8.0-fpm-alpine

ADD ./docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel

RUN mkdir -p /var/www/html

RUN chown laravel:laravel /var/www/html

WORKDIR /var/www/html

RUN apk add sqlite sqlite-dev linux-headers && docker-php-ext-install pdo pdo_mysql pdo_sqlite mysqli

# install and enable xdebug
RUN apk add --no-cache $PHPIZE_DEPS \
	&& pecl install xdebug-3.1.6 \
	&& docker-php-ext-enable xdebug
