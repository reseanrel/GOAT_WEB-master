<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$applicationId = $data['application_id'] ?? null;
$petId = $data['pet_id'] ?? null;
$adopterId = $data['adopter_id'] ?? null;
$reviewNotes = $data['review_notes'] ?? '';

if (!$applicationId || !$petId || !$adopterId) {
    echo json_encode(['success' => false, 'message' => 'All parameters are required']);
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

try {
    // Verify the application exists and is approved
    $stmt = $conn->prepare("
        SELECT aa.*, p.name as pet_name, po.full_name as owner_name,
               ap.full_name as adopter_name, ap.email as adopter_email
        FROM adoption_applications aa
        JOIN pets p ON aa.pet_id = p.id
        JOIN users po ON aa.pet_owner_id = po.id
        JOIN users ap ON aa.applicant_id = ap.id
        WHERE aa.id = ? AND aa.status = 'approved'
    ");
    $stmt->execute([$applicationId]);
    $application = $stmt->fetch();

    if (!$application) {
        echo json_encode(['success' => false, 'message' => 'Application not found or not approved']);
        exit();
    }

    // Mark pet as adopted
    $stmt = $conn->prepare("
        UPDATE pets
        SET adoption_completed = 1, adopted_by = ?, adoption_date = NOW(), available_for_adoption = 0
        WHERE id = ?
    ");
    $stmt->execute([$adopterId, $petId]);

    // Update application status to completed
    $stmt = $conn->prepare("
        UPDATE adoption_applications
        SET status = 'completed', reviewed_by = ?, reviewed_at = NOW(),
            review_notes = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        !empty($reviewNotes) ? $reviewNotes : 'Adoption finalized by administrator',
        $applicationId
    ]);

    // Add comment about successful adoption
    $stmt = $conn->prepare("
        INSERT INTO comments (pet_id, user_id, comment, is_admin_reply, created_at)
        VALUES (?, ?, ?, 1, NOW())
    ");
    $stmt->execute([
        $petId,
        $_SESSION['user_id'],
        "🎉 ADOPTION FINALIZED: Congratulations! {$application['pet_name']} has been successfully adopted by {$application['adopter_name']}. Adoption processed by administrator."
    ]);

    // Here you could add email notifications to both owner and adopter
    // For now, we'll just return success

    echo json_encode([
        'success' => true,
        'message' => 'Adoption finalized successfully! The pet has been marked as adopted.'
    ]);

} catch (Exception $e) {
    error_log('Finalize adoption error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>