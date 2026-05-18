<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$conn = $db->getConnection();

// Get statistics (with error handling)
$totalUsers = 0;
$userGrowth = 0;
$totalPets = 0;
$petGrowth = 0;
$pendingPets = 0;
$lostPets = 0;
$adoptionPets = 0;
$totalApprovedPets = 0;

try {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE archived = 0");
    $totalUsers = $stmt->fetch()['total'];
} catch (Exception $e) {
    $totalUsers = 0;
}

// Calculate user growth (last 30 days vs previous 30 days)
try {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE archived = 0 AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute();
    $currentUsers = $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE archived = 0 AND created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL 60 DAY) AND DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute();
    $previousUsers = $stmt->fetchColumn();

    if ($previousUsers > 0) {
        $userGrowth = round((($currentUsers - $previousUsers) / $previousUsers) * 100, 1);
    }
} catch (Exception $e) {
    $userGrowth = 0;
}

// Total pets
try {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE archived = 0 AND status = 'approved'");
    $totalPets = $stmt->fetch()['total'];
} catch (Exception $e) {
    $totalPets = 0;
}

// Calculate pet registration growth
try {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM pets WHERE archived = 0 AND status = 'approved' AND registered_on >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute();
    $currentPets = $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM pets WHERE archived = 0 AND status = 'approved' AND registered_on BETWEEN DATE_SUB(CURDATE(), INTERVAL 60 DAY) AND DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute();
    $previousPets = $stmt->fetchColumn();

    if ($previousPets > 0) {
        $petGrowth = round((($currentPets - $previousPets) / $previousPets) * 100, 1);
    }
} catch (Exception $e) {
    $petGrowth = 0;
}

// Pending pets
try {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE status = 'pending' AND archived = 0");
    $pendingPets = $stmt->fetch()['total'];
} catch (Exception $e) {
    $pendingPets = 0;
}

// Lost pets
try {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE lost = 1 AND archived = 0 AND status = 'approved' AND deceased = 0");
    $lostPets = $stmt->fetch()['total'];
} catch (Exception $e) {
    $lostPets = 0;
}

// For adoption pets
try {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE available_for_adoption = 1 AND archived = 0 AND status = 'approved' AND deceased = 0");
    $adoptionPets = $stmt->fetch()['total'];
} catch (Exception $e) {
    $adoptionPets = 0;
}

// Initialize empty arrays for analytics
$monthlyData = [];
$userMonthlyData = [];
$categoryData = [];
$recentActivities = [];

try {
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

    // Get user registration trends (last 12 months)
    $stmt = $conn->prepare("
        SELECT
            DATE_FORMAT(created_at, '%Y-%m-01') as month,
            COUNT(*) as count
        FROM users
        WHERE archived = 0
            AND created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m-01')
        ORDER BY DATE_FORMAT(created_at, '%Y-%m-01')
    ");
    $stmt->execute();
    $userMonthlyData = $stmt->fetchAll();

    // Get pet category distribution
    $stmt = $conn->query("
        SELECT category, COUNT(*) as count
        FROM pets
        WHERE archived = 0 AND status = 'approved' AND category IS NOT NULL AND category != ''
        GROUP BY category
        ORDER BY count DESC
        LIMIT 5
    ");
    $categoryData = $stmt->fetchAll();

    // Get recent activities (last 10)
    $stmt = $conn->prepare("
        SELECT 'pet_approved' as type, p.name as title, u.full_name as user_name,
               p.approved_at as activity_date, 'Pet registration approved' as description
        FROM pets p
        JOIN users u ON p.owner_id = u.id
        WHERE p.status = 'approved' AND p.approved_at IS NOT NULL
        UNION ALL
        SELECT 'user_registered' as type, u.full_name as title, u.full_name as user_name,
               u.created_at as activity_date, 'New user registered' as description
        FROM users u
        WHERE u.archived = 0
        UNION ALL
        SELECT 'pet_reported_lost' as type, p.name as title, u.full_name as user_name,
               p.updated_at as activity_date, 'Pet reported as lost' as description
        FROM pets p
        JOIN users u ON p.owner_id = u.id
        WHERE p.lost = 1 AND p.archived = 0
        ORDER BY activity_date DESC
        LIMIT 10
    ");
    $stmt->execute();
    $recentActivities = $stmt->fetchAll();
} catch (Exception $e) {
    // Database queries failed - use empty arrays
}

// Get recent pending pets
$recentPending = [];
try {
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
} catch (Exception $e) {
    // Database query failed - use empty array
}
?>

<?php include '../includes/header.php'; ?>

<link rel="stylesheet" href="admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .admin-dashboard {
        max-width: 1400px;
        margin: 0 auto;
    }

    .dashboard-welcome {
        position: relative;
        background: #fff7ed;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        padding: var(--spacing-2xl);
        margin-bottom: var(--spacing-2xl);
        text-align: center;
        overflow: hidden;
    }

    .dashboard-welcome::before {
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

    .dashboard-welcome > div {
        position: relative;
        z-index: 1;
        color: rgba(17,24,39,0.95);
    }

    .welcome-title {
        font-size: 32px;
        font-weight: 1000;
        margin-bottom: var(--spacing-sm);
    }

    .welcome-subtitle {
        font-size: 18px;
        opacity: 0.85;
        margin: 0;
        color: rgba(17,24,39,0.72);
        font-weight: 650;
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
    .stat-card.lost::before { background: linear-gradient(90deg, #f59e0b, #fb7185); }
    .stat-card.adoption::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

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
        align-items: start;
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
        justify-content: space-between;
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

    /* Pending cards - align with Lost Pets / Adoption (image-first cards) */
    .pending-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: var(--spacing-lg);
        align-items: stretch;
    }

    .pending-item {
        background: rgba(255,255,255,0.92);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        display: flex;
        flex-direction: column;
        min-height: 480px;
    }

    .pending-item:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(245,158,11,0.30);
    }

    .pending-media {
        height: 210px;
        position: relative;
        background: #fff;
    }

    .pending-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .pending-media .media-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(17,24,39,0.03);
        color: rgba(17,24,39,0.45);
        font-size: 52px;
    }

    .pending-status-badge {
        position: absolute;
        top: 14px;
        right: 14px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 1000;
        letter-spacing: 0.3px;
        color: #fff;
        background: var(--color-warning);
        box-shadow: 0 10px 20px rgba(245,158,11,0.18);
        text-transform: uppercase;
        border: 1px solid rgba(255,255,255,0.18);
    }

    .pending-type-pill {
        position: absolute;
        top: 14px;
        left: 14px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 1000;
        letter-spacing: 0.2px;
        color: #0f172a;
        background: rgba(255,255,255,0.78);
        border: 1px solid rgba(0,0,0,0.06);
        backdrop-filter: blur(6px);
    }

    /* Reuse public card-ish type colors */
    .pending-type-dog { background: rgba(37,99,235,0.12); border-color: rgba(37,99,235,0.25); color: #1d4ed8; }
    .pending-type-cat { background: rgba(16,185,129,0.12); border-color: rgba(16,185,129,0.25); color: #0f766e; }
    .pending-type-other { background: rgba(148,163,184,0.18); border-color: rgba(148,163,184,0.28); color: #334155; }

    .pending-body {
        padding: var(--spacing-lg);
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
    }

    .pending-name {
        font-size: 18px;
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
        margin: 0;
        line-height: 1.2;
    }

    .pending-brief {
        margin: 0;
        color: rgba(17,24,39,0.62);
        font-weight: 650;
        font-size: 13px;
        line-height: 1.5;
    }

    .pending-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 12px;
        margin-top: 6px;
    }

    .pending-meta .meta-item {
        border: 1px solid rgba(0,0,0,0.05);
        background: rgba(255,255,255,0.65);
        border-radius: 14px;
        padding: 8px 10px;
    }

    .pending-meta .meta-label {
        font-size: 11px;
        font-weight: 1000;
        color: rgba(17,24,39,0.55);
        text-transform: uppercase;
        letter-spacing: .3px;
        margin-bottom: 4px;
    }

    .pending-meta .meta-value {
        font-size: 13px;
        font-weight: 900;
        color: rgba(17,24,39,0.9);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pending-actions {
        margin-top: auto;
        padding-top: var(--spacing-md);
        border-top: 1px solid rgba(0,0,0,0.06);
        display: grid;
        gap: 10px;
    }

    .pending-review-btn {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        border-radius: var(--radius-lg, 18px);
        padding: 12px 14px;
        background: #111827;
        color: #fff;
        text-decoration: none;
        font-weight: 1000;
        border: 1px solid rgba(0,0,0,0.08);
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    }

    .pending-review-btn:hover {
        transform: translateY(-2px);
        background: #0f172a;
        box-shadow: var(--shadow-md);
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

    /* Analytics Section */
    .analytics-section {
        margin-bottom: var(--spacing-2xl);
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: var(--spacing-xl);
        align-items: start;
    }

    .chart-container,
    .category-breakdown,
    .activities-feed {
        width: 100%;
        height: 100%;
    }

    .chart-container {
        background: rgba(255,255,255,0.92);
        border-radius: var(--radius-xl);
        padding: var(--spacing-xl);
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.06);
        margin-bottom: 0;
        position: relative;
        overflow: hidden;
    }

    .chart-container::before{
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: #f59e0b;
        opacity: 0.55;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--spacing-lg);
    }

    .chart-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .chart-canvas {
        width: 100% !important;
        height: 300px !important;
    }

    .category-breakdown {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        padding: var(--spacing-xl);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--color-border);
        margin-bottom: var(--spacing-lg);
    }

    .category-item {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xs);
        padding: var(--spacing-md);
        margin-bottom: var(--spacing-sm);
        background: var(--color-bg-secondary);
        border-radius: var(--radius-md);
        border: 1px solid var(--color-border);
    }

    .category-item-row {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: var(--spacing-md);
    }

    .category-bar {
        height: 10px;
        border-radius: 999px;
        background: rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .category-bar-fill {
        height: 100%;
        background: var(--color-primary);
        border-radius: 999px;
    }

    .category-name {
        font-weight: 500;
        color: var(--color-text);
    }

    .category-count {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-primary);
    }

    .activities-feed {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--color-border);
        overflow: hidden;
        margin-top: var(--spacing-lg);
    }

    .activity-item {
        padding: var(--spacing-lg);
        border-bottom: 1px solid var(--color-border);
        display: flex;
        align-items: flex-start;
        gap: var(--spacing-md);
        transition: background-color 0.2s ease;
    }

    .activity-item:hover {
        background: var(--color-bg-secondary);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        flex-shrink: 0;
    }

    .activity-icon.pet-approved { background: var(--color-success); }
    .activity-icon.user-registered { background: var(--color-primary); }
    .activity-icon.pet-reported-lost { background: var(--color-error); }

    .activity-content {
        flex: 1;
        min-width: 0;
    }

    .activity-title {
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: var(--spacing-xs);
        font-size: 14px;
    }

    .activity-description {
        color: var(--color-text-secondary);
        font-size: 13px;
        margin-bottom: var(--spacing-xs);
    }

    .activity-time {
        color: var(--color-text-muted);
        font-size: 12px;
    }

    @media (max-width: 1024px) {
        .analytics-grid {
            grid-template-columns: 1fr;
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
        <div>
            <h1 class="welcome-title">Welcome back, Administrator</h1>
            <p class="welcome-subtitle">Manage the Pila Pet Registration System and oversee community operations</p>
        </div>
        <div style="display: flex; gap: var(--spacing-md); align-items: center;">
            <div style="font-size: 14px; color: var(--color-text-secondary);">
                <i class="fas fa-clock"></i>
                Last updated: <span id="lastUpdate"><?php echo date('M j, Y g:i A'); ?></span>
            </div>
            <button onclick="window.location.reload()" class="btn-action" style="background: var(--color-accent); color: white; font-size: 12px; padding: var(--spacing-xs) var(--spacing-sm);">
                <i class="fas fa-sync-alt"></i>
                Refresh
            </button>
        </div>
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
                    <div class="stat-change <?php echo $userGrowth >= 0 ? 'positive' : 'negative'; ?>">
                        <i class="fas fa-<?php echo $userGrowth >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                        <?php echo abs($userGrowth); ?>% this month
                    </div>
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
                    <div class="stat-change <?php echo $petGrowth >= 0 ? 'positive' : 'negative'; ?>">
                        <i class="fas fa-<?php echo $petGrowth >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                        <?php echo abs($petGrowth); ?>% this month
                    </div>
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

    <div style="margin-bottom: var(--spacing-2xl);">
        <div class="quick-actions">
            <a href="manage_users.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="action-title">Manage Users</div>
                <p class="action-description">View, archive, and manage user roles</p>
            </a>
            <a href="manage_adoption_applications.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <div class="action-title">Adoption Applications</div>
                <p class="action-description">Review and finalize adoptions</p>
            </a>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="analytics-section">
        <div class="analytics-grid">
            <!-- Charts -->
            <div>
                <div class="chart-container">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-chart-line"></i>
                            Registration Trends
                        </h3>
                        <div style="display: flex; gap: var(--spacing-sm);">
                            <button onclick="switchChart('pet')" id="petChartBtn" style="background: var(--color-primary); color: white; border: none; padding: var(--spacing-xs) var(--spacing-sm); border-radius: var(--radius-md); font-size: 12px; font-weight: 500; cursor: pointer;">Pets</button>
                            <button onclick="switchChart('user')" id="userChartBtn" style="background: var(--color-bg-secondary); color: var(--color-text); border: 1px solid var(--color-border); padding: var(--spacing-xs) var(--spacing-sm); border-radius: var(--radius-md); font-size: 12px; font-weight: 500; cursor: pointer;">Users</button>
                        </div>
                    </div>
                    <canvas id="registrationChart" class="chart-canvas"></canvas>
                </div>
            </div>

            <!-- Category Breakdown & Activities -->
            <div>
                    <div class="category-breakdown">
                    <div class="panel-header" style="padding: 0 0 var(--spacing-lg) 0; border: none;">
                        <h3 class="panel-title" style="font-size: 18px; margin-bottom: 6px;">
                            <i class="fas fa-chart-pie"></i>
                            Pet Categories
                        </h3>
                        <p style="margin: 0; color: rgba(17,24,39,0.62); font-weight: 650; font-size: 13px; line-height: 1.5;">
                            Top pet types approved in the system (shown with share %).
                        </p>
                    </div>
                    <div>
                        <?php if (empty($categoryData)): ?>
                            <div style="text-align: center; padding: var(--spacing-xl); color: var(--color-text-secondary);">
                                <i class="fas fa-chart-pie fa-2x" style="opacity: 0.5; margin-bottom: var(--spacing-md);"></i>
                                <p>No category data available</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($categoryData as $category): ?>
                                <?php
                                    $categoryCount = (int)$category['count'];
                                    $percent = $totalApprovedPets > 0 ? round(($categoryCount / $totalApprovedPets) * 100, 1) : 0;
                                    $barWidth = $totalApprovedPets > 0 ? min(100, round(($categoryCount / $totalApprovedPets) * 100, 1)) : 0;
                                ?>
                                <div class="category-item">
                                    <div class="category-item-row">
                                        <span class="category-name"><?php echo htmlspecialchars(ucfirst($category['category'])); ?></span>
                                        <span class="category-count"><?php echo $categoryCount; ?> (<?php echo $percent; ?>%)</span>
                                    </div>
                                    <div class="category-bar" aria-hidden="true">
                                        <div class="category-bar-fill" style="width: <?php echo $barWidth; ?>%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="activities-feed">
                    <div class="panel-header" style="padding: var(--spacing-lg); border-bottom: 1px solid var(--color-border);">
                        <h3 class="panel-title" style="font-size: 18px; margin: 0;">
                            <i class="fas fa-history"></i>
                            Recent Activities
                        </h3>
                    </div>
                    <div>
                        <?php if (empty($recentActivities)): ?>
                            <div style="text-align: center; padding: var(--spacing-2xl); color: var(--color-text-secondary);">
                                <i class="fas fa-history fa-2x" style="opacity: 0.5; margin-bottom: var(--spacing-md);"></i>
                                <p>No recent activities</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recentActivities as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon activity-icon-<?php echo str_replace('_', '-', $activity['type']); ?>">
                                        <?php
                                        switch ($activity['type']) {
                                            case 'pet_approved':
                                                echo '<i class="fas fa-check"></i>';
                                                break;
                                            case 'user_registered':
                                                echo '<i class="fas fa-user-plus"></i>';
                                                break;
                                            case 'pet_reported_lost':
                                                echo '<i class="fas fa-exclamation-triangle"></i>';
                                                break;
                                        }
                                        ?>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title"><?php echo htmlspecialchars($activity['title']); ?></div>
                                        <div class="activity-description"><?php echo htmlspecialchars($activity['description']); ?></div>
                                        <div class="activity-time"><?php echo date('M j, Y g:i A', strtotime($activity['activity_date'])); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
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
                                <?php
                                    $photo = $pet['photo_path'] ?? ($pet['photo_url'] ?? '');
                                    $photo = is_string($photo) ? $photo : '';
                                    $photoOk = !empty($photo) && file_exists('../uploads/' . $photo);

                                    $category = (string)($pet['category'] ?? 'PET');
                                    $typeClass = 'pending-type-other';
                                    if (strtolower($category) === 'dog') $typeClass = 'pending-type-dog';
                                    if (strtolower($category) === 'cat') $typeClass = 'pending-type-cat';

                                    $registeredOn = !empty($pet['registered_on']) ? date('m/d/Y', strtotime($pet['registered_on'])) : 'Unknown';
                                    $petType = (string)($pet['pet_type'] ?? 'Unknown');
                                    $ownerName = (string)($pet['owner_name'] ?? 'Unknown');
                                ?>
                                <div class="pending-item">
                                    <div class="pending-media">
                                        <?php if ($photoOk): ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars((string)$pet['name']); ?>">
                                        <?php else: ?>
                                            <div class="media-fallback">
                                                <i class="fas fa-paw"></i>
                                            </div>
                                        <?php endif; ?>

                                        <div class="pending-type-pill <?php echo htmlspecialchars($typeClass); ?>">
                                            <?php echo htmlspecialchars(ucfirst($category)); ?>
                                        </div>
                                        <div class="pending-status-badge">Pending</div>
                                    </div>

                                    <div class="pending-body">
                                        <h3 class="pending-name"><?php echo htmlspecialchars((string)$pet['name']); ?></h3>
                                        <p class="pending-brief">
                                            <strong>Registered:</strong> <?php echo htmlspecialchars($registeredOn); ?>
                                        </p>

                                        <div class="pending-meta">
                                            <div class="meta-item">
                                                <div class="meta-label">Type</div>
                                                <div class="meta-value"><?php echo htmlspecialchars($petType); ?></div>
                                            </div>
                                            <div class="meta-item">
                                                <div class="meta-label">Owner</div>
                                                <div class="meta-value"><?php echo htmlspecialchars($ownerName); ?></div>
                                            </div>
                                        </div>

                                        <div class="pending-actions">
                                            <a href="review_pet.php?id=<?php echo (int)$pet['id']; ?>" class="pending-review-btn">
                                                <i class="fas fa-eye"></i>
                                                Review
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// Chart data
const petMonthlyData = <?php echo json_encode($monthlyData); ?>;
const userMonthlyData = <?php echo json_encode($userMonthlyData); ?>;

// Prepare data for charts
function prepareChartData(data, label) {
    const labels = [];
    const values = [];

    // Generate labels for last 12 months
    const now = new Date();
    for (let i = 11; i >= 0; i--) {
        const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
        const monthKey = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-01';
        labels.push(date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' }));

        const found = data.find(item => item.month === monthKey);
        values.push(found ? parseInt(found.count) : 0);
    }

    return { labels, values, label };
}

let currentChart = 'pet';
let chart = null;

function createChart(type = 'pet') {
    const ctx = document.getElementById('registrationChart').getContext('2d');

    if (chart) {
        chart.destroy();
    }

    const data = type === 'pet' ? petMonthlyData : userMonthlyData;
    const preparedData = prepareChartData(data, type === 'pet' ? 'Pet Registrations' : 'User Registrations');

    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: preparedData.labels,
            datasets: [{
                label: preparedData.label,
                data: preparedData.values,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.12)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'var(--color-primary)',
                pointBorderColor: 'white',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'var(--color-border)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'var(--color-border)'
                    },
                    ticks: {
                        color: 'var(--color-text-secondary)',
                        font: {
                            size: 12
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'var(--color-border)'
                    },
                    ticks: {
                        color: 'var(--color-text-secondary)',
                        font: {
                            size: 12
                        }
                    }
                }
            },
            elements: {
                point: {
                    hoverBorderWidth: 3
                }
            }
        }
    });
}

function switchChart(type) {
    currentChart = type;
    createChart(type);

    // Update button styles
    const petBtn = document.getElementById('petChartBtn');
    const userBtn = document.getElementById('userChartBtn');

    if (type === 'pet') {
        petBtn.style.background = 'var(--color-primary)';
        petBtn.style.color = 'white';
        petBtn.style.border = 'none';
        userBtn.style.background = 'var(--color-bg-secondary)';
        userBtn.style.color = 'var(--color-text)';
        userBtn.style.border = '1px solid var(--color-border)';
    } else {
        userBtn.style.background = 'var(--color-primary)';
        userBtn.style.color = 'white';
        userBtn.style.border = 'none';
        petBtn.style.background = 'var(--color-bg-secondary)';
        petBtn.style.color = 'var(--color-text)';
        petBtn.style.border = '1px solid var(--color-border)';
    }
}

// Initialize chart on page load
document.addEventListener('DOMContentLoaded', function() {
    createChart('pet');
});
</script>
