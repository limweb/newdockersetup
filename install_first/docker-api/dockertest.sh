export HOST=your_dns_host.com
export DOCKER_TLS_VERIFY=1
export DOCKER_HOST=$HOST:2376
export DOCKER_CERT_PATH=${PWD}/server/
export COMPOSE_TLS_VERSION=TLSv1_2
export CURL_CA_BUNDLE=${PWD}/server/server-cert.pem
docker-compose ps
docker ps