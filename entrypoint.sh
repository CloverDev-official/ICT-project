#!/bin/sh
set -e

# Generate key if not exists and not in production
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is not set. Generating one..."
    php artisan key:generate --show
fi

# Run migrations (optional, uncomment if you want auto-migration)
# php artisan migrate --force

# Cache configuration for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Execute the main command (Apache)
exec "$@"
