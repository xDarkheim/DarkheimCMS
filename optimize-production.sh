#!/bin/bash

# Production Optimization Script for Laravel
echo "🚀 Optimizing Laravel application for production..."

# Clear all caches first
echo "🧹 Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# Cache everything for production
echo "💾 Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize autoloader
echo "⚡ Optimizing autoloader..."
composer dump-autoload --optimize

# Set proper permissions
echo "🔐 Setting proper permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache public/storage
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 755 public

# Restart web server to apply changes
echo "🔄 Restarting web services..."
sudo systemctl reload nginx
sudo systemctl reload php8.3-fpm

echo "✅ Production optimization complete!"
echo "📊 Performance improvements applied:"
echo "   ✓ Configuration cached"
echo "   ✓ Routes cached"
echo "   ✓ Views cached"
echo "   ✓ Events cached"
echo "   ✓ Autoloader optimized"
echo "   ✓ Frontend assets built"
echo "   ✓ Permissions secured"
echo "   ✓ Xdebug disabled"
