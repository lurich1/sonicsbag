# Paystack Payment Integration Setup

This document explains how to configure Paystack payment integration for the PHP version of SONCIS store.

## Configuration

### 1. Get Your Paystack API Keys

1. Sign up or log in to [Paystack Dashboard](https://dashboard.paystack.com/)
2. Go to **Settings** → **API Keys & Webhooks**
3. Copy your **Secret Key** (starts with `sk_test_` for test mode or `sk_live_` for live mode)
4. Copy your **Public Key** (starts with `pk_test_` for test mode or `pk_live_` for live mode)

### 2. Configure Keys in PHP

Edit `config.php` and add your Paystack keys:

```php
// Paystack Configuration
define('PAYSTACK_SECRET_KEY', 'sk_test_xxxxxxxxxxxxx'); // Your Paystack Secret Key
define('PAYSTACK_PUBLIC_KEY', 'pk_test_xxxxxxxxxxxxx'); // Your Paystack Public Key (optional)
```

### 3. Update Site URL

Make sure `SITE_URL` in `config.php` matches your actual domain:

```php
define('SITE_URL', 'http://localhost'); // Change to your actual URL for production
```

For production, use your actual domain:
```php
define('SITE_URL', 'https://yourdomain.com');
```

## How It Works

### Payment Flow

1. **User selects Mobile Money payment** on checkout page
2. **User enters mobile money number** (9-15 digits)
3. **System initializes Paystack payment** via `paystack-init.php`
4. **Order data is saved** to browser sessionStorage
5. **User is redirected** to Paystack payment page
6. **User completes payment** on Paystack
7. **Paystack redirects back** to `paystack-callback.php`
8. **System verifies payment** via `paystack-verify.php`
9. **Order is created** in database with payment details
10. **User sees confirmation** page

### Files Involved

- **`paystack-init.php`** - Initializes Paystack payment transaction
- **`paystack-verify.php`** - Verifies payment with Paystack API and creates order
- **`paystack-callback.php`** - Handles the callback page after payment
- **`checkout.php`** - Contains the payment flow logic
- **`config.php`** - Contains Paystack API keys configuration

## Testing

### Test Mode

Use test keys (starting with `sk_test_` and `pk_test_`) for development.

### Test Mobile Money Numbers

Paystack provides test numbers for different networks:
- **MTN**: Use any valid MTN number format
- **Vodafone**: Use any valid Vodafone number format

### Test Amounts

- Minimum: 1 GHS (100 pesewas)
- Use small amounts for testing

## Production Checklist

- [ ] Replace test keys with live keys (`sk_live_` and `pk_live_`)
- [ ] Update `SITE_URL` to production domain
- [ ] Test payment flow end-to-end
- [ ] Set up webhook (optional, for server-to-server notifications)
- [ ] Configure SSL certificate (HTTPS required for production)

## Troubleshooting

### "Paystack secret key is not configured"
- Make sure `PAYSTACK_SECRET_KEY` is set in `config.php`
- Check that the key starts with `sk_test_` or `sk_live_`

### "Failed to initialize Paystack transaction"
- Check your internet connection
- Verify the API key is correct
- Check Paystack dashboard for API status
- Review PHP error logs

### Payment verified but order not created
- Check database connection
- Review `paystack-verify.php` error logs
- Ensure order data is being passed correctly from sessionStorage

### Callback URL issues
- Make sure `SITE_URL` in `config.php` is correct
- The callback URL should be accessible from the internet (for production)
- For local testing, use a tool like ngrok to expose localhost

## API Reference

The implementation uses Paystack's Transaction API:
- **Initialize**: `POST https://api.paystack.co/transaction/initialize`
- **Verify**: `GET https://api.paystack.co/transaction/verify/{reference}`

For more details, see [Paystack API Documentation](https://paystack.com/docs/api/)

## Security Notes

- **Never commit API keys to version control**
- Use environment variables or secure configuration files
- Keep secret keys server-side only
- Use HTTPS in production
- Validate all payment callbacks server-side

