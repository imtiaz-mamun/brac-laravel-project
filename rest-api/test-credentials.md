# 🔐 Test Credentials for API Authentication

## Available Test Accounts

All test accounts use the password: `password123`

### Valid Email Addresses for Authentication:

1. **abdul.karim16@microcredit.com** (Client ID: 17)
2. **abdul.karim33@microcredit.com** (Client ID: 34)
3. **abdur.rahman18@microcredit.com** (Client ID: 19)
4. **abdur.rahman9@microcredit.com** (Client ID: 10)
5. **abu.bakkar23@microcredit.com** (Client ID: 24)

## Quick Authentication Test

```bash
# Test authentication with curl
curl -X POST -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"email": "abdul.karim16@microcredit.com", "password": "password123"}' \
  http://localhost:8000/api/auth/get-token
```

## Postman Collection Usage

1. **Import** the `postman_collection.json` file
2. **Run** "Get API Token" request (pre-configured with valid credentials)
3. **Token Auto-Set**: JWT token will automatically be saved for other requests
4. **Test Endpoints**: All authenticated endpoints will work automatically

## Client-Specific Endpoints

After authentication, you can use these client-specific endpoints:

- `GET /api/client/loans` - Get authenticated client's loans
- `GET /api/client/loan-repayment-history` - Get repayment history

## Getting More Test Credentials

To get additional test credentials, run this in Docker:

```bash
# Get more clients with email addresses
docker exec laravel_microfinance_app php -r "
use App\Models\Client;
require '/var/www/html/vendor/autoload.php';
\$app = require '/var/www/html/bootstrap/app.php';
\$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();
\$clients = Client::whereNotNull('email')->limit(10)->get(['id', 'name', 'email']);
echo json_encode(\$clients->toArray(), JSON_PRETTY_PRINT);
"
```
