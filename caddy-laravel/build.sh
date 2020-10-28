docker network create proxy-network
docker-compose up -d
docker-compose exec phpweb composer install
docker-compose exec phpweb chmod 755 -R /srv/storage