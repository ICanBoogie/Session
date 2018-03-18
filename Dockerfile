FROM php:7.1-alpine

RUN apk add --update --no-cache make $PHPIZE_DEPS && \
	pecl install xdebug-2.6.1 && \
	docker-php-ext-enable xdebug

RUN echo $'\
xdebug.remote_autostart=1\n\
xdebug.remote_enable=1\n\
xdebug.remote_host=host.docker.internal\n\
' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN echo $'\
date.timezone=UTC\n\
' >> /usr/local/etc/php/conf.d/php.ini

ENV PHP_IDE_CONFIG serverName=icanboogie-tests
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer && \
    curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig && \
    php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" && \
    php /tmp/composer-setup.php && \
    mv composer.phar /usr/local/bin/composer
