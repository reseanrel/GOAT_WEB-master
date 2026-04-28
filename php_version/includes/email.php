<?php
// Email configuration
define('MAIL_SERVER', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'resedelrio9@gmail.com'); // Your Gmail
define('MAIL_PASSWORD', getenv('GMAIL_APP_PASSWORD') ?: 'dswqlieetyuezanb'); // Your Gmail App Password
define('MAIL_ENCRYPTION', 'tls');
define('COMPANY_NAME', 'Pila Pet Registration');

function sendVerificationEmail($userEmail, $verificationCode) {
    $subject = "Verify Your Email - " . COMPANY_NAME;

    $htmlContent = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px; }
            .verification-code { background: #e8f5e8; padding: 20px; text-align: center; margin: 20px 0; border-radius: 5px; border: 2px dashed #4CAF50; }
            .code { font-size: 32px; font-weight: bold; color: #2e7d32; letter-spacing: 5px; }
            .footer { margin-top: 20px; padding: 20px; background: #f1f1f1; text-align: center; border-radius: 5px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>" . COMPANY_NAME . " - Email Verification</h1>
        </div>
        <div class='content'>
            <p>Hello,</p>
            <p>Thank you for registering with " . COMPANY_NAME . ". Please use the verification code below to verify your email address:</p>

            <div class='verification-code'>
                <h3>Your Verification Code</h3>
                <div class='code'>" . $verificationCode . "</div>
                <p>This code will expire in 1 hour.</p>
            </div>

            <p>Enter this 6-digit code on the verification page to complete your registration.</p>
            <p>If you didn't create an account with " . COMPANY_NAME . ", please ignore this email.</p>
        </div>
        <div class='footer'>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; 2024 " . COMPANY_NAME . ". All rights reserved.</p>
        </div>
    </body>
    </html>
    ";

    $textContent = COMPANY_NAME . " - Email Verification\n\n" .
                   "Thank you for registering with " . COMPANY_NAME . ".\n\n" .
                   "Your verification code is: " . $verificationCode . "\n\n" .
                   "Enter this 6-digit code on the verification page to complete your registration.\n\n" .
                   "This code will expire in 1 hour.\n\n" .
                   "If you didn't create an account with " . COMPANY_NAME . ", please ignore this email.\n\n" .
                   "This is an automated message. Please do not reply to this email.";

    return sendEmail($userEmail, $subject, $htmlContent, $textContent, $verificationCode);
}

function sendEmail($to, $subject, $htmlContent, $textContent = '', $verificationCode = '') {
    try {
        // Create socket connection to Gmail SMTP (port 587 with STARTTLS)
        $socket = fsockopen('tcp://smtp.gmail.com', 587, $errno, $errstr, 30);

        if (!$socket) {
            throw new Exception("Cannot connect to Gmail SMTP: $errstr ($errno)");
        }

        // Read server greeting
        $response = fgets($socket, 512);
        if (!preg_match('/^220/', $response)) {
            throw new Exception("SMTP greeting failed: $response");
        }

        // Send EHLO
        fputs($socket, "EHLO localhost\r\n");
        do {
            $response = fgets($socket, 512);
        } while (!preg_match('/^250\s/', $response) && !feof($socket));

        // Start TLS (Gmail requires this on port 587)
        fputs($socket, "STARTTLS\r\n");
        $response = fgets($socket, 512);
        if (!preg_match('/^220/', $response)) {
            throw new Exception("STARTTLS failed: $response");
        }

        // Enable encryption
        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

        // Send EHLO again after TLS
        fputs($socket, "EHLO localhost\r\n");
        do {
            $response = fgets($socket, 512);
        } while (!preg_match('/^250\s/', $response) && !feof($socket));

        // Authenticate
        fputs($socket, "AUTH LOGIN\r\n");
        $response = fgets($socket, 512);
        if (!preg_match('/^334/', $response)) {
            throw new Exception("AUTH LOGIN failed: $response");
        }

        // Send username (base64 encoded)
        fputs($socket, base64_encode(MAIL_USERNAME) . "\r\n");
        $response = fgets($socket, 512);
        if (!preg_match('/^334/', $response)) {
            throw new Exception("Username authentication failed: $response");
        }

        // Send password (base64 encoded)
        fputs($socket, base64_encode(MAIL_PASSWORD) . "\r\n");
        $response = fgets($socket, 512);
        if (!preg_match('/^235/', $response)) {
            throw new Exception("Password authentication failed: $response");
        }

        // Send email
        fputs($socket, "MAIL FROM:<" . MAIL_USERNAME . ">\r\n");
        $response = fgets($socket, 512);
        if (!preg_match('/^250/', $response)) {
            throw new Exception("MAIL FROM failed: $response");
        }

        fputs($socket, "RCPT TO:<$to>\r\n");
        $response = fgets($socket, 512);
        if (!preg_match('/^250/', $response)) {
            throw new Exception("RCPT TO failed: $response");
        }

        fputs($socket, "DATA\r\n");
        $response = fgets($socket, 512);
        if (!preg_match('/^354/', $response)) {
            throw new Exception("DATA command failed: $response");
        }

        // Create email content
        $boundary = 'boundary_' . md5(time());
        $emailContent = "Subject: $subject\r\n";
        $emailContent .= "MIME-Version: 1.0\r\n";
        $emailContent .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
        $emailContent .= "From: " . COMPANY_NAME . " <" . MAIL_USERNAME . ">\r\n";
        $emailContent .= "To: $to\r\n";
        $emailContent .= "Reply-To: " . MAIL_USERNAME . "\r\n";
        $emailContent .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $emailContent .= "\r\n";

        // Text part
        $emailContent .= "--$boundary\r\n";
        $emailContent .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $emailContent .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $emailContent .= $textContent . "\r\n\r\n";

        // HTML part
        $emailContent .= "--$boundary\r\n";
        $emailContent .= "Content-Type: text/html; charset=UTF-8\r\n";
        $emailContent .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $emailContent .= $htmlContent . "\r\n\r\n";

        $emailContent .= "--$boundary--\r\n";
        $emailContent .= ".\r\n";

        // Send the email content
        fputs($socket, $emailContent);

        // Check response
        $response = fgets($socket, 512);
        if (!preg_match('/^250/', $response)) {
            throw new Exception("Email sending failed: $response");
        }

        // Quit
        fputs($socket, "QUIT\r\n");
        fclose($socket);

        error_log("[SUCCESS] Email sent to $to via Gmail SMTP");
        return true;

    } catch (Exception $e) {
        if (isset($socket)) {
            fputs($socket, "QUIT\r\n");
            fclose($socket);
        }

        // Fallback: save to file if SMTP fails
        error_log("[ERROR] Gmail SMTP failed: " . $e->getMessage() . " - Falling back to file storage");

        $emailFile = __DIR__ . '/../emails/' . time() . '_' . md5($to) . '.html';
        $emailsDir = __DIR__ . '/../emails';
        if (!is_dir($emailsDir)) {
            mkdir($emailsDir, 0755, true);
        }

        $emailContent = "<!-- GMAIL SMTP FAILED - EMAIL SAVED TO FILE -->\n" . $htmlContent;
        file_put_contents($emailFile, $emailContent);

        return false; // Return false so the system knows SMTP failed
    }
}

function smtpCommand($socket, $command) {
    fputs($socket, $command . "\r\n");
    $response = fgets($socket, 512);
    return $response;
}

function smtpOk($response) {
    $code = substr($response, 0, 3);
    return $code >= 200 && $code < 400;
}
?>