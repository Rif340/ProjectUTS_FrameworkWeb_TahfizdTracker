#!/bin/sh
set -e

# Run storage link and clear cache
php artisan storage:link || true
php artisan optimize:clear || true

# Start PHP built-in server with PORT provided by Render
PORT="${PORT:-80}"
echo "Starting Laravel on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT
