# Use an official PHP runtime as a parent image
#FROM php:7.3-apache
FROM php:8.1.18-apache
#FROM php:8.3-apache
#FROM php:8.3.0RC3-apache-bullseye
# Set the COMPOSER_ALLOW_SUPERUSER environment variable
ENV COMPOSER_ALLOW_SUPERUSER=1



# Set an environment variable with the build date
ARG BUILD_DATE
ARG COMMIT_HASH

LABEL build_date="${BUILD_DATE}"
LABEL commit_hash="${COMMIT_HASH}"


# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql pcntl 

#installing required packags.
RUN apt-get update && apt-get install -y  unzip  libicu-dev  libicu-dev iputils-ping libhiredis-dev &&  docker-php-ext-install intl  \
    && rm -rf /var/lib/apt/lists/* 
    
#RUN pecl install redis && docker-php-ext-enable redis;

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the CakePHP application code to the container
COPY . /var/www/html


#COPY docker/entrscript /var/www/html/bin
# COPY docker/entrypoint.sh /var/www/html/bin/entrypoint.sh
RUN chmod a+x /var/www/html/bin/*


# Install dependencies using Composer

RUN composer install --no-interaction --prefer-dist #//temporary disabled.
#RUN composer install

# Set file permissions for CakePHP cache directories
RUN chown -R www-data:www-data /var/www/html
#RUN chown -R www-data:www-data app/logs


#COPY  docker/000-default.conf /etc/apache2/sites-enabled/000-default.conf 
#COPY docker/passenv.conf /etc/apache2/conf-available/passenv.conf

#RUN ln -s conf/env config/.env

# Set up Apache configuration
#COPY docker/apache2.conf /etc/apache2/apache2.conf
RUN a2enmod rewrite

# Expose port 80 for Apache
EXPOSE 80


# Start the Apache web server

# CMD ["apache2-foreground"]

#CMD ["bash", "-c", "service apache2 restart & /var/www/html/bin/cake Processrcvq & /var/www/html/bin/cake Processsendq"]
USER www-data

ENTRYPOINT ["/var/www/html/bin/entrypoint.sh"]