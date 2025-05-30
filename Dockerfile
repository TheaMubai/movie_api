FROM php:8.2-cli

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application code
COPY . .

# Ensure necessary directories exist
RUN mkdir -p storage/logs bootstrap/cache

# Set correct permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose the port Laravel will run on
EXPOSE 8000

# Start the Laravel server
CMD php artisan serve --host=0.0.0.0 --port=8000
