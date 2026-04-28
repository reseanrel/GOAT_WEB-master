<?php
session_start();

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
        SELECT id, name, category, pet_type, age, color, gender, photo_url,
               available_for_adoption, lost, deceased, status
        FROM pets
        WHERE owner_id = ? AND archived = 0 AND status = 'approved'
        ORDER BY registered_on DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}
?>