# Use an official PHP 8.2 FPM image
FROM php:8.2-fpm

# Update package repository and install required packages
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    git \
    libpq-dev \
    curl \
    unzip # Added unzip for handling zip files

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) iconv && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd && \
    docker-php-ext-install pdo_pgsql zip # Install pdo_pgsql and zip extension

# Install Composer (PHP dependency manager)
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Install Node.js and npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install Yarn (optional, but recommended)
RUN npm install --global yarn

# Create a directory for your application
RUN mkdir -p /var/www/html
RUN chown -R www-data:www-data /var/www/html

COPY . /var/www/html

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Install dependencies (e.g., Composer, npm)
RUN composer install --no-plugins --no-scripts
RUN npm install

# Expose the PHP-FPM port
EXPOSE 9000

# Start the PHP-FPM and Tailwind build process with watch mode
CMD php bin/console tailwind:build --watch & php-fpm