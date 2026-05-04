<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$petId = (int)$_GET['id'];

$db = Database::getInstance();
$conn = $db->getConnection();

// Get pet details
$stmt = $conn->prepare("SELECT * FROM pets WHERE id = ? AND owner_id = ?");
$stmt->execute([$petId, $_SESSION['user_id']]);
$pet = $stmt->fetch();

if (!$pet) {
    $_SESSION['error'] = 'Pet not found or access denied.';
    header('Location: dashboard.php');
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

    $photoPath = $pet['photo']; // Keep existing photo by default

    // Handle file upload (only if a new file is uploaded)
    if (isset($_FILES['pet_photo']) && $_FILES['pet_photo']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $fileName = basename($_FILES['pet_photo']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowedExts)) {
            $newFileName = uniqid('pet_') . '.' . $fileExt;
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['pet_photo']['tmp_name'], $uploadPath)) {
                // Delete old photo if it exists
                if ($pet['photo'] && file_exists($uploadDir . $pet['photo'])) {
                    unlink($uploadDir . $pet['photo']);
                }
                $photoPath = $newFileName;
            } else {
                $errors[] = 'Failed to upload photo';
            }
        } else {
            $errors[] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
        }
    }

    $errors = [];

    if (empty($name)) {
        $errors[] = 'Pet name is required';
    }

    if (empty($category)) {
        $errors[] = 'Pet category is required';
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("
                UPDATE pets SET
                    name = ?, category = ?, pet_type = ?, age = ?, color = ?,
                    gender = ?, available_for_adoption = ?, photo_url = ?
                WHERE id = ? AND owner_id = ?
            ");
            $stmt->execute([
                $name, $category, $petType, $age, $color, $gender,
                $availableForAdoption, $photoPath, $petId, $_SESSION['user_id']
            ]);

            $_SESSION['success'] = "Pet '$name' details updated successfully!";
            header('Location: pet_details.php?id=' . $petId);
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
    :root {
        --primary-color: #4F46E5;
        --primary-hover: #3730a3;
        --bg-light: #f8fafc;
        --card-bg: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --radius: 12px;
        --transition: all 0.2s ease;
    }

    body {
        background-color: var(--bg-light) !important;
    }

    .registration-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }

    .registration-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 2.5rem;
        width: 100%;
        max-width: 800px;
        position: relative;
        overflow: hidden;
    }

    .registration-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), #8b5cf6);
    }

    .form-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .form-header h1 {
        color: var(--text-primary);
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .form-header p {
        color: var(--text-secondary);
        font-size: 1rem;
        margin: 0;
    }

    .image-upload-section {
        margin-bottom: 2rem;
        text-align: center;
    }

    .image-upload-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto 1rem;
        border-radius: 50%;
        border: 3px dashed var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
        overflow: hidden;
    }

    .image-upload-container:hover {
        border-color: var(--primary-color);
        background-color: rgba(79, 70, 229, 0.05);
    }

    .image-upload-container.has-image {
        border-style: solid;
        border-color: var(--primary-color);
    }

    .image-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        display: none;
    }

    .image-preview.show {
        display: block;
    }

    .upload-icon {
        font-size: 2.5rem;
        color: var(--text-secondary);
        transition: var(--transition);
    }

    .image-upload-container:hover .upload-icon {
        color: var(--primary-color);
        transform: scale(1.1);
    }

    .upload-text {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-top: 0.5rem;
    }

    #pet_photo {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .form-group {
        position: relative;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .form-input {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 3rem;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 1rem;
        color: var(--text-primary);
        background-color: var(--card-bg);
        transition: var(--transition);
        font-family: inherit;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .form-input::placeholder {
        color: var(--text-secondary);
    }

    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 3rem;
    }

    .input-icon {
        position: absolute;
        left: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        font-size: 1.125rem;
        z-index: 1;
    }

    .toggle-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        background-color: var(--card-bg);
        transition: var(--transition);
        cursor: pointer;
    }

    .toggle-container:hover {
        border-color: var(--primary-color);
    }

    .toggle-label {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .toggle-switch {
        position: relative;
        width: 50px;
        height: 24px;
        background-color: var(--border-color);
        border-radius: 12px;
        transition: var(--transition);
    }

    .toggle-switch::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        background-color: white;
        border-radius: 50%;
        transition: var(--transition);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .toggle-container.active .toggle-switch {
        background-color: var(--primary-color);
    }

    .toggle-container.active .toggle-switch::after {
        transform: translateX(26px);
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.875rem 2rem;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: var(--transition);
        cursor: pointer;
        border: none;
        font-family: inherit;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .btn-outline {
        background-color: transparent;
        color: var(--text-secondary);
        border: 2px solid var(--border-color);
    }

    .btn-outline:hover {
        background-color: var(--text-secondary);
        color: white;
        transform: translateY(-1px);
    }

    @media (max-width: 640px) {
        .registration-card {
            padding: 1.5rem;
        }

        .form-header h1 {
            font-size: 1.5rem;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }

    .required {
        color: #ef4444;
    }

    .current-photo-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }
</style>

<div class="registration-container">
    <div class="registration-card">
        <div class="form-header">
            <h1><i class="fas fa-edit" style="color: var(--primary-color); margin-right: 0.5rem;"></i>Edit Pet Details</h1>
            <p>Update information for <?php echo htmlspecialchars($pet['name']); ?></p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <!-- Image Upload Section -->
            <div class="image-upload-section">
                <div class="image-upload-container <?php echo $pet['photo_url'] ? 'has-image' : ''; ?>" onclick="document.getElementById('pet_photo').click()">

                        <img id="imagePreview" class="image-preview show" src="../uploads/<?php echo htmlspecialchars($pet['photo_url']); ?>" alt="Current Pet Photo">
                    <?php else: ?>
                        <i class="fas fa-camera upload-icon"></i>
                    <?php endif; ?>
                    <input type="file" id="pet_photo" name="pet_photo" accept="image/*" style="display: none;">
                </div>
                <p class="upload-text">Click to change photo</p>
                <?php if ($pet['photo']): ?>
                    <p class="current-photo-label">Current photo will be replaced if you upload a new one</p>
                <?php endif; ?>
            </div>

            <!-- Form Fields -->
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="pet_name">
                        Pet Name <span class="required">*</span>
                    </label>
                    <i class="fas fa-tag input-icon"></i>
                    <input type="text" class="form-input" id="pet_name" name="pet_name" placeholder="Enter pet name"
                           value="<?php echo htmlspecialchars($pet['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="pet_category">
                        Category <span class="required">*</span>
                    </label>
                    <i class="fas fa-list input-icon"></i>
                    <select class="form-input form-select" id="pet_category" name="pet_category" required>
                        <option value="">Select Category</option>
                        <option value="Dog" <?php echo ($pet['category'] == 'Dog') ? 'selected' : ''; ?>>🐕 Dog</option>
                        <option value="Cat" <?php echo ($pet['category'] == 'Cat') ? 'selected' : ''; ?>>🐱 Cat</option>
                        <option value="Bird" <?php echo ($pet['category'] == 'Bird') ? 'selected' : ''; ?>>🐦 Bird</option>
                        <option value="Other" <?php echo ($pet['category'] == 'Other') ? 'selected' : ''; ?>>🐾 Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="pet_type">Breed/Type</label>
                    <i class="fas fa-dna input-icon"></i>
                    <input type="text" class="form-input" id="pet_type" name="pet_type" placeholder="e.g., Golden Retriever"
                           value="<?php echo htmlspecialchars($pet['pet_type'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="age">Age (years)</label>
                    <i class="fas fa-birthday-cake input-icon"></i>
                    <input type="number" class="form-input" id="age" name="age" placeholder="Enter age" min="0" max="30"
                           value="<?php echo htmlspecialchars($pet['age'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="color">Color</label>
                    <i class="fas fa-palette input-icon"></i>
                    <input type="text" class="form-input" id="color" name="color" placeholder="e.g., Brown & White"
                           value="<?php echo htmlspecialchars($pet['color'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="gender">Gender</label>
                    <i class="fas fa-venus-mars input-icon"></i>
                    <select class="form-input form-select" id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($pet['gender'] == 'Male') ? 'selected' : ''; ?>>♂️ Male</option>
                        <option value="Female" <?php echo ($pet['gender'] == 'Female') ? 'selected' : ''; ?>>♀️ Female</option>
                    </select>
                </div>
            </div>

            <!-- Toggle Switch -->
            <div class="toggle-container <?php echo $pet['available_for_adoption'] ? 'active' : ''; ?>" onclick="toggleAdoption()">
                <p class="toggle-label">Available for Adoption</p>
                <div class="toggle-switch"></div>
                <input type="checkbox" id="for_adoption" name="for_adoption" style="display: none;"
                       <?php echo $pet['available_for_adoption'] ? 'checked' : ''; ?>>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="pet_details.php?id=<?php echo $petId; ?>" class="btn btn-outline">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Pet
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Image preview functionality
    document.getElementById('pet_photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        const container = document.querySelector('.image-upload-container');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.add('show');
                container.classList.add('has-image');
            };
            reader.readAsDataURL(file);
        }
    });

    // Toggle switch functionality
    function toggleAdoption() {
        const container = document.querySelector('.toggle-container');
        const checkbox = document.getElementById('for_adoption');

        container.classList.toggle('active');
        checkbox.checked = !checkbox.checked;
    }

    // Initialize toggle state on page load
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('for_adoption');
        const container = document.querySelector('.toggle-container');

        if (checkbox.checked) {
            container.classList.add('active');
        }
    });

    // Form validation enhancement
    document.querySelector('form').addEventListener('submit', function(e) {
        const requiredFields = ['pet_name', 'pet_category'];
        let isValid = true;

        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field.value.trim()) {
                field.style.borderColor = '#ef4444';
                isValid = false;
            } else {
                field.style.borderColor = 'var(--border-color)';
            }
        });

        if (!isValid) {
            e.preventDefault();
            // Could add error message here
        }
    });
</script>

<?php include '../includes/footer.php'; ?>