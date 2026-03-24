FROM oven/bun:1 as base
WORKDIR /app

COPY package.json .
RUN bun install

COPY src ./src
COPY tsconfig.json .

EXPOSE 3000
CMD ["bun", "run", "src/index.ts"]
