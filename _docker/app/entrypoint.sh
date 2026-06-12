#!/bin/sh
set -e

su -s /bin/sh www-data -c "composer install --no-interaction --prefer-dist --optimize-autoloader"
su -s /bin/sh www-data -c "php artisan migrate --force"

exec "$@"
