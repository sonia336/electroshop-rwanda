# ============================================================
# ElectroShop Rwanda - Dockerfile
# PHP 8.2 with Apache + PDO MySQL extension
# ============================================================

FROM php:8.2-apache

# Install PDO MySQL extension (required for database access)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite (useful for clean URLs later)
RUN a2enmod rewrite

# Set the working directory to Apache's web root
WORKDIR /var/www/html

# Copy application source code into the container
COPY . /var/www/html/

# Give Apache correct permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 (Apache default)
EXPOSE 80

# Apache runs in the foreground by default in this base image
CMD ["apache2-foreground"]
