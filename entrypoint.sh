#!/bin/sh
set -e

# Ensure permissions on every startup
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Generate key if not exists (hanya untuk pengamanan)
if [ -z "$APP_KEY" ]; then
    echo "Warning: APP_KEY is not set. Generating one..."
    php artisan key:generate --show --no-interaction
fi

# Run database migrations automatically in production
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force
fi

# Production optimizations
if [ "$APP_ENV" = "production" ]; then
    echo "Optimizing for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Create storage link
php artisan storage:link --force

echo "Starting Apache..."
exec "$@"
