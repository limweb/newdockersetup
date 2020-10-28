docker network create proxy-network
docker-compose up -d
docker-compose exec phpweb composer install
docker-compose exec phpweb chmod 776 -R /srv/storage