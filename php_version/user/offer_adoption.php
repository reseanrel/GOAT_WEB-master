<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$petId = $data['pet_id'] ?? null;
$comment = $data['comment'] ?? '';

if (!$petId) {
    echo json_encode(['success' => false, 'message' => 'Pet ID is required']);
    exit();
}

if (empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Please provide information about the adoption']);
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

try {
    // Check if pet belongs to user and is not already available for adoption
    $stmt = $conn->prepare("SELECT * FROM pets WHERE id = ? AND owner_id = ? AND available_for_adoption = 0");
    $stmt->execute([$petId, $_SESSION['user_id']]);
    $pet = $stmt->fetch();

    if (!$pet) {
        echo json_encode(['success' => false, 'message' => 'Pet not found or already available for adoption']);
        exit();
    }

    // Update pet as available for adoption
    $stmt = $conn->prepare("UPDATE pets SET available_for_adoption = 1 WHERE id = ?");
    $stmt->execute([$petId]);

    // Add comment to comments table
    $stmt = $conn->prepare("
        INSERT INTO comments (pet_id, user_id, comment, is_admin_reply, created_at)
        VALUES (?, ?, ?, 0, NOW())
    ");
    $stmt->execute([$petId, $_SESSION['user_id'], "ADOPTION OFFER: " . $comment]);

    echo json_encode(['success' => true, 'message' => 'Pet offered for adoption successfully. It will be reviewed by administrators.']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>