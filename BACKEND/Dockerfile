FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    libsqlite3-dev sqlite3 git unzip \
    && docker-php-ext-install pdo_sqlite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

# desactiva warnings de deprecated
RUN echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT" > /usr/local/etc/php/conf.d/error-reporting.ini

WORKDIR /var/www/html

COPY . /var/www/html/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --prefer-dist

# crea carpeta per la bd
RUN mkdir -p /var/www/html/var && chmod 777 /var/www/html/var

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80
