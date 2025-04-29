FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
  libonig-dev \
  libzip-dev \
  zip \
  unzip \
  && docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath opcache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["php-fpm"]