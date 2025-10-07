# Laravel Microfinance Loan Management System

This Laravel REST API project manages microfinance loan data with the following features:

## Project Structure

- RESTful API for clients, branches, loans, and repayments
- SQLite database for development
- Best practices for scalability, caching, and security
- Comprehensive test data seeding

## Database Schema

- **clients**: id, name, gender, branch_id, registration_date
- **branches**: id, name, district, region
- **loans**: id, client_id, branch_id, loan_amount, interest_rate, issue_date, tenure_months, status
- **repayments**: id, loan_id, payment_date, amount_paid, payment_mode, reference_no

## Development Guidelines

- Use Laravel best practices and conventions
- Implement proper validation and error handling
- Include comprehensive API documentation
- Follow RESTful API design principles
- Use SQLite for local development
