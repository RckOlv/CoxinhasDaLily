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

# PHP upload limits
COPY php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# Nginx config (proxies to PHP-FPM on localhost:9000)
COPY nginx.render.conf /etc/nginx/sites-available/default

# Create SQLite database with correct ownership
RUN touch /var/www/database/database.sqlite \
    && php artisan storage:link --force \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/database/database.sqlite

EXPOSE 80

CMD ["sh", "-c", "php artisan storage:link --force && chown www-data:www-data /var/www/database/database.sqlite && php artisan config:clear && php artisan migrate --force && php artisan db:seed --class=AdminUserSeeder --force && service nginx start && php-fpm"]
