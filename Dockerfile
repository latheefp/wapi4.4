# Use an official PHP runtime as a parent image
#FROM php:7.3-apache
FROM php:8.1.18-apache
# Set the COMPOSER_ALLOW_SUPERUSER environment variable
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql pcntl 

#installing required packags.
RUN apt-get update && apt-get install -y  unzip  libicu-dev  libicu-dev iputils-ping &&  docker-php-ext-install intl  \
    && rm -rf /var/lib/apt/lists/* 
    

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the CakePHP application code to the container
COPY . /var/www/html
#COPY docker/app_local.php  /var/www/html/config/app_local.php
# Install dependencies using Composer

RUN composer install --no-interaction --prefer-dist #//temporary disabled.
#RUN composer install

# Set file permissions for CakePHP cache directories
RUN chown -R www-data:www-data /var/www/html
#RUN chown -R www-data:www-data app/logs


#RUN ln -s conf/env config/.env

# Set up Apache configuration
#COPY docker/apache2.conf /etc/apache2/apache2.conf
RUN a2enmod rewrite

# Expose port 80 for Apache
EXPOSE 80


# Start the Apache web server

CMD ["apache2-foreground"]
#CMD ["bash", "-c", "service apache2 restart &&  /var/www/html/bin/cake Processrcvq"]


