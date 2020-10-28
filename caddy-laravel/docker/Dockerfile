FROM abiosoft/caddy:php as install 
LABEL maintainer="Lin Shenghai <limweb@hotmail.com>"
WORKDIR /srv
COPY . /srv/app
COPY docker/Caddyfile /etc/Caddyfile
RUN composer install

ENTRYPOINT ["/bin/parent", "caddy"]
CMD ["--conf", "/etc/Caddyfile", "--log", "stdout", "--agree=$ACME_AGREE"]