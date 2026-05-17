<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

if (isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

$userPets = getUserPets($_SESSION['user_id']);
?>

<?php include '../includes/header.php'; ?>

<style>
    .dashboard-stats {
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
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin: 0 auto var(--spacing-md);
        font-size: 20px;
    }

    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: #f59e0b;
        margin-bottom: var(--spacing-xs);
    }

    .stat-label {
        font-size: 14px;
        color: var(--color-text-secondary);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: var(--spacing-lg);
        padding: var(--spacing-lg);
        margin: var(--spacing-lg) 0;
    }

    .pet-card {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--color-border);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .pet-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .pet-image {
        height: 140px;
        background: var(--color-bg-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .pet-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pet-image .no-image {
        color: var(--color-text-muted);
        font-size: 36px;
    }

    .pet-badges {
        position: absolute;
        top: var(--spacing-md);
        right: var(--spacing-md);
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xs);
    }

    .pet-badge {
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-md);
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pet-badge.lost {
        background: var(--color-warning);
        color: white;
    }

    .pet-badge.adoption {
        background: var(--color-success);
        color: white;
    }

    .pet-badge.deceased {
        background: var(--color-text-muted);
        color: white;
    }

    .pet-content {
        padding: var(--spacing-lg);
        flex: 1;
        display: flex;
        flex-direction: column;
        background: var(--color-bg); /* ensure body area is visible under header theme */
        color: var(--color-text);
    }

    .pet-name {
        font-size: 18px;
        font-weight: 700;
        color: var(--color-text) !important;
        margin-bottom: var(--spacing-sm);
        text-align: center;
    }

    .pet-category {
        color: var(--color-primary) !important;
    }

    .pet-category {
        font-size: 12px;
        font-weight: 700;
        color: var(--color-primary);
        text-align: center;
        margin-bottom: var(--spacing-md);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pet-details {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xs);
        margin-bottom: var(--spacing-lg);
        flex: 1;
    }

    .pet-detail {
        font-size: 13px;
        color: var(--color-text-secondary);
        line-height: 1.4;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pet-detail-label {
        font-weight: 600;
        color: var(--color-text);
        min-width: 45px;
    }

    .pet-detail-value {
        color: var(--color-text-secondary);
        flex: 1;
        text-align: right;
    }

    .pet-actions {
        display: flex;
        gap: var(--spacing-sm);
        margin-top: auto;
    }

    .btn-pet {
        flex: 1;
        padding: var(--spacing-sm) var(--spacing-md);
        border-radius: var(--radius-lg);
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        text-align: center;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--spacing-xs);
    }

    .btn-pet.primary {
        background: #f59e0b;
        color: white;
        border: 1px solid rgba(0,0,0,0.06);
    }

    .btn-pet.primary:hover {
        background: #d97706;
        transform: translateY(-1px);
    }

    .btn-pet.secondary {
        background: var(--color-bg-tertiary);
        color: var(--color-text);
        border: 1px solid var(--color-border);
    }

    .btn-pet.secondary:hover {
        background: var(--color-bg);
        transform: translateY(-1px);
    }

    .btn-pet.warning {
        background: var(--color-warning);
        color: white;
    }

    .btn-pet.warning:hover {
        background: var(--color-warning-dark, #c82333);
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: var(--spacing-2xl);
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        border: 2px dashed var(--color-border);
    }

    .empty-state-icon {
        font-size: 64px;
        color: var(--color-text-muted);
        margin-bottom: var(--spacing-lg);
    }

    .empty-state-title {
        font-size: 24px;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: var(--spacing-md);
    }

    .empty-state-text {
        font-size: 16px;
        color: var(--color-text-secondary);
        margin-bottom: var(--spacing-xl);
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--spacing-2xl);
        flex-wrap: wrap;
        gap: var(--spacing-md);
    }

    .dashboard-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--color-text);
        margin: 0;
    }

    .quick-actions {
        display: flex;
        gap: var(--spacing-md);
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .dashboard-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .pets-grid {
            grid-template-columns: 1fr;
            padding: var(--spacing-md);
            gap: var(--spacing-md);
        }

        .pet-actions {
            flex-direction: column;
            gap: var(--spacing-sm);
        }

        .btn-pet {
            width: 100%;
        }

        .dashboard-header {
            flex-direction: column;
            align-items: stretch;
        }

        .quick-actions {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .pets-grid {
            padding: var(--spacing-sm);
            gap: var(--spacing-sm);
        }

        .pet-content {
            padding: var(--spacing-md);
        }

        .pet-details {
            margin-bottom: var(--spacing-md);
        }
    }
</style>

<?php
// Get user stats
$db = Database::getInstance();
$conn = $db->getConnection();

$stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE owner_id = {$_SESSION['user_id']} AND archived = 0 AND status = 'approved'");
$totalPets = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE owner_id = {$_SESSION['user_id']} AND lost = 1 AND archived = 0 AND status = 'approved' AND deceased = 0");
$lostPets = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE owner_id = {$_SESSION['user_id']} AND available_for_adoption = 1 AND archived = 0 AND status = 'approved' AND deceased = 0");
$adoptionPets = $stmt->fetch()['total'];

?>

    <div class="dashboard-header">
        <h1 class="dashboard-title">My Pets</h1>
        <div class="quick-actions">
            <a href="register_pet.php" class="btn-pet primary">
                <i class="fas fa-plus"></i>
                Register New Pet
            </a>
            <a href="manage_adoption_applications.php" class="btn-pet secondary">
                <i class="fas fa-clipboard-list"></i>
                Adoption Applications
            </a>
        </div>
    </div>

<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-paw"></i>
        </div>
        <div class="stat-number"><?php echo $totalPets; ?></div>
        <div class="stat-label">Total Pets</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-search"></i>
        </div>
        <div class="stat-number"><?php echo $lostPets; ?></div>
        <div class="stat-label">Lost Pets</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-heart"></i>
        </div>
        <div class="stat-number"><?php echo $adoptionPets; ?></div>
        <div class="stat-label">For Adoption</div>
    </div>

</div>

<?php if (empty($userPets)): ?>
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="fas fa-paw"></i>
        </div>
        <h2 class="empty-state-title">No pets registered yet</h2>
        <p class="empty-state-text">Start building your pet family by registering your first companion!</p>
        <a href="register_pet.php" class="btn-pet primary">
            <i class="fas fa-plus"></i>
            Register Your First Pet
        </a>
    </div>
<?php else: ?>
    <div class="pets-grid">
        <?php foreach ($userPets as $pet): ?>
            <div class="pet-card">
                <div class="pet-image">
<?php if (!empty($pet['photo_path']) && file_exists('../static/uploads/' . $pet['photo_path'])): ?>
                        <img src="../static/uploads/<?php echo htmlspecialchars($pet['photo_path']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                    <?php else: ?>
                        <i class="fas fa-paw no-image"></i>
                    <?php endif; ?>

                    <div class="pet-badges">
                        <?php if ($pet['lost']): ?>
                            <span class="pet-badge lost">Lost</span>
                        <?php endif; ?>
                        <?php if ($pet['available_for_adoption']): ?>
                            <span class="pet-badge adoption">For Adoption</span>
                        <?php endif; ?>
                        <?php if ($pet['deceased']): ?>
                            <span class="pet-badge deceased">Deceased</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pet-content">
                    <div class="pet-category"><?php echo htmlspecialchars($pet['category'] ?? 'PET'); ?></div>
                    <h3 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></h3>

                    <div class="pet-details">
                        <div class="pet-detail">
                            <span class="pet-detail-label">Age:</span>
                            <span class="pet-detail-value"><?php echo $pet['age'] ? htmlspecialchars($pet['age']) . ' yrs' : 'Unknown'; ?></span>
                        </div>
                        <div class="pet-detail">
                            <span class="pet-detail-label">Gender:</span>
                            <span class="pet-detail-value"><?php echo htmlspecialchars($pet['gender'] ?? 'Unknown'); ?></span>
                        </div>
                        <div class="pet-detail">
                            <span class="pet-detail-label">Type:</span>
                            <span class="pet-detail-value"><?php echo htmlspecialchars($pet['pet_type'] ?? 'Unknown'); ?></span>
                        </div>
                        <div class="pet-detail">
                            <span class="pet-detail-label">Color:</span>
                            <span class="pet-detail-value"><?php echo htmlspecialchars($pet['color'] ?? 'Unknown'); ?></span>
                        </div>
                        <div class="pet-detail">
                            <span class="pet-detail-label">Status:</span>
                            <span class="pet-detail-value">
                                <span style="
                                    display: inline-block;
                                    width: 8px;
                                    height: 8px;
                                    border-radius: 50%;
                                    background: <?php echo $pet['status'] === 'approved' ? 'var(--color-success)' : 'var(--color-warning)'; ?>;
                                    margin-right: 4px;
                                "></span>
                                <?php echo ucfirst($pet['status']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="pet-actions">
                        <a href="pet_details.php?id=<?php echo $pet['id']; ?>" class="btn-pet primary">
                            <i class="fas fa-eye"></i>
                            View Details
                        </a>
                        <a href="medical_records.php?pet_id=<?php echo $pet['id']; ?>" class="btn-pet secondary">
                            <i class="fas fa-notes-medical"></i>
                            Records
                        </a>
                        <?php if ($pet['lost']): ?>
                            <button class="btn-pet warning" onclick="markFound(<?php echo $pet['id']; ?>)">
                                <i class="fas fa-check"></i>
                                Mark Found
                            </button>
                        <?php else: ?>
                            <button class="btn-pet warning" onclick="reportLost(<?php echo $pet['id']; ?>)">
                                <i class="fas fa-exclamation-triangle"></i>
                                Report Lost
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Lost Pet Modal -->
<div class="modal-overlay" id="lostPetModal" style="display: none;">
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Report Pet as Lost</h3>
                <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="lostPetForm">
                <div class="modal-body">
                    <input type="hidden" id="lostPetId" name="pet_id">
                    <div class="form-group">
                        <label for="lostComment" class="form-label">Details about how your pet was lost</label>
                        <textarea class="form-textarea" id="lostComment" name="comment" rows="4" required
                                  placeholder="Please provide details about when and where your pet was lost..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-pet secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-pet warning">Report as Lost</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        backdrop-filter: blur(4px);
    }

    .modal-container {
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-content {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--color-border);
    }

    .modal-header {
        padding: var(--spacing-xl);
        border-bottom: 1px solid var(--color-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--color-text);
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: var(--color-text-secondary);
        cursor: pointer;
        padding: var(--spacing-xs);
        border-radius: var(--radius-sm);
        transition: all 0.2s ease;
    }

    .modal-close:hover {
        background: var(--color-bg-secondary);
        color: var(--color-text);
    }

    .modal-body {
        padding: var(--spacing-xl);
    }

    .modal-footer {
        padding: var(--spacing-xl);
        border-top: 1px solid var(--color-border);
        display: flex;
        gap: var(--spacing-md);
        justify-content: flex-end;
    }
</style>

<script>
function reportLost(petId) {
    document.getElementById('lostPetId').value = petId;
    document.getElementById('lostPetModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('lostPetModal').style.display = 'none';
    document.body.style.overflow = '';
}

function markFound(petId) {
    if (confirm('Are you sure you want to mark this pet as found?')) {
        fetch('mark_found.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                pet_id: petId,
                comment: 'Pet has been found and returned home.'
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
}

// Handle lost pet form submission
document.getElementById('lostPetForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const petId = formData.get('pet_id');
    const comment = formData.get('comment');

    fetch('report_lost.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            pet_id: petId,
            comment: comment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

// Close modal when clicking outside
document.getElementById('lostPetModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('lostPetModal').style.display === 'flex') {
        closeModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>
