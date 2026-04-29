<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$conn = $db->getConnection();

// Get statistics
$stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE archived = 0");
$totalUsers = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE archived = 0 AND status = 'approved'");
$totalPets = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE status = 'pending' AND archived = 0");
$pendingPets = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE lost = 1 AND archived = 0 AND status = 'approved' AND deceased = 0");
$lostPets = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE available_for_adoption = 1 AND archived = 0 AND status = 'approved' AND deceased = 0");
$adoptionPets = $stmt->fetch()['total'];

// Get monthly registrations for the last 12 months
$stmt = $conn->prepare("
    SELECT
        DATE_FORMAT(registered_on, '%Y-%m-01') as month,
        COUNT(*) as count
    FROM pets
    WHERE archived = 0 AND status = 'approved'
        AND registered_on >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(registered_on, '%Y-%m-01')
    ORDER BY DATE_FORMAT(registered_on, '%Y-%m-01')
");
$stmt->execute();
$monthlyData = $stmt->fetchAll();

// Get recent pending pets
$stmt = $conn->prepare("
    SELECT p.*, u.full_name as owner_name
    FROM pets p
    JOIN users u ON p.owner_id = u.id
    WHERE p.status = 'pending' AND p.archived = 0
    ORDER BY p.registered_on DESC
    LIMIT 5
");
$stmt->execute();
$recentPending = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<style>
    .admin-dashboard {
        max-width: 1400px;
        margin: 0 auto;
    }

    .dashboard-welcome {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);
        color: white;
        padding: var(--spacing-2xl);
        border-radius: var(--radius-xl);
        margin-bottom: var(--spacing-2xl);
        text-align: center;
    }

    .welcome-title {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: var(--spacing-sm);
    }

    .welcome-subtitle {
        font-size: 18px;
        opacity: 0.9;
        margin: 0;
    }

    .admin-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: var(--spacing-xl);
        margin-bottom: var(--spacing-2xl);
    }

    .stat-card {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        padding: var(--spacing-xl);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--color-border);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--color-primary), var(--color-accent));
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-card.users::before { background: linear-gradient(90deg, #4285f4, #8ab4f8); }
    .stat-card.pets::before { background: linear-gradient(90deg, #34a853, #81c784); }
    .stat-card.pending::before { background: linear-gradient(90deg, #fbbc04, #fdd835); }
    .stat-card.lost::before { background: linear-gradient(90deg, #ea4335, #ef5350); }
    .stat-card.adoption::before { background: linear-gradient(90deg, #9c27b0, #ba68c8); }

    .stat-content {
        display: flex;
        align-items: center;
        gap: var(--spacing-lg);
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        background: rgba(26, 115, 232, 0.1);
        border-radius: var(--radius-xl);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-primary);
        font-size: 24px;
        flex-shrink: 0;
    }

    .stat-info {
        flex: 1;
    }

    .stat-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: var(--spacing-xs);
    }

    .stat-value {
        font-size: 36px;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: var(--spacing-xs);
        display: block;
    }

    .stat-change {
        font-size: 14px;
        color: var(--color-text-secondary);
    }

    .stat-change.positive {
        color: var(--color-success);
    }

    .stat-change.negative {
        color: var(--color-error);
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: var(--spacing-2xl);
    }

    .main-panel {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xl);
    }

    .panel-card {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--color-border);
        overflow: hidden;
    }

    .panel-header {
        padding: var(--spacing-xl);
        border-bottom: 1px solid var(--color-border);
        display: flex;
        justify-content: between;
        align-items: center;
    }

    .panel-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--color-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .panel-actions {
        display: flex;
        gap: var(--spacing-sm);
    }

    .panel-content {
        padding: var(--spacing-xl);
    }

    .pending-list {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-md);
    }

    .pending-item {
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
        padding: var(--spacing-md);
        background: var(--color-bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--color-border);
        transition: all 0.2s ease;
    }

    .pending-item:hover {
        background: var(--color-bg-tertiary);
        border-color: var(--color-border-hover);
    }

    .pending-avatar {
        width: 48px;
        height: 48px;
        background: var(--color-bg);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-primary);
        font-weight: 600;
        font-size: 18px;
        flex-shrink: 0;
    }

    .pending-info {
        flex: 1;
        min-width: 0;
    }

    .pending-name {
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: var(--spacing-xs);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pending-meta {
        font-size: 14px;
        color: var(--color-text-secondary);
        margin-bottom: var(--spacing-xs);
    }

    .pending-owner {
        font-size: 13px;
        color: var(--color-text-muted);
    }

    .pending-actions {
        display: flex;
        gap: var(--spacing-xs);
        flex-shrink: 0;
    }

    .btn-approve {
        background: var(--color-success);
        color: white;
        border: none;
        padding: var(--spacing-sm) var(--spacing-md);
        border-radius: var(--radius-md);
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-approve:hover {
        background: #2e7d32;
        transform: translateY(-1px);
    }

    .btn-reject {
        background: var(--color-error);
        color: white;
        border: none;
        padding: var(--spacing-sm) var(--spacing-md);
        border-radius: var(--radius-md);
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-reject:hover {
        background: #c62828;
        transform: translateY(-1px);
    }

    .quick-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-md);
    }

    .action-card {
        background: var(--color-bg);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        text-align: center;
        border: 1px solid var(--color-border);
        transition: all 0.3s ease;
        text-decoration: none;
        color: var(--color-text);
        display: block;
    }

    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--color-primary);
    }

    .action-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        margin: 0 auto var(--spacing-md);
    }

    .action-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: var(--spacing-xs);
    }

    .action-description {
        font-size: 14px;
        color: var(--color-text-secondary);
        margin: 0;
    }

    .sidebar-panel {
        position: sticky;
        top: var(--spacing-xl);
    }

    @media (max-width: 1024px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .admin-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .admin-stats {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            grid-template-columns: 1fr;
        }

        .stat-content {
            flex-direction: column;
            text-align: center;
        }

        .pending-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .pending-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>

<div class="admin-dashboard">
    <div class="dashboard-welcome">
        <h1 class="welcome-title">Welcome back, Administrator</h1>
        <p class="welcome-subtitle">Manage the Pila Pet Registration System and oversee community operations</p>
    </div>

    <div class="admin-stats">
        <div class="stat-card users">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-title">Total Users</div>
                    <span class="stat-value"><?php echo $totalUsers; ?></span>
                    <div class="stat-change positive">Active members</div>
                </div>
            </div>
        </div>

        <div class="stat-card pets">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-paw"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-title">Total Pets</div>
                    <span class="stat-value"><?php echo $totalPets; ?></span>
                    <div class="stat-change positive">Registered pets</div>
                </div>
            </div>
        </div>

        <div class="stat-card pending">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-title">Pending Approval</div>
                    <span class="stat-value"><?php echo $pendingPets; ?></span>
                    <div class="stat-change">Awaiting review</div>
                </div>
            </div>
        </div>

        <div class="stat-card lost">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-title">Lost Pets</div>
                    <span class="stat-value"><?php echo $lostPets; ?></span>
                    <div class="stat-change">Need attention</div>
                </div>
            </div>
        </div>

        <div class="stat-card adoption">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-title">For Adoption</div>
                    <span class="stat-value"><?php echo $adoptionPets; ?></span>
                    <div class="stat-change positive">Finding homes</div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="main-panel">
            <div class="panel-card">
                <div class="panel-header">
                    <h2 class="panel-title">
                        <i class="fas fa-clock"></i>
                        Pending Pet Registrations
                    </h2>
                    <div class="panel-actions">
                        <a href="manage_pets.php?status=pending" class="btn-pet secondary">
                            <i class="fas fa-list"></i>
                            View All
                        </a>
                    </div>
                </div>
                <div class="panel-content">
                    <?php if (empty($recentPending)): ?>
                        <div style="text-align: center; padding: var(--spacing-2xl); color: var(--color-text-secondary);">
                            <i class="fas fa-check-circle fa-3x" style="color: var(--color-success); margin-bottom: var(--spacing-lg);"></i>
                            <h3 style="color: var(--color-text); margin-bottom: var(--spacing-md);">All Caught Up!</h3>
                            <p>No pending pet registrations at the moment. Great job keeping up with reviews!</p>
                        </div>
                    <?php else: ?>
                        <div class="pending-list">
                            <?php foreach ($recentPending as $pet): ?>
                                <div class="pending-item">
                                    <div class="pending-avatar">
                                        <?php echo strtoupper(substr($pet['name'], 0, 1)); ?>
                                    </div>
                                    <div class="pending-info">
                                        <div class="pending-name"><?php echo htmlspecialchars($pet['name']); ?></div>
                                        <div class="pending-meta">
                                            <?php echo htmlspecialchars($pet['category'] ?? 'Not specified'); ?> •
                                            <?php echo htmlspecialchars($pet['pet_type'] ?? 'Not specified'); ?>
                                        </div>
                                        <div class="pending-owner">
                                            Owner: <?php echo htmlspecialchars($pet['owner_name']); ?> •
                                            Registered: <?php echo date('M j, Y', strtotime($pet['registered_on'])); ?>
                                        </div>
                                    </div>
                                    <div class="pending-actions">
                                        <a href="review_pet.php?id=<?php echo $pet['id']; ?>" class="btn-approve">
                                            <i class="fas fa-eye"></i>
                                            Review
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="sidebar-panel">
            <div class="panel-card">
                <div class="panel-header">
                    <h3 class="panel-title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="panel-content">
                    <div class="quick-actions">
                        <a href="manage_pets.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-paw"></i>
                            </div>
                            <div class="action-title">Manage Pets</div>
                            <div class="action-description">Review and manage all pet registrations</div>
                        </a>

                        <a href="../lost_pets.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="action-title">Lost Pets</div>
                            <div class="action-description">Help reunite lost pets with owners</div>
                        </a>

                        <a href="../adoption.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="action-title">Adoption</div>
                            <div class="action-description">Manage pet adoption listings</div>
                        </a>

                        <a href="manage_users.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="action-title">Users</div>
                            <div class="action-description">Manage user accounts and permissions</div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="panel-card" style="margin-top: var(--spacing-xl);">
                <div class="panel-header">
                    <h3 class="panel-title">
                        <i class="fas fa-chart-line"></i>
                        System Overview
                    </h3>
                </div>
                <div class="panel-content">
                    <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--spacing-md); background: var(--color-bg-secondary); border-radius: var(--radius-md);">
                            <span style="font-weight: 500; color: var(--color-text);">Active Users</span>
                            <span style="font-size: 20px; font-weight: 600; color: var(--color-primary);"><?php echo $totalUsers; ?></span>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--spacing-md); background: var(--color-bg-secondary); border-radius: var(--radius-md);">
                            <span style="font-weight: 500; color: var(--color-text);">Total Pets</span>
                            <span style="font-size: 20px; font-weight: 600; color: var(--color-success);"><?php echo $totalPets; ?></span>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--spacing-md); background: var(--color-bg-secondary); border-radius: var(--radius-md);">
                            <span style="font-weight: 500; color: var(--color-text);">For Adoption</span>
                            <span style="font-size: 20px; font-weight: 600; color: var(--color-accent);"><?php echo $adoptionPets; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>