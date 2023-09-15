FROM php:8.1-fpm-buster

RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libpq-dev libldap2-dev zip git wget \
    g++ cpp sudo python \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pcntl sockets

RUN pecl install xdebug-3.2.0 && \
    docker-php-ext-enable xdebug

USER root

RUN apt-get update && apt-get install -y software-properties-common

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN mkdir -p /var/www/html/

COPY . /var/www/html/

ENV USER=admin USER_ID=1234 USER_GID=1234

RUN groupadd --gid "${USER_GID}" "${USER}" && \
    useradd \
        --uid ${USER_ID} \
        --gid ${USER_GID} \
        --create-home \
        --shell /bin/bash \
    ${USER}

# Set user permissions
RUN chown -R ${USER}:${USER_GID} /var/www/html/

RUN sed -i "s/www-data/$USER/" /usr/local/etc/php-fpm.d/www.conf

USER ${USER}

EXPOSE 9000
