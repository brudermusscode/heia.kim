FROM php:8.5-fpm

RUN DEBIAN_FRONTEND=noninteractive apt update -y \
    && apt install -y \
    libicu-dev \
    libcurl4-openssl-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libgif-dev \
    libavif-dev \
    libwebp-dev \
    nano

# Set the production php.ini to default
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Remove unused files from php directory
RUN rm -rf -r /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini-production

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-avif \
    --with-webp
RUN docker-php-ext-install gd pdo_mysql intl exif curl zip
RUN docker-php-ext-enable gd pdo_mysql intl exif curl zip

# Custom ini and config files.
COPY zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY zz-custom.ini /usr/local/etc/php/conf.d/zz-custom.ini

EXPOSE 9000

CMD ["php-fpm", "-F"]
