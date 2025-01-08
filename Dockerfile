FROM php:8.2-fpm

# Install necessary dependencies like composer
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader
