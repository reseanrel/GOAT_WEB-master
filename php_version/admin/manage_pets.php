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
    } elseif ($_POST['action'] === 'unarchive') {
        $stmt = $conn->prepare("UPDATE pets SET archived = 0, archived_at = NULL WHERE id = ?");
        $stmt->execute([$petId]);
        $_SESSION['success'] = 'Pet unarchived successfully!';
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
    /* Manage Pets - warm modern rescue UI */
    .manage-pets {
        max-width: 1400px;
        margin: 0 auto;
        padding: var(--spacing-lg);
    }

    .manage-pets-hero {
        position: relative;
        border-radius: var(--radius-xl, 20px);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: var(--shadow-lg);
        background: #fff7ed;
        margin-bottom: var(--spacing-2xl);
    }

    .manage-pets-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 15% 20%, rgba(245,158,11,0.35) 0%, rgba(245,158,11,0) 45%),
            radial-gradient(circle at 80% 30%, rgba(16,185,129,0.25) 0%, rgba(16,185,129,0) 45%),
            radial-gradient(circle at 60% 90%, rgba(37,99,235,0.12) 0%, rgba(37,99,235,0) 55%),
            linear-gradient(180deg, rgba(255,255,255,0.7), rgba(255,255,255,0.35));
        pointer-events: none;
        opacity: 0.95;
    }

    .manage-pets-hero-inner{
        position: relative;
        z-index: 1;
        padding: var(--spacing-2xl);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: var(--spacing-md);
        flex-wrap: wrap;
    }

    .manage-pets-title{
        margin: 0;
        font-size: 44px;
        font-weight: 1000;
        letter-spacing: -0.5px;
        color: rgba(17,24,39,0.95);
    }

    .manage-pets-subtitle{
        margin: 0;
        color: rgba(17,24,39,0.72);
        font-weight: 650;
        font-size: 16px;
        max-width: 620px;
        line-height: 1.6;
    }

    /* Stats cards */
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-2xl);
    }

    .stat-card {
        background: rgba(255,255,255,0.92);
        border-radius: var(--radius-xl, 20px);
        padding: var(--spacing-xl);
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.06);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        text-align: center;
    }

    .stat-card:hover{
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(217,119,6,0.25);
    }

    .stat-value{
        font-size: 38px;
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
        margin-bottom: var(--spacing-xs);
        display: block;
    }

    .stat-label{
        font-size: 13px;
        font-weight: 950;
        color: rgba(17,24,39,0.62);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    /* Filters */
    .filters-section{
        margin-bottom: var(--spacing-2xl);
    }

    .filters-grid{
        display: grid;
        grid-template-columns: 1fr 260px 160px;
        gap: var(--spacing-md);
        align-items: end;
    }

    @media (max-width: 900px){
        .filters-grid{
            grid-template-columns: 1fr;
        }
    }

    .filter-group{ display:flex; flex-direction:column; gap: 8px; }

    .filter-label{
        font-weight: 900;
        font-size: 13px;
        color: rgba(17,24,39,0.72);
    }

    .filter-input, .filter-select{
        width: 100%;
        padding: 12px 12px;
        border: 1px solid var(--color-border);
        border-radius: 12px;
        background: #fff;
        font-family: inherit;
        font-size: 14px;
        outline: none;
    }

    .filter-input:focus, .filter-select:focus{
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.12);
    }

    .btn-filter{
        border: none;
        background: var(--color-warning);
        color: #fff;
        border-radius: var(--radius-lg, 18px);
        padding: 13px 16px;
        font-size: 15px;
        font-weight: 1000;
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }

    .btn-filter:hover{
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(217,119,6,0.28);
        filter: saturate(1.05);
    }

    /* Table container restyle */
    .pets-table-container{
        background: rgba(255,255,255,0.92);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        overflow: hidden;
    }

    .pets-table{
        width: 100%;
        border-collapse: collapse;
    }

    .pets-table thead th{
        text-align: left;
        padding: 14px 18px;
        font-size: 12px;
        font-weight: 1000;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: rgba(17,24,39,0.62);
        background: rgba(255,255,255,0.75);
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }

    .pets-table tbody td{
        padding: 14px 18px;
        border-bottom: 1px solid rgba(0,0,0,0.06);
        color: rgba(17,24,39,0.85);
        font-weight: 600;
        font-size: 14px;
    }

    .pets-table tbody tr:hover td{
        background: rgba(255,247,237,0.65);
    }

    /* Pet name column */
    .pet-name{
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        min-width: 180px;
    }

    .pet-image{
        width: 44px;
        height: 44px;
        border-radius: 14px;
        object-fit: cover;
        border: 1px solid rgba(0,0,0,0.06);
        background: #fff;
    }

    .pet-image.placeholder{
        background: rgba(255,247,237,0.9);
        display:flex;
        align-items:center;
        justify-content:center;
        color: rgba(17,24,39,0.4);
        font-size: 18px;
    }

    .pet-actions{
        display: flex;
        gap: var(--spacing-sm);
        align-items: center;
        flex-wrap: wrap;
    }

    .btn-action{
        border: 1px solid rgba(0,0,0,0.08);
        background: rgba(255,255,255,0.85);
        color: rgba(17,24,39,0.92);
        border-radius: var(--radius-lg, 18px);
        padding: 10px 12px;
        font-weight: 1000;
        font-size: 12px;
        cursor: pointer;
        text-decoration: none;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-action:hover{
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        background: rgba(255,255,255,0.95);
    }

    @media (max-width: 768px) {
        .pet-actions { flex-direction: column; align-items: stretch; }
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
