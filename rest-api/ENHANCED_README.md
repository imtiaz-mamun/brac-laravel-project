# 🏦 BRAC Microfinance API - Complete Enterprise Solution

A comprehensive REST API for managing microfinance operations with JWT authentication, Redis caching, Docker deployment, and production-ready features.

## ✨ Features

### 🔐 Authentication & Security

- **JWT Authentication** with token refresh and expiration
- **Client-specific API endpoints** for secure loan access
- **Password encryption** with bcrypt hashing
- **Request validation** and sanitization
- **CORS support** for cross-origin requests

### 🎯 Core Functionality

- **Branch Management** - 35+ branches across Bangladesh
- **Client Management** - 200+ clients with authentication
- **Loan Operations** - 320+ loans with various statuses
- **Repayment Tracking** - 1000+ repayment records
- **Analytics & Reporting** - Performance metrics and trends

### ⚡ Performance & Scalability

- **Redis Caching** for improved response times
- **Database Optimization** with eager loading and indexing
- **Pagination** for large datasets
- **Background Queues** for heavy operations

### 🐳 Docker Infrastructure

- **Multi-container setup** with Docker Compose
- **MySQL 8.0** for robust data storage
- **Redis 7** for caching and sessions
- **Nginx** for reverse proxy and load balancing
- **phpMyAdmin & Redis Commander** for monitoring

## 🚀 Quick Start

### Option 1: Docker Deployment (Recommended)

```bash
# Clone repository
git clone <repository-url>
cd rest-api

# Copy environment configuration
cp .env.docker .env

# Start all services
docker-compose up -d

# Access the application
# API: http://localhost:8000
# phpMyAdmin: http://localhost:8080 (root/root123)
# Redis Commander: http://localhost:8081
```

### Option 2: Local Development

```bash
# Install PHP dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Set up database
php artisan migrate:fresh --seed

# Install Redis (optional)
# Install Predis: composer require predis/predis

# Start development server
php artisan serve
```

## 📚 API Documentation

### 🔑 Authentication Flow

1. **Get JWT Token**

   ```bash
   curl -X POST http://localhost:8000/api/auth/get-token \
     -H "Content-Type: application/json" \
     -d '{"email":"rashida.begum0@microcredit.com","password":"password123"}'
   ```

2. **Use Token for API Calls**
   ```bash
   curl -H "Authorization: Bearer <your_token>" \
     http://localhost:8000/api/client/loans
   ```

### 🎯 Key Endpoints

| Category       | Endpoint                                 | Description                      |
| -------------- | ---------------------------------------- | -------------------------------- |
| **Auth**       | `POST /api/auth/get-token`               | Get JWT access token             |
| **Auth**       | `POST /api/auth/register`                | Register new client              |
| **Client**     | `GET /api/client/loans`                  | Get authenticated client's loans |
| **Client**     | `GET /api/client/loan-repayment-history` | Get repayment history            |
| **Management** | `GET /api/clients`                       | List all clients (admin)         |
| **Management** | `GET /api/loans`                         | List all loans (admin)           |
| **Management** | `GET /api/repayments`                    | List all repayments (admin)      |

### 📋 Postman Collection

Import `postman_collection.json` for comprehensive API testing with:

- Pre-configured authentication flow
- Sample requests for all endpoints
- Environment variables for easy switching
- Error handling examples

## 🗄️ Database Schema

### Enhanced Data Model

```
Branches (35) → Clients (200) → Loans (320+) → Repayments (1000+)
    ↓              ↓               ↓              ↓
- Multi-region  - Auth enabled   - Various      - Multiple
- District-wise - Contact info   - statuses     - payment modes
- Performance   - Gender data    - Interest     - Reference
  metrics       - Registration   - rates       - tracking
```

### Sample Data Volume

- **35 Branches** across Central, Eastern, North-Eastern, Northern, South-Western, Southern regions
- **200 Clients** with 40 having authentication credentials
- **320+ Loans** with ACTIVE, CLOSED, and DEFAULTED statuses
- **1000+ Repayments** with CASH, BANK, and MOBILE payment modes

## 🧪 Testing & Validation

### Test Credentials

```json
{
  "email": "rashida.begum0@microcredit.com",
  "password": "password123"
}
```

### Testing Flow

1. **Import Postman Collection** (`postman_collection.json`)
2. **Run Authentication** to get JWT token
3. **Test Client APIs** with authenticated requests
4. **Validate CRUD Operations** for all entities
5. **Check Error Handling** with invalid data

### Sample API Calls

```bash
# Get client's loans (cached response)
curl -H "Authorization: Bearer <token>" \
  http://localhost:8000/api/client/loans

# Get paginated repayment history
curl -H "Authorization: Bearer <token>" \
  "http://localhost:8000/api/client/loan-repayment-history?page=1&per_page=10"

# List clients with filters
curl -H "Authorization: Bearer <token>" \
  "http://localhost:8000/api/clients?branch_id=1&gender=FEMALE"
```

## 🐳 Docker Services

### Service Architecture

```yaml
app: # Laravel API (PHP 8.2, Nginx)
mysql: # MySQL 8.0 (persistent storage)
redis: # Redis 7 (caching & sessions)
phpmyadmin: # Database management
redis-commander: # Redis monitoring
```

### Container Management

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f app

# Scale application
docker-compose up -d --scale app=2

# Stop services
docker-compose down
```

### Production Deployment

```bash
# Production environment
cp .env.docker .env
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env

# Deploy with scaling
docker-compose up -d --scale app=3

# Monitor performance
docker stats
```

## ⚙️ Configuration

### Environment Variables

```bash
# Application
APP_NAME="BRAC Microfinance API"
APP_ENV=local
APP_DEBUG=true

# Database (Docker)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=microfinance

# Redis (Docker)
REDIS_HOST=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# JWT Configuration
JWT_SECRET=your-secret-key
JWT_TTL=60

# Microfinance Settings
SEED_DATABASE=true
MAX_LOAN_AMOUNT=500000
DEFAULT_INTEREST_RATE=12.5
```

## 🔧 Development Commands

### Laravel Commands

```bash
# Database operations
php artisan migrate:fresh --seed
php artisan cache:clear
php artisan queue:work

# Generate JWT secret
php artisan jwt:secret

# Run tests
php artisan test
```

### Docker Commands

```bash
# Execute Laravel commands in container
docker-compose exec app php artisan migrate:fresh --seed
docker-compose exec app php artisan cache:clear
docker-compose exec app composer install

# Database backup
docker-compose exec mysql mysqldump -u root -p microfinance > backup.sql

# Redis CLI access
docker-compose exec redis redis-cli
```

## 📊 Performance Features

### Redis Caching Strategy

- **Client Loans**: 15-minute cache (per client)
- **Branch Data**: 1-hour cache (global)
- **Authentication**: Session management
- **Analytics**: 30-minute computed cache

### Database Optimizations

- **Eager Loading**: Prevents N+1 queries
- **Indexing**: Foreign keys and search columns
- **Pagination**: Configurable page sizes
- **Query Optimization**: Selective loading

## 🛡️ Security Implementation

### Authentication Security

- **JWT Tokens** with configurable expiration
- **Password Hashing** with bcrypt (cost: 12)
- **Token Refresh** mechanism
- **Logout Invalidation** with blacklisting

### API Security

- **Request Validation** with Laravel Form Requests
- **CORS Headers** for cross-origin security
- **Rate Limiting** on authentication endpoints
- **Input Sanitization** and XSS protection

## 📈 Monitoring & Analytics

### Application Monitoring

- **Laravel Logs**: `storage/logs/laravel.log`
- **Docker Logs**: `docker-compose logs -f`
- **Performance Metrics**: Response times and cache hits

### Database Monitoring

- **phpMyAdmin**: Real-time query monitoring
- **MySQL Performance**: Slow query logging
- **Connection Pooling**: Optimized database connections

### Cache Monitoring

- **Redis Commander**: Key inspection and TTL management
- **Cache Analytics**: Hit/miss ratios
- **Memory Usage**: Redis memory optimization

## 🔍 Troubleshooting

### Common Issues

1. **Port Conflicts**: Change ports in `docker-compose.yml`
2. **Database Connection**: Verify MySQL container health
3. **Cache Issues**: Clear Redis keys or restart container
4. **JWT Errors**: Regenerate JWT secret key

### Debug Commands

```bash
# Check service status
docker-compose ps

# View application logs
docker-compose logs app

# Test database connection
docker-compose exec app php artisan migrate:status

# Verify Redis connection
docker-compose exec app php artisan tinker
>>> Cache::put('test', 'value', 60)
>>> Cache::get('test')
```

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## 📞 Support

For issues and questions:

- Create an issue on GitHub
- Check the `COMPREHENSIVE_API_DOCS.md` for detailed documentation
- Review the Postman collection for API examples

---

**Built with Laravel 10.x, PHP 8.2, MySQL 8.0, Redis 7, and Docker** 🚀
