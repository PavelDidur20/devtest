FROM php:8.2-fpm


RUN set -eux; \
    apt-get update && \
    apt-get install -y --no-install-recommends \
      git \
      unzip \
      ca-certificates \
      curl \
      build-essential \
      autoconf \
      pkg-config \
      libssl-dev \
      libicu-dev \
      libzip-dev \
      libpng-dev \
      libjpeg-dev \
      libfreetype6-dev \
      zlib1g-dev \
      libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql intl gd zip bcmath opcache \
    && pecl channel-update pecl.php.net || true \
    && (pecl install redis || pecl install redis-5.3.7 || true) \
    && docker-php-ext-enable redis || true \
    && apt-get purge -y --auto-remove build-essential autoconf pkg-config libssl-dev || true \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html


COPY composer.json composer.lock ./


RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-progress || true


COPY . .


RUN composer dump-autoload --optimize || true \
    && chown -R www-data:www-data /var/www/html


EXPOSE 9000


CMD ["php-fpm"]
