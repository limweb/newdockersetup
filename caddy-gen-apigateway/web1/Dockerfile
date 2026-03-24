# Build stage with Bun
FROM oven/bun:latest as build-stage

WORKDIR /app

# Copy package files
COPY package.json bun.lockb ./

# Install dependencies
RUN bun install --frozen-lockfile

# Copy source code
COPY . .

# Build the application
RUN bun run build

# Production stage with Caddy
FROM caddy:alpine as production-stage

# Copy built app to Caddy
COPY --from=build-stage /app/dist /srv/www

# Copy Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

# Expose port
EXPOSE 8080

# Start Caddy
CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile"]