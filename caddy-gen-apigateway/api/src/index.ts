import { Elysia } from "elysia";
import { jwtVerify, createRemoteJWKSet } from "jose";
import { cors } from "@elysiajs/cors";

const SSO_SERVER = process.env.SSO_AUTH_SERVER || "sso.shopsthai.com";
const SSO_REALM = process.env.SSO_REALM || "shopsthai.app";
const API_PORT = parseInt(process.env.API_PORT || "3000");
const BIND_ADDRESS = process.env.BIND_ADDRESS || "0.0.0.0";

const JWKS_URI = `https://${SSO_SERVER}/realms/${SSO_REALM}/protocol/openid-connect/certs`;
const ISSUER = `https://${SSO_SERVER}/realms/${SSO_REALM}`;

const jwks = createRemoteJWKSet(new URL(JWKS_URI));

const app = new Elysia()
  .use(
    cors({
      origin: true,
      methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
      allowedHeaders: ["Content-Type", "Authorization"],
      credentials: true,
    }),
  )
  .onBeforeHandle(async ({ request, set }) => {
    // const authHeader = request.headers.get("authorization");
    // if (!authHeader || !authHeader.startsWith("Bearer ")) {
    //   set.status = 401;
    //   return { error: "Missing or invalid Authorization header" };
    // }
    // const token = authHeader.substring(7);
    // try {
    //   const { payload } = await jwtVerify(token, jwks, {
    //     issuer: ISSUER,
    //     audience: "account",
    //   });
    //   (request as any).user = payload;
    // } catch (err) {
    //   set.status = 401;
    //   return { error: "Invalid token" };
    // }
  })
  .get("/me", ({ request }) => {
    // const user = (request as any).user;
    return {
      headers: JSON.parse(JSON.stringify(request.headers)),
      // sub: user.sub,
      // email: user.email,
      // name: user.name,
      // preferred_username: user.preferred_username,
    };
  })
  .get("/datas", () => {
    return {
      datas: [
        { id: 1, name: "Data 1", value: 100 },
        { id: 2, name: "Data 2", value: 200 },
        { id: 3, name: "Data 3", value: 300 },
      ],
    };
  })
  .get("/health", () => ({ status: "ok" }))
  .all(
    "/testapi",
    ({ request, query, body, cookie, headers, path, params }) => {
      const url = new URL(request.url);
      return {
        method: request.method,
        path: path,
        url: request.url,
        pathname: url.pathname,
        search: url.search,
        query: query,
        params: params,
        headers: Object.fromEntries(request.headers.entries()),
        cookies: cookie,
        body: body,
        contentType: request.headers.get("content-type"),
        contentLength: request.headers.get("content-length"),
        userAgent: request.headers.get("user-agent"),
        host: request.headers.get("host"),
        origin: request.headers.get("origin"),
        referer: request.headers.get("referer"),
        timestamp: new Date().toISOString(),
      };
    },
  )
  .listen({ port: API_PORT, hostname: BIND_ADDRESS });

console.log(`API server running at http://${BIND_ADDRESS}:${API_PORT}`);
