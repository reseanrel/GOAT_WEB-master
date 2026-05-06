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
$newStatus = $data['status'] ?? null;

if (!$applicationId || !$newStatus) {
    echo json_encode(['success' => false, 'message' => 'Application ID and status are required']);
    exit();
}

$validStatuses = ['pending', 'under_review', 'approved', 'rejected', 'withdrawn'];
if (!in_array($newStatus, $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

try {
    // Verify the application belongs to the current user
    $stmt = $conn->prepare("
        SELECT aa.*, p.name as pet_name
        FROM adoption_applications aa
        JOIN pets p ON aa.pet_id = p.id
        WHERE aa.id = ? AND aa.pet_owner_id = ?
    ");
    $stmt->execute([$applicationId, $_SESSION['user_id']]);
    $application = $stmt->fetch();

    if (!$application) {
        echo json_encode(['success' => false, 'message' => 'Application not found or access denied']);
        exit();
    }

    // Update application status
    $stmt = $conn->prepare("
        UPDATE adoption_applications
        SET status = ?, reviewed_by = ?, reviewed_at = NOW(), updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$newStatus, $_SESSION['user_id'], $applicationId]);

    // Add comment about status change
    $statusMessages = [
        'under_review' => 'Application is now under review',
        'approved' => 'Application has been approved',
        'rejected' => 'Application has been rejected',
        'withdrawn' => 'Application has been withdrawn'
    ];

    $stmt = $conn->prepare("
        INSERT INTO comments (pet_id, user_id, comment, is_admin_reply, created_at)
        VALUES (?, ?, ?, 0, NOW())
    ");
    $stmt->execute([
        $application['pet_id'],
        $_SESSION['user_id'],
        "ADOPTION APPLICATION UPDATE: " . ($statusMessages[$newStatus] ?? "Status changed to " . ucfirst(str_replace('_', ' ', $newStatus)))
    ]);

    // If approved, mark pet as no longer available for adoption (but don't complete adoption yet)
    if ($newStatus === 'approved') {
        $stmt = $conn->prepare("UPDATE pets SET available_for_adoption = 0 WHERE id = ?");
        $stmt->execute([$application['pet_id']]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Application status updated successfully',
        'new_status' => $newStatus
    ]);

} catch (Exception $e) {
    error_log('Update adoption application error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>