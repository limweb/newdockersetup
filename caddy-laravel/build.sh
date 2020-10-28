docker network create proxy-network
docker-compose up -d
docker-compose phpweb composer install
docker-compose phpweb chmod 755 -R /srv/storage