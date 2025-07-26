# recipe_back/Dockerfile
FROM php:8.2-fpm

# Installe les dépendances système et les extensions PHP
RUN apt-get update \
 && apt-get install -y \
    git unzip libzip-dev libonig-dev libxml2-dev zip \
 && docker-php-ext-install pdo pdo_mysql intl opcache mbstring xml \
 && pecl install xdebug \
 && docker-php-ext-enable xdebug

# Installe Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copie le code et installe les dépendances
COPY . .
RUN composer install --no-interaction --optimize-autoloader

# Permissions (si besoin)
RUN chown -R www-data:www-data var/

EXPOSE 9000

CMD ["php-fpm"]
