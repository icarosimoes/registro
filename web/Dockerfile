FROM node:22.17.0-alpine3.22 AS deps

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

FROM deps AS development

COPY . .
EXPOSE 3000
CMD ["npm", "run", "dev"]

FROM deps AS build

ENV NEXT_TELEMETRY_DISABLED=1
COPY . .
RUN npm run build

FROM node:22.17.0-alpine3.22 AS production

ENV NODE_ENV=production \
    NEXT_TELEMETRY_DISABLED=1 \
    HOSTNAME=0.0.0.0 \
    PORT=3000

WORKDIR /app
RUN addgroup --system --gid 1001 nodejs \
    && adduser --system --uid 1001 nextjs

COPY --from=build --chown=nextjs:nodejs /app/.next/standalone ./
COPY --from=build --chown=nextjs:nodejs /app/.next/static ./.next/static
COPY --from=build --chown=nextjs:nodejs /app/public ./public

USER nextjs
EXPOSE 3000
HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 \
  CMD ["node", "-e", "fetch('http://127.0.0.1:3000').then(r=>{if(!r.ok)process.exit(1)}).catch(()=>process.exit(1))"]
CMD ["node", "server.js"]
