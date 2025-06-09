FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files into the container
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set folder permissions
RUN chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Expose port 8000
EXPOSE 8000

# Run migrations & seed, then start the Laravel server
CMD php artisan migrate:fresh --seed --force && php artisan serve --host=0.0.0.0 --port=8000
