FROM php:7.1-cli-buster

RUN docker-php-ext-enable opcache

RUN echo '\
xdebug.client_host=host.docker.internal\n\
xdebug.mode=develop\n\
xdebug.start_with_request=yes\n\
' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN echo '\
date.timezone=UTC\n\
' >> /usr/local/etc/php/conf.d/php.ini

ENV PHP_IDE_CONFIG serverName=icanboogie-session-tests
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update && \
	apt-get install unzip && \
    curl -s https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer | php -- --quiet && \
    mv composer.phar /usr/local/bin/composer
