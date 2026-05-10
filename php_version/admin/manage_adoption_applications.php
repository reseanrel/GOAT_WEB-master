<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$conn = $db->getConnection();

// Get all adoption applications that need admin review
$stmt = $conn->prepare("
    SELECT aa.*, p.name as pet_name, p.pet_type, p.age as pet_age, p.photo_url AS photo_path,
           po.full_name as pet_owner_name, ap.full_name as applicant_name
    FROM adoption_applications aa
    JOIN pets p ON aa.pet_id = p.id
    JOIN users po ON aa.pet_owner_id = po.id
    JOIN users ap ON aa.applicant_id = ap.id
    WHERE aa.status IN ('approved', 'under_review')
    ORDER BY aa.application_date DESC
");
$stmt->execute();
$applications = $stmt->fetchAll();

// Group applications by status
$applicationsByStatus = [
    'under_review' => [],
    'approved' => []
];

foreach ($applications as $app) {
    $applicationsByStatus[$app['status']][] = $app;
}
?>

<?php include '../includes/header.php'; ?>

<style>
    .admin-applications {
        max-width: 1400px;
        margin: 0 auto;
    }

    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--spacing-2xl);
        flex-wrap: wrap;
        gap: var(--spacing-md);
    }

    .admin-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--color-text);
        margin: 0;
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
    }

    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: var(--spacing-sm);
    }

    .stat-label {
        font-size: 14px;
        color: var(--color-text-secondary);
        font-weight: 500;
    }

    .status-tabs {
        display: flex;
        gap: var(--spacing-sm);
        margin-bottom: var(--spacing-xl);
        border-bottom: 2px solid var(--color-border);
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
    }

    .status-tab.active {
        background: var(--color-bg);
        border-color: var(--color-border);
        color: var(--color-text);
        border-bottom-color: var(--color-bg);
    }

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

    .pet-applicant-info {
        display: flex;
        gap: var(--spacing-lg);
        flex: 1;
    }

    .info-section {
        flex: 1;
    }

    .info-section h4 {
        margin: 0 0 var(--spacing-sm) 0;
        color: var(--color-text);
        font-size: 16px;
        font-weight: 600;
    }

    .info-section p {
        margin: var(--spacing-xs) 0;
        color: var(--color-text-secondary);
        font-size: 14px;
    }

    .status-info {
        text-align: right;
        min-width: 150px;
    }

    .status-badge {
        display: inline-block;
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-md);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: var(--spacing-sm);
    }

    .status-badge.under_review {
        background: var(--color-accent);
        color: white;
    }

    .status-badge.approved {
        background: var(--color-success);
        color: white;
    }

    .application-details {
        margin-bottom: var(--spacing-lg);
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-lg);
    }

    .detail-item {
        background: var(--color-bg-secondary);
        padding: var(--spacing-md);
        border-radius: var(--radius-lg);
        border: 1px solid var(--color-border);
    }

    .detail-label {
        font-weight: 600;
        color: var(--color-text);
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: var(--spacing-xs);
    }

    .detail-value {
        color: var(--color-text-secondary);
        font-size: 14px;
        line-height: 1.4;
    }

    .application-actions {
        display: flex;
        gap: var(--spacing-md);
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn-admin {
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

    .btn-contact {
        background: var(--color-primary);
        color: white;
    }

    .btn-contact:hover {
        background: var(--color-primary-dark, #2c5282);
        transform: translateY(-1px);
    }

    .review-notes {
        margin-top: var(--spacing-lg);
        padding: var(--spacing-lg);
        background: var(--color-bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--color-border);
    }

    .review-notes h5 {
        margin: 0 0 var(--spacing-md) 0;
        color: var(--color-text);
        font-size: 16px;
        font-weight: 600;
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

    @media (max-width: 768px) {
        .admin-header {
            flex-direction: column;
            align-items: stretch;
        }

        .pet-applicant-info {
            flex-direction: column;
        }

        .status-info {
            text-align: left;
            margin-top: var(--spacing-md);
        }

        .details-grid {
            grid-template-columns: 1fr;
        }

        .application-actions {
            justify-content: center;
        }
    }
</style>

<div class="admin-applications">
    <div class="admin-header">
        <h1 class="admin-title">Adoption Applications Review</h1>
        <a href="dashboard.php" class="btn-pet primary">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>

    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-number"><?php echo count($applications); ?></div>
            <div class="stat-label">Total Applications</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo count($applicationsByStatus['under_review']); ?></div>
            <div class="stat-label">Under Review</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo count($applicationsByStatus['approved']); ?></div>
            <div class="stat-label">Approved by Owners</div>
        </div>
    </div>

    <div class="status-tabs">
        <div class="status-tab active" data-status="all">All (<?php echo count($applications); ?>)</div>
        <div class="status-tab under_review" data-status="under_review">Under Review (<?php echo count($applicationsByStatus['under_review']); ?>)</div>
        <div class="status-tab approved" data-status="approved">Approved (<?php echo count($applicationsByStatus['approved']); ?>)</div>
    </div>

    <div id="applicationsContainer">
        <?php if (empty($applications)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h3>No Applications to Review</h3>
                <p>All adoption applications have been processed.</p>
            </div>
        <?php else: ?>
            <?php foreach ($applications as $app): ?>
                <div class="application-card" data-status="<?php echo $app['status']; ?>">
                    <div class="application-header">
                        <div class="pet-applicant-info">
                            <div class="info-section">
                                <h4><?php echo htmlspecialchars($app['pet_name']); ?></h4>
                                <p><strong>Pet Type:</strong> <?php echo htmlspecialchars($app['pet_type'] ?? 'Unknown'); ?></p>
                                <p><strong>Age:</strong> <?php echo $app['pet_age'] ? $app['pet_age'] . ' years' : 'Unknown'; ?></p>
                                <p><strong>Owner:</strong> <?php echo htmlspecialchars($app['pet_owner_name']); ?></p>
                            </div>
                            <div class="info-section">
                                <h4><?php echo htmlspecialchars($app['applicant_name']); ?></h4>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($app['applicant_email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($app['applicant_phone']); ?></p>
                                <p><strong>Age:</strong> <?php echo $app['applicant_age']; ?> years</p>
                            </div>
                        </div>
                        <div class="status-info">
                            <div class="status-badge <?php echo $app['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                            </div>
                            <div style="font-size: 12px; color: var(--color-text-secondary);">
                                <?php echo date('M j, Y', strtotime($app['application_date'])); ?>
                            </div>
                        </div>
                    </div>

                    <div class="application-details">
                        <div class="details-grid">
                            <div class="detail-item">
                                <div class="detail-label">Household</div>
                                <div class="detail-value">
                                    <?php echo $app['household_members']; ?> members, <?php echo ucfirst($app['housing_type']); ?>
                                    <?php echo $app['has_yard'] ? ' (with yard)' : ''; ?>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Other Pets</div>
                                <div class="detail-value">
                                    <?php echo $app['has_other_pets'] ? 'Yes - ' . htmlspecialchars($app['other_pets_details']) : 'No'; ?>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Contact Preference</div>
                                <div class="detail-value"><?php echo ucfirst($app['preferred_contact_method']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Home Visit Allowed</div>
                                <div class="detail-value"><?php echo $app['home_visit_allowed'] ? 'Yes' : 'No'; ?></div>
                            </div>
                        </div>

                        <div class="detail-item" style="margin-bottom: 0;">
                            <div class="detail-label">Adoption Reason</div>
                            <div class="detail-value"><?php echo nl2br(htmlspecialchars(substr($app['adoption_reason'], 0, 200))); ?><?php echo strlen($app['adoption_reason']) > 200 ? '...' : ''; ?></div>
                        </div>
                    </div>

                    <?php if ($app['reviewed_by'] && $app['review_notes']): ?>
                        <div class="review-notes">
                            <h5>Admin Review Notes</h5>
                            <p><em>Reviewed by admin on <?php echo date('M j, Y', strtotime($app['reviewed_at'])); ?></em></p>
                            <p><?php echo nl2br(htmlspecialchars($app['review_notes'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="application-actions">
                        <button class="btn-admin btn-contact" onclick="contactApplicant('<?php echo htmlspecialchars($app['applicant_email']); ?>', '<?php echo htmlspecialchars($app['pet_name']); ?>')">
                            <i class="fas fa-envelope"></i>
                            Contact Applicant
                        </button>
                        <button class="btn-admin btn-contact" onclick="contactOwner('<?php echo htmlspecialchars($app['pet_owner_name']); ?>', '<?php echo htmlspecialchars($app['pet_name']); ?>', '<?php echo htmlspecialchars($app['applicant_name']); ?>')">
                            <i class="fas fa-envelope"></i>
                            Contact Owner
                        </button>
                        <?php if ($app['status'] === 'approved'): ?>
                            <button class="btn-admin btn-approve" onclick="finalizeAdoption(<?php echo $app['id']; ?>, <?php echo $app['pet_id']; ?>, <?php echo $app['applicant_id']; ?>)">
                                <i class="fas fa-check-double"></i>
                                Finalize Adoption
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function contactApplicant(email, petName) {
    const subject = encodeURIComponent(`Adoption Application Review for ${petName}`);
    const body = encodeURIComponent(`Dear Applicant,\n\nThank you for your interest in adopting ${petName}.\n\nWe are currently reviewing your application.\n\nBest regards,\nPila Pet Registration Administration`);
    window.open(`mailto:${email}?subject=${subject}&body=${body}`);
}

function contactOwner(ownerName, petName, applicantName) {
    const subject = encodeURIComponent(`Adoption Application Review for ${petName}`);
    const body = encodeURIComponent(`Dear ${ownerName},\n\nWe are reviewing an adoption application for your pet ${petName} from ${applicantName}.\n\nPlease let us know if you need any additional information.\n\nBest regards,\nPila Pet Registration Administration`);
    // Since we don't have owner email in this context, we'll show an alert
    alert('Please contact the pet owner directly through their profile or contact information.');
}

function finalizeAdoption(applicationId, petId, adopterId) {
    if (!confirm('Finalize this adoption? This will mark the pet as successfully adopted and remove it from available listings.')) {
        return;
    }

    const reviewNotes = prompt('Please add any final review notes (optional):');

    fetch('finalize_adoption.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            application_id: applicationId,
            pet_id: petId,
            adopter_id: adopterId,
            review_notes: reviewNotes
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
