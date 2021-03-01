FROM caddy/caddy:v2.0.0-beta.17 as caddy
FROM microsoft/mssql-tools as mssql
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

COPY --from=mssql /opt/microsoft/ /opt/microsoft/
COPY --from=mssql /opt/mssql-tools/ /opt/mssql-tools/
COPY --from=mssql /usr/lib/libmsodbcsql-13.so /usr/lib/libmsodbcsql-13.so

RUN set -xe \
    && apk add --no-cache --virtual .persistent-deps \
        freetds \
        unixodbc \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        unixodbc-dev \
        freetds-dev \
    && docker-php-source extract \
    && docker-php-ext-install pdo_dblib \
    && pecl install \
        sqlsrv \
        pdo_sqlsrv \
    && docker-php-ext-enable --ini-name 30-sqlsrv.ini sqlsrv \
    && docker-php-ext-enable --ini-name 35-pdo_sqlsrv.ini pdo_sqlsrv \
    && docker-php-source delete \
    && apk del .build-deps

RUN apk add --no-cache --virtual .build-deps \
    unixodbc \
    unixodbc-dev \
 && docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr \
 && docker-php-ext-install pdo_odbc

RUN set -ex; \
	docker-php-source extract; \
	{ \
		echo '# https://github.com/docker-library/php/issues/103#issuecomment-271413933'; \
		echo 'AC_DEFUN([PHP_ALWAYS_SHARED],[])dnl'; \
		echo; \
		cat /usr/src/php/ext/odbc/config.m4; \
	} > temp.m4; \
	mv temp.m4 /usr/src/php/ext/odbc/config.m4; \
	docker-php-ext-configure odbc --with-unixODBC=shared,/usr; \
	docker-php-ext-install odbc;   


RUN apk --update add \
    alpine-sdk \
    openssl-dev \
    php7-pear \
    php7-dev \
    && rm -rf /var/cache/apk/*

RUN pecl install mongodb \
    && pecl clear-cache

RUN echo "extension=mongodb.so" >  /usr/local/etc/php/conf.d/mongodb.ini

RUN apk add --no-cache --virtual .build-deps $BUILD_DEPS \
    && docker-php-ext-configure gd --with-jpeg \
    && pecl install $PECL_EXTENSIONS \
    && docker-php-ext-install -j "$(nproc)" $PHP_EXTENSIONS \
    && docker-php-ext-enable $PECL_EXTENSIONS \
    && dd \
    && docker-php-source delete \
    && rm -rf /var/cache/apk/* \
    && rm -rf /tmp/* 

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