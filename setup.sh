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
