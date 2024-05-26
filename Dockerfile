FROM node:18.18.2-alpine as build-assets

FROM php:8.2-fpm-alpine as build-stage


# Use the default production configuration
# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=build-assets /usr/lib /usr/lib
COPY --from=build-assets /usr/local/share /usr/local/share
COPY --from=build-assets /usr/local/lib /usr/local/lib
COPY --from=build-assets /usr/local/include /usr/local/include
COPY --from=build-assets /usr/local/bin /usr/local/bin


COPY deploy/php/php.ini /usr/local/etc/php/php.ini
COPY deploy/php/php-fpm.conf  /usr/local/etc/php-fpm.conf
COPY deploy/php/php-fpm.d/www.conf  /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/

LABEL maintainer="Agung Laksmana <agung@sumbarprov.go.id> X Reyan Dirul Adha <reyan@sumbarprov.go.id>"

RUN apk --no-cache add \
    busybox-suid \
    nginx \
    build-base \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    oniguruma-dev \
    libxml2-dev \
    freetype-dev \
    libpq-dev \
    nano

# Copy crontab file ke container
COPY deploy/cronjob/crontab.txt /etc/crontabs/root

# pastikan cron di eksekusi
RUN chmod 0644 /etc/crontabs/root


# Install extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip exif pcntl
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/
RUN docker-php-ext-install gd
# RUN pecl install -o -f redis &&  rm -rf /tmp/pear && docker-php-ext-enable redis
# Install composer
# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#pindah config nginx ke dalam image alpine php fpm
COPY deploy/alpine/nginx/ /etc/nginx/
RUN nginx -t

# Buat direktori untuk PID file Nginx
RUN mkdir -p /run/nginx

# Copy project ke dalam container
COPY . /var/www/

RUN cat .env

# Copy directory project permission ke container
COPY --chown=www-data:www-data . /var/www/
RUN chown -R www-data:www-data /var/www

# Install dependency
RUN composer install

#aktifkan jika mengunakan nodejs untuk compile css atau js
RUN npm install

# build css dan js
RUN npm run build

RUN chmod -R 777 /var/www/storage/

# Expose port 9000
EXPOSE 80

# Script untuk memulai PHP-FPM dan Nginx serta cron
CMD ["sh", "-c","crond -f && php-fpm -D && nginx -g 'daemon off;'"]

# # Ganti user ke www-data
# USER www-data


