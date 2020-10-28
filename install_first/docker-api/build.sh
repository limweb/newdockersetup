# ตัวอย่าง build script โดยที่ มี dockerfile and docker-compose อยู่ บน ของ folder นี้นะ
#++++!!!!!+++ ห้ามย้ายไลฟ์ build.sh ไปที่อื่น 
#//------ สำหรับ server มี  resource น้อย ใช้ เครื่องที่มี resource มาก build ก่อน และ นำขึ้น hub.docker.com แล้วค่อย pull 
#// แต่ถ้ามี resource มาก ไม่ต้องทำตรงนี้ 
cd .. #// คำสั่งไม่ต้องทำถ้า resource มาก
echo ${PWD} #// คำสั่งไม่ต้องทำถ้า resource มาก
DOCKER_USERNAME="ีuser_in_hub.docker.com"
DOCKER_PASSWORD="password_hub.docker.com"
DOCKER_IMGNAME="your_hub_id/your-repo:your_tag"
docker login -u="$DOCKER_USERNAME" -p="$DOCKER_PASSWORD"
docker build -t $DOCKER_IMGNAME .
docker push $DOCKER_IMGNAME

cd docker-api #// คำสั่งไม่ต้องทำถ้า resource มาก
echo ${PWD}   #// คำสั่งไม่ต้องทำถ้า resource มาก
export HOST=your_dns_host.com
export DOCKER_TLS_VERIFY=1
export DOCKER_HOST=$HOST:2376
export DOCKER_CERT_PATH=${PWD}/server/
export COMPOSE_TLS_VERSION=TLSv1_2
export CURL_CA_BUNDLE=${PWD}/server/server-cert.pem

#// copy ด้านบนมาไว้ตรงนี้แทน  
cd .. #// คำสั่งไม่ต้องทำถ้า resource มาก
echo ${PWD} #// คำสั่งไม่ต้องทำถ้า resource มาก
docker-compose pull &&  docker-compose up --force-recreate -d
docker image prune -f
docker-compose ps
docker ps
