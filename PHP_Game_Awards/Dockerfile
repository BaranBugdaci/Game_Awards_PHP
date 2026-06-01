FROM php:8.2-apache

# URL Yönlendirmeleri için Apache Rewrite Modülünü Aktif Et
RUN a2enmod rewrite

# Çalışma Dizinini Ayarla
WORKDIR /var/www/html

# Proje dosyalarını imajın içine kopyala
COPY . /var/www/html/

# DocumentRoot'u public klasörü olarak ayarla
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Storage ve avatars klasörleri için yazma izinlerini ayarla
RUN mkdir -p /var/www/html/storage/users && \
    mkdir -p /var/www/html/storage/comments && \
    mkdir -p /var/www/html/public/avatars && \
    chown -R www-data:www-data /var/www/html/storage && \
    chmod -R 775 /var/www/html/storage && \
    chown -R www-data:www-data /var/www/html/public/avatars && \
    chmod -R 775 /var/www/html/public/avatars

EXPOSE 80
