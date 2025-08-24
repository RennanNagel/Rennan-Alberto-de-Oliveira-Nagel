FROM php:8.3-fpm-alpine

# libs do sistema
RUN apk add --no-cache git curl bash libzip-dev oniguruma-dev icu-dev zlib-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev mariadb-connector-c-dev postgresql-dev

# extensões PHP (MySQL + Postgres, já deixamos ambas habilitadas)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
