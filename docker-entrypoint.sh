#!/bin/sh
set -e

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force || true
fi

# Run migrations (pakai --force untuk production)
php artisan migrate --force || true

# Link storage
php artisan storage:link || true

# Clear dan rebuild cache
php artisan optimize:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start server
PORT="${PORT:-80}"
echo "Starting Laravel on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT
