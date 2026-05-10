<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../adoption.php');
    exit();
}

$petId = $_POST['pet_id'] ?? null;
if (!$petId) {
    header('Location: ../adoption.php');
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

// Verify pet exists and is available for adoption
$stmt = $conn->prepare("
    SELECT p.*, u.id as owner_id
    FROM pets p
    JOIN users u ON p.owner_id = u.id
    WHERE p.id = ? AND p.available_for_adoption = 1 AND p.archived = 0 AND p.status = 'approved' AND p.deceased = 0
");
$stmt->execute([$petId]);
$pet = $stmt->fetch();

if (!$pet) {
    $_SESSION['error'] = 'Pet not found or not available for adoption.';
    header('Location: ../adoption.php');
    exit();
}

// Check if user already applied
$stmt = $conn->prepare("
    SELECT id FROM adoption_applications
    WHERE pet_id = ? AND applicant_id = ? AND status IN ('pending', 'under_review', 'approved')
");
$stmt->execute([$petId, $_SESSION['user_id']]);
$existingApplication = $stmt->fetch();

if ($existingApplication) {
    $_SESSION['error'] = 'You have already submitted an application for this pet.';
    header('Location: ../adoption.php');
    exit();
}

// Validate required fields
$requiredFields = [
    'full_name', 'email', 'phone', 'age', 'address',
    'housing_type', 'household_members', 'adoption_reason', 'pet_experience',
    'preferred_contact', 'emergency_contact_name', 'emergency_contact_phone'
];

foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = 'Please fill in all required fields.';
        header('Location: adoption_application.php?pet_id=' . $petId);
        exit();
    }
}

if (!isset($_POST['terms_agreed']) || $_POST['terms_agreed'] !== 'on') {
    $_SESSION['error'] = 'You must agree to the terms to submit an application.';
    header('Location: adoption_application.php?pet_id=' . $petId);
    exit();
}

if ((int)$_POST['age'] < 18) {
    $_SESSION['error'] = 'You must be at least 18 years old to adopt a pet.';
    header('Location: adoption_application.php?pet_id=' . $petId);
    exit();
}

try {
    // Insert adoption application
    $stmt = $conn->prepare("
        INSERT INTO adoption_applications (
            pet_id, applicant_id, pet_owner_id, applicant_full_name, applicant_email,
            applicant_phone, applicant_address, applicant_age, household_members,
            has_other_pets, other_pets_details, housing_type, has_yard,
            adoption_reason, pet_experience, preferred_contact_method,
            emergency_contact_name, emergency_contact_phone, reference_name,
            reference_phone, home_visit_allowed, additional_notes
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

    $stmt->execute([
        $petId,
        $_SESSION['user_id'],
        $pet['owner_id'],
        trim($_POST['full_name']),
        trim($_POST['email']),
        trim($_POST['phone']),
        trim($_POST['address']),
        (int)$_POST['age'],
        (int)$_POST['household_members'],
        isset($_POST['has_other_pets']) ? (int)$_POST['has_other_pets'] : 0,
        trim($_POST['other_pets_details'] ?? ''),
        $_POST['housing_type'],
        isset($_POST['has_yard']) ? (int)$_POST['has_yard'] : 0,
        trim($_POST['adoption_reason']),
        trim($_POST['pet_experience']),
        $_POST['preferred_contact'],
        trim($_POST['emergency_contact_name']),
        trim($_POST['emergency_contact_phone']),
        trim($_POST['reference_name'] ?? ''),
        trim($_POST['reference_phone'] ?? ''),
        isset($_POST['home_visit_allowed']) ? (int)$_POST['home_visit_allowed'] : 1,
        trim($_POST['additional_notes'] ?? '')
    ]);

    $applicationId = $conn->lastInsertId();

    // Add notification/comment to the pet
    $stmt = $conn->prepare("
        INSERT INTO comments (pet_id, user_id, comment, is_admin_reply, created_at)
        VALUES (?, ?, ?, 0, NOW())
    ");
    $stmt->execute([
        $petId,
        $_SESSION['user_id'],
        "ADOPTION APPLICATION SUBMITTED: New adoption application received from " . trim($_POST['full_name'])
    ]);

    // Send email notification to pet owner (if email functionality exists)
    // This would typically use a mail library like PHPMailer

    $_SESSION['success'] = 'Your adoption application has been submitted successfully! The pet owner will review your application and contact you soon.';

    header('Location: ../adoption.php');
    exit();

} catch (Exception $e) {
    error_log('Adoption application error: ' . $e->getMessage());
    $_SESSION['error'] = 'An error occurred while submitting your application. Please try again.';
    header('Location: adoption_application.php?pet_id=' . $petId);
    exit();
}
?>
