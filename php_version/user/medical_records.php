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

// Detect if editing a record
$editingRecord = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM medical_records WHERE id = ? AND pet_id = ?");
    $stmt->execute([$editId, $petId]);
    $editingRecord = $stmt->fetch() ?: null;
}

// Handle add / update / delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_record']) || isset($_POST['update_record'])) {
        $isUpdate = isset($_POST['update_record']);
        $recordId = $isUpdate ? (int)($_POST['record_id'] ?? 0) : 0;

        $recordType = sanitizeInput($_POST['record_type'] ?? '');
        $recordDate = $_POST['record_date'] ?? '';
        $description = sanitizeInput($_POST['description'] ?? '');
        $provider = sanitizeInput($_POST['provider'] ?? '');
        $nextDueDate = !empty($_POST['next_due_date']) ? $_POST['next_due_date'] : null;

        $currentPhoto = null;
        if ($isUpdate) {
            $stmt = $conn->prepare("SELECT id, photo_url FROM medical_records WHERE id = ? AND pet_id = ?");
            $stmt->execute([$recordId, $petId]);
            $existing = $stmt->fetch();
            if (!$existing) {
                $_SESSION['error'] = 'Record not found or access denied.';
                header('Location: medical_records.php?pet_id=' . $petId);
                exit();
            }
            $currentPhoto = $existing['photo_url'] ?? null;
        }

        $errors = [];
        if (empty($recordType) || empty($recordDate) || empty($description)) {
            $errors[] = 'Please fill all required fields.';
        }

        // Handle optional attachment upload (images or PDF)
        $uploadedPhotoPath = null;
        if (isset($_FILES['attachment']) && is_array($_FILES['attachment'])) {
            $fileErr = $_FILES['attachment']['error'] ?? UPLOAD_ERR_NO_FILE;
            if ($fileErr !== UPLOAD_ERR_NO_FILE) {
                if ($fileErr !== UPLOAD_ERR_OK) {
                    $errors[] = 'Attachment upload failed. Please try again.';
                } else {
                    $tmpPath = $_FILES['attachment']['tmp_name'];
                    $originalName = (string)($_FILES['attachment']['name'] ?? '');
                    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf'];
                    if (!in_array($extension, $allowedExtensions, true)) {
                        $errors[] = 'Unsupported attachment type. Allowed: jpg, jpeg, png, webp, gif, pdf.';
                    }

                    $maxSizeBytes = 16 * 1024 * 1024; // 16MB
                    $sizeBytes = (int)($_FILES['attachment']['size'] ?? 0);
                    if ($sizeBytes <= 0) {
                        $errors[] = 'Invalid attachment file.';
                    } elseif ($sizeBytes > $maxSizeBytes) {
                        $errors[] = 'Attachment is too large (max 16MB).';
                    }

                    if (empty($errors)) {
                        $uploadsDir = dirname(__DIR__) . '/uploads';
                        if (!is_dir($uploadsDir)) {
                            if (!mkdir($uploadsDir, 0755, true) && !is_dir($uploadsDir)) {
                                $errors[] = 'Server error: uploads directory unavailable.';
                            }
                        }

                        if (empty($errors) && is_dir($uploadsDir) && is_uploaded_file($tmpPath)) {
                            $newFileName = 'medical_' . $petId . '_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
                            $destination = $uploadsDir . DIRECTORY_SEPARATOR . $newFileName;

                            if (move_uploaded_file($tmpPath, $destination)) {
                                $uploadedPhotoPath = $newFileName;
                            } else {
                                $errors[] = 'Server error: could not save the attachment.';
                            }
                        }
                    }
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
        } else {
            $photoToSave = $uploadedPhotoPath ?? ($isUpdate ? $currentPhoto : null);
            try {
                if ($isUpdate) {
                    $stmt = $conn->prepare("
                        UPDATE medical_records
                        SET record_type = ?, record_date = ?, description = ?, next_due_date = ?, provider = ?, photo_url = ?
                        WHERE id = ? AND pet_id = ?
                    ");
                    $stmt->execute([
                        $recordType, $recordDate, $description, $nextDueDate,
                        ($provider !== '' ? $provider : null), $photoToSave,
                        $recordId, $petId
                    ]);
                    $_SESSION['success'] = 'Medical record updated successfully!';
                } else {
                    $stmt = $conn->prepare("
                        INSERT INTO medical_records (pet_id, record_type, record_date, description, next_due_date, provider, photo_url)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $petId, $recordType, $recordDate, $description, $nextDueDate,
                        ($provider !== '' ? $provider : null), $photoToSave
                    ]);
                    $_SESSION['success'] = 'Medical record added successfully!';
                }
                header('Location: medical_records.php?pet_id=' . $petId);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Database error: ' . $e->getMessage();
            }
        }
    } elseif (isset($_POST['delete_record'])) {
        $recordId = (int)($_POST['record_id'] ?? 0);
        if ($recordId > 0) {
            $stmt = $conn->prepare("DELETE FROM medical_records WHERE id = ? AND pet_id = ?");
            $stmt->execute([$recordId, $petId]);
            $_SESSION['success'] = 'Medical record deleted successfully.';
        }
        header('Location: medical_records.php?pet_id=' . $petId);
        exit();
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

    .btn-edit, .btn-delete {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: var(--radius-sm);
        border: 1px solid;
        background: transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        transition: all 0.15s ease;
    }
    .btn-edit {
        color: var(--color-primary);
        border-color: var(--color-primary);
    }
    .btn-edit:hover {
        background: var(--color-primary);
        color: #fff;
    }
    .btn-delete {
        color: var(--color-error);
        border-color: var(--color-error);
        padding: 4px 6px;
    }
    .btn-delete:hover {
        background: var(--color-error);
        color: #fff;
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

    /* Compact horizontal stats row (replaces old sidebar stats) */
    .stats-row {
        display: flex;
        gap: var(--spacing-md);
        margin: var(--spacing-lg) 0;
        flex-wrap: wrap;
    }

    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-sm);
        background: var(--color-bg);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        padding: var(--spacing-sm) var(--spacing-lg);
        font-size: 14px;
        color: var(--color-text-secondary);
        box-shadow: var(--shadow-sm);
    }

    .stat-pill strong {
        color: var(--color-text);
        font-weight: 700;
    }

    .stat-pill i {
        color: var(--color-primary);
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

    @media (max-width: 768px) {
        .pet-header {
            flex-direction: column;
            text-align: center;
        }

        .pet-header .btn-back {
            margin-left: 0;
            align-self: flex-start;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .stats-row {
            flex-direction: column;
        }
    }
</style>

<div class="medical-records">
    <div class="pet-header">
        <?php
            $petPhotoSrc = null;
            if (!empty($pet['photo_url'])) {
                $candidate = dirname(__DIR__) . '/uploads/' . $pet['photo_url'];
                if (file_exists($candidate)) {
                    $petPhotoSrc = '../uploads/' . htmlspecialchars($pet['photo_url']);
                }
            }
        ?>
        <?php if ($petPhotoSrc): ?>
            <img src="<?php echo $petPhotoSrc; ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" class="pet-image">
        <?php else: ?>
            <div class="pet-image-placeholder">
                <i class="fas fa-paw"></i>
            </div>
        <?php endif; ?>

        <div class="pet-info">
            <h1><?php echo htmlspecialchars($pet['name']); ?>'s Medical Records</h1>
            <p class="pet-category">
                <?php echo htmlspecialchars($pet['category'] ?? 'Not specified'); ?> •
                <?php echo htmlspecialchars($pet['pet_type'] ?? 'Not specified'); ?> •
                <?php echo $pet['age'] ? $pet['age'] . ' years old' : 'Age not specified'; ?>
            </p>
        </div>

        <a href="pet_details.php?id=<?php echo $pet['id']; ?>" class="btn-back" style="margin-left:auto; white-space:nowrap;">
            <i class="fas fa-arrow-left"></i> Back to Pet Details
        </a>
    </div>

    <!-- Add / Edit Form -->
    <div class="content-card">
        <h2 class="card-title">
            <i class="fas <?php echo $editingRecord ? 'fa-edit' : 'fa-plus-circle'; ?>"></i>
            <?php echo $editingRecord ? 'Edit Medical Record' : 'Add New Medical Record'; ?>
        </h2>
        <form method="POST" class="add-record-form" enctype="multipart/form-data">
            <?php if ($editingRecord): ?>
                <input type="hidden" name="update_record" value="1">
                <input type="hidden" name="record_id" value="<?php echo (int)$editingRecord['id']; ?>">
            <?php else: ?>
                <input type="hidden" name="add_record" value="1">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="record_type" class="form-label">Record Type *</label>
                    <select name="record_type" id="record_type" class="form-select" required>
                        <option value="">Select Record Type</option>
                        <?php
                            $types = ['Vaccination','Check-up','Treatment','Surgery','Medication','Test Results','Other'];
                            $currentType = $editingRecord['record_type'] ?? '';
                            foreach ($types as $t) {
                                $sel = ($currentType === $t) ? 'selected' : '';
                                echo "<option value=\"$t\" $sel>$t</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="record_date" class="form-label">Date *</label>
                    <input type="date" name="record_date" id="record_date" class="form-input" required
                           value="<?php echo htmlspecialchars($editingRecord['record_date'] ?? date('Y-m-d')); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="provider" class="form-label">Provider / Vet / Clinic (Optional)</label>
                    <input type="text" name="provider" id="provider" class="form-input"
                           placeholder="e.g. Pila Animal Clinic"
                           value="<?php echo htmlspecialchars($editingRecord['provider'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="attachment" class="form-label">Attachment (Optional)</label>
                    <input type="file" name="attachment" id="attachment" class="form-input"
                           accept="image/*,.pdf">
                    <?php if ($editingRecord && !empty($editingRecord['photo_url'])): ?>
                        <div style="font-size:12px; margin-top:4px;">
                            Current: <a href="../uploads/<?php echo htmlspecialchars($editingRecord['photo_url']); ?>" target="_blank">view attachment</a>
                            <span style="color:var(--color-text-muted)">(upload new to replace)</span>
                        </div>
                    <?php endif; ?>
                    <small style="color:var(--color-text-muted); display:block; margin-top:2px;">Images or PDF • max 16MB</small>
                </div>
            </div>

            <div class="form-group full-width">
                <label for="description" class="form-label">Description/Details *</label>
                <textarea name="description" id="description" class="form-textarea" required
                          placeholder="Describe the medical record, treatment given, or observations..."><?php echo htmlspecialchars($editingRecord['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="next_due_date" class="form-label">Next Due Date (Optional)</label>
                    <input type="date" name="next_due_date" id="next_due_date" class="form-input"
                           value="<?php echo htmlspecialchars($editingRecord['next_due_date'] ?? ''); ?>">
                </div>
                <div class="form-group"></div>
            </div>

            <div style="display:flex; gap: var(--spacing-md); align-items:center;">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i>
                    <?php echo $editingRecord ? 'Update Medical Record' : 'Add Medical Record'; ?>
                </button>
                <?php if ($editingRecord): ?>
                    <a href="medical_records.php?pet_id=<?php echo $petId; ?>" class="btn-back" style="padding: var(--spacing-md) var(--spacing-lg); font-size:14px;">
                        Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Compact Stats Row -->
    <div class="stats-row">
        <div class="stat-pill">
            <i class="fas fa-file-medical"></i>
            <span><strong><?php echo count($medicalRecords); ?></strong> Total Records</span>
        </div>
        <div class="stat-pill">
            <i class="fas fa-clock"></i>
            <span>Last: <strong><?php echo !empty($medicalRecords) ? date('M j, Y', strtotime($medicalRecords[0]['record_date'])) : '—'; ?></strong></span>
        </div>
    </div>

    <!-- Medical History -->
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
                            <div>
                                <h3 class="record-type">
                                    <i class="fas fa-notes-medical"></i>
                                    <?php echo htmlspecialchars($record['record_type']); ?>
                                </h3>
                                <?php if (!empty($record['provider'])): ?>
                                    <div style="font-size:12px; color:var(--color-text-muted); margin-top:2px;">
                                        by <?php echo htmlspecialchars($record['provider']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="record-date">
                                <?php echo date('M j, Y', strtotime($record['record_date'])); ?>
                            </div>
                        </div>
                        <div class="record-description">
                            <?php echo nl2br(htmlspecialchars($record['description'])); ?>
                        </div>

                        <?php if (!empty($record['photo_url'])): ?>
                            <div class="record-attachment" style="margin-bottom: var(--spacing-md);">
                                <a href="../uploads/<?php echo htmlspecialchars($record['photo_url']); ?>" target="_blank" style="color: var(--color-primary); text-decoration: none; font-size: 13px;">
                                    <i class="fas fa-paperclip"></i> View Attachment
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="record-meta">
                            <span>Record #<?php echo $record['id']; ?></span>
                            <div style="display:flex; align-items:center; gap: var(--spacing-sm);">
                                <?php if ($record['next_due_date']): ?>
                                    <span class="next-due">
                                        Next Due: <?php echo date('M j, Y', strtotime($record['next_due_date'])); ?>
                                    </span>
                                <?php endif; ?>
                                <a href="medical_records.php?pet_id=<?php echo $petId; ?>&amp;edit=<?php echo (int)$record['id']; ?>" class="btn-edit" title="Edit record">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Delete this medical record? This cannot be undone.');">
                                    <input type="hidden" name="delete_record" value="1">
                                    <input type="hidden" name="record_id" value="<?php echo (int)$record['id']; ?>">
                                    <button type="submit" class="btn-delete" title="Delete record">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>