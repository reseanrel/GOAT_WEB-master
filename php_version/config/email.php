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
        require_once 'PHPMailer/PHPMailer.php';
        require_once 'PHPMailer/SMTP.php';
        require_once 'PHPMailer/Exception.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = MAIL_SERVER;
            $mail->SMTPAuth = true;
            $mail->Username = MAIL_USERNAME;
            $mail->Password = MAIL_PASSWORD;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = MAIL_PORT;
            
            $mail->setFrom(MAIL_USERNAME, COMPANY_NAME);
            $mail->addAddress($to);
            
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlContent;
            $mail->AltBody = $textContent;
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email error: " . $mail->ErrorInfo);
            return false;
        }
    }
}
