# 🐳 Docker Deployment Guide

## BRAC Microfinance API - Complete Docker Setup

[![Laravel](https://img.shields.io/badge/Laravel-10+-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Required-blue.svg)](https://docker.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange.svg)](https://mysql.com)
[![Redis](https://img.shields.io/badge/Redis-7.0-red.svg)](https://redis.io)

This guide provides step-by-step instructions to deploy the BRAC Microfinance Loan Management API using Docker. The containerized setup includes Laravel application, MySQL database, Redis cache, Nginx web server, and management interfaces.

---

## 📋 Table of Contents

- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [Detailed Setup](#detailed-setup)
- [Services Overview](#services-overview)
- [API Testing](#api-testing)
- [Management Interfaces](#management-interfaces)
- [Troubleshooting](#troubleshooting)
- [Development](#development)

---

## 🚀 Prerequisites

### System Requirements

- **Docker Desktop**: Version 20.10+ ([Install Docker](https://docs.docker.com/get-docker/))
- **Docker Compose**: Version 2.0+ (included with Docker Desktop)
- **Git**: For cloning the repository
- **Operating System**: Windows 10+, macOS 10.15+, or Linux

### Hardware Requirements

- **RAM**: Minimum 4GB, Recommended 8GB+
- **Storage**: 2GB free space
- **CPU**: 2+ cores recommended

### Verify Prerequisites

```bash
# Check Docker installation
docker --version
# Expected: Docker version 20.10.0 or higher

# Check Docker Compose
docker-compose --version
# Expected: Docker Compose version 2.0.0 or higher

# Check Git
git --version
# Expected: git version 2.30.0 or higher
```

---

## ⚡ Quick Start

### 1. Clone the Repository

```bash
# Clone the project
git clone https://github.com/imtiaz-mamun/brac-laravel-project.git

# Navigate to project directory
cd brac-laravel-project/rest-api
```

### 2. Deploy with Docker

```bash
# Build and start all services
docker-compose up -d --build

# Wait for services to initialize (2-3 minutes)
# Monitor the startup process
docker-compose logs -f app
```

### 3. Verify Deployment

```bash
# Check all services are running
docker-compose ps

# Test API endpoint
curl -H "Accept: application/json" http://localhost:8000/api/branches
```

### 4. Access the Application

- **API Base URL**: `http://localhost:8000`
- **Database Manager**: `http://localhost:8081` (phpMyAdmin)
- **Redis Manager**: `http://localhost:8082` (Redis Commander)

**🎉 Your API is now running!** Continue to [API Testing](#api-testing) section.

---

## 📝 Detailed Setup

### Step 1: Environment Preparation

```bash
# Create project directory
mkdir ~/microfinance-api && cd ~/microfinance-api

# Clone repository
git clone https://github.com/imtiaz-mamun/brac-laravel-project.git .

# Navigate to API directory
cd rest-api

# Verify project structure
ls -la
# You should see: docker-compose.yml, Dockerfile, .env.docker, etc.
```

### Step 2: Docker Environment Configuration

The project includes pre-configured Docker environment settings:

```bash
# View Docker configuration (optional)
cat docker-compose.yml

# View Laravel Docker environment (optional)
cat .env.docker
```

**Configuration Highlights:**

- **MySQL Database**: `microfinance_db`
- **Database User**: `microfinance_user`
- **Redis Cache**: Persistent storage enabled
- **Nginx**: Load balancer and reverse proxy

### Step 3: Build and Deploy Services

```bash
# Pull latest base images
docker-compose pull

# Build application container
docker-compose build --no-cache

# Start all services in detached mode
docker-compose up -d

# Alternative: Start with build in one command
docker-compose up -d --build
```

### Step 4: Initialize Database

The database is automatically initialized with:

- **Migration tables creation**
- **Sample data seeding**
- **35 branches** across different regions
- **200+ clients** with realistic data
- **320+ loans** with various statuses
- **1000+ repayment** records

```bash
# Monitor database initialization
docker-compose logs -f app

# Verify database setup (optional)
docker exec laravel_microfinance_mysql mysql -u microfinance_user -pmicrofinance_password -e "USE microfinance_db; SELECT COUNT(*) as branches FROM branches;"
```

### Step 5: Verify Deployment

```bash
# Check service status
docker-compose ps

# Expected output:
# NAME                               COMMAND                  SERVICE           CREATED          STATUS          PORTS
# laravel_microfinance_app           "/usr/local/bin/entr…"   app               2 minutes ago    Up 2 minutes    0.0.0.0:8000->8000/tcp
# laravel_microfinance_mysql         "docker-entrypoint.s…"   mysql             2 minutes ago    Up 2 minutes    0.0.0.0:3306->3306/tcp
# laravel_microfinance_nginx         "/docker-entrypoint.…"   nginx             2 minutes ago    Up 2 minutes    0.0.0.0:80->80/tcp, 0.0.0.0:443->443/tcp
# laravel_microfinance_phpmyadmin    "/docker-entrypoint.…"   phpmyadmin        2 minutes ago    Up 2 minutes    0.0.0.0:8081->80/tcp
# laravel_microfinance_redis         "docker-entrypoint.s…"   redis             2 minutes ago    Up 2 minutes    0.0.0.0:6379->6379/tcp
# laravel_microfinance_redis_commander "/usr/bin/dumb-init …"   redis-commander   2 minutes ago    Up 2 minutes    0.0.0.0:8082->8081/tcp

# Test API connectivity
curl -H "Accept: application/json" http://localhost:8000/api/branches | jq '.data | length'
# Expected output: 15 (first page of branches)

# Test specific endpoint
curl -H "Accept: application/json" http://localhost:8000/api/clients | jq '{total: .total, per_page: .per_page}'
# Expected output: {"total": 200, "per_page": 15}
```

---

## 🏗️ Services Overview

### Service Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     Nginx       │    │   Laravel App   │    │     MySQL       │
│  Load Balancer  │───▶│   PHP 8.2-FPM   │───▶│   Database      │
│   Port: 80/443  │    │   Port: 8000    │    │   Port: 3306    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │
                                ▼
                       ┌─────────────────┐
                       │     Redis       │
                       │     Cache       │
                       │   Port: 6379    │
                       └─────────────────┘
```

### Core Services

| Service         | Description                | Port    | URL                     |
| --------------- | -------------------------- | ------- | ----------------------- |
| **Laravel App** | Main API application       | 8000    | `http://localhost:8000` |
| **MySQL**       | Primary database           | 3306    | Internal only           |
| **Redis**       | Cache & session store      | 6379    | Internal only           |
| **Nginx**       | Web server & load balancer | 80, 443 | `http://localhost`      |

### Management Services

| Service             | Description      | Port | URL                     | Credentials                                   |
| ------------------- | ---------------- | ---- | ----------------------- | --------------------------------------------- |
| **phpMyAdmin**      | MySQL management | 8081 | `http://localhost:8081` | `microfinance_user` / `microfinance_password` |
| **Redis Commander** | Redis management | 8082 | `http://localhost:8082` | No authentication                             |

### Data Volumes

| Volume       | Purpose                | Persistence |
| ------------ | ---------------------- | ----------- |
| `mysql_data` | MySQL database files   | Persistent  |
| `redis_data` | Redis cache & sessions | Persistent  |

---

## 🧪 API Testing

### Authentication Setup

```bash
# Get authentication token
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email": "abdul.karim16@microcredit.com", "password": "password123"}' \
  http://localhost:8000/api/auth/get-token

# Save token for subsequent requests
export API_TOKEN="your-jwt-token-here"
```

### Test API Endpoints

```bash
# Test public endpoints
curl -H "Accept: application/json" http://localhost:8000/api/branches
curl -H "Accept: application/json" http://localhost:8000/api/clients
curl -H "Accept: application/json" http://localhost:8000/api/loans
curl -H "Accept: application/json" http://localhost:8000/api/repayments

# Test authenticated endpoints
curl -H "Authorization: Bearer $API_TOKEN" \
     -H "Accept: application/json" \
     http://localhost:8000/api/client/loans

curl -H "Authorization: Bearer $API_TOKEN" \
     -H "Accept: application/json" \
     http://localhost:8000/api/client/loan-repayment-history

# Test analytics endpoints
curl -H "Accept: application/json" http://localhost:8000/api/analytics/branch-performance
curl -H "Accept: application/json" http://localhost:8000/api/analytics/loan-portfolio
curl -H "Accept: application/json" http://localhost:8000/api/analytics/repayment-trends?months=12
```

### Sample Test Data

**Valid Test Credentials:**

```json
{
  "email": "abdul.karim16@microcredit.com",
  "password": "password123"
}
```

**Alternative Test Accounts:**

- `abdul.karim33@microcredit.com`
- `abdur.rahman18@microcredit.com`
- `abdur.rahman9@microcredit.com`
- `abu.bakkar23@microcredit.com`

All accounts use password: `password123`

### Postman Collection

Import the pre-configured Postman collection:

```bash
# Collection file location
./postman_collection.json

# Contains 50+ pre-configured requests with:
# - Authentication flows
# - CRUD operations for all entities
# - Advanced filtering and pagination
# - Analytics and reporting endpoints
```

---

## 🖥️ Management Interfaces

### phpMyAdmin (Database Management)

**URL**: `http://localhost:8081`

```bash
# Access credentials
Server: mysql
Username: microfinance_user
Password: microfinance_password
Database: microfinance_db
```

**Features:**

- Browse and edit database tables
- Execute SQL queries
- Import/export data
- View database statistics

### Redis Commander (Cache Management)

**URL**: `http://localhost:8082`

**Features:**

- View cached data
- Monitor Redis performance
- Execute Redis commands
- Manage keys and values

### Direct Database Access

```bash
# Connect to MySQL container
docker exec -it laravel_microfinance_mysql mysql -u microfinance_user -pmicrofinance_password microfinance_db

# Example queries
SELECT COUNT(*) FROM branches;
SELECT COUNT(*) FROM clients;
SELECT COUNT(*) FROM loans;
SELECT COUNT(*) FROM repayments;

# Connect to Redis container
docker exec -it laravel_microfinance_redis redis-cli

# Example Redis commands
KEYS *
INFO memory
DBSIZE
```

---

## 🔧 Troubleshooting

### Common Issues and Solutions

#### Services Not Starting

```bash
# Check Docker daemon
sudo systemctl status docker  # Linux
# or restart Docker Desktop on Mac/Windows

# Check port conflicts
netstat -tulpn | grep :8000  # Linux/Mac
# Kill processes using required ports if needed

# Clean and rebuild
docker-compose down --volumes
docker-compose up -d --build --force-recreate
```

#### Database Connection Issues

```bash
# Check MySQL container logs
docker-compose logs mysql

# Verify MySQL service is running
docker-compose ps mysql

# Test database connection
docker exec laravel_microfinance_app php artisan migrate:status
```

#### Application Errors

```bash
# Check application logs
docker-compose logs app

# Check Laravel logs
docker exec laravel_microfinance_app tail -f storage/logs/laravel.log

# Clear Laravel caches
docker exec laravel_microfinance_app php artisan config:clear
docker exec laravel_microfinance_app php artisan cache:clear
```

#### Performance Issues

```bash
# Monitor resource usage
docker stats

# Check available disk space
df -h

# Optimize Docker
docker system prune -a
```

### Health Checks

```bash
# Comprehensive health check script
#!/bin/bash
echo "=== Docker Services Health Check ==="

echo "1. Checking Docker Compose services..."
docker-compose ps

echo "2. Testing API endpoints..."
curl -f -H "Accept: application/json" http://localhost:8000/api/branches > /dev/null && echo "✅ API accessible" || echo "❌ API not accessible"

echo "3. Testing database connection..."
docker exec laravel_microfinance_app php artisan migrate:status > /dev/null && echo "✅ Database connected" || echo "❌ Database connection failed"

echo "4. Testing Redis connection..."
docker exec laravel_microfinance_redis redis-cli ping | grep -q PONG && echo "✅ Redis connected" || echo "❌ Redis connection failed"

echo "5. Checking management interfaces..."
curl -f http://localhost:8081 > /dev/null && echo "✅ phpMyAdmin accessible" || echo "❌ phpMyAdmin not accessible"
curl -f http://localhost:8082 > /dev/null && echo "✅ Redis Commander accessible" || echo "❌ Redis Commander not accessible"

echo "=== Health Check Complete ==="
```

### Log Analysis

```bash
# View all service logs
docker-compose logs

# Follow specific service logs
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f redis

# Search for errors
docker-compose logs | grep -i error
docker-compose logs | grep -i exception
```

---

## 🛠️ Development

### Local Development Setup

```bash
# Start services in development mode
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d

# Enable hot reloading (if configured)
docker-compose exec app php artisan serve --host=0.0.0.0

# Run tests
docker-compose exec app php artisan test
```

### Database Management

```bash
# Run fresh migrations
docker exec laravel_microfinance_app php artisan migrate:fresh --seed

# Create new migration
docker exec laravel_microfinance_app php artisan make:migration create_new_table

# Seed specific data
docker exec laravel_microfinance_app php artisan db:seed --class=BranchSeeder
```

### Code Updates

```bash
# Update application code
git pull origin main

# Rebuild application container
docker-compose build app

# Restart application service
docker-compose restart app

# Clear application caches
docker exec laravel_microfinance_app php artisan optimize:clear
```

### Environment Customization

```bash
# Create custom environment file
cp .env.docker .env.custom

# Edit configuration
vim .env.custom

# Use custom environment
docker-compose --env-file .env.custom up -d
```

---

## 📚 Additional Resources

### Documentation

- [API Documentation](./API_DOCUMENTATION.md)
- [Test Credentials](./test-credentials.md)
- [Postman Collection](./postman_collection.json)
- [Docker Setup Guide](./DOCKER_SETUP_GUIDE.md)

### Project Links

- **Repository**: [https://github.com/imtiaz-mamun/brac-laravel-project](https://github.com/imtiaz-mamun/brac-laravel-project)
- **Issues**: [GitHub Issues](https://github.com/imtiaz-mamun/brac-laravel-project/issues)
- **Documentation**: [Project Wiki](https://github.com/imtiaz-mamun/brac-laravel-project/wiki)

### Support

For technical support or questions:

1. Check the [troubleshooting section](#troubleshooting)
2. Search [existing issues](https://github.com/imtiaz-mamun/brac-laravel-project/issues)
3. Create a [new issue](https://github.com/imtiaz-mamun/brac-laravel-project/issues/new) with:
   - Steps to reproduce
   - Error messages
   - Environment details
   - Docker logs

---

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

**Happy Coding! 🚀**

_Built with ❤️ using Laravel, Docker, and modern DevOps practices._
