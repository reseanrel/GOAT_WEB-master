<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

if (isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

// Get all adoption applications for user's pets
$stmt = $conn->prepare("
    SELECT aa.*, p.name as pet_name, p.pet_type, p.age as pet_age, p.photo_path
    FROM adoption_applications aa
    JOIN pets p ON aa.pet_id = p.id
    WHERE aa.pet_owner_id = ?
    ORDER BY aa.application_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll();

// Group applications by status
$applicationsByStatus = [
    'pending' => [],
    'under_review' => [],
    'approved' => [],
    'rejected' => [],
    'withdrawn' => []
];

foreach ($applications as $app) {
    $applicationsByStatus[$app['status']][] = $app;
}
?>

<?php include '../includes/header.php'; ?>

<style>
    .applications-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .applications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--spacing-2xl);
        flex-wrap: wrap;
        gap: var(--spacing-md);
    }

    .applications-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--color-text);
        margin: 0;
    }

    .status-tabs {
        display: flex;
        gap: var(--spacing-sm);
        margin-bottom: var(--spacing-xl);
        border-bottom: 2px solid var(--color-border);
        overflow-x: auto;
    }

    .status-tab {
        padding: var(--spacing-md) var(--spacing-lg);
        background: var(--color-bg-secondary);
        border: 2px solid transparent;
        border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        color: var(--color-text-secondary);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        position: relative;
    }

    .status-tab.active {
        background: var(--color-bg);
        border-color: var(--color-border);
        color: var(--color-text);
        border-bottom-color: var(--color-bg);
    }

    .status-tab:hover {
        background: var(--color-bg);
        color: var(--color-text);
    }

    .status-tab.pending { border-top-color: var(--color-warning); }
    .status-tab.under_review { border-top-color: var(--color-accent); }
    .status-tab.approved { border-top-color: var(--color-success); }
    .status-tab.rejected { border-top-color: var(--color-error, #e53e3e); }
    .status-tab.withdrawn { border-top-color: var(--color-text-muted); }

    .status-badge {
        display: inline-block;
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-md);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.pending { background: var(--color-warning); color: white; }
    .status-badge.under_review { background: var(--color-accent); color: white; }
    .status-badge.approved { background: var(--color-success); color: white; }
    .status-badge.rejected { background: var(--color-error, #e53e3e); color: white; }
    .status-badge.withdrawn { background: var(--color-text-muted); color: white; }

    .application-card {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        padding: var(--spacing-xl);
        margin-bottom: var(--spacing-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--color-border);
        transition: all 0.3s ease;
    }

    .application-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .application-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: var(--spacing-lg);
        flex-wrap: wrap;
        gap: var(--spacing-md);
    }

    .pet-info {
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
        flex: 1;
        min-width: 200px;
    }

    .pet-image {
        width: 60px;
        height: 60px;
        border-radius: var(--radius-lg);
        background: var(--color-bg-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .pet-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pet-image .no-image {
        color: var(--color-text-muted);
        font-size: 24px;
    }

    .pet-details h4 {
        margin: 0 0 var(--spacing-xs) 0;
        color: var(--color-text);
        font-size: 18px;
    }

    .pet-details p {
        margin: 0;
        color: var(--color-text-secondary);
        font-size: 14px;
    }

    .application-meta {
        text-align: right;
        min-width: 150px;
    }

    .application-date {
        color: var(--color-text-secondary);
        font-size: 14px;
        margin-bottom: var(--spacing-xs);
    }

    .applicant-info {
        margin-bottom: var(--spacing-lg);
        padding: var(--spacing-lg);
        background: var(--color-bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--color-border);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--spacing-md);
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xs);
    }

    .info-label {
        font-weight: 600;
        color: var(--color-text);
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        color: var(--color-text-secondary);
        font-size: 14px;
        line-height: 1.4;
    }

    .application-content {
        margin-bottom: var(--spacing-lg);
    }

    .content-section {
        margin-bottom: var(--spacing-lg);
        padding: var(--spacing-lg);
        background: var(--color-bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--color-border);
    }

    .content-section h5 {
        margin: 0 0 var(--spacing-md) 0;
        color: var(--color-text);
        font-size: 16px;
        font-weight: 600;
    }

    .application-actions {
        display: flex;
        gap: var(--spacing-md);
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: var(--spacing-sm) var(--spacing-lg);
        border-radius: var(--radius-lg);
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: var(--spacing-xs);
    }

    .btn-approve {
        background: var(--color-success);
        color: white;
    }

    .btn-approve:hover {
        background: var(--color-success-dark, #38a169);
        transform: translateY(-1px);
    }

    .btn-reject {
        background: var(--color-error, #e53e3e);
        color: white;
    }

    .btn-reject:hover {
        background: #c53030;
        transform: translateY(-1px);
    }

    .btn-review {
        background: var(--color-accent);
        color: white;
    }

    .btn-review:hover {
        background: var(--color-accent-dark, #805ad5);
        transform: translateY(-1px);
    }

    .btn-contact {
        background: var(--color-primary);
        color: white;
    }

    .btn-contact:hover {
        background: var(--color-primary-dark, #2c5282);
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: var(--spacing-3xl);
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        border: 2px dashed var(--color-border);
    }

    .empty-state-icon {
        font-size: 64px;
        color: var(--color-text-muted);
        margin-bottom: var(--spacing-lg);
    }

    .empty-state h3 {
        color: var(--color-text);
        font-size: 24px;
        font-weight: 600;
        margin-bottom: var(--spacing-md);
    }

    .empty-state p {
        color: var(--color-text-secondary);
        font-size: 16px;
        margin: 0;
    }

    @media (max-width: 768px) {
        .applications-header {
            flex-direction: column;
            align-items: stretch;
        }

        .application-header {
            flex-direction: column;
            align-items: stretch;
        }

        .application-meta {
            text-align: left;
            margin-top: var(--spacing-md);
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .application-actions {
            justify-content: center;
        }

        .status-tabs {
            justify-content: center;
        }
    }
</style>

<div class="applications-container">
    <div class="applications-header">
        <h1 class="applications-title">Adoption Applications</h1>
        <a href="../adoption.php" class="btn-pet primary">
            <i class="fas fa-arrow-left"></i>
            Back to Adoption
        </a>
    </div>

    <div class="status-tabs">
        <div class="status-tab active" data-status="all">All Applications (<?php echo count($applications); ?>)</div>
        <div class="status-tab pending" data-status="pending">Pending (<?php echo count($applicationsByStatus['pending']); ?>)</div>
        <div class="status-tab under_review" data-status="under_review">Under Review (<?php echo count($applicationsByStatus['under_review']); ?>)</div>
        <div class="status-tab approved" data-status="approved">Approved (<?php echo count($applicationsByStatus['approved']); ?>)</div>
        <div class="status-tab rejected" data-status="rejected">Rejected (<?php echo count($applicationsByStatus['rejected']); ?>)</div>
        <div class="status-tab withdrawn" data-status="withdrawn">Withdrawn (<?php echo count($applicationsByStatus['withdrawn']); ?>)</div>
    </div>

    <div id="applicationsContainer">
        <?php if (empty($applications)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3>No Adoption Applications</h3>
                <p>You haven't received any adoption applications for your pets yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($applications as $app): ?>
                <div class="application-card" data-status="<?php echo $app['status']; ?>">
                    <div class="application-header">
                        <div class="pet-info">
                            <div class="pet-image">
                                <?php if (!empty($app['photo_path']) && file_exists('../uploads/' . $app['photo_path'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($app['photo_path']); ?>" alt="Pet photo">
                                <?php else: ?>
                                    <i class="fas fa-paw no-image"></i>
                                <?php endif; ?>
                            </div>
                            <div class="pet-details">
                                <h4><?php echo htmlspecialchars($app['pet_name']); ?></h4>
                                <p><?php echo htmlspecialchars($app['pet_type'] ?? 'Unknown'); ?>, <?php echo $app['pet_age'] ? $app['pet_age'] . ' years old' : 'Age unknown'; ?></p>
                            </div>
                        </div>
                        <div class="application-meta">
                            <div class="application-date">
                                <?php echo date('M j, Y', strtotime($app['application_date'])); ?>
                            </div>
                            <div class="status-badge <?php echo $app['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                            </div>
                        </div>
                    </div>

                    <div class="applicant-info">
                        <h5>Applicant Information</h5>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Name</span>
                                <span class="info-value"><?php echo htmlspecialchars($app['applicant_full_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <span class="info-value"><?php echo htmlspecialchars($app['applicant_email']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phone</span>
                                <span class="info-value"><?php echo htmlspecialchars($app['applicant_phone']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Age</span>
                                <span class="info-value"><?php echo $app['applicant_age']; ?> years old</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Household</span>
                                <span class="info-value"><?php echo $app['household_members']; ?> members, <?php echo $app['housing_type']; ?><?php echo $app['has_yard'] ? ' (with yard)' : ''; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Other Pets</span>
                                <span class="info-value"><?php echo $app['has_other_pets'] ? 'Yes - ' . htmlspecialchars($app['other_pets_details']) : 'No'; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="application-content">
                        <div class="content-section">
                            <h5>Adoption Reason</h5>
                            <p><?php echo nl2br(htmlspecialchars($app['adoption_reason'])); ?></p>
                        </div>

                        <div class="content-section">
                            <h5>Pet Experience</h5>
                            <p><?php echo nl2br(htmlspecialchars($app['pet_experience'])); ?></p>
                        </div>

                        <?php if (!empty($app['additional_notes'])): ?>
                            <div class="content-section">
                                <h5>Additional Notes</h5>
                                <p><?php echo nl2br(htmlspecialchars($app['additional_notes'])); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="content-section">
                            <h5>Emergency Contact</h5>
                            <p><strong><?php echo htmlspecialchars($app['emergency_contact_name']); ?></strong> - <?php echo htmlspecialchars($app['emergency_contact_phone']); ?></p>
                        </div>
                    </div>

                    <div class="application-actions">
                        <?php if ($app['status'] === 'pending'): ?>
                            <button class="btn-action btn-review" onclick="updateApplicationStatus(<?php echo $app['id']; ?>, 'under_review')">
                                <i class="fas fa-eye"></i>
                                Mark as Under Review
                            </button>
                            <button class="btn-action btn-approve" onclick="updateApplicationStatus(<?php echo $app['id']; ?>, 'approved')">
                                <i class="fas fa-check"></i>
                                Approve
                            </button>
                            <button class="btn-action btn-reject" onclick="updateApplicationStatus(<?php echo $app['id']; ?>, 'rejected')">
                                <i class="fas fa-times"></i>
                                Reject
                            </button>
                        <?php elseif ($app['status'] === 'under_review'): ?>
                            <button class="btn-action btn-approve" onclick="updateApplicationStatus(<?php echo $app['id']; ?>, 'approved')">
                                <i class="fas fa-check"></i>
                                Approve
                            </button>
                            <button class="btn-action btn-reject" onclick="updateApplicationStatus(<?php echo $app['id']; ?>, 'rejected')">
                                <i class="fas fa-times"></i>
                                Reject
                            </button>
                        <?php elseif ($app['status'] === 'approved'): ?>
                            <button class="btn-action btn-contact" onclick="contactApplicant('<?php echo htmlspecialchars($app['applicant_email']); ?>', '<?php echo htmlspecialchars($app['pet_name']); ?>')">
                                <i class="fas fa-envelope"></i>
                                Contact Applicant
                            </button>
                            <button class="btn-action btn-approve" onclick="markAsAdopted(<?php echo $app['id']; ?>, <?php echo $app['pet_id']; ?>, <?php echo $app['applicant_id']; ?>)">
                                <i class="fas fa-heart"></i>
                                Mark as Adopted
                            </button>
                        <?php endif; ?>

                        <button class="btn-action btn-contact" onclick="contactApplicant('<?php echo htmlspecialchars($app['applicant_email']); ?>', '<?php echo htmlspecialchars($app['pet_name']); ?>')">
                            <i class="fas fa-envelope"></i>
                            Contact
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function updateApplicationStatus(applicationId, newStatus) {
    const statusMessages = {
        'under_review': 'Mark this application as under review?',
        'approved': 'Approve this adoption application?',
        'rejected': 'Reject this adoption application?'
    };

    if (!confirm(statusMessages[newStatus] || 'Update application status?')) {
        return;
    }

    fetch('update_adoption_application.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            application_id: applicationId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function markAsAdopted(applicationId, petId, adopterId) {
    if (!confirm('Mark this pet as adopted? This will remove it from the adoption listings.')) {
        return;
    }

    fetch('mark_pet_adopted.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            application_id: applicationId,
            pet_id: petId,
            adopter_id: adopterId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function contactApplicant(email, petName) {
    const subject = encodeURIComponent(`Regarding Your Adoption Application for ${petName}`);
    const body = encodeURIComponent(`Dear Applicant,\n\nThank you for your interest in adopting ${petName}.\n\nBest regards,`);
    window.open(`mailto:${email}?subject=${subject}&body=${body}`);
}

// Tab filtering
document.querySelectorAll('.status-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        const status = this.dataset.status;

        // Update active tab
        document.querySelectorAll('.status-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        // Filter applications
        document.querySelectorAll('.application-card').forEach(card => {
            if (status === 'all' || card.dataset.status === status) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>