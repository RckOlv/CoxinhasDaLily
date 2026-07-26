FROM php:8.4-fpm

# Install Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

# Install Nginx + system deps
RUN apt-get update && apt-get install -y \
    nginx git curl zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_sqlite mbstring exif pcntl bcmath gd \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install JS deps and build assets
RUN npm install && npm run build

# Nginx config (proxies to PHP-FPM on localhost:9000)
COPY nginx.render.conf /etc/nginx/sites-available/default

# Permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

CMD ["sh", "-c", "service nginx start && php-fpm"]
