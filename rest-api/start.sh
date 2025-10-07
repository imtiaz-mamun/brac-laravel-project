#!/bin/bash

# BRAC Microfinance API - Docker Startup Script
# This script sets up and starts the complete microfinance API environment

echo "🏦 BRAC Microfinance API - Docker Setup"
echo "========================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker is not installed.${NC}"
    echo -e "${YELLOW}📥 To install Docker Desktop on macOS:${NC}"
    echo "   1. Visit: https://docs.docker.com/desktop/install/mac-install/"
    echo "   2. Download Docker Desktop for Mac"
    echo "   3. Install and start Docker Desktop"
    echo "   4. Run this script again"
    echo ""
    echo -e "${BLUE}🔄 Alternative: Use local development setup${NC}"
    echo "   ./setup-local.sh"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}❌ Docker Compose is not installed.${NC}"
    echo -e "${YELLOW}💡 Docker Compose comes with Docker Desktop${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Docker and Docker Compose are installed${NC}"

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    echo -e "${YELLOW}📋 Copying environment configuration...${NC}"
    cp .env.docker .env
    echo -e "${GREEN}✅ Environment file created${NC}"
else
    echo -e "${GREEN}✅ Environment file already exists${NC}"
fi

# Stop any running containers
echo -e "${YELLOW}🛑 Stopping any existing containers...${NC}"
docker-compose down

# Pull latest images
echo -e "${YELLOW}📥 Pulling Docker images...${NC}"
docker-compose pull

# Build and start services
echo -e "${YELLOW}🔨 Building and starting services...${NC}"
docker-compose up -d --build

# Wait for services to be ready
echo -e "${YELLOW}⏳ Waiting for services to start...${NC}"
sleep 30

# Check service health
echo -e "${YELLOW}🔍 Checking service health...${NC}"

# Check MySQL
if docker-compose exec -T mysql mysqladmin ping -h localhost -u root -proot123 &> /dev/null; then
    echo -e "${GREEN}✅ MySQL is running${NC}"
else
    echo -e "${RED}❌ MySQL is not responding${NC}"
fi

# Check Redis
if docker-compose exec -T redis redis-cli ping &> /dev/null; then
    echo -e "${GREEN}✅ Redis is running${NC}"
else
    echo -e "${RED}❌ Redis is not responding${NC}"
fi

# Check Laravel app
if curl -s http://localhost:8000 &> /dev/null; then
    echo -e "${GREEN}✅ Laravel application is running${NC}"
else
    echo -e "${YELLOW}⚠️  Laravel application is starting (may take a few more seconds)${NC}"
fi

echo ""
echo "🚀 Services Started Successfully!"
echo "=================================="
echo ""
echo -e "${GREEN}📊 Access URLs:${NC}"
echo "   • API Application: http://localhost:8000"
echo "   • phpMyAdmin:      http://localhost:8080 (root/root123)"
echo "   • Redis Commander: http://localhost:8081"
echo ""
echo -e "${GREEN}🧪 Test Authentication:${NC}"
echo "   curl -X POST http://localhost:8000/api/auth/get-token \\"
echo "     -H \"Content-Type: application/json\" \\"
echo "     -d '{\"email\":\"rashida.begum0@microcredit.com\",\"password\":\"password123\"}'"
echo ""
echo -e "${GREEN}📚 Documentation:${NC}"
echo "   • API Docs: COMPREHENSIVE_API_DOCS.md"
echo "   • Setup Guide: ENHANCED_README.md"
echo "   • Postman Collection: postman_collection.json"
echo ""
echo -e "${GREEN}🔧 Useful Commands:${NC}"
echo "   • View logs:     docker-compose logs -f"
echo "   • Stop services: docker-compose down"
echo "   • Restart:       docker-compose restart"
echo ""
echo -e "${YELLOW}💡 Note: First startup may take a few minutes to initialize the database.${NC}"