FROM php:8.3-fpm-alpine

# System deps
RUN apk add --no-cache \
    bash git curl unzip icu-dev oniguruma-dev \
    libzip-dev zip \
    freetype-dev libjpeg-turbo-dev libpng-dev \
    mysql-client

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
    pdo_mysql mbstring exif pcntl bcmath intl zip gd opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP config
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Create non-root user
RUN addgroup -g 1000 www && adduser -G www -g www -s /bin/sh -D -u 1000 www \
 && git config --global --add safe.directory /var/www

WORKDIR /var/www

# Ensure storage permissions (Laravel)
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache \
 && chown -R www:www /var/www

USER www

CMD ["php-fpm"]
