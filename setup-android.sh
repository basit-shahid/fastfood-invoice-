#!/bin/bash

# FastFood Invoice System - Android Setup Script for Termux
# This script automates the installation of dependencies for running this project on Android.

echo "--- FastFood Invoice System Android Setup ---"

# 1. Update Packages
echo "[1/6] Updating packages..."
pkg update -y && pkg upgrade -y

# 2. Install Dependencies
echo "[2/6] Installing PHP, Node.js, and Composer..."
pkg install -y php nodejs composer git

# 3. Setup Environment File
echo "[3/6] Configuring environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
    echo "Created .env file and generated app key."
else
    echo ".env file already exists."
fi

# Ensure SQLite database exists
mkdir -p database
touch database/database.sqlite
sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/g' .env
sed -i 's/DB_DATABASE=laravel/DB_DATABASE=database\/database.sqlite/g' .env

# 4. Install PHP Dependencies
echo "[4/6] Installing PHP dependencies (Composer)..."
composer install --no-dev --optimize-autoloader

# 5. Install JS Dependencies & Build
echo "[5/6] Installing JS dependencies and building assets..."
npm install
npm run build

# 6. Run Migrations
echo "[6/6] Running database migrations..."
php artisan migrate --force

echo "-----------------------------------------------"
echo "Setup Complete!"
echo "To start the server, run:"
echo "php artisan serve --host=0.0.0.0 --port=8000"
echo "-----------------------------------------------"
