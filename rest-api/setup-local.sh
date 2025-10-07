#!/bin/bash

# BRAC Microfinance API - Local Development Setup
# This script sets up the project for local development without Docker

echo "🏦 BRAC Microfinance API - Local Development Setup"
echo "================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}Setting up local development environment...${NC}"

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo -e "${RED}❌ Composer is not installed. Please install Composer first.${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Composer is available${NC}"

# Install/update dependencies
echo -e "${YELLOW}📦 Installing PHP dependencies...${NC}"
composer install

# Copy environment file for local development
echo -e "${YELLOW}📋 Setting up local environment...${NC}"
if [ ! -f .env ]; then
    cp .env.example .env
    echo -e "${GREEN}✅ Environment file created from example${NC}"
else
    echo -e "${YELLOW}⚠️  .env file already exists, keeping current configuration${NC}"
fi

# Configure for SQLite (local development)
echo -e "${YELLOW}🔧 Configuring database for local development...${NC}"
sed -i '' 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
sed -i '' 's/DB_HOST=mysql/#DB_HOST=mysql/' .env
sed -i '' 's/DB_PORT=3306/#DB_PORT=3306/' .env
sed -i '' 's/DB_DATABASE=microfinance_db/#DB_DATABASE=microfinance_db/' .env
sed -i '' 's/DB_USERNAME=microfinance_user/#DB_USERNAME=microfinance_user/' .env
sed -i '' 's/DB_PASSWORD=microfinance_password/#DB_PASSWORD=microfinance_password/' .env

# Add SQLite database path
echo "DB_DATABASE=database/database.sqlite" >> .env

# Configure cache and session for file-based (local development)
sed -i '' 's/CACHE_DRIVER=redis/CACHE_DRIVER=file/' .env
sed -i '' 's/SESSION_DRIVER=redis/SESSION_DRIVER=file/' .env
sed -i '' 's/QUEUE_CONNECTION=redis/QUEUE_CONNECTION=sync/' .env
sed -i '' 's/REDIS_HOST=redis/REDIS_HOST=127.0.0.1/' .env

# Generate application key
echo -e "${YELLOW}🔑 Generating application key...${NC}"
php artisan key:generate

# Generate JWT secret
echo -e "${YELLOW}🔐 Generating JWT secret...${NC}"
php artisan jwt:secret --force

# Create SQLite database
echo -e "${YELLOW}🗄️  Creating SQLite database...${NC}"
touch database/database.sqlite

# Clear caches
echo -e "${YELLOW}🧹 Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations and seed data
echo -e "${YELLOW}📊 Setting up database schema and sample data...${NC}"
php artisan migrate:fresh --seed

echo ""
echo -e "${GREEN}🎉 Local development setup complete!${NC}"
echo "=================================="
echo ""
echo -e "${GREEN}📱 Quick Start:${NC}"
echo "   php artisan serve"
echo ""
echo -e "${GREEN}🧪 Test Authentication:${NC}"
echo "   curl -X POST http://127.0.0.1:8000/api/auth/get-token \\"
echo "     -H \"Content-Type: application/json\" \\"
echo "     -d '{\"email\":\"razia.begum0@microcredit.com\",\"password\":\"password123\"}'"
echo ""
echo -e "${GREEN}📚 Available Resources:${NC}"
echo "   • API Documentation: COMPREHENSIVE_API_DOCS.md"
echo "   • Postman Collection: postman_collection.json"
echo "   • Enhanced README: ENHANCED_README.md"
echo ""
echo -e "${YELLOW}💡 For Docker setup: Install Docker Desktop and run ./start.sh${NC}"
echo -e "${YELLOW}💡 Current setup uses SQLite for simplicity${NC}"