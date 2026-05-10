<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$applicationId = $data['application_id'] ?? null;
$petId = $data['pet_id'] ?? null;

if (!$applicationId || !$petId) {
    echo json_encode(['success' => false, 'message' => 'All parameters are required']);
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

try {
    // Verify the application belongs to the current user and is completed
    $stmt = $conn->prepare("
        SELECT aa.*, p.adopted_by
        FROM adoption_applications aa
        JOIN pets p ON aa.pet_id = p.id
        WHERE aa.id = ? AND aa.pet_owner_id = ? AND aa.status = 'completed' AND aa.pet_id = ?
    ");
    $stmt->execute([$applicationId, $_SESSION['user_id'], $petId]);
    $application = $stmt->fetch();

    if (!$application) {
        echo json_encode(['success' => false, 'message' => 'Completed adoption not found or access denied']);
        exit();
    }

    // Undo adoption on pets table
    $stmt = $conn->prepare("
        UPDATE pets
        SET adoption_completed = 0, adopted_by = NULL, adoption_date = NULL, available_for_adoption = 1
        WHERE id = ?
    ");
    $stmt->execute([$petId]);

    // Move application back so it can be adopted again
    // (We return it to 'approved' since the adoption request was approved before finalization)
    $stmt = $conn->prepare("
        UPDATE adoption_applications
        SET status = 'approved', updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$applicationId]);

    echo json_encode(['success' => true, 'message' => 'Adoption has been undone. Pet is available for adoption again.']);
} catch (Exception $e) {
    error_log('Undo adoption error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
