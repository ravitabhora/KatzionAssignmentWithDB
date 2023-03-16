FROM php:8.2.0-apache

WORKDIR /var/www/html

# Mod Rewrite
RUN a2enmod rewrite

# Linux Library
RUN apt-get update -y && apt-get install -y \
    libicu-dev \
    libmariadb-dev \
    unzip zip \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev 
# Copy the current directory contents into the container at /app
COPY . /var/www/html

COPY .env.example .env
# Set up the virtual host for Apache

COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf
# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP Extension
RUN docker-php-ext-install gettext intl pdo_mysql gd

RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd
    
# Update composer to get vender folder
RUN composer install --no-interaction --no-plugins --no-scripts
# Artisan command to generate key
RUN php artisan key:generate

# Set file permissions
RUN chown -R www-data:www-data /var/www 
RUN chmod -R 755 /var/www/html/storage   
RUN chmod -R 755 /var/www/html/storage/logs

