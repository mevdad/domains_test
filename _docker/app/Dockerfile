FROM php:8.5-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    git \
    cron \
    supervisor \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libicu-dev && \
    docker-php-ext-configure intl && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Remove default server definition
RUN rm -rf /var/www/html

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Install scheduler cron job
COPY _docker/app/scheduler.cron /etc/cron.d/laravel-scheduler
RUN chmod 0644 /etc/cron.d/laravel-scheduler

# Configure supervisor (php-fpm + cron + queue worker)
COPY _docker/app/supervisord.conf /etc/supervisor/supervisord.conf
RUN mkdir -p /var/log/supervisor

# Entrypoint runs migrations before starting supervisor
COPY _docker/app/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 9000 and start supervisor (php-fpm, cron, queue worker)
EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]