FROM node:20.9.0-alpine as install 
RUN apk add --no-cache git 
RUN apk add tzdata && \ 
    cp /usr/share/zoneinfo/Asia/Bangkok /etc/localtime && \ 
    echo "Asia/Bangkok" > /etc/timezone 
 
FROM install as git 
WORKDIR /usr/src/app 
USER root 
COPY package.json /usr/src/app 
COPY package-lock.json /usr/src/app 
RUN npm install

FROM git as build
COPY . .  
RUN npm run build

FROM nginxinc/nginx-unprivileged:stable 
WORKDIR /usr/share/nginx/html
COPY --from=2 /usr/src/app/dist /usr/share/nginx/html
# COPY ./nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf 
# จะมีหรือไม่ก็ได้ ถ้าไม่ได้เปลี่ยนค่าอะไรเป็นพิเศษ
COPY nginx.conf /etc/nginx/conf.d/default.conf    
CMD ["nginx", "-g", "daemon off;"] 
