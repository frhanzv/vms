#!/usr/bin/env bash
# =============================================================================
# deploy-jetson.sh — run after every git pull on Jetson
# Usage: bash deploy-jetson.sh
# =============================================================================
set -e

VMS_DIR="/var/www/html/vms"
cd "$VMS_DIR"

echo "==> Pulling latest code..."
git pull origin main

echo "==> Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "==> Running migrations..."
php spark migrate

echo "==> Fixing writable permissions (Apache + CLI)..."
sudo chown -R www-data:www-data writable
sudo chmod -R 775 writable

echo "==> Reloading Apache..."
sudo systemctl reload apache2

echo "Done. Site: http://localhost/"
