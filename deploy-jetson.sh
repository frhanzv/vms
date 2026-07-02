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
sudo chown -R www-data:www-data public/uploads
sudo chmod -R 775 public/uploads

echo "==> Clearing application cache..."
rm -rf writable/cache/*
touch writable/cache/index.html

echo "==> Restarting Apache (reloads PHP OPcache)..."
sudo systemctl restart apache2

echo "Done. Site: http://localhost/"
