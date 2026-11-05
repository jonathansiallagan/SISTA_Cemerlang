# ==========================================
# STAGE 1: Frontend Build (Node.js)
# Membangun aset Tailwind CSS v4 & Vite
# ==========================================
FROM node:22-alpine AS frontend
WORKDIR /app

# Manfaatkan cache: Copy file dependencies frontend terlebih dahulu
COPY package.json package-lock.json ./
RUN npm ci

# Copy resource JS/CSS/Blade untuk proses kompilasi Vite
COPY vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/
# Jika Tailwind config terpisah dari vite.config.js, pastikan untuk dicopy juga

# Eksekusi build, menghasilkan aset statis di /app/public/build
RUN npm run build


# ==========================================
# STAGE 2: Backend Dependencies (Composer)
# Mengunduh package PHP secara terisolasi
# ==========================================
FROM composer:2 AS vendor
WORKDIR /app

ENV COMPOSER_PROCESS_TIMEOUT=2000
RUN composer config --global process-timeout 2000

COPY composer.json composer.lock ./

# [BEST PRACTICE]: Tambahkan --no-scripts agar Composer 
# tidak mencoba menjalankan artisan sebelum kodenya ada.
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs \
    --no-scripts


# ==========================================
# STAGE 3: Production Image (FrankenPHP)
# Ringan, aman, dan tanpa Node.js/Composer
# ==========================================
FROM dunglas/frankenphp:1-php8.3-alpine AS production

ENV APP_ENV=production
ENV APP_DEBUG=false

RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    bcmath \
    gd \
    zip \
    exif \
    pcntl \
    intl \
    opcache

WORKDIR /app

COPY . .
COPY --from=vendor /app/vendor/ ./vendor/
COPY --from=frontend /app/public/build/ ./public/build/
COPY Caddyfile /etc/caddy/Caddyfile

RUN chown -R www-data:www-data storage bootstrap/cache

# [BEST PRACTICE]: Karena kita membungkam skrip Composer di Stage 2,
# kita WAJIB menjalankan package:discover di sini sebelum melakukan cache.
RUN php artisan package:discover --ansi && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

EXPOSE 80