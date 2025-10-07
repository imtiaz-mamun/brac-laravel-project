# 📧 Email Notification System

## Overview

The BRAC Microfinance API includes an automated email notification system that sends confirmation emails when loan repayments are successfully recorded.

## ✨ Features

- **Automatic Email Notifications** when repayments are created via API
- **Professional HTML Email Templates** with loan and client details
- **Multi-recipient Support** (client + admin CC)
- **Event-driven Architecture** using Laravel Events & Listeners
- **Queue Support** for background email processing
- **Error Handling & Logging** for email delivery issues
- **Test Commands** for email functionality verification

## 🔧 Configuration

### Environment Variables

Add these variables to your `.env` file:

```bash
# SMTP Configuration
SMTP_USER=your_smtp_mail@gmail.com
SMTP_PASS="your_smtp_mail_password"
CC_MAIL=imtiazmamunantu@gmail.com

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME="${SMTP_USER}"
MAIL_PASSWORD="${SMTP_PASS}"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="${SMTP_USER}"
MAIL_FROM_NAME="${APP_NAME}"
```

### Gmail App Password Setup

1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password:
   - Go to Google Account Settings
   - Security → 2-Step Verification → App passwords
   - Select "Mail" and generate password
   - Use the generated password as `SMTP_PASS`

## 🚀 How It Works

### API Workflow

1. **Repayment Created**: POST to `/api/repayments`
2. **Event Fired**: `RepaymentCreated` event is triggered
3. **Listener Executes**: `SendRepaymentNotification` processes the event
4. **Email Sent**: Professional confirmation email delivered
5. **Logging**: Success/failure logged for monitoring

### Email Recipients

- **Primary**: Client email (if available)
- **CC**: Admin email from `CC_MAIL` environment variable
- **Fallback**: Admin-only if client has no email

## 📨 Email Content

The email includes:

- ✅ **Payment Confirmation** with success badge
- 💰 **Amount Paid** prominently displayed
- 📋 **Transaction Details** (reference, date, method)
- 🏦 **Loan Information** (amount, interest, status)
- 📊 **Updated Balance** (total repaid, remaining)
- 🎉 **Loan Completion** notification (if fully paid)
- 📞 **Contact Information** for support

## 🧪 Testing

### API Testing

```bash
# Create a repayment (triggers email)
curl -X POST http://localhost:8000/api/repayments \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "loan_id": 1,
    "payment_date": "2024-10-08",
    "amount_paid": 2500.00,
    "payment_mode": "BANK",
    "reference_no": "TXN-12345678"
  }'
```

### Email Testing Command

```bash
# Test with latest repayment
php artisan test:repayment-email

# Test with specific repayment ID
php artisan test:repayment-email 123

# Test with custom email
php artisan test:repayment-email --email=test@example.com

# Test with specific repayment and custom email
php artisan test:repayment-email 123 --email=test@example.com
```

### Docker Testing

```bash
# Test in Docker environment
docker exec laravel_microfinance_app php artisan test:repayment-email --email=test@example.com
```

## 📊 Monitoring & Logs

### Log Entries

**Successful Email:**

```
[INFO] Repayment confirmation email sent
{
  "repayment_id": 123,
  "client_id": 45,
  "amount": 2500.00,
  "reference_no": "TXN-12345678",
  "recipients": ["client@example.com"],
  "cc": ["admin@example.com"]
}
```

**Warning (No Recipients):**

```
[WARNING] No email recipients found for repayment notification
{
  "repayment_id": 123,
  "client_id": 45,
  "client_email": null
}
```

**Error:**

```
[ERROR] Failed to send repayment confirmation email
{
  "repayment_id": 123,
  "error": "Connection timeout",
  "trace": "..."
}
```

### Viewing Logs

```bash
# Local development
tail -f storage/logs/laravel.log

# Docker environment
docker exec laravel_microfinance_app tail -f storage/logs/laravel.log

# Filter for email logs
docker exec laravel_microfinance_app grep -i "repayment.*email" storage/logs/laravel.log
```

## 🔄 Queue Configuration

For production environments, enable queue processing:

```bash
# Update .env
QUEUE_CONNECTION=redis

# Start queue worker
php artisan queue:work

# Or in Docker
docker exec laravel_microfinance_app php artisan queue:work
```

## 🚨 Troubleshooting

### Common Issues

**1. Gmail Authentication Error**

```bash
# Solution: Use App Password instead of regular password
SMTP_PASS="your-16-char-app-password"
```

**2. Connection Timeout**

```bash
# Solution: Check firewall/network settings
# Ensure port 587 is accessible
telnet smtp.gmail.com 587
```

**3. No Emails Sent**

```bash
# Check logs
docker exec laravel_microfinance_app tail -f storage/logs/laravel.log

# Test email configuration
docker exec laravel_microfinance_app php artisan test:repayment-email --email=your@email.com
```

**4. Queue Jobs Failing**

```bash
# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Debug Commands

```bash
# Check email configuration
php artisan tinker
>>> config('mail')

# Test SMTP connection
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });

# Check event listeners
php artisan event:list
```

## 🔐 Security Considerations

- **App Passwords**: Use Gmail App Passwords, never regular passwords
- **Environment Variables**: Keep SMTP credentials in `.env`, never commit to version control
- **TLS Encryption**: Always use `MAIL_ENCRYPTION=tls` for secure transmission
- **Access Control**: Limit CC_MAIL to trusted admin addresses
- **Rate Limiting**: Consider email sending limits for high-volume environments

## 📈 Production Deployment

### Docker Environment

The email system works seamlessly in Docker:

```bash
# Deploy with updated configuration
docker-compose down
docker-compose up -d --build

# Verify email configuration
docker exec laravel_microfinance_app php artisan config:cache
docker exec laravel_microfinance_app php artisan test:repayment-email --email=admin@yourdomain.com
```

### Environment-Specific Configuration

**Development:**

```bash
MAIL_MAILER=log  # Emails logged to storage/logs/laravel.log
```

**Staging:**

```bash
MAIL_MAILER=smtp
CC_MAIL=staging-admin@yourdomain.com
```

**Production:**

```bash
MAIL_MAILER=smtp
QUEUE_CONNECTION=redis  # Background processing
CC_MAIL=admin@yourdomain.com
```

## 📞 Support

If you encounter issues with the email system:

1. Check the troubleshooting section above
2. Verify environment configuration
3. Test with the provided commands
4. Review application logs
5. Contact system administrator

---

**✅ Your email notification system is now ready to automatically send professional repayment confirmations!**
