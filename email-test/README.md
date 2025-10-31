# Email Test - SMTP Image Sending

This folder contains a test setup for sending emails with image attachments via SMTP using Mailpit.

## Files

- `index.html` - Web interface for testing email sending
- `send-image-email.php` - PHP script that sends email with image via SMTP
- `config/smtp.php` - SMTP configuration file (separate settings)
- `sample-image.jpg` - Will be auto-generated if it doesn't exist

## Requirements

- PHP with GD extension (for generating test image if needed)
- Mailpit running locally on port 1025 (SMTP) and 8025 (Web UI)

## Usage

1. **Ensure Mailpit is running:**
   ```bash
   # If using Docker:
   docker run -d -p 1025:1025 -p 8025:8025 axllent/mailpit
   ```

2. **Open the test interface:**
   - Navigate to: `http://localhost/email-test/index.html`
   - Or use your MAMP URL: `http://localhost:8080/email-test/index.html` (adjust port as needed)

3. **Send test email:**
   - Fill in the recipient email address
   - Optionally change the "From" email and name
   - Click "Send Test Email with Image"
   - Check Mailpit web interface at `http://localhost:8025` to see the email

## SMTP Configuration

SMTP settings are stored in `config/smtp.php` for easy modification. The default configuration is set for Mailpit:

- **SMTP Host:** localhost
- **SMTP Port:** 1025
- **Encryption:** None (local testing)
- **Authentication:** None (local Mailpit)

To change SMTP settings, edit the `config/smtp.php` file.

## Features

- ✅ Sends email via SMTP (not using Mailpit API)
- ✅ Includes image as both attachment and inline/embedded image
- ✅ Supports HTML and plain text versions
- ✅ Auto-generates sample image if none exists
- ✅ Proper MIME multipart message structure

## Email Structure

The email includes:
1. Plain text version
2. HTML version with embedded image
3. Inline/embedded image (shown in HTML)
4. Attached image (downloadable)

## Troubleshooting

- **Cannot connect to SMTP server:** Make sure Mailpit is running on port 1025
- **Image not generated:** Ensure PHP GD extension is installed
- **Email not appearing in Mailpit:** Check the console/error output in browser dev tools

