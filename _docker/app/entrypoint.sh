#!/bin/sh
set -e

su -s /bin/sh www-data -c "php artisan migrate --force"

exec "$@"
