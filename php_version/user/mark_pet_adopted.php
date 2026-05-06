<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$applicationId = $data['application_id'] ?? null;
$petId = $data['pet_id'] ?? null;
$adopterId = $data['adopter_id'] ?? null;

if (!$applicationId || !$petId || !$adopterId) {
    echo json_encode(['success' => false, 'message' => 'All parameters are required']);
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

try {
    // Verify the application belongs to the current user and is approved
    $stmt = $conn->prepare("
        SELECT aa.*, p.name as pet_name, u.full_name as adopter_name
        FROM adoption_applications aa
        JOIN pets p ON aa.pet_id = p.id
        JOIN users u ON aa.applicant_id = u.id
        WHERE aa.id = ? AND aa.pet_owner_id = ? AND aa.status = 'approved'
    ");
    $stmt->execute([$applicationId, $_SESSION['user_id']]);
    $application = $stmt->fetch();

    if (!$application) {
        echo json_encode(['success' => false, 'message' => 'Application not found, not approved, or access denied']);
        exit();
    }

    // Mark pet as adopted
    $stmt = $conn->prepare("
        UPDATE pets
        SET adoption_completed = 1, adopted_by = ?, adoption_date = NOW(), available_for_adoption = 0
        WHERE id = ?
    ");
    $stmt->execute([$adopterId, $petId]);

    // Update application status to completed (we'll add a completed status)
    $stmt = $conn->prepare("
        UPDATE adoption_applications
        SET status = 'completed', updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$applicationId]);

    // Add comment about successful adoption
    $stmt = $conn->prepare("
        INSERT INTO comments (pet_id, user_id, comment, is_admin_reply, created_at)
        VALUES (?, ?, ?, 0, NOW())
    ");
    $stmt->execute([
        $petId,
        $_SESSION['user_id'],
        "🎉 PET SUCCESSFULLY ADOPTED: Congratulations! {$application['pet_name']} has found a loving home with {$application['adopter_name']}."
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Pet marked as adopted successfully! Congratulations on finding your pet a loving home.'
    ]);

} catch (Exception $e) {
    error_log('Mark pet adopted error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>