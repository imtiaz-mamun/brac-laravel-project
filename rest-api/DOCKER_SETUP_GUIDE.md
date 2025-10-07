# 🐳 Docker Setup Guide for BRAC Microfinance API

This guide will help you install Docker and run the complete microfinance API with Laravel, MySQL, and Redis.

## 📋 Prerequisites

### macOS (Current System)

1. **Install Docker Desktop for Mac**

   ```bash
   # Option 1: Download from official website
   # Visit: https://docs.docker.com/desktop/install/mac-install/
   # Download and install Docker Desktop for Mac

   # Option 2: Install via Homebrew (if you have Homebrew)
   brew install --cask docker
   ```

2. **Start Docker Desktop**
   - Open Docker Desktop from Applications
   - Wait for Docker to start (you'll see the whale icon in the menu bar)
   - Verify installation: `docker --version`

### Linux

```bash
# Ubuntu/Debian
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
newgrp docker

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### Windows

1. Download Docker Desktop for Windows from: https://docs.docker.com/desktop/install/windows-install/
2. Install and restart your system
3. Enable WSL 2 backend if prompted

## 🚀 Running with Docker

### Quick Start

1. **Install Docker** (see prerequisites above)

2. **Clone and Setup**

   ```bash
   cd /path/to/brac-laravel-project/rest-api

   # Use the automated Docker startup script
   ./start.sh
   ```

3. **Manual Setup** (if start.sh doesn't work)

   ```bash
   # Copy Docker environment configuration
   cp .env.docker .env

   # Build and start all services
   docker-compose up -d --build

   # Wait for services to initialize (about 2-3 minutes)
   docker-compose logs -f app
   ```

### 🔍 Verify Services

```bash
# Check all services are running
docker-compose ps

# Expected output:
# laravel_microfinance_app      Up      0.0.0.0:8000->8000/tcp
# laravel_microfinance_mysql    Up      0.0.0.0:3306->3306/tcp
# laravel_microfinance_redis    Up      0.0.0.0:6379->6379/tcp
# laravel_microfinance_nginx    Up      0.0.0.0:80->80/tcp, 0.0.0.0:443->443/tcp
# laravel_microfinance_phpmyadmin Up    0.0.0.0:8080->80/tcp
# laravel_microfinance_redis_commander Up 0.0.0.0:8081->8081/tcp
```

## 🌐 Access Points

Once Docker is running, you can access:

| Service                | URL                   | Credentials                                                    |
| ---------------------- | --------------------- | -------------------------------------------------------------- |
| **API Application**    | http://localhost:8000 | -                                                              |
| **phpMyAdmin**         | http://localhost:8080 | user: `microfinance_user`<br>password: `microfinance_password` |
| **Redis Commander**    | http://localhost:8081 | -                                                              |
| **Nginx (Production)** | http://localhost      | -                                                              |

## 🧪 Test Docker Setup

### 1. Test API Health

```bash
curl http://localhost:8000/api/branches
# Should return JSON with branch data
```

### 2. Test Authentication

```bash
curl -X POST http://localhost:8000/api/auth/get-token \
  -H "Content-Type: application/json" \
  -d '{"email":"razia.begum0@microcredit.com","password":"password123"}'

# Should return JWT token
```

### 3. Test Authenticated Endpoints

```bash
# Use the token from step 2
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  http://localhost:8000/api/client/loans
```

## 🔧 Docker Management

### Common Commands

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# Restart services
docker-compose restart

# View logs
docker-compose logs -f
docker-compose logs -f app  # Just Laravel app logs

# Scale application (multiple instances)
docker-compose up -d --scale app=3

# Rebuild containers (after code changes)
docker-compose up -d --build

# Execute Laravel commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear

# Access MySQL directly
docker-compose exec mysql mysql -u microfinance_user -p microfinance_db

# Access Redis CLI
docker-compose exec redis redis-cli
```

### Database Management

```bash
# Reset database with fresh data
docker-compose exec app php artisan migrate:fresh --seed

# Backup database
docker-compose exec mysql mysqldump -u microfinance_user -pmicrofinance_password microfinance_db > backup.sql

# Restore database
docker-compose exec -T mysql mysql -u microfinance_user -pmicrofinance_password microfinance_db < backup.sql
```

## 📊 Monitoring

### Performance Monitoring

```bash
# View resource usage
docker stats

# Monitor container health
docker-compose ps
docker inspect laravel_microfinance_app

# View detailed logs
docker-compose logs --tail=100 -f app
```

### Database Monitoring

- **phpMyAdmin**: http://localhost:8080
- **MySQL Workbench**: Connect to localhost:3306
- **Command Line**: `docker-compose exec mysql mysql -u root -p`

### Redis Monitoring

- **Redis Commander**: http://localhost:8081
- **Command Line**: `docker-compose exec redis redis-cli monitor`

## 🐛 Troubleshooting

### Common Issues

1. **Port Conflicts**

   ```bash
   # Check what's using port 8000
   lsof -i :8000

   # Kill process if needed
   kill -9 $(lsof -t -i:8000)

   # Or modify docker-compose.yml to use different ports
   ```

2. **Database Connection Issues**

   ```bash
   # Check MySQL container logs
   docker-compose logs mysql

   # Verify database is ready
   docker-compose exec mysql mysqladmin ping -h localhost
   ```

3. **Redis Connection Issues**

   ```bash
   # Check Redis container
   docker-compose logs redis

   # Test Redis connection
   docker-compose exec redis redis-cli ping
   ```

4. **Laravel Application Issues**

   ```bash
   # Check application logs
   docker-compose logs app

   # Clear Laravel caches
   docker-compose exec app php artisan config:clear
   docker-compose exec app php artisan cache:clear
   ```

### Reset Everything

```bash
# Stop and remove all containers, networks, volumes
docker-compose down -v --remove-orphans

# Remove Docker images (optional)
docker system prune -a

# Start fresh
docker-compose up -d --build
```

## 🚀 Production Deployment

### Environment Configuration

```bash
# Copy production environment
cp .env.docker .env

# Modify for production
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env

# Add production settings
echo "SEED_DATABASE=false" >> .env
```

### Security Hardening

```bash
# Use secrets for sensitive data
# Create Docker secrets or use environment-specific .env files

# Limit container resources
# Add resource limits to docker-compose.yml

# Use non-root users in containers
# Configure proper user permissions
```

### Scaling

```bash
# Scale application containers
docker-compose up -d --scale app=5

# Use load balancer (nginx already configured)
# Add health checks and monitoring

# Consider using Docker Swarm or Kubernetes for production
```

## 📚 Additional Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Docker Documentation**: https://docs.docker.com
- **MySQL 8.0 Reference**: https://dev.mysql.com/doc/refman/8.0/en/
- **Redis Documentation**: https://redis.io/documentation
- **JWT Auth Package**: https://github.com/PHP-Open-Source-Saver/jwt-auth

## 💡 Tips

1. **Development Workflow**

   - Use `docker-compose logs -f app` to watch Laravel logs
   - Code changes are automatically reflected (volume mounting)
   - Use phpMyAdmin for database inspection
   - Use Redis Commander for cache monitoring

2. **Performance**

   - Enable Redis caching for better performance
   - Use MySQL indexes for large datasets
   - Monitor container resources with `docker stats`

3. **Backup Strategy**
   - Regular database backups
   - Volume backups for persistent data
   - Configuration backups (.env files)

---

**Need Help?** Check the troubleshooting section or review the container logs for specific error messages.
