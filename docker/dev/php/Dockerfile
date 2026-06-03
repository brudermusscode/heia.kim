FROM php:8.5.7RC2-fpm-alpine3.22

ENV DEBIAN_FRONTEND=noninteractive
RUN apk add --no-cache \
  icu-dev \
  curl-dev \
  libzip-dev \
  freetype-dev \
  jpeg-dev \
  libpng-dev \
  giflib-dev \
  libavif-dev \
  libwebp-dev \
  nano \
  $PHPIZE_DEPS

# Set the production php.ini to default
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Remove unused files from php directory
RUN rm -rf -r /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini-production

RUN docker-php-ext-configure gd \
  --with-freetype \
  --with-jpeg \
  --with-avif \
  --with-webp
RUN pecl install redis
RUN docker-php-ext-enable redis # gd pdo_mysql intl exif curl zip
RUN docker-php-ext-install gd pdo_mysql intl exif zip # curl

# Custom ini and config files.
COPY zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY zz-custom.ini /usr/local/etc/php/conf.d/zz-custom.ini

EXPOSE 9000

CMD ["php-fpm", "-F"]
