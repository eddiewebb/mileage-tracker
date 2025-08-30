#!/bin/bash

echo "🚗 Setting up Mobile Mileage Tracker..."
echo "======================================"

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer first."
    exit 1
fi

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.1 or higher."
    exit 1
fi

echo "✅ Prerequisites check passed"

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    echo "🔧 Creating environment file..."
    cat > .env << 'EOF'
APP_NAME="Mileage Tracker"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=database.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Google Maps API for location services
GOOGLE_MAPS_API_KEY=your_api_key_here

EOF
    
    # Generate application key
    echo "🔑 Generating application key..."
    php artisan key:generate
else
    echo "✅ Environment file already exists"
fi

# Create SQLite database if it doesn't exist
if [ ! -f database/database.sqlite ]; then
    echo "💾 Creating SQLite database..."
    touch database/database.sqlite
else
    echo "✅ Database file already exists"
fi

# Run database migrations
echo "🗃️  Running database migrations..."
php artisan migrate --force

# Create storage directories
echo "📁 Setting up storage directories..."
php artisan storage:link

echo ""
echo "🎉 Setup complete!"
echo ""
echo "Next steps:"
echo "1. Add your Google Maps API key to .env file:"
echo "   GOOGLE_MAPS_API_KEY=your_api_key_here"
echo ""
echo "2. Start the development server:"
echo "   php artisan serve"
echo ""
echo "3. Visit http://localhost:8000 to access the application"
echo ""
echo "4. Run tests to verify everything works:"
echo "   php artisan test"
echo ""
echo "📚 See README.md for detailed setup instructions and Google Maps API configuration."
