<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$conn = $db->getConnection();

// Handle approve / reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['user_id'])) {
    $targetUserId = (int)$_POST['user_id'];
    $adminId = (int)$_SESSION['user_id'];

    try {
        if ($_POST['action'] === 'approve') {
            $stmt = $conn->prepare("
                UPDATE users 
                SET residency_status = 'verified', 
                    residency_verified_at = NOW(), 
                    residency_verified_by = ?,
                    residency_rejection_reason = NULL
                WHERE id = ?
            ");
            $stmt->execute([$adminId, $targetUserId]);
            $_SESSION['success'] = 'User residency verified successfully.';
        } elseif ($_POST['action'] === 'reject') {
            $reason = sanitizeInput($_POST['rejection_reason'] ?? 'Document does not clearly show Pila, Laguna residency.');
            $stmt = $conn->prepare("
                UPDATE users 
                SET residency_status = 'rejected', 
                    residency_rejection_reason = ?,
                    residency_verified_at = NULL,
                    residency_verified_by = NULL
                WHERE id = ?
            ");
            $stmt->execute([$reason, $targetUserId]);
            $_SESSION['success'] = 'User residency marked as rejected.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database columns for residency verification are missing. Please run the migration first.';
    }

    header('Location: verify_residents.php');
    exit();
}

// Filters + Data loading (protected against missing columns)
$statusFilter = $_GET['status'] ?? 'pending';
$search = sanitizeInput($_GET['search'] ?? '');
$migrationNeeded = false;
$users = [];
$stats = ['pending' => 0, 'verified' => 0, 'rejected' => 0, 'unverified' => 0];

try {
    $query = "
        SELECT u.id, u.full_name, u.email, u.contact_number, u.address, 
               u.residency_status, u.residency_document, u.residency_rejection_reason,
               u.residency_verified_at, admin.full_name as verified_by_name
        FROM users u
        LEFT JOIN users admin ON u.residency_verified_by = admin.id
        WHERE 1=1
    ";
    $params = [];

    if ($statusFilter === 'pending') {
        $query .= " AND u.residency_status = 'pending'";
    } elseif ($statusFilter === 'verified') {
        $query .= " AND u.residency_status = 'verified'";
    } elseif ($statusFilter === 'rejected') {
        $query .= " AND u.residency_status = 'rejected'";
    } elseif ($statusFilter === 'unverified') {
        $query .= " AND (u.residency_status = 'unverified' OR u.residency_status IS NULL)";
    } 
    // 'all' shows everything

    if (!empty($search)) {
        $query .= " AND (u.full_name LIKE ? OR u.email LIKE ? OR u.address LIKE ?)";
        $like = "%$search%";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $query .= " ORDER BY 
        CASE 
            WHEN u.residency_status = 'pending' THEN 1
            WHEN u.residency_status = 'unverified' THEN 2
            WHEN u.residency_status = 'rejected' THEN 3
            ELSE 4
        END, 
        u.full_name ASC";

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll();

    // Stats
    $stats = [
        'pending' => $conn->query("SELECT COUNT(*) FROM users WHERE residency_status = 'pending'")->fetchColumn(),
        'verified' => $conn->query("SELECT COUNT(*) FROM users WHERE residency_status = 'verified'")->fetchColumn(),
        'rejected' => $conn->query("SELECT COUNT(*) FROM users WHERE residency_status = 'rejected'")->fetchColumn(),
        'unverified' => $conn->query("SELECT COUNT(*) FROM users WHERE residency_status = 'unverified' OR residency_status IS NULL")->fetchColumn(),
    ];
} catch (PDOException $e) {
    if (stripos($e->getMessage(), 'residency_status') !== false || stripos($e->getMessage(), '1054') !== false) {
        $migrationNeeded = true;
    } else {
        throw $e; // re-throw unrelated errors
    }
}
?>
<?php include '../includes/header.php'; ?>

<link rel="stylesheet" href="admin.css">

<style>
    .manage-residents { max-width: 1400px; margin: 0 auto; }

    .residency-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;
    }
    .residency-unverified { background:#f3f4f6; color:#374151; }
    .residency-pending { background:#fef3c7; color:#92400e; }
    .residency-verified { background:#d1fae5; color:#065f46; }
    .residency-rejected { background:#fee2e2; color:#991b1b; }
    .doc-link { color: var(--color-primary); font-weight:600; }

    /* Match Manage Pets button style */
    .btn-filter {
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
    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(217,119,6,0.28);
        filter: saturate(1.05);
    }
</style>

<div class="manage-residents">
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success" style="margin-bottom:var(--spacing-md);">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-error" style="margin-bottom:var(--spacing-md);">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if ($migrationNeeded): ?>
        <div style="background:#fef2f2; border:2px solid #f87171; border-radius:var(--radius-xl); padding:32px; margin:20px 0; text-align:center;">
            <h2 style="margin:0 0 12px; color:#991b1b; font-size:22px;">
                <i class="fas fa-exclamation-triangle"></i> Database Update Required
            </h2>
            <p style="max-width:520px; margin:0 auto 20px; color:#7f1d1d; font-weight:600;">
                The residency verification feature requires new database columns that have not been added yet.
            </p>
            <a href="../run_residency_migration.php" target="_blank" 
               style="display:inline-block; background:#dc2626; color:white; padding:12px 28px; border-radius:999px; font-weight:800; text-decoration:none;">
                <i class="fas fa-play"></i> Run Migration Now
            </a>
            <div style="margin-top:16px; font-size:13px; color:#991b1b;">
                After running it, refresh this page.
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$migrationNeeded): ?>
    <!-- Stats (matches manage_pets / manage_users style) -->
    <div class="stats-cards" style="margin-bottom:var(--spacing-lg);">
        <div class="stat-card">
            <span class="stat-value"><?php echo $stats['pending']; ?></span>
            <div class="stat-label">Pending Review</div>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?php echo $stats['verified']; ?></span>
            <div class="stat-label">Verified</div>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?php echo $stats['rejected']; ?></span>
            <div class="stat-label">Rejected</div>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?php echo $stats['unverified']; ?></span>
            <div class="stat-label">Not Submitted</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section" style="margin-bottom:var(--spacing-md);">
        <form method="GET" class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Search</label>
                <input type="text" name="search" class="filter-input" placeholder="Name, email or address..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select">
                    <option value="pending" <?php echo $statusFilter==='pending'?'selected':''; ?>>Pending Review</option>
                    <option value="unverified" <?php echo $statusFilter==='unverified'?'selected':''; ?>>Not Submitted</option>
                    <option value="verified" <?php echo $statusFilter==='verified'?'selected':''; ?>>Verified</option>
                    <option value="rejected" <?php echo $statusFilter==='rejected'?'selected':''; ?>>Rejected</option>
                    <option value="all" <?php echo $statusFilter==='all'?'selected':''; ?>>All</option>
                </select>
            </div>
            <button type="submit" class="btn-filter">Filter</button>
            <a href="verify_residents.php" class="btn-filter" style="background:#fff; color:#1a73e8; border:1px solid #1a73e8;">Reset</a>
        </form>
    </div>

    <div class="pets-table-container">
        <?php if (empty($users)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <h3>No residents found</h3>
                <p>Try adjusting your search criteria or check back later.</p>
            </div>
        <?php else: ?>
            <table class="pets-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Address</th>
                        <th>Contact</th>
                        <th>Document</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <div class="user-name">
                                    <div class="user-avatar"><?php echo strtoupper(substr($u['full_name'], 0, 1)); ?></div>
                                    <div>
                                        <div><?php echo htmlspecialchars($u['full_name']); ?></div>
                                        <div class="user-email"><?php echo htmlspecialchars($u['email']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:13px; max-width:240px;"><?php echo htmlspecialchars($u['address'] ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($u['contact_number'] ?? '—'); ?></td>
                            <td>
                                <?php if (!empty($u['residency_document'])): ?>
                                    <a href="../uploads/<?php echo htmlspecialchars($u['residency_document']); ?>" target="_blank" class="doc-link">
                                        <i class="fas fa-file"></i> View Document
                                    </a>
                                <?php else: ?>
                                    <span style="color:#9ca3af;">No document</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $s = $u['residency_status'] ?? 'unverified';
                                    $badgeClass = 'residency-unverified';
                                    if ($s === 'pending') $badgeClass = 'residency-pending';
                                    if ($s === 'verified') $badgeClass = 'residency-verified';
                                    if ($s === 'rejected') $badgeClass = 'residency-rejected';
                                ?>
                                <span class="residency-badge <?php echo $badgeClass; ?>">
                                    <?php echo ucfirst($s); ?>
                                </span>
                                <?php if ($s === 'verified' && !empty($u['verified_by_name'])): ?>
                                    <div style="font-size:11px; color:#6b7280; margin-top:2px;">
                                        by <?php echo htmlspecialchars($u['verified_by_name']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($s === 'rejected' && !empty($u['residency_rejection_reason'])): ?>
                                    <div style="font-size:11px; color:#b91c1c; max-width:200px; margin-top:3px;">
                                        <?php echo htmlspecialchars($u['residency_rejection_reason']); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                    <?php if (!empty($u['residency_document'])): ?>
                                        <a href="../uploads/<?php echo htmlspecialchars($u['residency_document']); ?>" target="_blank" 
                                           class="btn-action btn-view" style="font-size:12px; padding:6px 10px;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    <?php endif; ?>

                                    <?php if (in_array($u['residency_status'], ['pending', 'unverified', 'rejected'])): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn-action btn-unarchive" style="font-size:12px; padding:6px 10px;"
                                                    onclick="return confirm('Approve this user as a verified Pila resident?')">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (in_array($u['residency_status'], ['pending', 'unverified', 'verified'])): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="text" name="rejection_reason" placeholder="Reason (optional)" 
                                                   style="font-size:11px; padding:4px 6px; width:140px; border:1px solid #d1d5db; border-radius:6px;">
                                            <button type="submit" class="btn-action btn-archive" style="font-size:12px; padding:6px 10px;"
                                                    onclick="return confirm('Reject this residency verification?')">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php endif; /* end if (!$migrationNeeded) */ ?>

    <?php if (!$migrationNeeded): ?>
    <!-- Quick Links -->
    <div style="margin-top: var(--spacing-2xl); display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="manage_users.php" class="btn-action btn-back" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-users"></i> Back to All Users
        </a>
        <a href="dashboard.php" class="btn-action btn-back" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-tachometer-alt"></i> Admin Dashboard
        </a>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
