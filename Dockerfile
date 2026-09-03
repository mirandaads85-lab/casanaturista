FROM php:8.2-apache

# Habilita la extensión mysqli (necesaria para conectar a MySQL/freedb.tech)
RUN docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli

# Habilita mod_rewrite por si el sitio usa URLs amigables
RUN a2enmod rewrite

# Evita que Apache tenga que "adivinar" su propio nombre de host
# (silencia el warning "Could not reliably determine the server's
# fully qualified domain name" y hace más predecible la generación
# de URLs internas de Apache).
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copia todo el proyecto al directorio público de Apache
COPY . /var/www/html/

# Aplica la configuración de producción de PHP (oculta errores en pantalla,
# los deja solo en el log del contenedor)
RUN cp /var/www/html/docker/production.ini /usr/local/etc/php/conf.d/production.ini

# Permisos básicos
RUN chown -R www-data:www-data /var/www/html

# Render exige que el contenedor escuche en el puerto 10000 (su valor
# por defecto para la variable PORT). Configuramos Apache para usar
# ese puerto en vez del 80 por defecto.
RUN sed -i "s/80/10000/g" /etc/apache2/ports.conf \
    && sed -i "s/:80/:10000/g" /etc/apache2/sites-enabled/000-default.conf
EXPOSE 10000
