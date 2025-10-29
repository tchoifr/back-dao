# Étape 1 : utiliser PHP avec Apache
FROM php:8.2-apache

# Installer les extensions nécessaires à Symfony
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpng-dev libonig-dev libxml2-dev zip \
    && docker-php-ext-install intl pdo pdo_mysql opcache zip

# Activer le module Apache rewrite (indispensable pour Symfony)
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application
WORKDIR /var/www/html
COPY . .

# Installer les dépendances Symfony
RUN composer install --no-dev --optimize-autoloader

# Donner les bons droits sur le dossier var/
RUN chown -R www-data:www-data /var/www/html/var

# Exposer le port sur lequel Apache écoute
EXPOSE 80

# Lancer Apache
CMD ["apache2-foreground"]
