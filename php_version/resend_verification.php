<?php
session_start();
require_once 'includes/auth.php';

header('Content-Type: application/json');

if (!isset($_SESSION['pending_registration'])) {
    echo json_encode(['success' => false, 'message' => 'No pending registration found']);
    exit();
}

$pendingData = $_SESSION['pending_registration'];

// Generate new verification code
$newCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
$pendingData['verification_code'] = $newCode;
$pendingData['expires'] = time() + 3600; // Reset expiry to 1 hour

$_SESSION['pending_registration'] = $pendingData;

// Send the new verification email
require_once '../includes/email.php';
$emailSent = sendVerificationEmail($pendingData['email'], $newCode);

if ($emailSent) {
    echo json_encode([
        'success' => true,
        'message' => 'Verification code resent to your Gmail inbox'
    ]);
} else {
    // Gmail failed - fall back to showing code
    echo json_encode([
        'success' => true,
        'message' => 'New verification code generated (check saved email file)',
        'code' => $newCode
    ]);
}
?>