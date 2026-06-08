FROM php:8.2-apache

# Habilita mod_rewrite e headers
RUN a2enmod rewrite headers

# Copia todos os arquivos para o servidor
COPY . /var/www/html/

# Garante que a pasta data existe e tem permissão de escrita
RUN mkdir -p /var/www/html/data \
    && chmod 755 /var/www/html/data \
    && chown -R www-data:www-data /var/www/html/data

# Configura Apache para permitir .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

EXPOSE 80
