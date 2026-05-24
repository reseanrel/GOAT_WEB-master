<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['user_id'])) {
    $userId = (int)$_POST['user_id'];

    if ($_POST['action'] === 'archive') {
        $stmt = $conn->prepare("UPDATE users SET archived = 1, archived_at = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
        $_SESSION['success'] = 'User archived successfully!';
        header('Location: manage_users.php');
        exit();
    } elseif ($_POST['action'] === 'unarchive') {
        $stmt = $conn->prepare("UPDATE users SET archived = 0, archived_at = NULL WHERE id = ?");
        $stmt->execute([$userId]);
        $_SESSION['success'] = 'User unarchived successfully!';
        header('Location: manage_users.php');
        exit();
    } elseif ($_POST['action'] === 'approve_residency') {
        try {
            $stmt = $conn->prepare("UPDATE users SET residency_status = 'verified', residency_verified_at = NOW(), residency_verified_by = ?, residency_rejection_reason = NULL WHERE id = ?");
            $stmt->execute([$_SESSION['user_id'], $userId]);
            $_SESSION['success'] = 'Residency approved for this user.';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Migration not run yet. Please run the residency migration first.';
        }
        header('Location: user_details.php?id=' . $userId);
        exit();
    } elseif ($_POST['action'] === 'reject_residency') {
        try {
            $reason = sanitizeInput($_POST['rejection_reason'] ?? 'Document does not clearly prove residency in Pila, Laguna.');
            $stmt = $conn->prepare("UPDATE users SET residency_status = 'rejected', residency_rejection_reason = ?, residency_verified_at = NULL, residency_verified_by = NULL WHERE id = ?");
            $stmt->execute([$reason, $userId]);
            $_SESSION['success'] = 'Residency rejected.';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Migration not run yet. Please run the residency migration first.';
        }
        header('Location: user_details.php?id=' . $userId);
        exit();
    }
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_users.php');
    exit();
}

$userId = (int)$_GET['id'];

$stmt = $conn->prepare("
    SELECT *
    FROM users
    WHERE id = ? AND archived = 0
");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    // Try archived users too (for admin visibility)
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND archived = 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
}

if (!$user) {
    $_SESSION['error'] = 'User not found.';
    header('Location: manage_users.php');
    exit();
}

$isArchived = !empty($user['archived']);
$isAdminUser = !empty($user['is_admin']);

// Residency info
$residentStatus = $user['residency_status'] ?? 'unverified';
$residentDoc = $user['residency_document'] ?? null;
$residentRejectReason = $user['residency_rejection_reason'] ?? null;
$residentVerifiedAt = $user['residency_verified_at'] ?? null;
$residentVerifiedBy = $user['residency_verified_by'] ?? null;
?>

<?php include '../includes/header.php'; ?>

<link rel="stylesheet" href="admin.css">

<style>
    .user-details-page {
        max-width: 1200px;
        margin: 0 auto;
    }

    .user-details-hero {
        background: linear-gradient(135deg, rgba(26, 115, 232, 0.08), rgba(245, 158, 11, 0.10));
        border: 1px solid var(--color-border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: var(--spacing-xl);
    }

    .user-details-hero-inner {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: var(--spacing-xl);
        align-items: center;
        padding: var(--spacing-2xl);
    }

    .user-details-breadcrumb {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--color-text-secondary);
        margin-bottom: var(--spacing-md);
    }

    .user-details-title {
        margin: 0 0 var(--spacing-sm);
        font-size: 32px;
        font-weight: 800;
        color: var(--color-text);
        line-height: 1.1;
    }

    .user-details-subtitle {
        margin: 0;
        color: var(--color-text-secondary);
        font-weight: 500;
        line-height: 1.6;
        max-width: 720px;
    }

    .user-details-meta {
        display: flex;
        flex-wrap: wrap;
        gap: var(--spacing-sm);
        margin-top: var(--spacing-lg);
    }

    .detail-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .detail-pill.admin {
        background: rgba(26, 115, 232, 0.10);
        color: var(--color-primary);
        border-color: rgba(26, 115, 232, 0.18);
    }

    .detail-pill.user {
        background: rgba(52, 168, 83, 0.10);
        color: #1b7a37;
        border-color: rgba(52, 168, 83, 0.18);
    }

    .detail-pill.active {
        background: rgba(52, 168, 83, 0.10);
        color: #1b7a37;
        border-color: rgba(52, 168, 83, 0.18);
    }

    .detail-pill.archived {
        background: rgba(95, 99, 104, 0.10);
        color: var(--color-text-secondary);
        border-color: rgba(95, 99, 104, 0.18);
    }

    .user-avatar-large {
        width: 96px;
        height: 96px;
        border-radius: 28px;
        background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: 800;
        box-shadow: var(--shadow-md);
    }

    .user-summary-card {
        min-width: 240px;
        background: var(--color-bg);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-xl);
        padding: var(--spacing-lg);
        box-shadow: var(--shadow-sm);
    }

    .user-summary-name {
        margin: var(--spacing-md) 0 4px;
        font-size: 20px;
        font-weight: 800;
        color: var(--color-text);
    }

    .user-summary-email {
        margin: 0;
        color: var(--color-text-secondary);
        font-size: 14px;
        word-break: break-word;
    }

    .detail-panel {
        background: var(--color-bg);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: var(--spacing-xl);
    }

    .detail-panel-header {
        padding: var(--spacing-lg) var(--spacing-xl);
        border-bottom: 1px solid var(--color-border);
        background: var(--color-bg-secondary);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: var(--spacing-md);
    }

    .detail-panel-title {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: var(--color-text);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .detail-panel-body {
        padding: var(--spacing-xl);
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: var(--spacing-lg);
    }

    .detail-item {
        background: var(--color-bg-secondary);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        min-width: 0;
    }

    .detail-label {
        display: block;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: var(--color-text-muted);
        margin-bottom: 8px;
    }

    .detail-value {
        display: block;
        font-size: 16px;
        font-weight: 700;
        color: var(--color-text);
        word-break: break-word;
        line-height: 1.5;
    }

    .detail-value.muted {
        color: var(--color-text-secondary);
        font-weight: 600;
    }

    .user-details-actions {
        display: flex;
        flex-wrap: wrap;
        gap: var(--spacing-md);
        justify-content: flex-end;
        align-items: center;
    }

    .btn-back {
        background: var(--color-bg);
        border: 1px solid var(--color-border);
        color: var(--color-text);
    }

    .btn-back:hover {
        background: var(--color-bg-secondary);
        transform: translateY(-1px);
    }

    .btn-action {
        min-height: 44px;
        padding: 0 var(--spacing-lg);
        border-radius: var(--radius-lg);
        font-weight: 700;
        box-shadow: var(--shadow-sm);
    }

    .btn-archive {
        background: var(--color-warning);
        color: white;
    }

    .btn-unarchive {
        background: var(--color-success);
        color: white;
    }

    .btn-archive:hover,
    .btn-unarchive:hover {
        transform: translateY(-1px);
        filter: saturate(1.05);
    }

    .action-note {
        margin: var(--spacing-lg) 0 0;
        color: var(--color-text-muted);
        font-size: 13px;
        font-weight: 600;
        text-align: right;
    }

    @media (max-width: 900px) {
        .user-details-hero-inner {
            grid-template-columns: 1fr;
        }

        .user-summary-card {
            min-width: 0;
        }
    }

    @media (max-width: 768px) {
        .user-details-page {
            padding-bottom: var(--spacing-lg);
        }

        .user-details-hero-inner,
        .detail-panel-body {
            padding: var(--spacing-lg);
        }

        .user-details-title {
            font-size: 26px;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }

        .detail-panel-header {
            padding: var(--spacing-md) var(--spacing-lg);
        }

        .user-details-actions {
            justify-content: stretch;
        }

        .user-details-actions form,
        .user-details-actions a {
            width: 100%;
        }

        .user-details-actions .btn-action,
        .user-details-actions .btn-back {
            width: 100%;
            justify-content: center;
        }

        .action-note {
            text-align: left;
        }
    }
</style>

<div class="user-details-page">
    <div class="user-details-hero">
        <div class="user-details-hero-inner">
            <div>
                <div class="user-details-breadcrumb">
                    <i class="fas fa-users"></i>
                    <span>Admin</span>
                    <span>/</span>
                    <span>User Details</span>
                </div>

                <h1 class="user-details-title">User Profile Overview</h1>
                <p class="user-details-subtitle">
                    Review account information, registration details, and administrative status for this user.
                </p>

                <div class="user-details-meta">
                    <span class="detail-pill <?php echo $isAdminUser ? 'admin' : 'user'; ?>">
                        <i class="fas fa-<?php echo $isAdminUser ? 'crown' : 'user'; ?>"></i>
                        <?php echo $isAdminUser ? 'Admin Account' : 'Regular User'; ?>
                    </span>

                    <span class="detail-pill <?php echo $isArchived ? 'archived' : 'active'; ?>">
                        <i class="fas fa-<?php echo $isArchived ? 'archive' : 'check-circle'; ?>"></i>
                        <?php echo $isArchived ? 'Archived' : 'Active'; ?>
                    </span>
                </div>
            </div>

            <div class="user-summary-card">
                <div class="user-avatar-large">
                    <?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="user-summary-name"><?php echo htmlspecialchars($user['full_name'] ?? ''); ?></div>
                <p class="user-summary-email"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
            </div>
        </div>
    </div>

    <div class="detail-panel">
        <div class="detail-panel-header">
            <h2 class="detail-panel-title">
                <i class="fas fa-id-card"></i>
                Account Information
            </h2>
            <a href="manage_users.php" class="btn-action btn-back">
                <i class="fas fa-arrow-left"></i>
                Back to Manage Users
            </a>
        </div>

        <div class="detail-panel-body">
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Full Name</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['full_name'] ?? ''); ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Email</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['email'] ?? ''); ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Contact Number</span>
                    <span class="detail-value muted"><?php echo htmlspecialchars($user['contact_number'] ?? 'Not provided'); ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Role</span>
                    <span class="detail-value"><?php echo $isAdminUser ? 'Admin' : 'User'; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Status</span>
                    <span class="detail-value"><?php echo $isArchived ? 'Archived' : 'Active'; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Registered</span>
                    <span class="detail-value muted">
                        <?php echo !empty($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'Unknown'; ?>
                    </span>
                </div>
            </div>

            <div class="user-details-actions" style="margin-top: var(--spacing-xl);">
                <?php if ((int)$user['archived'] === 1): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                        <input type="hidden" name="action" value="unarchive">
                        <button type="submit" class="btn-action btn-unarchive" onclick="return confirm('Unarchive this user?')">
                            <i class="fas fa-folder-open"></i>
                            Unarchive User
                        </button>
                    </form>
                <?php else: ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                        <input type="hidden" name="action" value="archive">
                        <button type="submit" class="btn-action btn-archive" onclick="return confirm('Archive this user?')">
                            <i class="fas fa-trash-alt"></i>
                            Archive User
                        </button>
                    </form>
                <?php endif; ?>

                <?php // Admin privilege toggle removed per requirements ?>
            </div>

            <p class="action-note">
                Archive changes apply immediately and can be reversed later.
            </p>
        </div>
    </div>

    <!-- Residency Verification Panel -->
    <div class="detail-panel">
        <div class="detail-panel-header">
            <h2 class="detail-panel-title">
                <i class="fas fa-id-card"></i>
                Residency Verification (Pila, Laguna)
            </h2>
        </div>
        <div class="detail-panel-body">
            <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-bottom:16px;">
                <?php
                    $rBadge = 'status-active';
                    $rText = ucfirst($residentStatus);
                    if ($residentStatus === 'pending') $rBadge = 'status-pending';
                    if ($residentStatus === 'verified') $rBadge = 'status-approved';
                    if ($residentStatus === 'rejected') $rBadge = 'status-rejected';
                ?>
                <span class="status-badge <?php echo $rBadge; ?>" style="font-size:14px; padding:6px 14px;">
                    <?php echo $rText; ?>
                </span>
                <?php if ($residentVerifiedAt): ?>
                    <span style="font-size:13px; color:var(--color-text-secondary);">
                        Verified <?php echo date('M j, Y', strtotime($residentVerifiedAt)); ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($residentDoc): ?>
                <div style="margin-bottom:16px;">
                    <a href="../uploads/<?php echo htmlspecialchars($residentDoc); ?>" target="_blank" class="btn-action btn-view">
                        <i class="fas fa-file"></i> View Submitted Document
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($residentStatus === 'rejected' && $residentRejectReason): ?>
                <div style="background:#fef2f2; border:1px solid #fecaca; padding:12px; border-radius:8px; color:#991b1b; margin-bottom:16px; font-size:14px;">
                    <strong>Rejection Reason:</strong><br><?php echo nl2br(htmlspecialchars($residentRejectReason)); ?>
                </div>
            <?php endif; ?>

            <div class="user-details-actions">
                <?php if (in_array($residentStatus, ['pending', 'unverified', 'rejected'])): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                        <input type="hidden" name="action" value="approve_residency">
                        <button type="submit" class="btn-action btn-unarchive" onclick="return confirm('Approve this user as verified Pila resident?')">
                            <i class="fas fa-check-circle"></i> Approve Residency
                        </button>
                    </form>
                <?php endif; ?>

                <?php if (in_array($residentStatus, ['pending', 'unverified', 'verified'])): ?>
                    <form method="POST" style="display:inline; margin-left:8px;">
                        <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                        <input type="hidden" name="action" value="reject_residency">
                        <input type="text" name="rejection_reason" placeholder="Reason (optional)" style="width:180px; padding:6px 8px; font-size:12px; border:1px solid #d1d5db; border-radius:6px; margin-right:4px;">
                        <button type="submit" class="btn-action btn-archive" onclick="return confirm('Reject this residency claim?')">
                            <i class="fas fa-times-circle"></i> Reject
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <p style="margin-top:12px; font-size:12px; color:var(--color-text-muted);">
                Only approve if the document clearly shows the user's name and a Pila, Laguna address.
            </p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
