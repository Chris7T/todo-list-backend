FROM php:8.1.0-fpm

WORKDIR /var/www

RUN apt-get update && \
    apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 /var/www

EXPOSE 9000

CMD mkdir -p /var/www/storage/logs && chown -R www-data:www-data /var/www/storage && chmod -R 775 /var/www/storage && php-fpm
