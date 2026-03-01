FROM php:8.2-apache

# Ativa mod_rewrite
RUN a2enmod rewrite

# Dependências do sistema + Node
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    nodejs \
    npm \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        zip \
        mbstring \
        xml

# Ajusta DocumentRoot para /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copia primeiro package.json para cache de build
COPY package*.json ./

# Instala dependências do frontend
RUN npm install

# Copia o restante do projeto
COPY . .

# Build dos assets (Vite/Tailwind)
RUN npm run build

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80