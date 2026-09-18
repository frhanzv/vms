# SafeG VMS — CodeIgniter 4 / PHP 8.2 (Podman & Docker compatible)
#
# Prerequisites (host) before build:
#   composer install --no-dev --optimize-autoloader
#
# Build:
#   podman build --network=host --format=docker -t localhost/vms:v1.0.1 -f Containerfile .

FROM php:8.2-apache-bookworm

LABEL org.opencontainers.image.title="vms" \
      org.opencontainers.image.description="SafeG Visitor Management System" \
      org.opencontainers.image.version="v1.0.1"

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    COMPOSER_ALLOW_SUPERUSER=1 \
    TESSERACT_PATH=/usr/bin/tesseract

# System packages + PHP extensions (MySQL, not Postgres)
RUN sed -i 's|http://deb.debian.org|https://deb.debian.org|g' /etc/apt/sources.list.d/debian.sources \
    && printf 'Acquire::ForceIPv4 "true";\nAcquire::Retries "5";\n' > /etc/apt/apt.conf.d/99vms \
    && apt-get update && apt-get install -y --no-install-recommends \
        curl \
        git \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        default-mysql-client \
        tesseract-ocr \
        tesseract-ocr-eng \
        unzip \
        zip \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        mysqli \
        opcache \
        pdo_mysql \
        zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# PHP defaults for uploads / OCR workloads
RUN { \
      echo 'memory_limit=512M'; \
      echo 'upload_max_filesize=64M'; \
      echo 'post_max_size=64M'; \
      echo 'max_execution_time=120'; \
      echo 'opcache.enable=1'; \
      echo 'opcache.validate_timestamps=0'; \
    } > /usr/local/etc/php/conf.d/vms.ini

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
RUN echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN test -f vendor/autoload.php \
    || (echo "ERROR: vendor/ missing. Run: composer install --no-dev" && exit 1) \
    # Container serves public/ as site root (not /vms subdirectory like Laragon)
    && sed -i 's|RewriteBase /vms|RewriteBase /|g' public/.htaccess \
    && mkdir -p \
        writable/cache \
        writable/debugbar \
        writable/logs \
        writable/session \
        writable/uploads \
        public/uploads \
        public/files \
    && chown -R www-data:www-data writable public/uploads public/files \
    && chmod -R ug+rwx writable public/uploads public/files \
    && chmod +x /usr/local/bin/entrypoint.sh \
    && sed -i 's/\r$//' /usr/local/bin/entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=45s --retries=3 \
  CMD curl -fsS -o /dev/null -w '%{http_code}' http://127.0.0.1/ | grep -Eq '200|302|303|307' || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
