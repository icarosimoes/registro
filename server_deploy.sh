#!/bin/sh
set -e

echo "Deploying application ..."

# Enter maintenance mode
(php artisan down --message 'The app is being (quickly!) updated. Please try again in a minute.') || true
    # Update codebase
    git fetch origin deploy
    git reset --hard origin/deploy

    # Install dependencies based on lock file
    /usr/local/bin/composer install --no-interaction --prefer-dist --optimize-autoloader

    # Migrate database
    php artisan migrate --force

    # Note: If you're using queue workers, this is the place to restart them.
    # ...

    # Clear cache
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan view:cache
    php artisan route:clear

    # Reload PHP to update opcache
    echo "" | sudo -S service php7.4-fpm reload
    /usr/local/bin/composer dumpautoload -n
# Exit maintenance mode
php artisan up

echo "Application deployed!!"
