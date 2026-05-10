<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_users.php');
    exit();
}

$userId = (int)$_GET['id'];

$db = Database::getInstance();
$conn = $db->getConnection();

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
?>

<?php include '../includes/header.php'; ?>

<link rel="stylesheet" href="admin.css">

<div class="manage-users" style="max-width: 900px;">
    <div class="applications-header" style="margin-bottom: var(--spacing-xl);">
        <h1 class="applications-title" style="font-size: 26px;">User Details</h1>
        <a href="manage_users.php" class="btn-pet secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Manage Users
        </a>
    </div>

    <div class="content-card" style="padding: var(--spacing-xl);">
        <div class="info-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="info-item" style="border-bottom: none; padding: 0;">
                <span class="info-label">Full Name</span>
                <span class="info-value" style="text-align:left;"><?php echo htmlspecialchars($user['full_name'] ?? ''); ?></span>
            </div>

            <div class="info-item" style="border-bottom: none; padding: 0;">
                <span class="info-label">Email</span>
                <span class="info-value" style="text-align:left;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></span>
            </div>

            <div class="info-item" style="border-bottom: none; padding: 0;">
                <span class="info-label">Contact Number</span>
                <span class="info-value" style="text-align:left;"><?php echo htmlspecialchars($user['contact_number'] ?? 'Not provided'); ?></span>
            </div>

            <div class="info-item" style="border-bottom: none; padding: 0;">
                <span class="info-label">Role</span>
                <span class="info-value" style="text-align:left;">
                    <?php echo !empty($user['is_admin']) ? 'Admin' : 'User'; ?>
                </span>
            </div>

            <div class="info-item" style="border-bottom: none; padding: 0;">
                <span class="info-label">Status</span>
                <span class="info-value" style="text-align:left;">
                    <?php echo !empty($user['archived']) ? 'Archived' : 'Active'; ?>
                </span>
            </div>

            <div class="info-item" style="border-bottom: none; padding: 0;">
                <span class="info-label">Registered</span>
                <span class="info-value" style="text-align:left;">
                    <?php echo !empty($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'Unknown'; ?>
                </span>
            </div>
        </div>

        <div style="margin-top: var(--spacing-xl); display:flex; gap: var(--spacing-md); flex-wrap: wrap; justify-content:flex-end;">
            <?php if ((int)$user['archived'] === 1): ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                    <input type="hidden" name="action" value="unarchive">
                    <button type="submit" class="btn-action btn-unarchive" onclick="return confirm('Unarchive this user?')">
                        Unarchive
                    </button>
                </form>
            <?php else: ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                    <input type="hidden" name="action" value="archive">
                    <button type="submit" class="btn-action btn-archive" onclick="return confirm('Archive this user?')">
                        Delete
                    </button>
                </form>
            <?php endif; ?>

            <?php // Admin privilege toggle removed per requirements ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
