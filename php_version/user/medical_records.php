<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

if (!isset($_GET['pet_id']) || !is_numeric($_GET['pet_id'])) {
    header('Location: dashboard.php');
    exit();
}

$petId = (int)$_GET['pet_id'];

$db = Database::getInstance();
$conn = $db->getConnection();

// Verify pet ownership
$stmt = $conn->prepare("SELECT * FROM pets WHERE id = ? AND owner_id = ? AND archived = 0");
$stmt->execute([$petId, $_SESSION['user_id']]);
$pet = $stmt->fetch();

if (!$pet) {
    $_SESSION['error'] = 'Pet not found or access denied.';
    header('Location: dashboard.php');
    exit();
}

// Handle adding new medical record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_record'])) {
    $recordType = sanitizeInput($_POST['record_type']);
    $recordDate = $_POST['record_date'];
    $description = sanitizeInput($_POST['description']);
    $nextDueDate = !empty($_POST['next_due_date']) ? $_POST['next_due_date'] : null;

    if (empty($recordType) || empty($recordDate) || empty($description)) {
        $_SESSION['error'] = 'Please fill all required fields';
    } else {
        try {
            $stmt = $conn->prepare("
                INSERT INTO medical_records (pet_id, record_type, record_date, description, next_due_date)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$petId, $recordType, $recordDate, $description, $nextDueDate]);
            $_SESSION['success'] = 'Medical record added successfully!';
            header('Location: medical_records.php?pet_id=' . $petId);
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Get medical records
$stmt = $conn->prepare("
    SELECT * FROM medical_records
    WHERE pet_id = ?
    ORDER BY record_date DESC
");
$stmt->execute([$petId]);
$medicalRecords = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<style>
    .medical-records {
        max-width: 1200px;
        margin: 0 auto;
    }

    .pet-header {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        padding: var(--spacing-xl);
        margin-bottom: var(--spacing-xl);
        border: 1px solid var(--color-border);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: var(--spacing-lg);
    }

    .pet-image {
        width: 80px;
        height: 80px;
        border-radius: var(--radius-lg);
        object-fit: cover;
        border: 2px solid var(--color-border);
    }

    .pet-image-placeholder {
        width: 80px;
        height: 80px;
        background: var(--color-bg-secondary);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-text-muted);
        font-size: 32px;
        border: 2px solid var(--color-border);
    }

    .pet-info h1 {
        font-size: 24px;
        font-weight: 600;
        color: var(--color-text);
        margin: 0 0 var(--spacing-xs);
    }

    .pet-category {
        color: var(--color-text-secondary);
        font-size: 14px;
        margin: 0;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: var(--spacing-xl);
    }

    .main-content {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xl);
    }

    .content-card {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        padding: var(--spacing-xl);
        border: 1px solid var(--color-border);
        box-shadow: var(--shadow-sm);
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-text);
        margin: 0 0 var(--spacing-lg);
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .add-record-form {
        background: var(--color-bg-secondary);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        border: 1px solid var(--color-border);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
    }

    .form-group {
        margin-bottom: var(--spacing-lg);
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: var(--color-text);
        margin-bottom: var(--spacing-sm);
    }

    .form-input, .form-textarea, .form-select {
        width: 100%;
        padding: var(--spacing-sm) var(--spacing-md);
        border: 2px solid var(--color-border);
        border-radius: var(--radius-md);
        font-size: 14px;
        background: var(--color-bg);
        color: var(--color-text);
        transition: border-color 0.2s ease;
    }

    .form-input:focus, .form-textarea:focus, .form-select:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .btn-submit {
        background: var(--color-primary);
        color: white;
        border: none;
        padding: var(--spacing-md) var(--spacing-xl);
        border-radius: var(--radius-lg);
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        justify-content: center;
    }

    .btn-submit:hover {
        background: var(--color-primary-hover);
        transform: translateY(-1px);
    }

    .records-list {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-lg);
    }

    .record-item {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        padding: var(--spacing-xl);
        border: 1px solid var(--color-border);
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
    }

    .record-item:hover {
        box-shadow: var(--shadow-md);
        border-color: var(--color-border-hover);
    }

    .record-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: var(--spacing-lg);
        padding-bottom: var(--spacing-md);
        border-bottom: 1px solid var(--color-border);
    }

    .record-type {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .record-date {
        font-size: 14px;
        color: var(--color-text-secondary);
        background: var(--color-bg-secondary);
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-md);
    }

    .record-description {
        color: var(--color-text-secondary);
        line-height: 1.6;
        margin-bottom: var(--spacing-lg);
    }

    .record-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        color: var(--color-text-muted);
    }

    .next-due {
        background: var(--color-warning);
        color: white;
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-md);
        font-size: 12px;
        font-weight: 500;
    }

    .no-records {
        text-align: center;
        padding: var(--spacing-2xl);
        color: var(--color-text-secondary);
    }

    .no-records-icon {
        font-size: 64px;
        margin-bottom: var(--spacing-lg);
        opacity: 0.5;
    }

    .sidebar-card {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        padding: var(--spacing-xl);
        border: 1px solid var(--color-border);
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: var(--spacing-xl);
    }

    .sidebar-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--color-text);
        margin: 0 0 var(--spacing-lg);
    }

    .quick-stats {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-md);
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--spacing-md);
        background: var(--color-bg-secondary);
        border-radius: var(--radius-md);
    }

    .stat-label {
        font-weight: 500;
        color: var(--color-text-secondary);
        font-size: 14px;
    }

    .stat-value {
        font-weight: 600;
        color: var(--color-text);
        font-size: 16px;
    }

    .btn-back {
        background: var(--color-bg-secondary);
        color: var(--color-text);
        border: 2px solid var(--color-border);
        padding: var(--spacing-md) var(--spacing-xl);
        border-radius: var(--radius-lg);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-sm);
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-back:hover {
        background: var(--color-text);
        color: var(--color-bg);
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .sidebar-card {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .pet-header {
            flex-direction: column;
            text-align: center;
        }

        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="medical-records">
    <div class="pet-header">
        <div class="pet-image placeholder">
            <i class="fas fa-paw"></i>
        </div>

        <div class="pet-info">
            <h1><?php echo htmlspecialchars($pet['name']); ?>'s Medical Records</h1>
            <p class="pet-category">
                <?php echo htmlspecialchars($pet['category'] ?? 'Not specified'); ?> •
                <?php echo htmlspecialchars($pet['pet_type'] ?? 'Not specified'); ?> •
                <?php echo $pet['age'] ? $pet['age'] . ' years old' : 'Age not specified'; ?>
            </p>
        </div>
    </div>

    <div class="content-grid">
        <div class="main-content">
            <div class="content-card">
                <h2 class="card-title">
                    <i class="fas fa-plus-circle"></i>
                    Add New Medical Record
                </h2>
                <form method="POST" class="add-record-form">
                    <input type="hidden" name="add_record" value="1">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="record_type" class="form-label">Record Type *</label>
                            <select name="record_type" id="record_type" class="form-select" required>
                                <option value="">Select Record Type</option>
                                <option value="Vaccination">Vaccination</option>
                                <option value="Check-up">Check-up</option>
                                <option value="Treatment">Treatment</option>
                                <option value="Surgery">Surgery</option>
                                <option value="Medication">Medication</option>
                                <option value="Test Results">Test Results</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="record_date" class="form-label">Date *</label>
                            <input type="date" name="record_date" id="record_date" class="form-input" required
                                   value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label for="description" class="form-label">Description/Details *</label>
                        <textarea name="description" id="description" class="form-textarea" required
                                  placeholder="Describe the medical record, treatment given, or observations..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="next_due_date" class="form-label">Next Due Date (Optional)</label>
                        <input type="date" name="next_due_date" id="next_due_date" class="form-input">
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i>
                        Add Medical Record
                    </button>
                </form>
            </div>

            <div class="content-card">
                <h2 class="card-title">
                    <i class="fas fa-history"></i>
                    Medical History
                </h2>
                <?php if (empty($medicalRecords)): ?>
                    <div class="no-records">
                        <div class="no-records-icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <h4>No medical records yet</h4>
                        <p>Add your first medical record using the form above.</p>
                    </div>
                <?php else: ?>
                    <div class="records-list">
                        <?php foreach ($medicalRecords as $record): ?>
                            <div class="record-item">
                                <div class="record-header">
                                    <h3 class="record-type">
                                        <i class="fas fa-notes-medical"></i>
                                        <?php echo htmlspecialchars($record['record_type']); ?>
                                    </h3>
                                    <div class="record-date">
                                        <?php echo date('M j, Y', strtotime($record['record_date'])); ?>
                                    </div>
                                </div>
                                <div class="record-description">
                                    <?php echo nl2br(htmlspecialchars($record['description'])); ?>
                                </div>
                                <div class="record-meta">
                                    <span>Record #<?php echo $record['id']; ?></span>
                                    <?php if ($record['next_due_date']): ?>
                                        <span class="next-due">
                                            Next Due: <?php echo date('M j, Y', strtotime($record['next_due_date'])); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="sidebar-card">
            <h3 class="sidebar-title">Quick Stats</h3>
            <div class="quick-stats">
                <div class="stat-item">
                    <span class="stat-label">Total Records</span>
                    <span class="stat-value"><?php echo count($medicalRecords); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Last Updated</span>
                    <span class="stat-value">
                        <?php echo !empty($medicalRecords) ? date('M j', strtotime($medicalRecords[0]['record_date'])) : 'Never'; ?>
                    </span>
                </div>
            </div>

            <div style="margin-top: var(--spacing-xl);">
                <a href="pet_details.php?id=<?php echo $pet['id']; ?>" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Back to Pet Details
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>