# BRAC Microfinance API Documentation

This is a comprehensive REST API for managing microfinance operations including JWT authentication, client management, branches, loans, and repayments with Redis caching and Docker deployment.

## 🚀 Quick Start with Docker

### Prerequisites

- Docker and Docker Compose installed
- Git

### Setup Instructions

1. **Clone and Navigate**

   ```bash
   git clone <repository-url>
   cd rest-api
   ```

2. **Environment Configuration**

   ```bash
   cp .env.docker .env
   ```

3. **Start Services**

   ```bash
   docker-compose up -d
   ```

4. **Access Services**
   - **API**: http://localhost:8000
   - **phpMyAdmin**: http://localhost:8080 (root/root123)
   - **Redis Commander**: http://localhost:8081

### Docker Services

- **Laravel App**: Main API application with PHP 8.2
- **MySQL 8.0**: Primary database with persistent storage
- **Redis 7**: Caching and session storage
- **Nginx**: Reverse proxy and static file serving
- **phpMyAdmin**: Database management interface
- **Redis Commander**: Redis monitoring and management

## 🔐 Authentication System

### JWT Authentication Flow

#### 1. Get API Token

```http
POST /api/auth/get-token
Content-Type: application/json

{
    "email": "rashida.begum0@microcredit.com",
    "password": "password123"
}
```

**Response:**

```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "Rashida Begum",
    "email": "rashida.begum0@microcredit.com",
    "branch_id": 1
  }
}
```

#### 2. Use Token in Requests

```http
Authorization: Bearer <your_jwt_token>
```

#### 3. Other Authentication Endpoints

- `POST /api/auth/register` - Register new client
- `POST /api/auth/refresh` - Refresh token
- `GET /api/auth/me` - Get user profile
- `POST /api/auth/logout` - Logout and invalidate token

## 🎯 Client API Endpoints (Authenticated)

### Get Client's Loans

```http
GET /api/client/loans
Authorization: Bearer <token>
```

**Response with Redis Caching:**

```json
{
  "status": "success",
  "data": {
    "loans": [
      {
        "id": 1,
        "loan_amount": "50000.00",
        "interest_rate": "12.50",
        "issue_date": "2023-06-15",
        "tenure_months": 24,
        "status": "ACTIVE",
        "monthly_installment": "2395.83",
        "total_repaid": "14375.00",
        "remaining_balance": "35625.00",
        "next_payment_date": "2024-01-15"
      }
    ],
    "summary": {
      "total_loans": 2,
      "active_loans": 1,
      "total_disbursed": "75000.00",
      "total_outstanding": "45625.00"
    }
  },
  "cached_at": "2024-01-10 15:30:45"
}
```

### Get Repayment History

```http
GET /api/client/loan-repayment-history?loan_id=1&page=1&per_page=10
Authorization: Bearer <token>
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "repayments": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "payment_date": "2023-07-15",
          "amount_paid": "2395.83",
          "payment_mode": "BANK",
          "reference_no": "TXN-12345678",
          "loan": {
            "id": 1,
            "loan_amount": "50000.00"
          }
        }
      ],
      "per_page": 10,
      "total": 6
    },
    "analytics": {
      "total_paid": "14375.00",
      "average_payment": "2395.83",
      "payment_consistency": "100%",
      "last_payment_date": "2023-12-15"
    }
  }
}
```

## 📊 Core API Endpoints

### Branch Management

| Method | Endpoint             | Description        | Auth Required |
| ------ | -------------------- | ------------------ | ------------- |
| GET    | `/api/branches`      | List all branches  | No            |
| GET    | `/api/branches/{id}` | Get branch details | No            |
| POST   | `/api/branches`      | Create new branch  | Yes           |
| PUT    | `/api/branches/{id}` | Update branch      | Yes           |
| DELETE | `/api/branches/{id}` | Delete branch      | Yes           |

### Client Management

| Method | Endpoint            | Description              | Auth Required |
| ------ | ------------------- | ------------------------ | ------------- |
| GET    | `/api/clients`      | List clients (paginated) | Yes           |
| GET    | `/api/clients/{id}` | Get client details       | Yes           |
| POST   | `/api/clients`      | Create new client        | Yes           |
| PUT    | `/api/clients/{id}` | Update client            | Yes           |
| DELETE | `/api/clients/{id}` | Delete client            | Yes           |

**Client List with Filters:**

```http
GET /api/clients?page=1&per_page=20&branch_id=1&gender=FEMALE
```

### Loan Management

| Method | Endpoint          | Description             | Auth Required |
| ------ | ----------------- | ----------------------- | ------------- |
| GET    | `/api/loans`      | List loans with filters | Yes           |
| GET    | `/api/loans/{id}` | Get loan details        | Yes           |
| POST   | `/api/loans`      | Create new loan         | Yes           |
| PUT    | `/api/loans/{id}` | Update loan             | Yes           |
| DELETE | `/api/loans/{id}` | Delete loan             | Yes           |

**Loan Filters:**

- `status`: ACTIVE, CLOSED, DEFAULTED
- `client_id`: Filter by client
- `branch_id`: Filter by branch
- `min_amount`, `max_amount`: Amount range

### Repayment Management

| Method | Endpoint               | Description           | Auth Required |
| ------ | ---------------------- | --------------------- | ------------- |
| GET    | `/api/repayments`      | List repayments       | Yes           |
| GET    | `/api/repayments/{id}` | Get repayment details | Yes           |
| POST   | `/api/repayments`      | Record new repayment  | Yes           |
| PUT    | `/api/repayments/{id}` | Update repayment      | Yes           |
| DELETE | `/api/repayments/{id}` | Delete repayment      | Yes           |

## 💾 Database Schema

### Enhanced Schema with Authentication

#### Branches (35 branches across Bangladesh)

```sql
- id (Primary Key)
- name (Branch name)
- district (District location)
- region (Central/Eastern/North-Eastern/Northern/South-Western/Southern)
- created_at, updated_at
```

#### Clients (200 clients with auth)

```sql
- id (Primary Key)
- name (Full name)
- email (Unique, nullable - for authentication)
- password (Hashed, nullable)
- phone (Contact number, nullable)
- gender (MALE/FEMALE)
- branch_id (Foreign Key)
- registration_date
- created_at, updated_at
```

#### Loans (320+ loans)

```sql
- id (Primary Key)
- client_id (Foreign Key)
- branch_id (Foreign Key)
- loan_amount (Decimal 10,2)
- interest_rate (Decimal 5,2)
- issue_date
- tenure_months (Integer)
- status (ACTIVE/CLOSED/DEFAULTED)
- created_at, updated_at
```

#### Repayments (1000+ records)

```sql
- id (Primary Key)
- loan_id (Foreign Key)
- payment_date
- amount_paid (Decimal 10,2)
- payment_mode (CASH/BANK/MOBILE)
- reference_no (Nullable)
- created_at, updated_at
```

## ⚡ Performance Features

### Redis Caching Strategy

- **Client Loans**: 15-minute cache with client-specific keys
- **Branch Data**: 1-hour cache for branch information
- **Authentication**: Session and token caching
- **Analytics**: 30-minute cache for computed statistics

### Optimized Queries

- Eager loading relationships to prevent N+1 queries
- Indexed foreign keys and search columns
- Paginated responses for large datasets
- Conditional loading based on request parameters

## 🧪 Testing with Postman

### Import Collection

1. Import `postman_collection.json` into Postman
2. Set environment variables:
   - `base_url`: http://localhost:8000
   - `api_token`: (auto-set after authentication)

### Testing Flow

1. **Authentication**: Run "Get API Token" to authenticate
2. **Client API**: Test client-specific endpoints
3. **CRUD Operations**: Test all management endpoints
4. **Error Handling**: Test with invalid data

### Sample Test Clients

First 40 clients have authentication credentials:

- Email pattern: `{name}{index}@microcredit.com`
- Password: `password123`
- Example: `rashida.begum0@microcredit.com`

## 🚀 Production Deployment

### Docker Production Setup

```bash
# Production environment
cp .env.docker .env
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env

# Scale services
docker-compose up -d --scale app=2
```

### Health Checks

- **API Health**: `GET /api/health`
- **Database**: MySQL container health check
- **Cache**: Redis container health check
- **Queue**: Redis queue monitoring

### Monitoring URLs

- **Application**: http://localhost:8000
- **Database Admin**: http://localhost:8080
- **Redis Monitor**: http://localhost:8081
- **Logs**: `docker-compose logs -f app`

## 📈 Sample Data Volume

- **Branches**: 35 branches across Bangladesh regions
- **Clients**: 200 clients (40 with authentication)
- **Loans**: 320+ loans with various statuses
- **Repayments**: 1000+ repayment records
- **Authentication**: JWT-based client access system

## 🔧 Development Commands

### Local Development

```bash
# Install dependencies
composer install

# Database setup
php artisan migrate:fresh --seed

# Start development server
php artisan serve
```

### Docker Development

```bash
# Start all services
docker-compose up -d

# Run Laravel commands
docker-compose exec app php artisan migrate:fresh --seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan queue:work

# View logs
docker-compose logs -f
```

## 🛡️ Security Features

- JWT token authentication with configurable expiration
- Password hashing with bcrypt
- Request validation and sanitization
- CORS headers for cross-origin requests
- Rate limiting on authentication endpoints
- Secure environment variable management

## 🚨 Error Handling

### Standard Error Response

```json
{
  "status": "error",
  "message": "Error description",
  "errors": {
    "field": ["Validation error details"]
  },
  "code": 422
}
```

### Common HTTP Status Codes

- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Validation Error
- `500`: Server Error
