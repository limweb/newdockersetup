import { Elysia } from "elysia";

// Load environment variables using Bun's built-in support
const PORT = parseInt(Bun.env.PORT || '3000');
const HOST = Bun.env.HOST || '0.0.0.0';

const app = new Elysia()
  .get("/", () => "Hello Elysia ok")
  .listen({
    port: PORT,
    hostname: HOST
  });

console.log(
  `🦊 Elysia is running at ${HOST}:${PORT}`
);
