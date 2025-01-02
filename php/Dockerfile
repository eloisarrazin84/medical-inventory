FROM php:8.1-apache

# Installer les extensions nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Activer le module rewrite d'Apache
RUN a2enmod rewrite

# Copier les fichiers source
COPY . /var/www/html

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
