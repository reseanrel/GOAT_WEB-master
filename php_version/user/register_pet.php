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
    $availableForAdoption = isset($_POST['for_adoption']);

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

<div class="row">
    <div class="col-md-12">
        <h2>Register New Pet</h2>
        <p class="text-muted">Add your pet to the registration system. Your pet will be pending admin approval.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pet_name" class="form-label">Pet Name *</label>
                            <input type="text" class="form-control" id="pet_name" name="pet_name" required
                                   value="<?php echo isset($_POST['pet_name']) ? htmlspecialchars($_POST['pet_name']) : ''; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="pet_category" class="form-label">Category *</label>
                            <select class="form-control" id="pet_category" name="pet_category" required>
                                <option value="">Select Category</option>
                                <option value="Dog" <?php echo (isset($_POST['pet_category']) && $_POST['pet_category'] == 'Dog') ? 'selected' : ''; ?>>Dog</option>
                                <option value="Cat" <?php echo (isset($_POST['pet_category']) && $_POST['pet_category'] == 'Cat') ? 'selected' : ''; ?>>Cat</option>
                                <option value="Bird" <?php echo (isset($_POST['pet_category']) && $_POST['pet_category'] == 'Bird') ? 'selected' : ''; ?>>Bird</option>
                                <option value="Other" <?php echo (isset($_POST['pet_category']) && $_POST['pet_category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pet_type" class="form-label">Breed/Type</label>
                            <input type="text" class="form-control" id="pet_type" name="pet_type"
                                   value="<?php echo isset($_POST['pet_type']) ? htmlspecialchars($_POST['pet_type']) : ''; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label">Age (years)</label>
                            <input type="number" class="form-control" id="age" name="age" min="0" max="30"
                                   value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="color" class="form-label">Color</label>
                            <input type="text" class="form-control" id="color" name="color"
                                   value="<?php echo isset($_POST['color']) ? htmlspecialchars($_POST['color']) : ''; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="for_adoption" name="for_adoption"
                                   <?php echo isset($_POST['for_adoption']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="for_adoption">
                                Available for adoption
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Register Pet</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>