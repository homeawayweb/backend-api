# Use the official Apache with PHP 8.2 image as our base
FROM php:8.2-apache

# Install required PHP extensions for your project (MySQL and ZIP handling)
# Render will run these commands on their servers.
RUN docker-php-ext-install pdo pdo_mysql zip fileinfo

# Enable Apache's rewrite module, which is good practice
RUN a2enmod rewrite

# Copy your entire application code from the GitHub repo into the container's web root
COPY . /var/www/html/

# Set the correct permissions for the web server to be able to write to the 'uploads' folder
# This is crucial for your file uploads to work on Render.
# Note: The 'uploads' directory must exist in your GitHub repository.
RUN chown -R www-data:www-data /var/www/html/uploads
RUN chmod -R 775 /var/www/html/uploads