FROM dunglas/frankenphp:php8.3

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    pdo_mysql \
    intl \
    zip \
    gd \
    bcmath \
    exif \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY . .

RUN composer dump-autoload --optimize --no-dev

RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/framework/testing \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

RUN printf '{\n\
    auto_https off\n\
    admin off\n\
    frankenphp\n\
    servers {\n\
        trusted_proxies static private_ranges\n\
    }\n\
}\n\
\n\
:8080 {\n\
    root * /app/public\n\
    encode zstd br gzip\n\
    php_server\n\
}\n' > /etc/caddy/Caddyfile

EXPOSE 8080

CMD php artisan migrate --force && frankenphp run --config /etc/caddy/Caddyfile
