# syntax=docker/dockerfile:1.7-labs
FROM node:22-bookworm-slim

WORKDIR /app

RUN corepack enable && corepack prepare pnpm@latest --activate

CMD ["sh", "-lc", "if [ -f package.json ]; then pnpm install && pnpm dev --host 0.0.0.0 --port ${PORT:-3001}; else echo 'No package.json found in /app'; tail -f /dev/null; fi"]
