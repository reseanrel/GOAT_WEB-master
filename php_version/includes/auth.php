<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ../login.php');
        exit();
    }
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_hash($password, PASSWORD_DEFAULT) === $hash;
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function getUserById($userId) {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND archived = 0");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

function getPetById($petId) {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        SELECT p.*, u.full_name as owner_name, u.email as owner_email,
               u.contact_number as owner_contact, u.address as owner_address
        FROM pets p
        JOIN users u ON p.owner_id = u.id
        WHERE p.id = ? AND p.archived = 0
    ");
    $stmt->execute([$petId]);
    return $stmt->fetch();
}

function getUserPets($userId) {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        SELECT id, name, category, pet_type, age, color, gender, photo_url AS photo_path,
               available_for_adoption, lost, deceased, status
        FROM pets
        WHERE owner_id = ? AND archived = 0 AND status = 'approved'
        ORDER BY registered_on DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getResidencyStatus($userId = null) {
    if ($userId === null) {
        $userId = $_SESSION['user_id'] ?? 0;
    }
    if (!$userId) return 'unverified';

    $db = Database::getInstance();
    $conn = $db->getConnection();

    try {
        $stmt = $conn->prepare("SELECT residency_status FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row && !empty($row['residency_status']) ? $row['residency_status'] : 'unverified';
    } catch (PDOException $e) {
        // Column doesn't exist yet (migration not run) — safe default
        return 'unverified';
    }
}

function isResidencyVerified($userId = null) {
    return getResidencyStatus($userId) === 'verified';
}

function getUserResidencyInfo($userId) {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    try {
        $stmt = $conn->prepare("
            SELECT residency_status, residency_document, residency_rejection_reason,
                   residency_verified_at, residency_verified_by
            FROM users WHERE id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        // Migration not run yet — return safe empty structure
        return [
            'residency_status' => 'unverified',
            'residency_document' => null,
            'residency_rejection_reason' => null,
            'residency_verified_at' => null,
            'residency_verified_by' => null
        ];
    }
}

/**
 * Get the photo source for a pet.
 * Returns the uploaded photo if present.
 * Returns null (display nothing) if the user did not upload a photo.
 * No default files are used.
 */
function getPetPhotoSrc($pet) {
    $baseUploads = dirname(__DIR__) . '/uploads/';

    if (!empty($pet['photo_path']) || !empty($pet['photo_url'])) {
        $filename = $pet['photo_path'] ?? $pet['photo_url'];
        $fullPath = $baseUploads . $filename;

        if (file_exists($fullPath)) {
            return '../uploads/' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8');
        }
    }

    return null;
}
?>
