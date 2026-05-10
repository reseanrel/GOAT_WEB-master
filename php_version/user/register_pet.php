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
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            $stmt->execute([
                $name,
                $category,
                $petType,
                $age,
                $color,
                $gender,
                $_SESSION['user_id'],
                $availableForAdoption
            ]);

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
    /* Modern “Register Pet” layout (uses theme vars from header.php) */
    .register-page {
        max-width: 780px;
        margin: 0 auto;
        padding: 16px 0 32px;
    }

    .page-card {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid var(--color-border, #e8eaed);
        border-radius: var(--radius-xl, 16px);
        box-shadow: var(--shadow-md, 0 1px 3px rgba(0,0,0,0.1));
        overflow: hidden;
    }

    .page-card__header {
        padding: 20px 22px;
        border-bottom: 1px solid var(--color-border, #e8eaed);
        background: linear-gradient(135deg, rgba(26, 115, 232, 0.1) 0%, rgba(138, 180, 248, 0.08) 100%);
    }

    .page-card__header h1 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        color: var(--color-text, #202124);
    }

    .page-card__header p {
        margin: 6px 0 0 0;
        color: var(--color-text-secondary, #5f6368);
        font-size: 14px;
    }

    .page-card__body {
        padding: 18px 22px 22px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 14px;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .form-field label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 600;
        color: var(--color-text-secondary, #5f6368);
    }

    .form-field .req {
        color: var(--color-error, #ea4335);
        margin-left: 4px;
        font-weight: 700;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border-radius: var(--radius-md, 8px);
        border: 1px solid var(--color-border, #e8eaed);
        background: #fff;
        outline: none;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
        font-size: 14px;
        color: var(--color-text, #202124);
    }

    .form-control:focus {
        border-color: var(--color-primary, #1a73e8);
        box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.18);
    }

    .checkbox-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 0 14px;
    }

    .checkbox-row input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 10px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        border: 1px solid var(--color-border, #e8eaed);
        padding: 11px 16px;
        border-radius: var(--radius-lg, 12px);
        cursor: pointer;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        transition: transform 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
        user-select: none;
    }

    .btn-primary {
        background: var(--color-primary, #1a73e8);
        border-color: var(--color-primary, #1a73e8);
        color: white;
        box-shadow: 0 8px 22px rgba(26, 115, 232, 0.25);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        background: var(--color-primary-hover, #1557b0);
        box-shadow: 0 10px 26px rgba(21, 87, 176, 0.30);
    }

    .btn-link {
        background: transparent;
        color: var(--color-primary, #1a73e8);
        border-color: transparent;
        font-weight: 700;
    }

    .btn-link:hover {
        background: rgba(26, 115, 232, 0.08);
        border-color: rgba(26, 115, 232, 0.15);
    }

    .hint {
        margin-top: 14px;
        font-size: 13px;
        color: var(--color-text-muted, #80868b);
    }
</style>

<div class="register-page">
    <div class="page-card">
        <div class="page-card__header">
            <h1>Register New Pet</h1>
            <p>Add your pet to the registration system</p>
        </div>

        <div class="page-card__body">
            <form method="POST">
                <div class="form-grid">
                    <div class="form-field">
                        <label for="pet_name">Pet Name <span class="req">*</span></label>
                        <input
                            type="text"
                            class="form-control"
                            id="pet_name"
                            name="pet_name"
                            placeholder="Enter pet name"
                            required
                        >
                    </div>

                    <div class="form-field">
                        <label>Category <span class="req">*</span></label>
                        <select class="form-control" name="pet_category" required>
                            <option value="">Select Category</option>
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                            <option value="Bird">Bird</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-field">
                        <label for="pet_type">Breed / Type</label>
                        <input
                            type="text"
                            class="form-control"
                            id="pet_type"
                            name="pet_type"
                            placeholder="e.g., Golden Retriever"
                        >
                    </div>

                    <div class="form-field">
                        <label for="age">Age (years)</label>
                        <input type="number" class="form-control" id="age" name="age" min="0" max="30">
                    </div>

                    <div class="form-field">
                        <label for="color">Color</label>
                        <input type="text" class="form-control" id="color" name="color" placeholder="e.g., Brown & White">
                    </div>

                    <div class="form-field">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="checkbox-row">
                    <input type="checkbox" id="for_adoption" name="for_adoption">
                    <label for="for_adoption" style="margin: 0; font-size: 14px; font-weight: 600; color: var(--color-text-secondary, #5f6368);">
                        Available for Adoption
                    </label>
                </div>

                <div class="actions">
                    <a class="btn btn-link" href="dashboard.php">
                        ← Back to Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paw"></i>
                        Register Pet
                    </button>
                </div>

                <div class="hint">
                    Submitting will place your pet in <b>pending</b> status until an admin reviews it.
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Basic form validation (keep lightweight; UI is handled by native required attributes)
    document.querySelector('form')?.addEventListener('submit', function (e) {
        const petName = document.querySelector('[name="pet_name"]');
        const petCategory = document.querySelector('[name="pet_category"]');
        let ok = true;

        if (petName && !petName.value.trim()) { ok = false; petName.style.borderColor = '#ea4335'; }
        if (petCategory && !petCategory.value.trim()) { ok = false; petCategory.style.borderColor = '#ea4335'; }

        if (!ok) e.preventDefault();
    });
</script>

<?php include '../includes/footer.php'; ?>
