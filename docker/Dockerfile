FROM hanabosocom/php-dev:php-8.2-alpine

RUN apk update && apk upgrade && apk add --no-cache \
    autoconf build-base g++ krb5-dev imap-dev && \
    docker-php-ext-install imap && \
    docker-php-ext-install soap && \
    apk del autoconf build-base g++ krb5-dev && \
    rm -rf /var/cache/apk/* /var/log/* /tmp/* /var/www/*
