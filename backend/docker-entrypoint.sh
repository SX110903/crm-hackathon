#!/bin/sh
set -e

# Generate app key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Cache config in production
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
fi

exec "$@"
