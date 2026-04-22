#!/bin/sh
set -e

if [ "$SERVICE_ROLE" = "worker" ]; then
    echo "Starting queue worker..."
    exec php artisan queue:work --tries=3 --timeout=90 --sleep=3
else
    echo "Starting web server..."
    php artisan migrate --force
    exec php artisan serve --host=0.0.0.0 --port=$PORT
fi