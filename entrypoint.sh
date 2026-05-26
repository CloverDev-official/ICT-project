#!/bin/sh
set -e

# Generate key if not exists and avoid DB connection
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    # Kita menggunakan --no-interaction dan memastikan tidak ada yang memicu DB
    php artisan key:generate --force --no-interaction || echo "Key generation failed but continuing..."
fi

# Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "Starting Apache..."
exec "$@"
