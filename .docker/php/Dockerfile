FROM php:8.2-fpm-bookworm

# Instalamos dependencias y extensiones necesarias para Symfony
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && pecl install redis xdebug \    
    && docker-php-ext-install pdo_mysql zip intl opcache mbstring xml gd \
    && docker-php-ext-enable xdebug

# Composer (opcional, si quieres tener composer en el contenedor)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html