FROM php:8.5-cli

WORKDIR /var/www/html

COPY . .

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libonig-dev \
        libzip-dev \
        libssl-dev \
        default-mysql-client \
        build-essential \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        bcmath \
        zip \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-interaction --prefer-dist

RUN cp .env.example .env || true

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]