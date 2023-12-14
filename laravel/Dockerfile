FROM php:8.1

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    mariadb-client \
    curl \
    libcurl4 \
    libcurl4-openssl-dev \
    libgd3 \
    libgd-dev \
    libonig-dev \
    libxml2-dev \
    zlib1g-dev \
    libgrpc-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip gd bcmath mbstring xml

# Copy composer files and install dependencies
WORKDIR /var/www/html
COPY composer.json ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-scripts --no-autoloader && rm -rf /root/.composer

# Copy the rest of the application code
COPY . .

# Generate optimized autoload files
RUN composer dump-autoload --optimize

# Set up proper permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Set recommended PHP configurations
RUN echo "date.timezone=UTC" > /usr/local/etc/php/conf.d/timezone.ini

# Expose port (if necessary)
# EXPOSE 80

# Your application-specific command to start the PHP application (e.g., Laravel)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

# Alternatively, if your application is not using Laravel's Artisan serve, you might use a generic PHP server:
# CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]
