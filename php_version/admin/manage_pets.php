<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$conn = $db->getConnection();

// Handle pet status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $petId = (int)$_POST['pet_id'];

    if ($_POST['action'] === 'approve') {
        $stmt = $conn->prepare("UPDATE pets SET status = 'approved', approved_at = NOW(), approved_by = ? WHERE id = ?");
        $stmt->execute([$_SESSION['user_id'], $petId]);
        $_SESSION['success'] = 'Pet approved successfully!';
    } elseif ($_POST['action'] === 'reject') {
        $reason = sanitizeInput($_POST['rejection_reason']);
        $stmt = $conn->prepare("UPDATE pets SET status = 'rejected', rejection_reason = ? WHERE id = ?");
        $stmt->execute([$reason, $petId]);
        $_SESSION['success'] = 'Pet rejected successfully!';
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $conn->prepare("UPDATE pets SET archived = 1, archived_at = NOW() WHERE id = ?");
        $stmt->execute([$petId]);
        $_SESSION['success'] = 'Pet archived successfully!';
    }

    header('Location: manage_pets.php');
    exit();
}

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Build query based on filters
$query = "
    SELECT p.*, u.full_name as owner_name, u.email as owner_email
    FROM pets p
    JOIN users u ON p.owner_id = u.id
    WHERE p.archived = 0
";

$params = [];

if ($status !== 'all') {
    $query .= " AND p.status = ?";
    $params[] = $status;
}

if (!empty($search)) {
    $query .= " AND (p.name LIKE ? OR u.full_name LIKE ? OR p.category LIKE ? OR p.pet_type LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$query .= " ORDER BY p.registered_on DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$pets = $stmt->fetchAll();

// Get statistics
$stats = [
    'total' => $conn->query("SELECT COUNT(*) FROM pets WHERE archived = 0")->fetchColumn(),
    'pending' => $conn->query("SELECT COUNT(*) FROM pets WHERE status = 'pending' AND archived = 0")->fetchColumn(),
    'approved' => $conn->query("SELECT COUNT(*) FROM pets WHERE status = 'approved' AND archived = 0")->fetchColumn(),
    'rejected' => $conn->query("SELECT COUNT(*) FROM pets WHERE status = 'rejected' AND archived = 0")->fetchColumn(),
];
?>

<?php include '../includes/header.php'; ?>

<link rel="stylesheet" href="admin.css">

<style>
    .manage-pets {
        max-width: 1400px;
        margin: 0 auto;
    }

    .pet-name {
        font-weight: 600;
        color: var(--color-text);
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .pet-image {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        object-fit: cover;
    }

    .pet-image.placeholder {
        background: var(--color-bg-tertiary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-text-muted);
        font-size: 18px;
    }

    .pet-actions {
        display: flex;
        gap: var(--spacing-sm);
        align-items: center;
    }

    @media (max-width: 768px) {
        .pet-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="manage-pets">
    <div class="stats-cards">
        <div class="stat-card">
            <span class="stat-value"><?php echo $stats['total']; ?></span>
            <div class="stat-label">Total Pets</div>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?php echo $stats['pending']; ?></span>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?php echo $stats['approved']; ?></span>
            <div class="stat-label">Approved</div>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?php echo $stats['rejected']; ?></span>
            <div class="stat-label">Rejected</div>
        </div>
    </div>

    <div class="filters-section">
        <form method="GET" class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Search</label>
                <input type="text" name="search" class="filter-input" placeholder="Search by pet name, owner, category..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select">
                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            <button type="submit" class="btn-filter">Filter</button>
        </form>
    </div>

    <div class="pets-table-container">
        <?php if (empty($pets)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>No pets found</h3>
                <p>Try adjusting your search criteria or check back later.</p>
            </div>
        <?php else: ?>
            <table class="pets-table">
                <thead>
                    <tr>
                        <th>Pet</th>
                        <th>Owner</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pets as $pet): ?>
                        <tr>
                            <td>
                                <div class="pet-name">
                                    <div class="pet-image placeholder">
                                        <i class="fas fa-paw"></i>
                                    </div>
                                    <div>
                                        <div><?php echo htmlspecialchars($pet['name']); ?></div>
                                        <?php if ($pet['pet_type']): ?>
                                            <small style="color: var(--color-text-muted);"><?php echo htmlspecialchars($pet['pet_type']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div style="font-weight: 500; color: var(--color-text);"><?php echo htmlspecialchars($pet['owner_name']); ?></div>
                                    <small style="color: var(--color-text-muted);"><?php echo htmlspecialchars($pet['owner_email']); ?></small>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($pet['category'] ?? 'Not specified'); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $pet['status']; ?>">
                                    <?php echo ucfirst($pet['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($pet['registered_on'])); ?></td>
                            <td>
                                <div class="pet-actions">
                                    <a href="review_pet.php?id=<?php echo $pet['id']; ?>" class="btn-action btn-view">View</a>
                                    <?php if ($pet['status'] === 'pending'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="pet_id" value="<?php echo $pet['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn-action btn-approve" onclick="return confirm('Approve this pet?')">Approve</button>
                                        </form>
                                        <button class="btn-action btn-reject" onclick="showRejectModal(<?php echo $pet['id']; ?>, '<?php echo htmlspecialchars($pet['name']); ?>')">Reject</button>
                                    <?php endif; ?>
                                    <button class="btn-action btn-delete" onclick="deletePet(<?php echo $pet['id']; ?>, '<?php echo htmlspecialchars($pet['name']); ?>')">Delete</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
        <form id="rejectForm" method="POST">
            <div class="modal-body">
                <input type="hidden" id="rejectPetId" name="pet_id">
                <input type="hidden" name="action" value="reject">
                <div class="filter-group">
                    <label class="filter-label">Rejection Reason</label>
                    <textarea name="rejection_reason" class="filter-input" rows="4" required
                              placeholder="Please provide a reason for rejection..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action btn-delete" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="btn-action btn-reject">Reject Pet</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal(petId, petName) {
    document.getElementById('rejectPetId').value = petId;
    document.querySelector('#rejectModal .modal-title').textContent = `Reject ${petName}`;
    document.getElementById('rejectModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('rejectForm').reset();
}

function deletePet(petId, petName) {
    if (confirm(`Are you sure you want to delete ${petName}? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="pet_id" value="${petId}">
            <input type="hidden" name="action" value="delete">
        `;
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