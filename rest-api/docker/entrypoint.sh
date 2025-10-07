#!/bin/bash
set -e

# Function to wait for MySQL to be ready
wait_for_mysql() {
    echo "Waiting for MySQL to be ready..."
    while ! mysqladmin ping -h"mysql" -u"microfinance_user" -p"microfinance_password" --silent; do
        sleep 1
    done
    echo "MySQL is ready!"
}

# Function to wait for Redis to be ready
wait_for_redis() {
    echo "Waiting for Redis to be ready..."
    while ! redis-cli -h redis ping | grep -q PONG; do
        sleep 1
    done
    echo "Redis is ready!"
}

# Wait for dependencies
if [ "$1" = "laravel" ]; then
    wait_for_mysql
    wait_for_redis

    # Generate app key if not exists
    if [ -z "$APP_KEY" ]; then
        echo "Generating application key..."
        php artisan key:generate --force
    fi

    # Clear and cache configurations
    echo "Optimizing Laravel..."
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear

    # Run migrations
    echo "Running database migrations..."
    php artisan migrate --force

    # Seed database if needed
    if [ "$SEED_DATABASE" = "true" ]; then
        echo "Seeding database..."
        php artisan db:seed --force
    fi

    # Cache configurations for production
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo "Starting Laravel application..."
    php artisan serve --host=0.0.0.0 --port=8000
else
    # Default: run supervisor
    exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
fi