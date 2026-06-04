FROM ubuntu:26.04

LABEL MAINTAINER="brudermusscode <justin@heia.kim>"

ENV DEBIAN_FRONTEND=noninteractive
RUN apt update -q && \
    apt install -y \
    apt-transport-https \
    lsb-release \
    software-properties-common \
    gnupg2 \
    curl \
    ca-certificates \
    ubuntu-keyring \
    libzip-dev \
    libicu-dev \
    libcurl4-openssl-dev \
    cron \
    git \
    zip \
    unzip \
    nodejs \
    php8.5-common \
    php8.5-cli \
    php8.5-curl \
    php8.5-exif \
    php8.5-intl \
    php8.5-mysql \
    php-pear \
    php-dev

RUN pecl version
RUN pecl install redis && \
    echo "extension=redis.so" > /etc/php/8.5/cli/conf.d/20-redis.ini

# nginx.
RUN mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://nginx.org/keys/nginx_signing.key \
    | gpg --dearmor -o /etc/apt/keyrings/nginx.gpg
RUN echo "deb [signed-by=/etc/apt/keyrings/nginx.gpg] \
    http://nginx.org/packages/ubuntu $(lsb_release -cs) nginx" \
    > /etc/apt/sources.list.d/nginx.list
RUN apt install -y nginx

# Copy wait-for-it to allow some containers to wait for the up of
# a dependend service.
COPY docker/wait-for-it.sh /wait-for-it.sh
RUN chmod +x /wait-for-it.sh

# Set working directory, so docker knows where it's operating.
WORKDIR /data

# Copy all local files to the image.
COPY . .

# Cron dependencies
RUN which cron
# COPY docker/crontab /etc/crontab
# COPY docker/cron /etc/init.d/cron
# RUN rm -rf /etc/cron.*/* && rm -f /etc/crontab
RUN chmod +x tasks/run-jobs.php

# Permissions for operating inside the public/ directory.
RUN chmod a+rw -R public

EXPOSE 80

ENTRYPOINT ["/data/docker/entrypoint.sh"]
