ARG NODE_VERSION=22.14.0

FROM node:${NODE_VERSION}-alpine AS dev

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm install

COPY . .

EXPOSE 3000

CMD ["npm", "run", "dev"]

FROM main AS builder

RUN npm run build

FROM node:${NODE_VERSION}-alpine AS prod

WORKDIR /app

COPY --from=builder /app/package* /app/
COPY --from=builder /app/.output/ /app/.output/

RUN npm ci --omit=dev

CMD ["node", ".output/server/index.mjs"]
