#!/bin/sh
set -e

# su -s /bin/sh www-data -c "[ -f .env ] || cp .env.example .env"
su -s /bin/sh root -c "composer install --no-interaction --prefer-dist --optimize-autoloader"
su -s /bin/sh root -c "grep -q '^APP_KEY=.\+' .env || php artisan key:generate --force"
su -s /bin/sh root -c "php artisan migrate --force"

exec "$@"
