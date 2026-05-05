<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_pets.php');
    exit();
}

$petId = (int)$_GET['id'];

$db = Database::getInstance();
$conn = $db->getConnection();

// Get pet details
$stmt = $conn->prepare("
    SELECT p.*, u.full_name as owner_name, u.email as owner_email,
           u.contact_number as owner_contact, u.address as owner_address,
           u.age as owner_age
    FROM pets p
    JOIN users u ON p.owner_id = u.id
    WHERE p.id = ? AND p.archived = 0
");
$stmt->execute([$petId]);
$pet = $stmt->fetch();

if (!$pet) {
    $_SESSION['error'] = 'Pet not found.';
    header('Location: manage_pets.php');
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

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'approve' && $pet['status'] === 'pending') {
        $stmt = $conn->prepare("UPDATE pets SET status = 'approved', approved_at = NOW(), approved_by = ? WHERE id = ?");
        $stmt->execute([$_SESSION['user_id'], $petId]);
        $_SESSION['success'] = 'Pet approved successfully!';
        header('Location: review_pet.php?id=' . $petId);
        exit();
    } elseif ($_POST['action'] === 'reject' && $pet['status'] === 'pending') {
        $reason = sanitizeInput($_POST['rejection_reason']);
        $stmt = $conn->prepare("UPDATE pets SET status = 'rejected', rejection_reason = ? WHERE id = ?");
        $stmt->execute([$reason, $petId]);
        $_SESSION['success'] = 'Pet rejected successfully!';
        header('Location: review_pet.php?id=' . $petId);
        exit();
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $conn->prepare("UPDATE pets SET archived = 1, archived_at = NOW() WHERE id = ?");
        $stmt->execute([$petId]);
        $_SESSION['success'] = 'Pet archived successfully!';
        header('Location: manage_pets.php');
        exit();
    }
}
?>

<?php include '../includes/header.php'; ?>

<link rel="stylesheet" href="admin.css">

<style>
    .pet-review {
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

    .status-display {
        padding: var(--spacing-sm) var(--spacing-lg);
        border-radius: var(--radius-lg);
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-xs);
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

    .btn-back {
        background: var(--color-bg-secondary);
        color: var(--color-text);
        border: 2px solid var(--color-border);
    }

    .btn-back:hover {
        background: var(--color-text);
        color: var(--color-bg);
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

    .rejection-reason {
        background: var(--color-bg-tertiary);
        border: 1px solid var(--color-error);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-top: var(--spacing-lg);
    }

    .rejection-title {
        font-weight: 600;
        color: var(--color-error);
        margin-bottom: var(--spacing-sm);
    }

    .rejection-text {
        color: var(--color-text-secondary);
        line-height: 1.5;
        margin: 0;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--color-border);
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        padding: var(--spacing-xl);
        border-bottom: 1px solid var(--color-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--color-text);
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: var(--color-text-secondary);
        cursor: pointer;
        padding: var(--spacing-xs);
        border-radius: var(--radius-sm);
        transition: all 0.2s ease;
    }

    .modal-close:hover {
        background: var(--color-bg-secondary);
        color: var(--color-text);
    }

    .modal-body {
        padding: var(--spacing-xl);
    }

    .modal-footer {
        padding: var(--spacing-xl);
        border-top: 1px solid var(--color-border);
        display: flex;
        gap: var(--spacing-md);
        justify-content: flex-end;
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

<div class="pet-review">
    <div class="pet-header">
        <div class="pet-image-placeholder">
            <i class="fas fa-paw"></i>
        </div>

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
            <div class="status-display status-<?php echo $pet['status']; ?>">
                <i class="fas fa-circle"></i>
                <?php echo ucfirst($pet['status']); ?>
            </div>
        </div>

        <div class="action-buttons">
            <a href="manage_pets.php" class="btn-action btn-back">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
            <?php if ($pet['status'] === 'pending'): ?>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" class="btn-action btn-approve" onclick="return confirm('Approve this pet registration?')">
                        <i class="fas fa-check"></i>
                        Approve
                    </button>
                </form>
                <button class="btn-action btn-reject" onclick="showRejectModal()">
                    <i class="fas fa-times"></i>
                    Reject
                </button>
            <?php endif; ?>
            <button class="btn-action btn-delete" onclick="deletePet()">
                <i class="fas fa-trash"></i>
                Delete
            </button>
        </div>
    </div>

    <?php if ($pet['status'] === 'rejected' && $pet['rejection_reason']): ?>
        <div class="rejection-reason">
            <div class="rejection-title">
                <i class="fas fa-exclamation-triangle"></i>
                Rejection Reason
            </div>
            <p class="rejection-text"><?php echo htmlspecialchars($pet['rejection_reason']); ?></p>
        </div>
    <?php endif; ?>

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
                <div class="info-item">
                    <span class="info-label">Age</span>
                    <span class="info-value"><?php echo $pet['owner_age'] ? $pet['owner_age'] . ' years' : 'Not specified'; ?></span>
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
                <i class="fas fa-file-medical fa-3x" style="color: var(--color-text-muted); margin-bottom: var(--spacing-lg);"></i>
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

<!-- Reject Modal -->
<div class="modal-overlay" id="rejectModal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Reject Pet Registration</h3>
            <button type="button" class="modal-close" onclick="closeRejectModal()">&times;</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="reject">
                <div style="margin-bottom: var(--spacing-lg);">
                    <label style="display: block; font-weight: 500; color: var(--color-text); margin-bottom: var(--spacing-sm);">Rejection Reason</label>
                    <textarea name="rejection_reason" rows="4" required
                              style="width: 100%; padding: var(--spacing-md); border: 2px solid var(--color-border); border-radius: var(--radius-lg); font-family: var(--font-family); resize: vertical;"
                              placeholder="Please provide a reason for rejection..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeRejectModal()" style="background: var(--color-bg-secondary); color: var(--color-text); border: 2px solid var(--color-border); padding: var(--spacing-sm) var(--spacing-lg); border-radius: var(--radius-lg); cursor: pointer;">Cancel</button>
                <button type="submit" style="background: var(--color-error); color: white; border: none; padding: var(--spacing-sm) var(--spacing-lg); border-radius: var(--radius-lg); cursor: pointer;">Reject Pet</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
    document.body.style.overflow = '';
}

function deletePet() {
    if (confirm('Are you sure you want to delete this pet? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="action" value="delete">';
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('rejectModal').style.display === 'flex') {
        closeRejectModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>