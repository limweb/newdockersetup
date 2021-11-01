FROM ubuntu:20.04 as install

# -----install php8-----------
RUN apt update
RUN apt -y upgrade
RUN apt install -y lsb-release ca-certificates apt-transport-https software-properties-common -y
RUN add-apt-repository ppa:ondrej/php
RUN apt -y install php8.0
RUN apt install -y php8.0-amqp php8.0-common php8.0-gd php8.0-ldap php8.0-odbc php8.0-readline  php8.0-sqlite3 php8.0-xsl php8.0-apcu php8.0-curl   php8.0-gmp    php8.0-opcache  php8.0-redis  php8.0-mbstring  php8.0-pgsql    php8.0-yaml php8.0-dev    php8.0-imagick   php8.0-memcached php8.0-uuid   php8.0-zip php8.0-bz2   php8.0-zmq php8.0-interbase php8.0-mysql  php8.0-soap  php8.0-cli    php8.0-fpm    php8.0-intl   php8.0-oauth  php8.0-xml php8.0-mongodb
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY ./www.conf /etc/php/8.0/fpm/pool.d/www.conf

# ----- Install Caddy --------
RUN apt install -y debian-keyring debian-archive-keyring apt-transport-https
RUN curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | apt-key add -
RUN curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | tee /etc/apt/sources.list.d/caddy-stable.list
RUN apt update
RUN apt install caddy
RUN caddy version
RUN setcap 'cap_net_bind_service=+ep' /usr/bin/caddy
COPY config /etc/php/8.0/fpm/conf.d
COPY php /var/www/html
RUN  mkdir /run/php
COPY Caddyfile /etc/caddy/
COPY php-caddy-entrypoint /usr/local/bin/
RUN chmod +x /usr/local/bin/php-caddy-entrypoint

# RUN  apt install -y  net-tools
RUN apt-get clean autoclean
RUN apt-get autoremove --yes
RUN rm -rf /var/lib/{apt,dpkg,cache,log}/

WORKDIR /var/www/html
COPY composer.json /var/www/html/composer.json
COPY composer.lock /var/www/html/composer.lock
RUN composer install

EXPOSE 8080
ENTRYPOINT [ "php-caddy-entrypoint" ]
CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
# CMD ["sleep","8000"]