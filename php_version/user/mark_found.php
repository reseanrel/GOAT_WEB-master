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

$db = Database::getInstance();
$conn = $db->getConnection();

try {
    // Check if pet belongs to user and is lost
    $stmt = $conn->prepare("SELECT * FROM pets WHERE id = ? AND owner_id = ? AND lost = 1");
    $stmt->execute([$petId, $_SESSION['user_id']]);
    $pet = $stmt->fetch();

    if (!$pet) {
        echo json_encode(['success' => false, 'message' => 'Pet not found or not marked as lost']);
        exit();
    }

    // Update pet as found
    $stmt = $conn->prepare("UPDATE pets SET lost = 0 WHERE id = ?");
    $stmt->execute([$petId]);

    // Add comment to comments table
    $stmt = $conn->prepare("
        INSERT INTO comments (pet_id, user_id, comment, is_admin_reply, created_at)
        VALUES (?, ?, ?, 0, NOW())
    ");
    $stmt->execute([$petId, $_SESSION['user_id'], $comment]);

    echo json_encode(['success' => true, 'message' => 'Pet marked as found successfully']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>