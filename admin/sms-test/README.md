# SMS Gateway Test Demo

This is a standalone demo to test the SMS gateway integration with Text.lk API.

## 📁 Folder Structure

```
sms-test/
├── index.html          # Test page UI
├── api/
│   └── send-sms.php   # SMS sending API endpoint
├── config/
│   └── sms.php        # SMS configuration (API key, settings)
└── README.md          # This file
```

## 🚀 How to Use

1. **Access via Web Server**
   - This demo must be accessed through a web server (like MAMP, XAMPP, or live hosting)
   - Open: `http://localhost/Daily-Collection-Manager/sms-test/index.html`
   - **Do NOT** open the HTML file directly (file://) - it won't work!

2. **Fill in the Test Form**
   - Customer Name: Enter any customer name
   - Customer Mobile: Enter mobile number (will be sent to test number: 94778553032)
   - Paid Amount: Enter payment amount
   - Remaining Balance: Enter remaining balance

3. **Send Test SMS**
   - Click "Send Test SMS" button
   - Check the result section for success/error messages
   - If successful, check your phone (94778553032) for the SMS

## ⚙️ Configuration

### Testing Mode
Currently in **Testing Mode** - all SMS will be sent to: `94778553032`

To disable testing mode and send to actual customer numbers:
1. Open `config/sms.php`
2. Change `TESTING_MODE` to `false`:
   ```php
   private const TESTING_MODE = false;
   ```

### SMS Preview
The page shows a live preview of the SMS message that will be sent, including:
- Date and time
- Customer name
- Paid amount
- Remaining balance

## 🔍 Troubleshooting

### SMS Not Received?
1. **Check Testing Mode**: Make sure you're checking the correct phone number (94778553032)
2. **Check Console**: Open browser DevTools (F12) → Console tab for error messages
3. **Check API Response**: Look at the result section for detailed error messages
4. **Verify API Key**: Make sure the API key in `config/sms.php` is correct
5. **Verify Sender ID**: Ensure "Collection" is registered in your Text.lk account
6. **Check Account Balance**: Make sure your Text.lk account has sufficient credits

### Common Errors

**"Failed to connect to SMS service"**
- Check internet connection
- Verify API endpoint URL is accessible
- Check firewall settings

**"401 Unauthorized"**
- API key is incorrect or expired
- Check API key in `config/sms.php`

**"Invalid sender_id"**
- Sender ID "Collection" is not registered
- Register sender ID in Text.lk dashboard

## 📱 SMS Message Format

```
Payment Confirmation

Date: 2025-01-XX XX:XX
Customer: [Customer Name]
Paid Amount: Rs. XXX.XX
Remaining Balance: Rs. XXX.XX

Thank you for your payment!
```

## 🔒 Security Notes

- API key is stored server-side only (in `config/sms.php`)
- Never expose API keys in frontend code
- In production, use environment variables for sensitive data
- Enable SSL verification for production (`VERIFY_SSL_DEFAULT = true`)

## 🌐 Production Setup

When moving to production:

1. **Disable Testing Mode**: Set `TESTING_MODE = false` in `config/sms.php`
2. **Enable SSL Verification**: Set `VERIFY_SSL_DEFAULT = true`
3. **Update Sender ID**: Use your registered sender ID
4. **Remove CORS Headers**: Restrict CORS in `api/send-sms.php` to your domain only
5. **Secure API Key**: Move API key to environment variables

## 📝 API Endpoint

**URL**: `api/send-sms.php`  
**Method**: POST  
**Content-Type**: application/json

**Request Body**:
```json
{
  "customer_name": "John Doe",
  "customer_mobile": "0778553032",
  "payment_amount": 250.00,
  "remaining_balance": 150.00
}
```

**Response** (Success):
```json
{
  "success": true,
  "message": "SMS sent successfully",
  "recipient": "Test number: 94778553032",
  "response": {...}
}
```

**Response** (Error):
```json
{
  "success": false,
  "error": "Error message",
  "http_code": 401,
  "raw": "..."
}
```


