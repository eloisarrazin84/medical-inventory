FROM php:8.1-apache

# Installation des extensions PHP nécessaires
RUN docker-php-ext-install mysqli

# Activation du module rewrite pour Apache
RUN a2enmod rewrite

# Copie des fichiers source dans le conteneur
COPY . /var/www/html

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
