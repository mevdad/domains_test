#!/bin/sh
set -e

# su -s /bin/sh www-data -c "[ -f .env ] || cp .env.example .env"

# Ensure Laravel writable directories are owned by the php-fpm user (www-data).
# Without this Laravel falls back to the system temp dir and PHP emits the
# "tempnam(): file created in the system's temporary directory" warning.
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

su -s /bin/sh root -c "composer install --no-interaction --prefer-dist --optimize-autoloader"

# Build frontend assets (public/build is gitignored, so it must be generated here).
su -s /bin/sh root -c "[ -d node_modules ] || npm ci"
su -s /bin/sh root -c "npm run build"
su -s /bin/sh root -c "grep -q '^APP_KEY=.\+' .env || php artisan key:generate --force"
su -s /bin/sh root -c "php artisan migrate --force"

exec "$@"
