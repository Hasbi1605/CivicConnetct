#!/bin/bash
set -e

echo "🚀 Starting Civic Connect Laravel..."

# ============================================================
# 1. Ensure SQLite database exists on persistent volume
# ============================================================
if [ ! -f /data/database.sqlite ]; then
    echo "📦 Creating SQLite database..."
    touch /data/database.sqlite
fi

# Symlink so Laravel finds it at the expected path too
ln -sf /data/database.sqlite /app/database/database.sqlite

# ============================================================
# 2. Ensure storage directories exist and are writable
# ============================================================
echo "📁 Setting up storage directories..."
mkdir -p /app/storage/framework/{sessions,views,cache/data}
mkdir -p /app/storage/logs
mkdir -p /app/bootstrap/cache

chmod -R 777 /app/storage /app/bootstrap/cache

# ============================================================
# 3. Create storage link if it doesn't exist
# ============================================================
if [ ! -L /app/public/storage ]; then
    php artisan storage:link --force 2>/dev/null || true
fi

# ============================================================
# 4. Generate app key if not set
# ============================================================
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# ============================================================
# 5. Cache configuration for production
# ============================================================
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ============================================================
# 6. Run migrations
# ============================================================
echo "🗄️ Running database migrations..."
php artisan migrate --force

# ============================================================
# 7. Start the Laravel application
# ============================================================
echo "✅ Application ready! Starting server on port 8080..."
exec php artisan serve --host=0.0.0.0 --port=8080
