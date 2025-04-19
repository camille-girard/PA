ARG NODE_VERSION=22.14.0

FROM node:${NODE_VERSION}-alpine AS dev

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm install

COPY . .

EXPOSE 3000

CMD ["npm", "run", "dev"]

FROM node:${NODE_VERSION}-alpine AS builder

WORKDIR /app

COPY package.json package-lock.json ./
COPY . .

RUN npm ci
RUN npm run build

FROM node:${NODE_VERSION}-alpine AS prod

WORKDIR /app

COPY --from=builder /app/package.json /app/package-lock.json ./
COPY --from=builder /app/.nuxt/ /app/.nuxt/

RUN npm ci --omit=dev

EXPOSE 3000

CMD ["node", ".nuxt/dist/server/server.mjs"]
