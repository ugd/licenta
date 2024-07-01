FROM node:18 as build-stage

WORKDIR /app

COPY package*.json ./
COPY . .

FROM nginx:stable-alpine as production-stage
COPY --from=build-stage /app /app
COPY nginx-default.conf /etc/nginx/conf.d/default.conf

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
