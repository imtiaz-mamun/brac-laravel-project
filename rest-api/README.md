# 🏦 BRAC Microfinance Loan Management API

[![Laravel](https://img.shields.io/badge/Laravel-10+-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://docker.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange.svg)](https://mysql.com)
[![Redis](https://img.shields.io/badge/Redis-7.0-red.svg)](https://redis.io)
[![API](https://img.shields.io/badge/API-RESTful-green.svg)](https://restfulapi.net/)

A comprehensive, production-ready RESTful API backend built with Laravel for managing microfinance loan operations. This system demonstrates enterprise-level best practices for scalability, caching, security, and Docker deployment in financial applications.

## 🚀 Quick Start

### Docker Deployment (Recommended)

```bash
# Clone the repository
git clone https://github.com/imtiaz-mamun/brac-laravel-project.git
cd brac-laravel-project/rest-api

# Deploy with Docker
docker-compose up -d --build

# Test the API
curl -H "Accept: application/json" http://localhost:8000/api/branches
```

**📖 [Complete Docker Deployment Guide](DOCKER_DEPLOYMENT.md)**

### Local Development

```bash
# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations and seeders
php artisan migrate --seed

# Start development server
php artisan serve
```

## ✨ Features

- **🔐 JWT Authentication** with client-specific endpoints
- **� Email Notifications** for repayment confirmations with professional templates
- **�📊 Advanced Analytics** with loan performance metrics
- **🐳 Docker Environment** with MySQL, Redis, Nginx, and management tools
- **🔄 Complete CRUD Operations** for all entities
- **📈 Real-time Analytics** with caching optimization
- **🛡️ Enterprise Security** with validation and error handling
- **📚 Comprehensive Documentation** with Postman collection
- **🎯 Production-Ready** with proper logging and monitoring

## Database Schema

### Branches

- `id` - Primary key
- `name` - Branch name (unique)
- `district` - District location
- `region` - Regional classification
- `timestamps` - Created/updated timestamps

### Clients

- `id` - Primary key
- `name` - Client full name
- `gender` - ENUM: MALE, FEMALE, OTHER
- `branch_id` - Foreign key to branches
- `registration_date` - Client registration date
- `timestamps` - Created/updated timestamps

### Loans

- `id` - Primary key
- `client_id` - Foreign key to clients
- `branch_id` - Foreign key to branches
- `loan_amount` - Decimal(15,2) loan principal
- `interest_rate` - Decimal(5,2) interest percentage
- `issue_date` - Loan issue date
- `tenure_months` - Loan duration in months
- `status` - ENUM: ACTIVE, CLOSED, DEFAULTED
- `timestamps` - Created/updated timestamps

### Repayments

- `id` - Primary key
- `loan_id` - Foreign key to loans
- `payment_date` - Date of payment
- `amount_paid` - Decimal(15,2) payment amount
- `payment_mode` - ENUM: CASH, BANK, MOBILE
- `reference_no` - Optional transaction reference
- `timestamps` - Created/updated timestamps

## API Endpoints

### Branches

```http
GET    /api/branches                    # List all branches with filtering
POST   /api/branches                    # Create new branch
GET    /api/branches/{id}               # Get specific branch
PUT    /api/branches/{id}               # Update branch
DELETE /api/branches/{id}               # Delete branch
GET    /api/analytics/branch-performance # Branch performance analytics
```

### Clients

```http
GET    /api/clients                     # List all clients with filtering
POST   /api/clients                     # Create new client
GET    /api/clients/{id}                # Get specific client
PUT    /api/clients/{id}                # Update client
DELETE /api/clients/{id}                # Delete client
GET    /api/branches/{id}/clients       # Get clients by branch
GET    /api/analytics/client-statistics # Client statistics
```

### Loans

```http
GET    /api/loans                       # List all loans with filtering
POST   /api/loans                       # Create new loan
GET    /api/loans/{id}                  # Get specific loan
PUT    /api/loans/{id}                  # Update loan
DELETE /api/loans/{id}                  # Delete loan
PATCH  /api/loans/{id}/status           # Update loan status
GET    /api/clients/{id}/loans          # Get loans by client
GET    /api/branches/{id}/loans         # Get loans by branch
GET    /api/analytics/loan-summary      # Loan summary analytics
```

### Repayments

```http
GET    /api/repayments                  # List all repayments with filtering
POST   /api/repayments                  # Create new repayment
GET    /api/repayments/{id}             # Get specific repayment
PUT    /api/repayments/{id}             # Update repayment
DELETE /api/repayments/{id}             # Delete repayment
GET    /api/loans/{id}/repayments       # Get repayments by loan
```

## Quick Start

### Prerequisites

- PHP 8.1 or higher
- Composer
- SQLite extension for PHP

### Installation

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd rest-api
   ```

2. **Install dependencies**

   ```bash
   composer install
   ```

3. **Set up environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Create database and run migrations**

   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

5. **Seed the database with test data**

   ```bash
   php artisan db:seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000`

## API Usage Examples

### Create a New Branch

```http
POST /api/branches
Content-Type: application/json

{
    "name": "Dhaka Main Branch",
    "district": "Dhaka",
    "region": "Central"
}
```

### Create a New Client

```http
POST /api/clients
Content-Type: application/json

{
    "name": "Rahman Ahmed",
    "gender": "MALE",
    "branch_id": 1,
    "registration_date": "2024-01-15"
}
```

### Issue a New Loan

```http
POST /api/loans
Content-Type: application/json

{
    "client_id": 1,
    "branch_id": 1,
    "loan_amount": 50000.00,
    "interest_rate": 12.5,
    "issue_date": "2024-01-20",
    "tenure_months": 24
}
```

### Record a Repayment

```http
POST /api/repayments
Content-Type: application/json

{
    "loan_id": 1,
    "payment_date": "2024-02-20",
    "amount_paid": 2500.00,
    "payment_mode": "BANK",
    "reference_no": "TXN-12345678"
}
```

## Filtering and Pagination

All listing endpoints support filtering and pagination:

### Branch Filtering

```http
GET /api/branches?region=Central&district=Dhaka&per_page=10
```

### Client Filtering

```http
GET /api/clients?branch_id=1&gender=FEMALE&from_date=2024-01-01&to_date=2024-12-31
```

### Loan Filtering

```http
GET /api/loans?status=ACTIVE&branch_id=1&from_date=2024-01-01
```

### Repayment Filtering

```http
GET /api/repayments?loan_id=1&payment_mode=BANK&from_date=2024-01-01
```

## Analytics Endpoints

### Loan Summary

```http
GET /api/analytics/loan-summary
```

Returns total loans, disbursed amounts, status breakdown, and averages.

### Branch Performance

```http
GET /api/analytics/branch-performance
```

Returns performance metrics for each branch including client counts and loan statistics.

### Client Statistics

```http
GET /api/analytics/client-statistics
```

Returns client demographics and distribution statistics.

## Data Validation

The API includes comprehensive validation:

- **Branch names** must be unique
- **Loan amounts** must be between 1,000 and 1,000,000
- **Interest rates** must be between 0% and 50%
- **Payment amounts** cannot exceed reasonable limits
- **Dates** are validated for format and logical constraints

## Business Logic

- **Automatic loan closure** when full repayment is made
- **Repayment validation** against remaining loan balance
- **Cascade restrictions** prevent deletion of records with dependencies
- **Status management** for loans (ACTIVE → CLOSED/DEFAULTED)

## Best Practices Implemented

- **RESTful API design** with proper HTTP methods and status codes
- **Eloquent relationships** for efficient database queries
- **Request validation** with Laravel's validation system
- **Database indexing** for optimal query performance
- **Proper error handling** with meaningful error messages
- **Consistent response format** across all endpoints

## 📚 Documentation

| Document               | Description                            | Link                                                  |
| ---------------------- | -------------------------------------- | ----------------------------------------------------- |
| **Docker Deployment**  | Complete containerized setup guide     | [📖 DOCKER_DEPLOYMENT.md](DOCKER_DEPLOYMENT.md)       |
| **API Reference**      | Detailed endpoint documentation        | [📋 API_DOCUMENTATION.md](API_DOCUMENTATION.md)       |
| **Test Credentials**   | Authentication credentials for testing | [🔐 test-credentials.md](test-credentials.md)         |
| **Postman Collection** | Ready-to-use API testing collection    | [⚡ postman_collection.json](postman_collection.json) |
| **Setup Instructions** | Local development setup                | [🛠️ setup-local.sh](setup-local.sh)                   |

## 🔧 Development Tools

- **Laravel Artisan** commands for database management
- **Database seeders** for realistic test data
- **Migration system** for database version control
- **Eloquent ORM** for database interactions

## 🌐 Deployment Options

### 🐳 Docker (Production Ready)

```bash
docker-compose up -d --build
```

**Includes**: Laravel + MySQL + Redis + Nginx + Management Tools

### 💻 Local Development

```bash
./setup-local.sh
```

**Includes**: Laravel + SQLite + File Cache

### ☁️ Cloud Deployment

- Compatible with AWS, Digital Ocean, Google Cloud
- Containerized architecture for easy scaling
- Environment-based configuration

## 🧪 API Testing

### Quick Test Commands

```bash
# Test API health
curl -H "Accept: application/json" http://localhost:8000/api/branches

# Authenticate and get token
curl -X POST -H "Content-Type: application/json" \
  -d '{"email":"abdul.karim16@microcredit.com","password":"password123"}' \
  http://localhost:8000/api/auth/get-token

# Test analytics
curl -H "Accept: application/json" http://localhost:8000/api/analytics/branch-performance
```

### Postman Collection

Import `postman_collection.json` for comprehensive API testing with:

- ✅ 50+ pre-configured requests
- ✅ Automatic JWT token management
- ✅ Environment variables setup
- ✅ Test data and examples

## 🗄️ Database Management

```bash
# Reset database (Docker)
docker exec laravel_microfinance_app php artisan migrate:fresh --seed

# Reset database (Local)
php artisan migrate:fresh --seed

# Backup database (Docker MySQL)
docker exec laravel_microfinance_mysql mysqldump -u microfinance_user -pmicrofinance_password microfinance_db > backup.sql
```

## 📊 Project Statistics

- **📁 Total Files**: 100+ organized files
- **🏢 Sample Branches**: 35 across Bangladesh
- **👥 Sample Clients**: 200 with realistic profiles
- **💰 Sample Loans**: 320+ with various statuses
- **💳 Sample Repayments**: 1000+ transaction records
- **📡 API Endpoints**: 25+ RESTful endpoints
- **🧪 Postman Requests**: 50+ pre-configured tests

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📞 Support

- **📖 Documentation**: Check the guides above
- **🐛 Issues**: [GitHub Issues](https://github.com/imtiaz-mamun/brac-laravel-project/issues)
- **💬 Discussions**: [GitHub Discussions](https://github.com/imtiaz-mamun/brac-laravel-project/discussions)

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

## 🎯 Built With

- **[Laravel](https://laravel.com/)** - PHP Web Framework
- **[MySQL](https://www.mysql.com/)** - Primary Database
- **[Redis](https://redis.io/)** - Caching & Session Store
- **[Docker](https://www.docker.com/)** - Containerization
- **[Nginx](https://www.nginx.com/)** - Web Server
- **[JWT](https://jwt.io/)** - Authentication

---

**⭐ Star this repository if you find it helpful!**

_Built with ❤️ for the microfinance community_

This will drop all tables, recreate them, and populate with fresh test data.

## Contributing

1. Follow Laravel coding standards
2. Add appropriate validation to new endpoints
3. Update this README when adding new features
4. Ensure database migrations are reversible
5. Add proper error handling and logging

## License

This project is open-sourced software licensed under the MIT license.
