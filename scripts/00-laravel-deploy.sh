#!/usr/bin/env bash

echo "Running composer"
composer install --no-dev --working-dir=/var/www/html

echo "Clearing old cached config..."
php artisan config:clear
php artisan route:clear

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force