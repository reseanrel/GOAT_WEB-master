<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

$db = Database::getInstance();
$conn = $db->getConnection();

// Get adoption statistics
$stats = [
    'total_adopted' => 0,
    'adoptions_this_month' => 0,
    'adoptions_this_year' => 0,
    'average_adoption_time' => 0,
    'successful_adoptions' => 0
];

try {
    // Total successful adoptions
    $stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE adoption_completed = 1");
    $stats['total_adopted'] = $stmt->fetch()['total'];

    // Adoptions this month
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pets WHERE adoption_completed = 1 AND adoption_date >= DATE_FORMAT(NOW(), '%Y-%m-01')");
    $stmt->execute();
    $stats['adoptions_this_month'] = $stmt->fetch()['total'];

    // Adoptions this year
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pets WHERE adoption_completed = 1 AND adoption_date >= DATE_FORMAT(NOW(), '%Y-01-01')");
    $stmt->execute();
    $stats['adoptions_this_year'] = $stmt->fetch()['total'];

    // Average time to adoption (in days)
    $stmt = $conn->prepare("
        SELECT AVG(DATEDIFF(adoption_date, registered_on)) as avg_days
        FROM pets
        WHERE adoption_completed = 1 AND registered_on IS NOT NULL AND adoption_date IS NOT NULL
    ");
    $stmt->execute();
    $avgDays = $stmt->fetch()['avg_days'];
    $stats['average_adoption_time'] = $avgDays ? round($avgDays) : 0;

} catch (Exception $e) {
    // Database queries failed - use defaults
}

// Get recent adoptions
$recentAdoptions = [];
try {
    $stmt = $conn->prepare("
        SELECT p.name as pet_name, p.pet_type, p.photo_url AS photo_path, p.adoption_date,
               u.full_name as adopter_name, po.full_name as previous_owner_name
        FROM pets p
        JOIN users u ON p.adopted_by = u.id
        JOIN users po ON p.owner_id = po.id
        WHERE p.adoption_completed = 1
        ORDER BY p.adoption_date DESC
        LIMIT 10
    ");
    $stmt->execute();
    $recentAdoptions = $stmt->fetchAll();
} catch (Exception $e) {
    $recentAdoptions = [];
}

// Get adoption trends (monthly for last 12 months)
$adoptionTrends = [];
try {
    $stmt = $conn->prepare("
        SELECT
            DATE_FORMAT(adoption_date, '%Y-%m') as month,
            COUNT(*) as count
        FROM pets
        WHERE adoption_completed = 1 AND adoption_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(adoption_date, '%Y-%m')
        ORDER BY DATE_FORMAT(adoption_date, '%Y-%m')
    ");
    $stmt->execute();
    $adoptionTrends = $stmt->fetchAll();
} catch (Exception $e) {
    $adoptionTrends = [];
}
?>

<?php include '../includes/header.php'; ?>

<style>
    .adoption-stats {
        max-width: 1200px;
        margin: 0 auto;
    }

    .stats-header {
        text-align: center;
        margin-bottom: var(--spacing-2xl);
    }

    .stats-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--color-text);
        margin-bottom: var(--spacing-md);
    }

    .stats-subtitle {
        font-size: 18px;
        color: var(--color-text-secondary);
        margin: 0;
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-2xl);
    }

    .stat-card {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        padding: var(--spacing-xl);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--color-border);
        text-align: center;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin: 0 auto var(--spacing-lg);
        font-size: 24px;
        box-shadow: var(--shadow-md);
    }

    .stat-number {
        font-size: 36px;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: var(--spacing-sm);
        display: block;
    }

    .stat-label {
        font-size: 14px;
        color: var(--color-text-secondary);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stats-sections {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-2xl);
        margin-bottom: var(--spacing-2xl);
    }

    .stats-section {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        padding: var(--spacing-2xl);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--color-border);
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: var(--spacing-lg);
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .recent-adoptions {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .adoption-item {
        display: flex;
        align-items: center;
        gap: var(--spacing-lg);
        padding: var(--spacing-lg);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        margin-bottom: var(--spacing-md);
        transition: all 0.3s ease;
    }

    .adoption-item:hover {
        background: var(--color-bg-secondary);
        transform: translateX(4px);
    }

    .adoption-image {
        width: 60px;
        height: 60px;
        border-radius: var(--radius-lg);
        background: var(--color-bg-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .adoption-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .adoption-image .no-image {
        color: var(--color-text-muted);
        font-size: 24px;
    }

    .adoption-details {
        flex: 1;
    }

    .adoption-pet {
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: var(--spacing-xs);
    }

    .adoption-meta {
        font-size: 14px;
        color: var(--color-text-secondary);
        line-height: 1.4;
    }

    .adoption-date {
        font-size: 12px;
        color: var(--color-text-muted);
        font-weight: 500;
    }

    .trends-chart {
        height: 300px;
        position: relative;
    }

    .empty-state {
        text-align: center;
        padding: var(--spacing-2xl);
        color: var(--color-text-secondary);
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: var(--spacing-lg);
        opacity: 0.6;
    }

    @media (max-width: 768px) {
        .stats-sections {
            grid-template-columns: 1fr;
        }

        .stats-overview {
            grid-template-columns: repeat(2, 1fr);
        }

        .adoption-item {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div class="adoption-stats">
    <div class="stats-header">
        <h1 class="stats-title">Adoption Statistics</h1>
        <p class="stats-subtitle">Track adoption success and community impact</p>
    </div>

    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-heart"></i>
            </div>
            <span class="stat-number"><?php echo $stats['total_adopted']; ?></span>
            <div class="stat-label">Total Adoptions</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <span class="stat-number"><?php echo $stats['adoptions_this_month']; ?></span>
            <div class="stat-label">This Month</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <span class="stat-number"><?php echo $stats['adoptions_this_year']; ?></span>
            <div class="stat-label">This Year</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <span class="stat-number"><?php echo $stats['average_adoption_time']; ?></span>
            <div class="stat-label">Avg Days to Adoption</div>
        </div>
    </div>

    <div class="stats-sections">
        <div class="stats-section">
            <h3 class="section-title">
                <i class="fas fa-history"></i>
                Recent Adoptions
            </h3>

            <?php if (empty($recentAdoptions)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-heart-broken"></i>
                    </div>
                    <p>No adoptions recorded yet</p>
                </div>
            <?php else: ?>
                <ul class="recent-adoptions">
                    <?php foreach ($recentAdoptions as $adoption): ?>
                        <li class="adoption-item">
                            <div class="adoption-image">
                                <?php if (!empty($adoption['photo_path']) && file_exists('../uploads/' . $adoption['photo_path'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($adoption['photo_path']); ?>" alt="Pet photo">
                                <?php else: ?>
                                    <i class="fas fa-paw no-image"></i>
                                <?php endif; ?>
                            </div>
                            <div class="adoption-details">
                                <div class="adoption-pet">
                                    <?php echo htmlspecialchars($adoption['pet_name']); ?> (<?php echo htmlspecialchars($adoption['pet_type'] ?? 'Pet'); ?>)
                                </div>
                                <div class="adoption-meta">
                                    Adopted by <?php echo htmlspecialchars($adoption['adopter_name']); ?><br>
                                    From <?php echo htmlspecialchars($adoption['previous_owner_name']); ?>
                                </div>
                                <div class="adoption-date">
                                    <?php echo date('M j, Y', strtotime($adoption['adoption_date'])); ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="stats-section">
            <h3 class="section-title">
                <i class="fas fa-chart-bar"></i>
                Adoption Trends
            </h3>

            <?php if (empty($adoptionTrends)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <p>No adoption data available yet</p>
                </div>
            <?php else: ?>
                <div class="trends-chart">
                    <canvas id="adoptionTrendsChart"></canvas>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($adoptionTrends)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('adoptionTrendsChart').getContext('2d');

    const data = <?php echo json_encode($adoptionTrends); ?>;

    const labels = data.map(item => {
        const date = new Date(item.month + '-01');
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
    });

    const values = data.map(item => parseInt(item.count));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Adoptions',
                data: values,
                borderColor: 'var(--color-primary)',
                backgroundColor: 'rgba(74, 144, 226, 0.1)',
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
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});
</script>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
