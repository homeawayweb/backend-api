# File Path: portfolio-api/Dockerfile (FINAL CORRECTED VERSION)

# Use the official Apache with PHP 8.2 image as our base
FROM php:8.2-apache

# --- THIS IS THE FIX ---
# We must first install the system-level dependencies that PHP extensions need.
# 'libzip-dev' is required by the 'zip' extension.
# We also install 'unzip' which is useful for the uploadProject.php script.
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Now that the dependencies are installed, we can install the PHP extensions.
RUN docker-php-ext-install pdo pdo_mysql zip fileinfo

# Enable Apache's rewrite module, which is good practice for routing.
RUN a2enmod rewrite

# Copy your entire application code from the GitHub repo into the container's web root.
COPY . /var/www/html/

# Set the correct permissions for the web server to be able to write to the 'uploads' folder.
# This is crucial for your file uploads to work on Render.
# Note: The 'uploads' directory must exist in your GitHub repository.
RUN chown -R www-data:www-data /var/www/html/uploads
RUN chmod -R 775 /var/www/html/uploads