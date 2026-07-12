FROM php:8.2-apache

# HMS uses raw mysqli only (no PDO, no GD) — kept lean on purpose.
# unzip is needed by Composer itself to install packages, not by the app.
RUN apt-get update && apt-get install -y --no-install-recommends unzip \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install mysqli \
    && a2enmod rewrite headers

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN { \
    echo 'upload_max_filesize = 20M'; \
    echo 'post_max_size = 20M'; \
    echo 'memory_limit = 256M'; \
    echo 'display_errors = Off'; \
    echo 'log_errors = On'; \
    } > /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html

# composer.json isn't committed to the repo (gitignored) — copy it in
# from this scaffold, or drop your own local copy over it before building.
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --optimize-autoloader

COPY . .
RUN composer install --no-dev --no-interaction --optimize-autoloader

# public/ is the real webroot (see public/.htaccess for the routing rules)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e "s!/var/www/html!\${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri -e "s!/var/www/!\${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
