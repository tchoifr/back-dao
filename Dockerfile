FROM php:8.2-apache

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpng-dev libonig-dev libxml2-dev zip \
    && docker-php-ext-install intl pdo pdo_mysql opcache zip gd

# Activer le module Apache rewrite
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier les fichiers du projet
WORKDIR /var/www/html
COPY . .

# Variables d'environnement minimales
ENV APP_ENV=prod
ENV APP_SECRET=dev123456

# Installer les dépendances PHP avec Composer
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-interaction

# Donner les bons droits sur le cache
RUN chown -R www-data:www-data /var/www/html/var

# Exposer le port d'Apache
EXPOSE 80

# Lancer Apache
CMD ["apache2-foreground"]
