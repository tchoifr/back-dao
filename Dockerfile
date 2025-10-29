# Étape 1 : choisir une image PHP avec Apache
FROM php:8.2-apache

# Installer les extensions nécessaires à Symfony
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpng-dev libonig-dev libxml2-dev zip \
    && docker-php-ext-install intl pdo pdo_mysql opcache zip

# Activer le module Apache rewrite (utile pour Symfony)
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier les fichiers du projet dans le conteneur
WORKDIR /var/www/html
COPY . .

# Installer les dépendances Symfony
RUN composer install --no-dev --optimize-autoloader

# Donner les bons droits d’accès
RUN chown -R www-data:www-data /var/www/html/var

# Exposer le port utilisé par Apache
EXPOSE 80

# Commande de démarrage
CMD ["apache2-foreground"]
