<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

if (isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['pet_name']);
    $category = sanitizeInput($_POST['pet_category']);
    $petType = sanitizeInput($_POST['pet_type']);
    $age = !empty($_POST['age']) ? (int)$_POST['age'] : null;
    $color = sanitizeInput($_POST['color']);
    $gender = sanitizeInput($_POST['gender']);
    $availableForAdoption = isset($_POST['for_adoption']) ? 1 : 0;

    $errors = [];

    if (empty($name)) {
        $errors[] = 'Pet name is required';
    }

    if (empty($category)) {
        $errors[] = 'Pet category is required';
    }

    if (empty($errors)) {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        try {
            $stmt = $conn->prepare("
                INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, available_for_adoption, status, registered_on)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            $stmt->execute([$name, $category, $petType, $age, $color, $gender, $_SESSION['user_id'], $availableForAdoption]);

            $_SESSION['success'] = "Pet '$name' registered successfully and is pending admin approval!";
            header('Location: dashboard.php');
            exit();

        } catch (Exception $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}
?>

<?php include '../includes/header.php'; ?>

<style>
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-input, .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 16px;
        background: white;
    }

    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        padding-right: 2rem;
    }

    .toggle-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: white;
        cursor: pointer;
    }

    .toggle-label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }

    .toggle-switch {
        width: 36px;
        height: 20px;
        background: #d1d5db;
        border-radius: 20px;
        position: relative;
        transition: background-color 0.2s;
    }



    .btn-primary {
        width: 100%;
        padding: 0.75rem;
        background: #4f46e5;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 1rem;
    }

    .required {
        color: #ef4444;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div style="max-width: 600px; margin: 2rem auto; padding: 0 1rem;">
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h1 style="text-align: center; color: #374151; margin-bottom: 0.5rem;">Register New Pet</h1>
        <p style="text-align: center; color: #6b7280; margin-bottom: 2rem;">Add your pet to the registration system</p>

        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="pet_name">Pet Name <span class="required">*</span></label>
                    <input type="text" class="form-input" id="pet_name" name="pet_name" placeholder="Enter pet name" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Category <span class="required">*</span></label>
                    <select class="form-select" name="pet_category" required>
                        <option value="">Select Category</option>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Bird">Bird</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Breed/Type</label>
                    <input type="text" class="form-input" name="pet_type" placeholder="e.g., Golden Retriever">
                </div>

                <div class="form-group">
                    <label class="form-label">Age (years)</label>
                    <input type="number" class="form-input" name="age" min="0" max="30">
                </div>

                <div class="form-group">
                    <label class="form-label">Color</label>
                    <input type="text" class="form-input" name="color" placeholder="e.g., Brown & White">
                </div>

                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <select class="form-select" name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>


            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="for_adoption" style="width: 16px; height: 16px;">
                    Available for Adoption
                </label>
            </div>

            <button type="submit" class="btn-primary">Register Pet</button>
        </form>

        <p style="text-align: center; margin-top: 1rem; color: #6b7280;">
            <a href="dashboard.php" style="color: #4f46e5; text-decoration: none;">← Back to Dashboard</a>
        </p>
    </div>
</div>

<script>
// Basic form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = ['pet_name', 'pet_category'];
    let isValid = true;

    requiredFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field.value.trim()) {
            field.style.borderColor = '#ef4444';
            isValid = false;
        } else {
            field.style.borderColor = '#d1d5db';
        }
    });

    if (!isValid) {
        e.preventDefault();
    }
});
</script>

<?php include '../includes/footer.php'; ?>