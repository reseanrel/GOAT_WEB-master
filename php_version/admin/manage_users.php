<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$conn = $db->getConnection();

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $userId = (int)$_POST['user_id'];

    if ($_POST['action'] === 'archive') {
        $stmt = $conn->prepare("UPDATE users SET archived = 1, archived_at = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
        $_SESSION['success'] = 'User archived successfully!';
    } elseif ($_POST['action'] === 'unarchive') {
        $stmt = $conn->prepare("UPDATE users SET archived = 0, archived_at = NULL WHERE id = ?");
        $stmt->execute([$userId]);
        $_SESSION['success'] = 'User unarchived successfully!';
    } elseif ($_POST['action'] === 'toggle_admin') {
        // Get current admin status
        $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $currentStatus = $stmt->fetch()['is_admin'];

        // Toggle admin status
        $newStatus = $currentStatus ? 0 : 1;
        $stmt = $conn->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
        $stmt->execute([$newStatus, $userId]);

        $action = $newStatus ? 'granted admin privileges' : 'removed admin privileges';
        $_SESSION['success'] = "User $action successfully!";
    }

    header('Location: manage_users.php');
    exit();
}

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : 'active';
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Build query based on filters
$query = "SELECT * FROM users WHERE 1=1";

$params = [];

if ($status === 'active') {
    $query .= " AND archived = 0";
} elseif ($status === 'archived') {
    $query .= " AND archived = 1";
}

if (!empty($search)) {
    $query .= " AND (full_name LIKE ? OR email LIKE ? OR contact_number LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$query .= " ORDER BY full_name ASC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Get statistics
$stats = [
    'total' => $conn->query("SELECT COUNT(*) FROM users WHERE archived = 0")->fetchColumn(),
    'admins' => $conn->query("SELECT COUNT(*) FROM users WHERE is_admin = 1 AND archived = 0")->fetchColumn(),
    'archived' => $conn->query("SELECT COUNT(*) FROM users WHERE archived = 1")->fetchColumn(),
];
?>

<?php include '../includes/header.php'; ?>

<link rel="stylesheet" href="admin.css">

<style>
    .manage-users {
        max-width: 1400px;
        margin: 0 auto;
    }

    .user-name {
        font-weight: 600;
        color: var(--color-text);
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 16px;
        flex-shrink: 0;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-email {
        font-size: 14px;
        color: var(--color-text-muted);
    }

    .user-actions {
        display: flex;
        gap: var(--spacing-sm);
        align-items: center;
    }

    @media (max-width: 768px) {
        .user-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="manage-users">
    <div class="stats-cards">
        <div class="stat-card">
            <span class="stat-value"><?php echo $stats['total']; ?></span>
            <div class="stat-label">Active Users</div>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?php echo $stats['admins']; ?></span>
            <div class="stat-label">Administrators</div>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?php echo $stats['archived']; ?></span>
            <div class="stat-label">Archived Users</div>
        </div>
    </div>

    <div class="filters-section">
        <form method="GET" class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Search</label>
                <input type="text" name="search" class="filter-input" placeholder="Search by name, email, or contact..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select">
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active Users</option>
                    <option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>>Archived Users</option>
                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Users</option>
                </select>
            </div>
            <button type="submit" class="btn-filter">Filter</button>
        </form>
    </div>

    <div class="users-table-container">
        <?php if (empty($users)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>No users found</h3>
                <p>Try adjusting your search criteria or check back later.</p>
            </div>
        <?php else: ?>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="user-name">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                    </div>
                                    <div class="user-info">
                                        <div><?php echo htmlspecialchars($user['full_name']); ?></div>
                                        <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($user['contact_number'] ?? 'Not provided'); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $user['archived'] ? 'archived' : 'active'; ?>">
                                    <?php echo $user['archived'] ? 'Archived' : 'Active'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['is_admin']): ?>
                                    <span class="admin-badge">Admin</span>
                                <?php else: ?>
                                    <span style="color: var(--color-text-secondary); font-size: 14px;">User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="user-actions">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <?php if ($user['archived']): ?>
                                            <input type="hidden" name="action" value="unarchive">
                                            <button type="submit" class="btn-action btn-unarchive" onclick="return confirm('Unarchive this user?')">Unarchive</button>
                                        <?php else: ?>
                                            <input type="hidden" name="action" value="archive">
                                            <button type="submit" class="btn-action btn-archive" onclick="return confirm('Archive this user?')">Archive</button>
                                        <?php endif; ?>
                                    </form>

                                    <?php if (!$user['archived'] && $user['id'] != $_SESSION['user_id']): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="toggle_admin">
                                            <button type="submit" class="btn-action btn-admin" onclick="return confirm('<?php echo $user['is_admin'] ? 'Remove' : 'Grant'; ?> admin privileges?')">
                                                <?php echo $user['is_admin'] ? 'Remove Admin' : 'Make Admin'; ?>
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
</div>

<?php include '../includes/footer.php'; ?>