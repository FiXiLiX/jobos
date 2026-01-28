#!/bin/bash
set -e

echo "=== JobOS Application Startup ==="
echo ""

# Copy .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "📋 Copying .env file..."
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "✓ .env file created from .env.example"
    else
        echo "⚠ .env.example not found"
    fi
else
    echo "✓ .env file already exists"
fi

# Generate API key if not set
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force 2>/dev/null || echo "⚠ Failed to generate APP_KEY (may already be set)"
    echo "✓ APP_KEY generation attempted"
else
    echo "✓ APP_KEY already set"
fi

# # Wait for database to be ready (up to 30 seconds)
# echo "⏳ Waiting for database to be ready..."
# max_attempts=30
# attempt=1
# while ! php artisan tinker --execute="DB::connection()->getPdo()" > /dev/null 2>&1; do
#     if [ $attempt -ge $max_attempts ]; then
#         echo "⚠ Database not ready after $max_attempts attempts (will continue anyway)"
#         break
#     fi
#     echo "  Attempt $attempt/$max_attempts..."
#     sleep 1
#     ((attempt++))
# done
# if [ $attempt -le $max_attempts ]; then
#     echo "✓ Database is ready"
# fi

# # Run migrations (if database is available)
# echo "🗄️  Running database migrations..."
# php artisan migrate --force 2>/dev/null || echo "⚠ Migrations skipped (database may not be ready)"
# echo "✓ Migration step completed"

# # Initialize S3 bucket
# echo "⏳ Initializing S3 bucket..."
# php artisan s3:init-bucket 2>/dev/null || echo "⚠ S3 bucket initialization failed (will try later)"
# echo "✓ S3 bucket step completed"

# # Ensure budgets exist
# echo "💰 Ensuring budgets exist..."
# php artisan budgets:ensure 2>/dev/null || echo "⚠ Budget ensure failed"
# echo "✓ Budgets step completed"

# # Seed currency exchanges if needed
# echo "📊 Checking exchange rates..."
# if ! php artisan tinker --execute="exit(\\App\\Models\\CurrencyExchange::count() > 0 ? 0 : 1);" > /dev/null 2>&1; then
#     echo "Seeding exchange rates..."
#     php artisan db:seed --class=CurrencyExchangeSeeder 2>/dev/null || echo "⚠ Exchange rate seeding failed"
# else
#     echo "✓ Exchange rates already in database"
# fi

# # Fetch latest exchange rates (non-blocking)
# echo "💱 Fetching latest exchange rates..."
# php artisan exchange-rates:fetch 2>/dev/null || echo "⚠ Exchange rate fetch failed (using existing rates)"
# echo "✓ Exchange rates step completed"

# # Fetch currencies (non-blocking)
# echo "🌍 Fetching currencies list..."
# php artisan currencies:fetch 2>/dev/null || echo "⚠ Currencies fetch failed (will use existing data)"
# echo "✓ Currencies step completed"

echo ""
echo "=== Application Ready ==="
echo "🚀 Starting Laravel development server..."
echo ""

exec php artisan serve --host=0.0.0.0 --port=8000
