#!/bin/bash
set -e

echo "🚀 Starting Civic Connect Laravel..."

# ============================================================
# 1. Ensure SQLite database exists
# ============================================================
# Use /data/ if volume is mounted, otherwise use /app/database/
if [ -d /data ]; then
    if [ ! -f /data/database.sqlite ]; then
        echo "📦 Creating SQLite database on persistent volume..."
        touch /data/database.sqlite
    fi
    ln -sf /data/database.sqlite /app/database/database.sqlite
else
    if [ ! -f /app/database/database.sqlite ]; then
        echo "📦 Creating SQLite database..."
        touch /app/database/database.sqlite
    fi
fi

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
# 6b. Seed database if empty (first deploy)
# ============================================================
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "🌱 Seeding database with demo data..."
    php artisan db:seed --force
fi

# ============================================================
# 7. Start the Laravel application
# ============================================================
PORT=${PORT:-8080}
echo "✅ Application ready! Starting server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT
