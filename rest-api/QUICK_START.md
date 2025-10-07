# 🚀 Quick Setup Guide

## One-Command Setup

```bash
# Clone and deploy in 3 commands
git clone https://github.com/imtiaz-mamun/brac-laravel-project.git
cd brac-laravel-project/rest-api
docker-compose up -d --build
```

## ✅ Verify Setup

```bash
# Test API (wait 2-3 minutes for initialization)
curl -H "Accept: application/json" http://localhost:8000/api/branches

# Expected: JSON response with 15 branches
```

## 🔗 Access Points

- **API**: http://localhost:8000
- **Database UI**: http://localhost:8081 (`microfinance_user` / `microfinance_password`)
- **Redis UI**: http://localhost:8082

## 🧪 Test Authentication

```bash
curl -X POST -H "Content-Type: application/json" \
  -d '{"email":"abdul.karim16@microcredit.com","password":"password123"}' \
  http://localhost:8000/api/auth/get-token
```

## 📚 Full Documentation

- **Complete Guide**: [DOCKER_DEPLOYMENT.md](DOCKER_DEPLOYMENT.md)
- **API Reference**: [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
- **Postman Collection**: [postman_collection.json](postman_collection.json)

**Done! Your microfinance API is running! 🎉**
