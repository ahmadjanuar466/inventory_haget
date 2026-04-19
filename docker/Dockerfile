FROM composer:2 AS composer_deps

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --no-scripts

COPY . .


FROM node:20-bookworm-slim AS frontend_assets

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm ci

COPY . .
COPY --from=composer_deps /app/vendor /app/vendor

RUN rm -f public/hot && npm run build


FROM php:8.3-fpm-bookworm AS runtime

ARG APP_DIR=/var/www/html
ARG IMAGE_APP_DIR=/opt/app

RUN apt-get update && apt-get install -y \
    curl \
    git \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    mariadb-client \
    nginx \
    passwd \
    supervisor \
    unzip \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        zip \
    && rm -f /etc/nginx/sites-enabled/default /etc/nginx/sites-available/default \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR ${APP_DIR}

COPY . ${IMAGE_APP_DIR}
COPY --from=composer_deps /app/vendor ${IMAGE_APP_DIR}/vendor
COPY --from=frontend_assets /app/public/build ${IMAGE_APP_DIR}/public/build

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/php/local.ini /usr/local/etc/php/conf.d/99-local.ini
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN mkdir -p \
    ${APP_DIR} \
    ${APP_DIR}/bootstrap/cache \
    ${APP_DIR}/public/build \
    ${APP_DIR}/storage/app/public \
    ${APP_DIR}/storage/framework/cache/data \
    ${APP_DIR}/storage/framework/sessions \
    ${APP_DIR}/storage/framework/views \
    ${APP_DIR}/storage/logs \
    /run/php \
    /var/lib/nginx/body \
    && chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data ${APP_DIR} ${IMAGE_APP_DIR} /run/php /var/lib/nginx /var/log/nginx

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
