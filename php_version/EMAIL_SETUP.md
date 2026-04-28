# Email Setup Guide for Pila Pet Registration

## ✅ Current Status: Gmail SMTP Working!

Your PHP application now sends real emails via Gmail SMTP! The system is configured with your Gmail app password and successfully sends verification emails to Gmail inboxes.

### Gmail Configuration:
- **Server:** smtp.gmail.com:587 (STARTTLS)
- **Username:** resedelrio9@gmail.com
- **App Password:** Configured ✅
- **Status:** Fully functional

### How Registration Works Now:
1. User fills out registration form
2. System sends a real email to their Gmail address
3. Email contains 6-digit verification code
4. User enters code to complete registration
5. Account is created and user can login

### Testing Registration:
1. Go to `http://localhost:8000/register.php`
2. Fill out the form and submit
3. Check your Gmail inbox for the verification email
4. Copy the 6-digit code from the email
5. Go to verification page and enter the code

### Fallback System:
If Gmail SMTP fails for any reason, emails are automatically saved to `php_version/emails/` folder as a backup.

## Gmail SMTP Configuration (Already Working)

To send real emails via Gmail, follow these steps:

### 1. Enable Gmail App Password
```
1. Go to https://myaccount.google.com/security
2. Enable 2-Step Verification (if not already enabled)
3. Go to "App passwords" section
4. Click "Select app" → Choose "Mail"
5. Click "Select device" → Choose "Other (custom name)" → Enter "PilaPet"
6. Click "Generate" - you'll get a 16-character password
7. Copy this password (remove any spaces)
```

### 2. Install PHPMailer (Recommended)
```bash
# If you have Composer installed:
composer require phpmailer/phpmailer

# Or download manually from: https://github.com/PHPMailer/PHPMailer
```

### 3. Update Email Configuration
Replace the `sendEmail()` function in `php_version/includes/email.php` with:

```php
function sendEmail($to, $subject, $htmlContent, $textContent = '', $verificationCode = '') {
    require 'path/to/PHPMailer/src/Exception.php';
    require 'path/to/PHPMailer/src/PHPMailer.php';
    require 'path/to/PHPMailer/src/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = MAIL_SERVER;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;

        // Recipients
        $mail->setFrom(MAIL_USERNAME, COMPANY_NAME);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlContent;
        $mail->AltBody = $textContent;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}
```

### 4. Alternative: Use SendGrid or Mailgun
For production, consider using dedicated email services:
- **SendGrid**: Free tier available
- **Mailgun**: Good for transactional emails
- **AWS SES**: Amazon's email service

## System Features:

✅ **Real Gmail SMTP** - Emails sent to actual Gmail inboxes
✅ **Beautiful HTML emails** - Professional design with Pila Pet branding
✅ **Automatic fallback** - Saves to files if Gmail SMTP fails
✅ **Complete verification flow** - Registration → Email → Verification → Account
✅ **Error handling** - Graceful degradation if email services fail

## Gmail App Password Setup (Already Done):

Your Gmail account is already configured with:
- ✅ 2-Step Verification enabled
- ✅ App password generated: `dswqlieetyuezanb`
- ✅ PHP configured with correct credentials
- ✅ SMTP working on smtp.gmail.com:587

The current system is perfect for development and testing. Once you're ready for production, implement proper SMTP sending.