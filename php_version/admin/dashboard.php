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

<div class="row mb-4">
    <div class="col-md-12">
        <h2>Admin Dashboard</h2>
        <p class="text-muted">Manage the Pila Pet Registration System</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Users</h5>
                        <h2><?php echo $totalUsers; ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Pets</h5>
                        <h2><?php echo $totalPets; ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-paw fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Pending Approval</h5>
                        <h2><?php echo $pendingPets; ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Lost Pets</h5>
                        <h2><?php echo $lostPets; ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Pending Pets -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Recent Pet Registrations (Pending Approval)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentPending)): ?>
                    <p class="text-muted">No pending pet registrations</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Pet Name</th>
                                    <th>Owner</th>
                                    <th>Category</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentPending as $pet): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($pet['name']); ?></td>
                                        <td><?php echo htmlspecialchars($pet['owner_name']); ?></td>
                                        <td><?php echo htmlspecialchars($pet['category'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($pet['registered_on'])); ?></td>
                                        <td>
                                            <a href="review_pet.php?id=<?php echo $pet['id']; ?>" class="btn btn-sm btn-primary">
                                                Review
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="manage_pets.php" class="btn btn-outline-primary">Manage All Pets</a>
                    <a href="manage_users.php" class="btn btn-outline-primary">Manage Users</a>
                    <a href="../lost_pets.php" class="btn btn-outline-warning">View Lost Pets</a>
                    <a href="../adoption.php" class="btn btn-outline-success">View Adoption</a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5>System Stats</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        Available for Adoption
                        <span class="badge bg-success"><?php echo $adoptionPets; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        Monthly Registrations
                        <span class="badge bg-info"><?php echo count($monthlyData); ?> months</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>