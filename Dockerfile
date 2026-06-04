FROM php:8.2-apache

# Habilitar mod_rewrite para que funcionen las rutas /api/...
RUN a2enmod rewrite

# Instalar extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
        libcurl4-openssl-dev \
        libxml2-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        curl \
        simplexml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Permitir .htaccess en el directorio raíz
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copiar todos los archivos del proyecto al directorio web
COPY . /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80
