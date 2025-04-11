FROM hanabosocom/php-dev:php-8.4-alpine

RUN apk update && apk upgrade && apk add --no-cache && \
    docker-php-ext-install soap && \
    rm -rf /var/cache/apk/* /var/log/* /tmp/* /var/www/*
