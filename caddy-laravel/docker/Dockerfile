FROM abiosoft/caddy:php
LABEL maintainer="Lin Shenghai <limweb@hotmail.com>"

COPY . /srv/app
COPY docker/Caddyfile /etc/Caddyfile

WORKDIR /srv/app/
RUN composer install

ENTRYPOINT ["/bin/parent", "caddy"]
CMD ["--conf", "/etc/Caddyfile", "--log", "stdout", "--agree=$ACME_AGREE"]