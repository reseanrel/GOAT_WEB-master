<?php
// Email configuration and utility functions
define('MAIL_SERVER', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'resedelrio9@gmail.com');
define('MAIL_PASSWORD', 'dswqlieetyuezanb');
define('COMPANY_NAME', 'Pila Pet Registration');

class EmailService {
    
    public static function sendVerificationEmail($userEmail, $verificationCode) {
        $subject = "Verify Your Email - " . COMPANY_NAME;
        $from = COMPANY_NAME . " <" . MAIL_USERNAME . ">";
        
        $htmlContent = self::getVerificationEmailHTML($verificationCode);
        $textContent = self::getVerificationEmailText($verificationCode);
        
        return self::sendEmail($userEmail, $subject, $htmlContent, $textContent, $from);
    }
    
    public static function sendPetApprovalEmail($ownerEmail, $ownerName, $petName) {
        $subject = "Good News! Your pet $petName has been approved";
        $from = COMPANY_NAME . " <" . MAIL_USERNAME . ">";
        
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px; }
                .footer { margin-top: 20px; padding: 20px; background: #f1f1f1; text-align: center; border-radius: 5px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>" . COMPANY_NAME . " - Pet Approved!</h1>
            </div>
            <div class='content'>
                <p>Dear $ownerName,</p>
                <p>Great news! Your pet registration for <strong>$petName</strong> has been approved by our administrators.</p>
                <p>Your pet is now officially registered in the Pila Pet Registration System and will be visible to other users.</p>
                <p>Best regards,<br>Pila Pets Administration<br>Municipality of Pila, Laguna</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>&copy; 2024 " . COMPANY_NAME . ". All rights reserved.</p>
            </div>
        </body>
        </html>";
        
        $textContent = "Dear $ownerName,\n\nGreat news! Your pet registration for $petName has been approved.\n\nBest regards,\nPila Pets Administration";
        
        return self::sendEmail($ownerEmail, $subject, $htmlContent, $textContent, $from);
    }
    
    public static function sendPetRejectionEmail($ownerEmail, $ownerName, $petName, $reason) {
        $subject = "Pet Registration Update: $petName";
        $from = COMPANY_NAME . " <" . MAIL_USERNAME . ">";
        
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #FF6B35; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px; }
                .footer { margin-top: 20px; padding: 20px; background: #f1f1f1; text-align: center; border-radius: 5px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>" . COMPANY_NAME . " - Pet Registration Update</h1>
            </div>
            <div class='content'>
                <p>Dear $ownerName,</p>
                <p>We regret to inform you that your pet registration for <strong>$petName</strong> has been reviewed and was not approved at this time.</p>
                <p><strong>Reason for rejection:</strong></p>
                <div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #dc3545; margin: 20px 0;'>
                    $reason
                </div>
                <p>You may submit a new registration with corrected information.</p>
                <p>Best regards,<br>Pila Pets Administration<br>Municipality of Pila, Laguna</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>&copy; 2024 " . COMPANY_NAME . ". All rights reserved.</p>
            </div>
        </body>
        </html>";
        
        $textContent = "Dear $ownerName,\n\nYour pet registration for $petName was not approved.\n\nReason: $reason\n\nBest regards,\nPila Pets Administration";
        
        return self::sendEmail($ownerEmail, $subject, $htmlContent, $textContent, $from);
    }

    public static function sendAdoptionApplicationSubmittedEmail($ownerEmail, $ownerName, $petName, $applicantName) {
        $subject = "New Adoption Application for Your Pet: $petName";
        $from = COMPANY_NAME . " <" . MAIL_USERNAME . ">";

        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1a73e8; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px; }
                .footer { margin-top: 20px; padding: 20px; background: #f1f1f1; text-align: center; border-radius: 5px; font-size: 12px; color: #666; }
                .pill { display:inline-block; background:#e8f5e8; color:#2e7d32; padding:6px 10px; border-radius:999px; font-weight:700; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>" . COMPANY_NAME . " - Adoption Application</h1>
            </div>
            <div class='content'>
                <p>Dear $ownerName,</p>
                <p><span class='pill'>New</span> an adopter has submitted an application for your pet:</p>
                <p style='font-size:16px; margin: 10px 0;'><strong>$petName</strong></p>
                <p>Applicant name: <strong>$applicantName</strong></p>
                <p>Your application status will be reviewed and you will be contacted through the details provided by the applicant.</p>
                <p>Best regards,<br>Pila Pets Administration<br>Municipality of Pila, Laguna</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>&copy; 2024 " . COMPANY_NAME . ". All rights reserved.</p>
            </div>
        </body>
        </html>";

        $textContent = "Dear $ownerName,\n\nAn adoption application has been submitted for your pet.\n\nPet: $petName\nApplicant: $applicantName\n\nBest regards,\nPila Pets Administration";

        return self::sendEmail($ownerEmail, $subject, $htmlContent, $textContent, $from);
    }
    
    private static function getVerificationEmailHTML($code) {
        return "
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
                    <div class='code'>$code</div>
                    <p>This code will expire in 1 hour.</p>
                </div>
                <p>Enter this 6-digit code on the verification page to complete your registration.</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>&copy; 2024 " . COMPANY_NAME . ". All rights reserved.</p>
            </div>
        </body>
        </html>";
    }
    
    private static function getVerificationEmailText($code) {
        return COMPANY_NAME . " - Email Verification\n\nYour verification code is: $code\n\nThis code will expire in 1 hour.\n\nThis is an automated message.";
    }
    
    private static function sendEmail($to, $subject, $htmlContent, $textContent, $from) {
        try {
            // Gmail SMTP via STARTTLS (port 587)
            $socket = fsockopen('tcp://' . MAIL_SERVER, MAIL_PORT, $errno, $errstr, 30);

            if (!$socket) {
                throw new Exception("Cannot connect to Gmail SMTP: $errstr ($errno)");
            }

            // Server greeting
            $response = fgets($socket, 512);
            if (!preg_match('/^220/', $response)) {
                throw new Exception("SMTP greeting failed: " . $response);
            }

            // EHLO
            fputs($socket, "EHLO localhost\r\n");
            do {
                $response = fgets($socket, 512);
            } while (!preg_match('/^250\s/', $response) && !feof($socket));

            // STARTTLS
            fputs($socket, "STARTTLS\r\n");
            $response = fgets($socket, 512);
            if (!preg_match('/^220/', $response)) {
                throw new Exception("STARTTLS failed: " . $response);
            }

            // Enable encryption
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

            // EHLO again
            fputs($socket, "EHLO localhost\r\n");
            do {
                $response = fgets($socket, 512);
            } while (!preg_match('/^250\s/', $response) && !feof($socket));

            // AUTH LOGIN
            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 512);
            if (!preg_match('/^334/', $response)) {
                throw new Exception("AUTH LOGIN failed: " . $response);
            }

            // Username
            fputs($socket, base64_encode(MAIL_USERNAME) . "\r\n");
            $response = fgets($socket, 512);
            if (!preg_match('/^334/', $response)) {
                throw new Exception("Username authentication failed: " . $response);
            }

            // Password
            fputs($socket, base64_encode(MAIL_PASSWORD) . "\r\n");
            $response = fgets($socket, 512);
            if (!preg_match('/^235/', $response)) {
                throw new Exception("Password authentication failed: " . $response);
            }

            // MAIL FROM
            fputs($socket, "MAIL FROM:<" . MAIL_USERNAME . ">\r\n");
            $response = fgets($socket, 512);
            if (!preg_match('/^250/', $response)) {
                throw new Exception("MAIL FROM failed: " . $response);
            }

            // RCPT TO
            fputs($socket, "RCPT TO:<$to>\r\n");
            $response = fgets($socket, 512);
            if (!preg_match('/^250/', $response)) {
                throw new Exception("RCPT TO failed: " . $response);
            }

            // DATA
            fputs($socket, "DATA\r\n");
            $response = fgets($socket, 512);
            if (!preg_match('/^354/', $response)) {
                throw new Exception("DATA command failed: " . $response);
            }

            // Build email (multipart alternative)
            $boundary = 'boundary_' . md5(time());

            $emailContent = "Subject: $subject\r\n";
            $emailContent .= "MIME-Version: 1.0\r\n";
            $emailContent .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
            $emailContent .= "From: $from\r\n";
            $emailContent .= "To: $to\r\n";
            $emailContent .= "Reply-To: " . MAIL_USERNAME . "\r\n";
            $emailContent .= "X-Mailer: PHP/" . phpversion() . "\r\n\r\n";

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

            // Send email body
            fputs($socket, $emailContent);

            // Response after end-of-data
            $response = fgets($socket, 512);
            if (!preg_match('/^250/', $response)) {
                throw new Exception("Email sending failed: " . $response);
            }

            // QUIT
            fputs($socket, "QUIT\r\n");
            fclose($socket);

            error_log("[SUCCESS] Email sent to $to via Gmail SMTP");
            return true;
        } catch (Exception $e) {
            if (isset($socket)) {
                try { fputs($socket, "QUIT\r\n"); } catch (Throwable $ignore) {}
                fclose($socket);
            }

            error_log("[ERROR] Gmail SMTP failed: " . $e->getMessage() . " - Falling back to file storage");

            // Fallback: save to file if SMTP fails
            $emailFile = __DIR__ . '/../emails/' . time() . '_' . md5($to) . '.html';
            $emailsDir = __DIR__ . '/../emails';
            if (!is_dir($emailsDir)) {
                mkdir($emailsDir, 0755, true);
            }

            $emailContent = "<!-- GMAIL SMTP FAILED - EMAIL SAVED TO FILE -->\n" . $htmlContent;
            file_put_contents($emailFile, $emailContent);

            return false;
        }
    }
}
