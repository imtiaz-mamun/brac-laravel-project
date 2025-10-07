# Laravel Microfinance Loan Management System - API Documentation

## Project Overview

A complete RESTful API backend built with Laravel for managing microfinance loan data efficiently. The system demonstrates best practices for scalability, caching, and security.

## Technology Stack

- **Framework**: Laravel 10.49.1
- **PHP Version**: 8.1+
- **Database**: SQLite (for development)
- **Server**: Built-in PHP development server
- **API Format**: RESTful JSON API with proper HTTP status codes

## Database Schema

The system manages four core entities with proper relationships:

### 1. Branches

- `id` - Primary key
- `name` - Branch name
- `district` - Geographic district
- `region` - Administrative region
- Timestamps: created_at, updated_at

### 2. Clients

- `id` - Primary key
- `name` - Client full name
- `gender` - MALE/FEMALE enum
- `branch_id` - Foreign key to branches
- `registration_date` - When client joined
- Timestamps: created_at, updated_at

### 3. Loans

- `id` - Primary key
- `client_id` - Foreign key to clients
- `branch_id` - Foreign key to branches
- `loan_amount` - Decimal amount
- `interest_rate` - Decimal percentage
- `issue_date` - When loan was issued
- `tenure_months` - Loan duration in months
- `status` - ACTIVE/CLOSED/DEFAULTED enum
- Timestamps: created_at, updated_at

### 4. Repayments

- `id` - Primary key
- `loan_id` - Foreign key to loans
- `payment_date` - When payment was made
- `amount_paid` - Decimal amount
- `payment_mode` - CASH/BANK/MOBILE enum
- `reference_no` - Optional transaction reference
- Timestamps: created_at, updated_at

## API Endpoints

### Branch Management

- `GET /api/branches` - List all branches with pagination
- `POST /api/branches` - Create new branch
- `GET /api/branches/{id}` - Get specific branch details
- `PUT/PATCH /api/branches/{id}` - Update branch
- `DELETE /api/branches/{id}` - Delete branch
- `GET /api/branches/{id}/clients` - Get clients for branch
- `GET /api/branches/{id}/loans` - Get loans for branch

### Client Management

- `GET /api/clients` - List all clients with pagination
- `POST /api/clients` - Create new client
- `GET /api/clients/{id}` - Get specific client details
- `PUT/PATCH /api/clients/{id}` - Update client
- `DELETE /api/clients/{id}` - Delete client
- `GET /api/clients/{id}/loans` - Get loans for client

### Loan Management

- `GET /api/loans` - List all loans with pagination
- `POST /api/loans` - Create new loan
- `GET /api/loans/{id}` - Get specific loan details
- `PUT/PATCH /api/loans/{id}` - Update loan
- `DELETE /api/loans/{id}` - Delete loan
- `PATCH /api/loans/{id}/status` - Update loan status
- `GET /api/loans/{id}/repayments` - Get repayments for loan

### Repayment Management

- `GET /api/repayments` - List all repayments with pagination
- `POST /api/repayments` - Create new repayment
- `GET /api/repayments/{id}` - Get specific repayment details
- `PUT/PATCH /api/repayments/{id}` - Update repayment
- `DELETE /api/repayments/{id}` - Delete repayment

### Analytics Endpoints

- `GET /api/analytics/branch-performance` - Branch performance metrics
- `GET /api/analytics/client-statistics` - Client demographic stats
- `GET /api/analytics/loan-summary` - Comprehensive loan analytics

## Key Features

### 1. Comprehensive Validation

- Input validation for all endpoints
- Business rule enforcement (e.g., positive loan amounts)
- Relationship integrity checks

### 2. Advanced Filtering & Pagination

- Query parameters for filtering by status, date ranges, amounts
- Consistent pagination with metadata
- Search functionality across relevant fields

### 3. Rich Data Relationships

- Eager loading to prevent N+1 queries
- Nested resource inclusion (loans with client and branch data)
- Aggregate counts (clients_count, loans_count)

### 4. Analytics & Reporting

- Branch performance metrics
- Loan portfolio summaries
- Client statistics by demographics
- Status-based loan categorization

### 5. API Best Practices

- RESTful URL structures
- Proper HTTP status codes
- JSON response format consistency
- Rate limiting middleware
- Error handling with meaningful messages

## Sample Data

The system includes comprehensive seed data:

- **10 branches** across different regions of Bangladesh
- **200 clients** with realistic Bangladeshi names and demographics
- **262 loans** with varied amounts, terms, and statuses
- **Realistic repayment records** with different payment modes

## Getting Started

### Prerequisites

- PHP 8.1 or higher
- Composer
- SQLite extension

### Installation & Setup

1. Install dependencies: `composer install`
2. Copy environment file: `cp .env.example .env`
3. Generate app key: `php artisan key:generate`
4. Run migrations: `php artisan migrate`
5. Seed database: `php artisan db:seed`
6. Start server: `php artisan serve`

### Testing the API

The API is accessible at `http://localhost:8000/api/`

Example requests:

```bash
# Get all branches with pagination
curl -H "Accept: application/json" "http://localhost:8000/api/branches?per_page=5"

# Get specific loan with relationships
curl -H "Accept: application/json" "http://localhost:8000/api/loans/1"

# Create new branch
curl -X POST -H "Accept: application/json" -H "Content-Type: application/json" \
     -d '{"name":"New Branch","district":"Dhaka","region":"Central"}' \
     "http://localhost:8000/api/branches"

# Get branch performance analytics
curl -H "Accept: application/json" "http://localhost:8000/api/analytics/branch-performance"
```

## Security Features

- Input validation and sanitization
- Rate limiting on API endpoints
- CORS configuration
- Error handling without sensitive data exposure

## Performance Optimizations

- Database indexing on foreign keys
- Eager loading for relationships
- Pagination to limit response sizes
- Efficient query structures

## Future Enhancements

Potential improvements could include:

- Authentication and authorization (JWT tokens)
- API versioning
- Caching layer (Redis)
- Database optimization for production (MySQL/PostgreSQL)
- Advanced reporting dashboards
- Loan calculation algorithms
- SMS/Email notifications
- Document management for loan applications

## Development Status

✅ **Completed Features:**

- Complete database schema with relationships
- Full CRUD operations for all entities
- Advanced filtering and pagination
- Analytics endpoints
- Data seeding with realistic test data
- API validation and error handling
- Proper HTTP status codes and JSON responses

The Laravel microfinance API is production-ready for development environments and provides a solid foundation for a comprehensive microfinance management system.
