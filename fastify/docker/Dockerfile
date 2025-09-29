# Build stage
FROM oven/bun AS builder

WORKDIR /app

# Install dependencies first for better layer caching
COPY package.json  ./
RUN bun install

# Copy source code
COPY . .

# Production stage
FROM oven/bun

WORKDIR /app

# Set timezone
ENV TZ=Asia/Bangkok \
    NODE_ENV=production

# Install wget
RUN apt-get update && apt-get install -y wget

# Create non-root user
RUN addgroup --system --gid 1001 nodejs && \
    adduser --system --uid 1001 bunuser && \
    chown -R bunuser:nodejs /app

# Clear cache
RUN rm -rf /var/lib/apt/lists/* && \
    rm -rf /var/cache/apt/archives/* && \
    rm -rf /var/cache/apt/pkgcache.bin && \
    apt-get clean && \
    rm -rf /tmp/*

# Copy necessary files from builder
COPY --from=builder --chown=bunuser:nodejs /app/node_modules ./node_modules
COPY --chown=bunuser:nodejs . .

# Expose port
EXPOSE 3000

# Use non-root user
USER bunuser

# Start the application
CMD ["bun", "src/index.ts"]