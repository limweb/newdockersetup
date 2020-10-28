FROM abiosoft/caddy:php as install 
LABEL maintainer="Lin Shenghai <limweb@hotmail.com>"
WORKDIR /srv
COPY composer.json   /srv/composer.json
RUN composer install

FROM install as composer 
COPY docker/Caddyfile /etc/Caddyfile

FROM composer as caddy 

COPY . /srv/app

ENTRYPOINT ["/bin/parent", "caddy"]
CMD ["--conf", "/etc/Caddyfile", "--log", "stdout", "--agree=$ACME_AGREE"]