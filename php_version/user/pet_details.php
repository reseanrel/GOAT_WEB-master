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

// Get pet details with owner info
$stmt = $conn->prepare("
    SELECT p.*, u.full_name as owner_name, u.email as owner_email,
           u.contact_number as owner_contact, u.address as owner_address
    FROM pets p
    JOIN users u ON p.owner_id = u.id
    WHERE p.id = ? AND p.owner_id = ? AND p.archived = 0
");
$stmt->execute([$petId, $_SESSION['user_id']]);
$pet = $stmt->fetch();

if (!$pet) {
    $_SESSION['error'] = 'Pet not found or access denied.';
    header('Location: dashboard.php');
    exit();
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
    .pet-details {
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
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: var(--spacing-xl);
        align-items: start;
    }

    .pet-image-large {
        width: 150px;
        height: 150px;
        border-radius: var(--radius-xl);
        object-fit: cover;
        border: 3px solid var(--color-border);
    }

    .pet-image-placeholder {
        width: 150px;
        height: 150px;
        background: var(--color-bg-secondary);
        border-radius: var(--radius-xl);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-text-muted);
        font-size: 48px;
        border: 3px solid var(--color-border);
    }

    .pet-info h1 {
        font-size: 28px;
        font-weight: 700;
        color: var(--color-text);
        margin: 0 0 var(--spacing-sm);
    }

    .pet-meta {
        display: flex;
        flex-wrap: wrap;
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-md);
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: var(--spacing-xs);
        color: var(--color-text-secondary);
        font-size: 14px;
    }

    .meta-item i {
        color: var(--color-primary);
    }

    .status-badges {
        display: flex;
        gap: var(--spacing-sm);
        flex-wrap: wrap;
    }

    .status-badge {
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-lg);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-xs);
    }

    .status-approved {
        background: var(--color-success);
        color: white;
    }

    .status-pending {
        background: var(--color-warning);
        color: white;
    }

    .status-rejected {
        background: var(--color-error);
        color: white;
    }

    .status-lost {
        background: var(--color-error);
        color: white;
    }

    .status-adoption {
        background: var(--color-accent);
        color: white;
    }

    .status-deceased {
        background: var(--color-text-muted);
        color: white;
    }

    .action-buttons {
        display: flex;
        gap: var(--spacing-md);
        flex-wrap: wrap;
    }

    .btn-action {
        padding: var(--spacing-md) var(--spacing-xl);
        border-radius: var(--radius-lg);
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .btn-primary {
        background: var(--color-primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--color-primary-hover);
        transform: translateY(-1px);
    }

    .btn-warning {
        background: var(--color-warning);
        color: white;
    }

    .btn-warning:hover {
        background: #e0a800;
        transform: translateY(-1px);
    }

    .btn-success {
        background: var(--color-success);
        color: white;
    }

    .btn-success:hover {
        background: #2e7d32;
        transform: translateY(-1px);
    }

    .btn-back {
        background: var(--color-bg-secondary);
        color: var(--color-text);
        border: 2px solid var(--color-border);
    }

    .btn-back:hover {
        background: var(--color-text);
        color: var(--color-bg);
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
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

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-md);
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--spacing-sm) 0;
        border-bottom: 1px solid var(--color-border);
    }

    .info-label {
        font-weight: 500;
        color: var(--color-text-secondary);
        font-size: 14px;
    }

    .info-value {
        font-weight: 600;
        color: var(--color-text);
        text-align: right;
    }

    .medical-records {
        margin-top: var(--spacing-lg);
    }

    .record-item {
        background: var(--color-bg-secondary);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-md);
        border: 1px solid var(--color-border);
        transition: all 0.2s ease;
    }

    .record-item:hover {
        background: var(--color-bg-tertiary);
        border-color: var(--color-border-hover);
    }

    .record-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: var(--spacing-sm);
    }

    .record-type {
        font-weight: 600;
        color: var(--color-text);
    }

    .record-date {
        font-size: 14px;
        color: var(--color-text-secondary);
    }

    .record-details {
        font-size: 14px;
        color: var(--color-text-secondary);
        line-height: 1.5;
    }

    .no-records {
        text-align: center;
        padding: var(--spacing-xl);
        color: var(--color-text-secondary);
    }

    .no-records-icon {
        font-size: 48px;
        margin-bottom: var(--spacing-lg);
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .pet-header {
            grid-template-columns: 1fr;
            text-align: center;
            gap: var(--spacing-md);
        }

        .content-grid {
            grid-template-columns: 1fr;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            justify-content: center;
        }
    }
</style>

<div class="pet-details">
    <div class="pet-header">
        <?php if ($pet['photo'] && file_exists("../uploads/{$pet['photo']}")): ?>
            <img src="../uploads/<?php echo htmlspecialchars($pet['photo_url']); ?>" alt="Pet Photo" class="pet-image-large">
        <?php else: ?>
            <div class="pet-image-placeholder">
                <i class="fas fa-paw"></i>
            </div>
        <?php endif; ?>

        <div class="pet-info">
            <h1><?php echo htmlspecialchars($pet['name']); ?></h1>
            <div class="pet-meta">
                <div class="meta-item">
                    <i class="fas fa-tag"></i>
                    <?php echo htmlspecialchars($pet['category'] ?? 'Not specified'); ?>
                </div>
                <div class="meta-item">
                    <i class="fas fa-paw"></i>
                    <?php echo htmlspecialchars($pet['pet_type'] ?? 'Not specified'); ?>
                </div>
                <div class="meta-item">
                    <i class="fas fa-birthday-cake"></i>
                    <?php echo $pet['age'] ? $pet['age'] . ' years old' : 'Age not specified'; ?>
                </div>
                <div class="meta-item">
                    <i class="fas fa-venus-mars"></i>
                    <?php echo htmlspecialchars($pet['gender'] ?? 'Not specified'); ?>
                </div>
            </div>
            <div class="status-badges">
                <span class="status-badge status-<?php echo $pet['status']; ?>">
                    <i class="fas fa-circle"></i>
                    <?php echo ucfirst($pet['status']); ?>
                </span>
                <?php if ($pet['lost']): ?>
                    <span class="status-badge status-lost">
                        <i class="fas fa-exclamation-triangle"></i>
                        Lost
                    </span>
                <?php endif; ?>
                <?php if ($pet['available_for_adoption']): ?>
                    <span class="status-badge status-adoption">
                        <i class="fas fa-heart"></i>
                        For Adoption
                    </span>
                <?php endif; ?>
                <?php if ($pet['deceased']): ?>
                    <span class="status-badge status-deceased">
                        <i class="fas fa-times"></i>
                        Deceased
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="action-buttons">
            <a href="dashboard.php" class="btn-action btn-back">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
            <a href="edit_pet.php?id=<?php echo $pet['id']; ?>" class="btn-action btn-primary">
                <i class="fas fa-edit"></i>
                Edit Details
            </a>
            <?php if ($pet['lost']): ?>
                <button class="btn-action btn-success" onclick="markFound()">
                    <i class="fas fa-check"></i>
                    Mark as Found
                </button>
            <?php else: ?>
                <button class="btn-action btn-warning" onclick="reportLost()">
                    <i class="fas fa-exclamation-triangle"></i>
                    Report Lost
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="content-grid">
        <div class="content-card">
            <h2 class="card-title">
                <i class="fas fa-info-circle"></i>
                Pet Information
            </h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Name</span>
                    <span class="info-value"><?php echo htmlspecialchars($pet['name']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Category</span>
                    <span class="info-value"><?php echo htmlspecialchars($pet['category'] ?? 'Not specified'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Breed/Type</span>
                    <span class="info-value"><?php echo htmlspecialchars($pet['pet_type'] ?? 'Not specified'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Age</span>
                    <span class="info-value"><?php echo $pet['age'] ? $pet['age'] . ' years' : 'Not specified'; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Color</span>
                    <span class="info-value"><?php echo htmlspecialchars($pet['color'] ?? 'Not specified'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Gender</span>
                    <span class="info-value"><?php echo htmlspecialchars($pet['gender'] ?? 'Not specified'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">For Adoption</span>
                    <span class="info-value"><?php echo $pet['available_for_adoption'] ? 'Yes' : 'No'; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Registered</span>
                    <span class="info-value"><?php echo date('M j, Y', strtotime($pet['registered_on'])); ?></span>
                </div>
            </div>
        </div>

        <div class="content-card">
            <h2 class="card-title">
                <i class="fas fa-user"></i>
                Owner Information
            </h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Name</span>
                    <span class="info-value"><?php echo htmlspecialchars($pet['owner_name']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value"><?php echo htmlspecialchars($pet['owner_email']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Contact</span>
                    <span class="info-value"><?php echo htmlspecialchars($pet['owner_contact'] ?? 'Not provided'); ?></span>
                </div>
            </div>
            <?php if ($pet['owner_address']): ?>
                <div style="margin-top: var(--spacing-lg);">
                    <div class="info-label" style="margin-bottom: var(--spacing-sm);">Address</div>
                    <div class="info-value" style="text-align: left; white-space: pre-line;"><?php echo htmlspecialchars($pet['owner_address']); ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="content-card medical-records">
        <h2 class="card-title">
            <i class="fas fa-notes-medical"></i>
            Medical Records
        </h2>
        <?php if (empty($medicalRecords)): ?>
            <div class="no-records">
                <div class="no-records-icon">
                    <i class="fas fa-file-medical"></i>
                </div>
                <h4>No medical records found</h4>
                <p>This pet doesn't have any medical records yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($medicalRecords as $record): ?>
                <div class="record-item">
                    <div class="record-header">
                        <div class="record-type"><?php echo htmlspecialchars($record['record_type']); ?></div>
                        <div class="record-date"><?php echo date('M j, Y', strtotime($record['record_date'])); ?></div>
                    </div>
                    <div class="record-details">
                        <?php echo nl2br(htmlspecialchars($record['description'] ?? 'No description provided')); ?>
                        <?php if ($record['next_due_date']): ?>
                            <br><strong>Next Due:</strong> <?php echo date('M j, Y', strtotime($record['next_due_date'])); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function reportLost() {
    if (confirm('Are you sure you want to report this pet as lost?')) {
        fetch('report_lost.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                pet_id: <?php echo $pet['id']; ?>,
                comment: 'Pet reported as lost from pet details page.'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

function markFound() {
    if (confirm('Are you sure you want to mark this pet as found?')) {
        fetch('mark_found.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                pet_id: <?php echo $pet['id']; ?>,
                comment: 'Pet marked as found from pet details page.'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}
</script>

<?php include '../includes/footer.php'; ?>