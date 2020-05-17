FROM php:7.4-fpm-alpine

RUN apk update && apk add autoconf g++ make \
    && pecl install xdebug \
    && rm -rf /tmp/pear

RUN set -ex \
  && apk --no-cache add \
    postgresql-dev

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql\
    && docker-php-ext-enable xdebug

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./conf.d /usr/local/etc/php/conf.d

WORKDIR /app

COPY entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint