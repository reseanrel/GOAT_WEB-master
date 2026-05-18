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
    /* Dashboard - warm modern rescue UI (like lost/adoption) */
    .dashboard-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--spacing-lg);
    }

    .dashboard-hero {
        position: relative;
        border-radius: var(--radius-xl, 20px);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: var(--shadow-lg);
        background: #fff7ed;
        margin-bottom: var(--spacing-2xl);
    }

    .dashboard-hero::before {
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

    .dashboard-hero-inner {
        position: relative;
        display: grid;
        grid-template-columns: 1.25fr 0.75fr;
        gap: var(--spacing-xl);
        padding: var(--spacing-2xl);
        align-items: center;
    }

    .dashboard-kicker {
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-xs);
        padding: 8px 12px;
        border-radius: var(--radius-lg, 16px);
        background: rgba(245,158,11,0.16);
        border: 1px solid rgba(245,158,11,0.25);
        width: fit-content;
        font-weight: 900;
        color: #9a3412;
        margin-bottom: var(--spacing-md);
    }

    .dashboard-title {
        font-size: 44px;
        line-height: 1.05;
        font-weight: 900;
        color: rgba(17,24,39,0.95);
        margin: 0 0 var(--spacing-md);
        letter-spacing: -0.5px;
    }

    .dashboard-subtitle {
        margin: 0 0 var(--spacing-lg);
        color: rgba(17,24,39,0.72);
        font-size: 16px;
        max-width: 620px;
        font-weight: 650;
        line-height: 1.6;
    }

    .dashboard-hero-actions {
        display: flex;
        gap: var(--spacing-md);
        flex-wrap: wrap;
        align-items: center;
    }

    .btn-primary-cta {
        background: var(--color-warning);
        color: #fff;
        border: none;
        border-radius: var(--radius-lg, 18px);
        padding: 14px 18px;
        font-size: 15px;
        font-weight: 1000;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 20px rgba(217,119,6,0.22);
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
        min-width: 240px;
        justify-content: center;
    }

    .btn-primary-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(217,119,6,0.28);
        filter: saturate(1.05);
    }

    .btn-secondary-cta {
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(0,0,0,0.08);
        color: rgba(17,24,39,0.88);
        border-radius: var(--radius-lg, 18px);
        padding: 13px 16px;
        font-size: 15px;
        font-weight: 1000;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        min-width: 200px;
        justify-content: center;
    }

    .btn-secondary-cta:hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,0.92);
        box-shadow: var(--shadow-md);
    }

    .dashboard-stat {
        width: 100%;
        max-width: 320px;
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        padding: var(--spacing-lg);
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .dashboard-stat .label {
        font-weight: 950;
        color: rgba(17,24,39,0.66);
        margin-bottom: 10px;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .dashboard-stat .value {
        font-size: 30px;
        font-weight: 1000;
        color: rgba(17,24,39,0.92);
        line-height: 1.1;
        margin-bottom: 8px;
    }

    .dashboard-stat .hint {
        margin-top: 10px;
        font-size: 13px;
        color: rgba(17,24,39,0.62);
        font-weight: 650;
    }

    .dashboard-section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-lg);
    }

    .dashboard-section-head h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
    }

    .dashboard-section-head p {
        margin: 0;
        color: rgba(17,24,39,0.62);
        font-weight: 650;
        max-width: 560px;
        line-height: 1.5;
    }

    .dashboard-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-2xl);
    }

    .dashboard-stat-item {
        background: rgba(255,255,255,0.92);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        padding: var(--spacing-xl);
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        text-align: center;
    }

    .dashboard-stat-item:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(217,119,6,0.25);
    }

    .dashboard-stat-item .num {
        font-size: 36px;
        font-weight: 1000;
        color: rgba(17,24,39,0.92);
        margin-bottom: 10px;
    }

    .dashboard-stat-item .lbl {
        font-size: 13px;
        font-weight: 950;
        color: rgba(17,24,39,0.62);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    /* Pets cards */
    .pets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: var(--spacing-lg);
        padding: 0;
        margin: var(--spacing-lg) 0;
        align-items: stretch;
    }

    .pet-card {
        background: rgba(255,255,255,0.92);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        display: flex;
        flex-direction: column;
        min-height: 520px;
    }

    .pet-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(217,119,6,0.25);
    }

    .pet-image {
        height: 190px;
        position: relative;
        background: #fff;
    }

    .pet-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .pet-image .no-image {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 52px;
        color: rgba(17,24,39,0.35);
        background: rgba(17,24,39,0.03);
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
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 1000;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #fff;
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(255,255,255,0.18);
    }

    .pet-badge.lost { background: #dc2626; }
    .pet-badge.adoption { background: #f59e0b; }
    .pet-badge.deceased { background: rgba(17,24,39,0.55); }

    .pet-content {
        padding: var(--spacing-lg);
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: rgba(255,255,255,0.92);
    }

    .pet-category {
        font-size: 12px;
        font-weight: 1000;
        color: rgba(26,115,232,0.95);
        text-align: center;
        margin-top: 2px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pet-name {
        font-size: 18px;
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
        margin: 0;
        text-align: center;
        line-height: 1.2;
    }

    .pet-details {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xs);
        margin: var(--spacing-md) 0 var(--spacing-lg);
    }

    .pet-detail {
        font-size: 13px;
        color: rgba(17,24,39,0.62);
        line-height: 1.4;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        white-space: nowrap;
    }

    .pet-detail-label {
        font-weight: 900;
        color: rgba(17,24,39,0.8);
        min-width: 50px;
    }

    .pet-detail-value {
        color: rgba(17,24,39,0.7);
        flex: 1;
        text-align: right;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pet-actions {
        display: flex;
        gap: var(--spacing-sm);
        margin-top: auto;
    }

    .btn-pet {
        flex: 1;
        border-radius: var(--radius-lg);
        font-size: 12px;
        font-weight: 900;
        text-decoration: none;
        text-align: center;
        border: none;
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: var(--spacing-xs);
        padding: 12px 10px;
    }

    .btn-pet.primary {
        background: #f59e0b;
        color: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: 0 10px 20px rgba(217,119,6,0.18);
    }

    .btn-pet.primary:hover {
        transform: translateY(-1px);
        background: #d97706;
        box-shadow: 0 14px 28px rgba(217,119,6,0.25);
    }

    .btn-pet.secondary {
        background: rgba(255,255,255,0.85);
        color: rgba(17,24,39,0.92);
        border: 1px solid rgba(0,0,0,0.08);
    }

    .btn-pet.secondary:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        background: rgba(255,255,255,0.95);
    }

    .btn-pet.warning {
        background: #f59e0b;
        color: #fff;
        border: 1px solid rgba(0,0,0,0.06);
    }

    .empty-state {
        text-align: center;
        padding: var(--spacing-2xl);
        background: rgba(255,255,255,0.92);
        border-radius: var(--radius-xl);
        border: 2px dashed rgba(0,0,0,0.12);
        box-shadow: 0 10px 25px rgba(0,0,0,0.03);
    }

    .empty-state-icon {
        font-size: 64px;
        color: rgba(17,24,39,0.35);
        margin-bottom: var(--spacing-lg);
    }

    .empty-state-title {
        font-size: 24px;
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
        margin-bottom: var(--spacing-md);
    }

    .empty-state-text {
        font-size: 16px;
        color: rgba(17,24,39,0.62);
        margin-bottom: var(--spacing-xl);
        font-weight: 650;
    }

    @media (max-width: 768px) {
        .dashboard-hero-inner { grid-template-columns: 1fr; }
        .pets-grid { grid-template-columns: 1fr; }
        .pet-actions { flex-direction: column; }
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

<div class="dashboard-page">
    <section class="dashboard-hero" aria-label="My Pets hero">
        <div class="dashboard-hero-inner">
            <div>
                <div class="dashboard-kicker">
                    <i class="fas fa-paw"></i>
                    My Pets • Register, track, protect
                </div>

                <h1 class="dashboard-title">My Pets</h1>
                <p class="dashboard-subtitle">
                    Manage your registered companions, report lost status, and keep adoption info up-to-date.
                </p>

                <div class="dashboard-hero-actions">
                    <a href="register_pet.php" class="btn-primary-cta">
                        <i class="fas fa-plus"></i>
                        Register New Pet
                    </a>

                    <a href="manage_adoption_applications.php" class="btn-secondary-cta">
                        <i class="fas fa-clipboard-list"></i>
                        Adoption Applications
                    </a>
                </div>
            </div>

            <div class="dashboard-stat">
                <div class="label">
                    <i class="fas fa-users"></i>
                    Community snapshot
                </div>
                <div class="value"><?php echo (int)$totalPets; ?></div>
                <div class="hint">Total pets registered under your account</div>
            </div>
        </div>
    </section>

    <div class="dashboard-section-head">
        <div>
            <h2>At a glance</h2>
            <p>Quick counts for your pets, lost reports, and adoption-ready listings.</p>
        </div>
    </div>

    <div class="dashboard-stats-grid">
        <div class="dashboard-stat-item">
            <span class="num"><?php echo (int)$totalPets; ?></span>
            <p class="lbl">Total Pets</p>
        </div>

        <div class="dashboard-stat-item">
            <span class="num"><?php echo (int)$lostPets; ?></span>
            <p class="lbl">Lost Pets</p>
        </div>

        <div class="dashboard-stat-item">
            <span class="num"><?php echo (int)$adoptionPets; ?></span>
            <p class="lbl">For Adoption</p>
        </div>
    </div>

    <?php if (empty($userPets)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-paw"></i>
            </div>
            <h2 class="empty-state-title">No pets registered yet</h2>
            <p class="empty-state-text">Start building your pet family by registering your first companion!</p>
            <a href="register_pet.php" class="btn-primary-cta" style="margin:0 auto; width: auto; min-width: 240px;">
                <i class="fas fa-plus"></i>
                Register Your First Pet
            </a>
        </div>
    <?php else: ?>
        <div class="pets-grid">
            <?php foreach ($userPets as $pet): ?>
                <div class="pet-card">
                    <div class="pet-image">
                        <?php if (!empty($pet['photo_path']) && file_exists('../uploads/' . $pet['photo_path'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($pet['photo_path']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                        <?php else: ?>
                            <div class="no-image">
                                <i class="fas fa-paw"></i>
                            </div>
                        <?php endif; ?>

                        <div class="pet-badges">
                            <?php if (!empty($pet['lost'])): ?>
                                <span class="pet-badge lost">Lost</span>
                            <?php endif; ?>
                            <?php if (!empty($pet['available_for_adoption'])): ?>
                                <span class="pet-badge adoption">For Adoption</span>
                            <?php endif; ?>
                            <?php if (!empty($pet['deceased'])): ?>
                                <span class="pet-badge deceased">Deceased</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="pet-content">
                        <div class="pet-category"><?php echo htmlspecialchars($pet['category'] ?? 'PET'); ?></div>
                        <h3 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></h3>

                        <div class="pet-details">
                            <div class="pet-detail">
                                <span class="pet-detail-label">Age</span>
                                <span class="pet-detail-value"><?php echo $pet['age'] ? htmlspecialchars((string)$pet['age']) . ' yrs' : 'Unknown'; ?></span>
                            </div>
                            <div class="pet-detail">
                                <span class="pet-detail-label">Gender</span>
                                <span class="pet-detail-value"><?php echo htmlspecialchars($pet['gender'] ?? 'Unknown'); ?></span>
                            </div>
                            <div class="pet-detail">
                                <span class="pet-detail-label">Type</span>
                                <span class="pet-detail-value"><?php echo htmlspecialchars($pet['pet_type'] ?? 'Unknown'); ?></span>
                            </div>
                            <div class="pet-detail">
                                <span class="pet-detail-label">Color</span>
                                <span class="pet-detail-value"><?php echo htmlspecialchars($pet['color'] ?? 'Unknown'); ?></span>
                            </div>
                            <div class="pet-detail">
                                <span class="pet-detail-label">Status</span>
                                <span class="pet-detail-value">
                                    <?php echo ucfirst((string)($pet['status'] ?? '')); ?>
                                </span>
                            </div>
                        </div>

                        <div class="pet-actions">
                            <a href="pet_details.php?id=<?php echo (int)$pet['id']; ?>" class="btn-pet primary">
                                <i class="fas fa-eye"></i>
                                View Details
                            </a>

                            <a href="medical_records.php?pet_id=<?php echo (int)$pet['id']; ?>" class="btn-pet secondary">
                                <i class="fas fa-notes-medical"></i>
                                Records
                            </a>

                            <?php if (!empty($pet['lost'])): ?>
                                <button class="btn-pet warning" onclick="markFound(<?php echo (int)$pet['id']; ?>)" style="flex: 1;">
                                    <i class="fas fa-check"></i>
                                    Mark Found
                                </button>
                            <?php else: ?>
                                <button class="btn-pet warning" onclick="reportLost(<?php echo (int)$pet['id']; ?>)" style="flex: 1;">
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

        .modal-body { padding: var(--spacing-xl); }
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
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ pet_id: petId, comment: 'Pet has been found and returned home.' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) location.reload();
                    else alert('Error: ' + data.message);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }

        document.getElementById('lostPetForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const petId = formData.get('pet_id');
            const comment = formData.get('comment');

            fetch('report_lost.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ pet_id: petId, comment: comment })
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

        document.getElementById('lostPetModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('lostPetModal').style.display === 'flex') closeModal();
        });
    </script>
</div>

<?php include '../includes/footer.php'; ?>
