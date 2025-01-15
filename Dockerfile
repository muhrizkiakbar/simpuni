# [BASE STAGE]
FROM php:8.2-fpm-alpine as base

# Install the php extension installer from its image
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install dependencies
RUN apk add --no-cache \
    openssl \
    ca-certificates \
    libxml2-dev \
    oniguruma-dev \
    mariadb-client \
    mariadb-dev

# Install PHP extensions
RUN install-php-extensions \
    bcmath \
    ctype \
    dom \
    fileinfo \
    mbstring \
    pdo pdo_mysql \
    tokenizer \
    pcntl \
    redis-stable

# Install the composer packages using www-data user
WORKDIR /app
RUN chown www-data:www-data /app
COPY --chown=www-data:www-data . .
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer
USER www-data
RUN composer install --no-dev --prefer-dist || cat /app/storage/logs/laravel.log
# [END BASE STAGE]

# [FRONTEND STAGE]
FROM node:22-alpine as frontend

WORKDIR /app
COPY . .
RUN apk add --no-progress --quiet --no-cache git
RUN yarn cache clean
RUN yarn install
RUN yarn build
# [END FRONTEND STAGE]

# [MYSQL STAGE]
FROM mariadb:10.11-alpine as mysql
ENV MYSQL_ROOT_PASSWORD=root
ENV MYSQL_DATABASE=laravel
ENV MYSQL_USER=laravel
ENV MYSQL_PASSWORD=laravel
# Expose MySQL port
EXPOSE 3306
# [END MYSQL STAGE]

# [APP STAGE]
FROM base as app

# Prepare the frontend files & caching
COPY --from=frontend --chown=www-data:www-data /app/public /app/public
RUN composer update
RUN php artisan optimize:clear
RUN php artisan view:cache

# Setup nginx & supervisor as root user
USER root
RUN apk add --no-progress --quiet --no-cache nginx supervisor

COPY .docker/nginx-default.conf /etc/nginx/http.d/default.conf
COPY .docker/supervisord.conf /etc/supervisord.conf

# Copy custom CA certificate and update the CA bundle
COPY .docker/ca-cert.pem /usr/local/share/ca-certificates/custom-ca-cert.pem
RUN update-ca-certificates

# Apply the required changes to run nginx as www-data user
RUN chown -R www-data:www-data /run/nginx /var/lib/nginx /var/log/nginx && \
    sed -i '/user nginx;/d' /etc/nginx/nginx.conf

# Switch to www-user
USER www-data
EXPOSE 8000 8443

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
# [END APP STAGE]

# DEFAULT STAGE
FROM base

