FROM oven/bun

WORKDIR /app

COPY package.json .
# COPY bun.lockb .

RUN bun install
COPY ./public ./public
COPY ./src ./src
COPY ./.env.prod ./.env

EXPOSE 3000

CMD ["bun",  "src/index.ts"]