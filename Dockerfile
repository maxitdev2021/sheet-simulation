# Use official PHP image with required extensions
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    bash \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Laravel app source
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set file permissions
RUN chmod -R 775 storage bootstrap/cache && chown -R www-data:www-data .

# Expose port (for php artisan serve)
EXPOSE 8000

# Start Laravel app (use artisan serve)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
