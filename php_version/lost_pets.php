<?php
session_start();
require_once 'includes/auth.php';
requireLogin();

$db = Database::getInstance();
$conn = $db->getConnection();

// Get lost pets
try {
    $stmt = $conn->prepare("
        SELECT p.*, u.full_name as owner_name, u.email as owner_email,
               u.contact_number as owner_contact
        FROM pets p
        JOIN users u ON p.owner_id = u.id
        WHERE p.lost = 1 AND p.archived = 0 AND p.status = 'approved' AND p.deceased = 0
        ORDER BY p.registered_on DESC
    ");
    $stmt->execute();
    $lostPets = $stmt->fetchAll();
} catch (Exception $e) {
    $lostPets = [];
}

// Get user's pets that could be reported as lost
try {
    $stmt = $conn->prepare("
        SELECT * FROM pets
        WHERE owner_id = ? AND lost = 0 AND archived = 0 AND status = 'approved' AND deceased = 0
        ORDER BY name ASC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $userPets = $stmt->fetchAll();
} catch (Exception $e) {
    $userPets = [];
}
?>

<?php include 'includes/header.php'; ?>

<style>
    .lost-pets {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--spacing-lg);
    }

    .page-header {
        text-align: center;
        margin-bottom: var(--spacing-2xl);
        padding: var(--spacing-2xl) 0;
    }

    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--color-text);
        margin-bottom: var(--spacing-md);
    }

    .page-subtitle {
        font-size: 16px;
        color: var(--color-text-secondary);
        margin: 0;
        max-width: 600px;
        margin: 0 auto;
    }

    .quick-actions {
        display: flex;
        justify-content: center;
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-2xl);
        flex-wrap: wrap;
    }

    .action-card {
        background: var(--color-bg);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        padding: var(--spacing-xl);
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        flex: 1;
        min-width: 200px;
        max-width: 300px;
    }

    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .action-icon {
        font-size: 24px;
        color: var(--color-warning);
        margin-bottom: var(--spacing-md);
    }

    .action-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: var(--spacing-sm);
    }

    .action-description {
        font-size: 14px;
        color: var(--color-text-secondary);
        margin: 0;
    }

    .section-divider {
        height: 1px;
        background: var(--color-border);
        margin: var(--spacing-2xl) 0;
    }

    .pets-section {
        margin-top: var(--spacing-2xl);
    }

    .section-title {
        font-size: 24px;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: var(--spacing-lg);
        text-align: center;
    }

    .pets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: var(--spacing-xl);
    }

    .pet-card {
        background: var(--color-bg);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .pet-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .pet-image {
        height: 180px;
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
        font-size: 48px;
    }

    .pet-badge {
        position: absolute;
        top: var(--spacing-md);
        right: var(--spacing-md);
        background: var(--color-warning);
        color: white;
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-md);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .pet-content {
        padding: var(--spacing-lg);
    }

    .pet-name {
        font-size: 20px;
        font-weight: 700;
        color: var(--color-text);
        margin-bottom: var(--spacing-sm);
        text-align: center;
    }

    .pet-category {
        display: inline-block;
        background: var(--color-warning);
        color: white;
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: var(--spacing-md);
    }

    .pet-details {
        margin-bottom: var(--spacing-lg);
    }

    .pet-detail {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--spacing-xs) 0;
        border-bottom: 1px solid var(--color-border);
    }

    .pet-detail:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: var(--color-text);
        font-size: 14px;
    }

    .detail-value {
        color: var(--color-text-secondary);
        font-size: 14px;
    }

    .pet-owner {
        background: var(--color-bg-secondary);
        padding: var(--spacing-lg);
        border-top: 1px solid var(--color-border);
    }

    .owner-info {
        font-size: 14px;
        color: var(--color-text-secondary);
        margin-bottom: var(--spacing-md);
        text-align: center;
    }

    .btn-pet {
        padding: var(--spacing-sm) var(--spacing-md);
        border-radius: var(--radius-md);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--spacing-xs);
        width: 100%;
    }

    .btn-pet.primary {
        background: var(--color-warning);
        color: white;
    }

    .btn-pet.primary:hover {
        background: var(--color-warning-dark, #c05621);
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: var(--spacing-3xl) var(--spacing-2xl);
        background: var(--color-bg);
        border: 2px dashed var(--color-border);
        border-radius: var(--radius-xl);
        grid-column: 1 / -1;
    }

    .empty-icon {
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

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: var(--spacing-lg);
    }

    .modal-content {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--color-border);
        max-width: 500px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        padding: var(--spacing-xl);
        border-bottom: 1px solid var(--color-border);
        background: var(--color-warning);
        color: white;
        border-radius: var(--radius-xl) var(--radius-xl) 0 0;
    }

    .modal-title {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        text-align: center;
    }

    .modal-body {
        padding: var(--spacing-xl);
    }

    .modal-body textarea {
        width: 100%;
        padding: var(--spacing-md);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-md);
        font-size: 16px;
        font-family: inherit;
        resize: vertical;
        min-height: 100px;
    }

    .modal-body textarea:focus {
        outline: none;
        border-color: var(--color-warning);
    }

    .modal-body label {
        display: block;
        margin-bottom: var(--spacing-md);
        font-weight: 600;
        color: var(--color-text);
    }

    .modal-footer {
        padding: var(--spacing-xl);
        border-top: 1px solid var(--color-border);
        display: flex;
        gap: var(--spacing-md);
        justify-content: flex-end;
        background: var(--color-bg-secondary);
        border-radius: 0 0 var(--radius-xl) var(--radius-xl);
    }

    .btn-cancel {
        background: var(--color-bg);
        color: var(--color-text);
        border: 1px solid var(--color-border);
        padding: var(--spacing-sm) var(--spacing-lg);
        border-radius: var(--radius-md);
        cursor: pointer;
    }

    .btn-cancel:hover {
        background: var(--color-text);
        color: var(--color-bg);
    }

    @media (max-width: 768px) {
        .lost-pets {
            padding: var(--spacing-md);
        }

        .pets-grid {
            grid-template-columns: 1fr;
            gap: var(--spacing-lg);
        }

        .quick-actions {
            flex-direction: column;
            align-items: center;
        }

        .action-card {
            width: 100%;
            max-width: 400px;
        }
    }
</style>

<div class="lost-pets">
    <div class="page-header">
        <h1 class="page-title">Lost Pets</h1>
        <p class="page-subtitle">Help reunite lost pets with their owners</p>
    </div>

    <div class="quick-actions">
        <?php if (!empty($userPets)): ?>
        <div class="action-card" onclick="document.getElementById('reportModal').style.display='flex'">
            <div class="action-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="action-title">Report Lost Pet</div>
            <div class="action-description">Let others know about your missing pet</div>
        </div>
        <?php endif; ?>

        <div class="action-card" onclick="document.getElementById('helpModal').style.display='flex'">
            <div class="action-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="action-title">How to Help</div>
            <div class="action-description">Tips for finding lost pets</div>
        </div>
    </div>

    <div class="section-divider"></div>

    <div class="pets-section">
        <h2 class="section-title">Reported Lost Pets</h2>

        <div class="pets-grid">
            <?php if (empty($lostPets)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>No Lost Pets Reported</h3>
                    <p>There are currently no reported lost pets. Check back later!</p>
                </div>
            <?php else: ?>
                <?php foreach ($lostPets as $pet): ?>
                    <div class="pet-card">
                        <div class="pet-image">
                            <?php if (!empty($pet['photo_path']) && file_exists('../uploads/' . $pet['photo_path'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($pet['photo_path']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-paw no-image"></i>
                            <?php endif; ?>
                            <div class="pet-badge">Lost</div>
                        </div>

                        <div class="pet-content">
                            <div class="pet-category"><?php echo htmlspecialchars($pet['category'] ?? 'PET'); ?></div>
                            <h3 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></h3>

                            <div class="pet-details">
                                <div class="pet-detail">
                                    <span class="detail-label">Age:</span>
                                    <span class="detail-value"><?php echo $pet['age'] ? htmlspecialchars($pet['age']) . ' years' : 'Unknown'; ?></span>
                                </div>
                                <div class="pet-detail">
                                    <span class="detail-label">Gender:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($pet['gender'] ?? 'Unknown'); ?></span>
                                </div>
                                <div class="pet-detail">
                                    <span class="detail-label">Type:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($pet['pet_type'] ?? 'Unknown'); ?></span>
                                </div>
                                <div class="pet-detail">
                                    <span class="detail-label">Color:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($pet['color'] ?? 'Unknown'); ?></span>
                                </div>
                            </div>

                            <div class="pet-owner">
                                <div class="owner-info">
                                    <strong>Owner:</strong> <?php echo htmlspecialchars($pet['owner_name']); ?><br>
                                    <strong>Contact:</strong> <?php echo htmlspecialchars($pet['owner_contact'] ?? 'Not provided'); ?>
                                </div>
                                <button class="btn-pet primary" onclick="contactOwner('<?php echo htmlspecialchars($pet['owner_email']); ?>', '<?php echo htmlspecialchars($pet['name']); ?>')">
                                    <i class="fas fa-envelope"></i>
                                    Contact Owner
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div class="modal-overlay" id="helpModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">How to Help Find Lost Pets</h3>
        </div>
        <div class="modal-body">
            <div style="margin-bottom: var(--spacing-lg);">
                <h4 style="color: var(--color-text); margin-bottom: var(--spacing-md);">If You Found a Lost Pet:</h4>
                <ul style="color: var(--color-text-secondary); line-height: 1.6;">
                    <li>Check for identification tags or microchips</li>
                    <li>Contact local animal shelters and veterinarians</li>
                    <li>Post on social media and community groups</li>
                    <li>Use this platform to check for lost pet reports</li>
                </ul>
            </div>
            <div style="margin-bottom: var(--spacing-lg);">
                <h4 style="color: var(--color-text); margin-bottom: var(--spacing-md);">If Your Pet is Lost:</h4>
                <ul style="color: var(--color-text-secondary); line-height: 1.6;">
                    <li>Report your pet immediately using our system</li>
                    <li>Search your neighborhood and check with neighbors</li>
                    <li>Contact local animal control and shelters</li>
                    <li>Post flyers and use social media</li>
                </ul>
            </div>
            <div>
                <h4 style="color: var(--color-text); margin-bottom: var(--spacing-md);">Prevention Tips:</h4>
                <ul style="color: var(--color-text-secondary); line-height: 1.6;">
                    <li>Keep pets on leashes when outside</li>
                    <li>Microchip your pets and keep tags current</li>
                    <li>Keep recent photos of your pets</li>
                    <li>Create a "pet emergency kit" with supplies</li>
                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeHelpModal()">Close</button>
        </div>
    </div>
</div>

<!-- Report Lost Pet Modal -->
<div class="modal-overlay" id="reportModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Report Lost Pet</h3>
        </div>
        <form id="lostPetForm">
            <div class="modal-body">
                <input type="hidden" id="modalPetId" name="pet_id">
                <div style="margin-bottom: var(--spacing-lg);">
                    <label style="display: block; margin-bottom: var(--spacing-md); font-weight: 600; color: var(--color-text);">
                        Select Your Pet
                    </label>
                    <select id="petSelect" style="width: 100%; padding: var(--spacing-md); border: 1px solid var(--color-border); border-radius: var(--radius-md);" required>
                        <option value="">Choose a pet...</option>
                        <?php foreach ($userPets as $pet): ?>
                            <option value="<?php echo $pet['id']; ?>">
                                <?php echo htmlspecialchars($pet['name']); ?> (<?php echo htmlspecialchars($pet['pet_type'] ?? 'Unknown'); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: var(--spacing-md); font-weight: 600; color: var(--color-text);">
                        Details About the Loss
                    </label>
                    <textarea name="comment" rows="4" style="width: 100%; padding: var(--spacing-md); border: 1px solid var(--color-border); border-radius: var(--radius-md); font-family: inherit;" required
                              placeholder="Describe when and where you last saw your pet, any distinctive features, etc."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeReportModal()">Cancel</button>
                <button type="submit" style="background: var(--color-warning); color: white; border: none; padding: var(--spacing-sm) var(--spacing-lg); border-radius: var(--radius-md); cursor: pointer;">Report Pet as Lost</button>
            </div>
        </form>
    </div>
</div>

<script>
function closeHelpModal() {
    document.getElementById('helpModal').style.display = 'none';
}

function reportLostPet() {
    document.getElementById('reportModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeReportModal() {
    document.getElementById('reportModal').style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('lostPetForm').reset();
}

function contactOwner(email, petName) {
    const subject = encodeURIComponent(`Regarding Lost Pet: ${petName}`);
    const body = encodeURIComponent(`Hello,\n\nI may have found your lost pet ${petName}. Please contact me to discuss.\n\nBest regards,`);
    window.open(`mailto:${email}?subject=${subject}&body=${body}`);
}

document.getElementById('lostPetForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const petSelect = document.getElementById('petSelect');
    if (!petSelect.value) {
        alert('Please select a pet to report as lost.');
        return;
    }

    const formData = new FormData(this);

    fetch('user/report_lost.php', {
        method: 'POST',
        body: JSON.stringify({
            pet_id: petSelect.value,
            comment: formData.get('comment')
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Pet reported as lost successfully!');
            closeReportModal();
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

// Close modals when clicking outside
document.getElementById('helpModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeHelpModal();
    }
});

document.getElementById('reportModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReportModal();
    }
});

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (document.getElementById('helpModal').style.display === 'flex') {
            closeHelpModal();
        }
        if (document.getElementById('reportModal').style.display === 'flex') {
            closeReportModal();
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>