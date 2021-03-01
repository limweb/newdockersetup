FROM caddy/caddy:v2.0.0-beta.17 as caddy

FROM php:7.4.15-fpm-alpine3.13

ENV RUN_DEPS \
    zlib \
    libzip \
    libpng \
    libjpeg-turbo \
    libxslt \
    postgresql-libs

ENV BUILD_DEPS \
    zlib-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    postgresql-dev \
    libgcrypt-dev \
    libxslt-dev

ENV PHP_EXTENSIONS \
    opcache \
    zip \
    gd \
    bcmath \
    mysqli \
    pdo_mysql \
    pgsql \
    pdo_pgsql \
    xsl

ENV PECL_EXTENSIONS \
    redis  

RUN apk add --no-cache --virtual .build-deps $BUILD_DEPS \
    && docker-php-ext-configure gd --with-jpeg \
    && pecl install $PECL_EXTENSIONS \
    && docker-php-ext-install -j "$(nproc)" $PHP_EXTENSIONS \
    && docker-php-ext-enable $PECL_EXTENSIONS \
    && apk del .build-deps

RUN apk add --no-cache --virtual .run-deps $RUN_DEPS

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY config $PHP_INI_DIR/conf.d
COPY php-fpm /usr/local/etc/php-fpm.d

RUN curl --tlsv1 -v -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY --from=caddy /config /config
COPY --from=caddy /data /data
COPY --from=caddy /usr/bin/caddy /usr/bin/caddy
COPY Caddyfile Caddyfile.public /etc/caddy/

COPY php-caddy-entrypoint /usr/local/bin/
RUN chmod +x /usr/local/bin/php-caddy-entrypoint

EXPOSE 8080

ENTRYPOINT [ "php-caddy-entrypoint" ]

CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]