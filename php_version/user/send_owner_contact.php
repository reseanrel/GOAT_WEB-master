<?php
session_start();
require_once '../includes/auth.php';
require_once '../config/email.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$petId = isset($_POST['pet_id']) ? (int)$_POST['pet_id'] : 0;
$message = trim($_POST['message'] ?? '');
$context = $_POST['context'] ?? 'general';

if ($petId <= 0 || $message === '') {
    echo json_encode(['success' => false, 'message' => 'Pet ID and message are required.']);
    exit();
}

if (strlen($message) < 10) {
    echo json_encode(['success' => false, 'message' => 'Please write a longer message (at least 10 characters).']);
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

try {
    // Securely fetch pet + owner (never trust client email)
    $stmt = $conn->prepare("
        SELECT 
            p.id, p.name, p.owner_id,
            u.full_name AS owner_name, u.email AS owner_email
        FROM pets p
        JOIN users u ON p.owner_id = u.id
        WHERE p.id = ? AND p.archived = 0 AND p.status = 'approved'
    ");
    $stmt->execute([$petId]);
    $pet = $stmt->fetch();

    if (!$pet || empty($pet['owner_email'])) {
        echo json_encode(['success' => false, 'message' => 'Pet not found or owner contact unavailable.']);
        exit();
    }

    // Prevent self-contact
    if ((int)$pet['owner_id'] === (int)$_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'You cannot contact yourself about your own pet.']);
        exit();
    }

    // Get current user details (authoritative)
    $currentUser = getUserById($_SESSION['user_id']);
    if (!$currentUser) {
        echo json_encode(['success' => false, 'message' => 'User session invalid. Please log in again.']);
        exit();
    }

    $senderName  = $currentUser['full_name'] ?? 'User';
    $senderEmail = $currentUser['email'] ?? '';
    $senderPhone = $currentUser['contact_number'] ?? '';

    // Send the email via platform SMTP with Reply-To = sender
    $sent = EmailService::sendContactInquiryEmail(
        $pet['owner_email'],
        $pet['owner_name'],
        $pet['name'],
        $senderName,
        $senderEmail,
        $senderPhone,
        $message,
        $context
    );

    if ($sent) {
        // Log the contact attempt (simple for now)
        error_log(sprintf(
            "[CONTACT] user_id=%d sent %s inquiry about pet_id=%d to owner_id=%d",
            $_SESSION['user_id'], $context, $petId, $pet['owner_id']
        ));

        echo json_encode([
            'success' => true,
            'message' => 'Your message has been sent to the owner via email.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'We could not send the email right now. Please try again later.'
        ]);
    }

} catch (Exception $e) {
    error_log('Contact owner error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
}
