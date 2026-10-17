#!/bin/sh
set -eu
cd /var/www/html

# Render Hobby needs no interactive shell: apply additive schema changes on boot.
mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
php artisan migrate --force --no-interaction
if [ ! -e public/storage ] && [ ! -L public/storage ]; then
    php artisan storage:link --no-interaction
fi
chown -R www-data:www-data storage bootstrap/cache
exec docker-php-entrypoint "$@"
