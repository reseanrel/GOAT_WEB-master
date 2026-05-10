<?php
session_start();
require_once 'includes/auth.php';
requireLogin();

$db = Database::getInstance();
$conn = $db->getConnection();

// Get pets available for adoption
try {
    $stmt = $conn->prepare("
        SELECT p.*, p.photo_url AS photo_path, u.full_name as owner_name, u.email as owner_email,
               u.contact_number as owner_contact
        FROM pets p
        JOIN users u ON p.owner_id = u.id
        WHERE p.available_for_adoption = 1 AND p.archived = 0 AND p.status = 'approved' AND p.deceased = 0
        ORDER BY p.registered_on DESC
    ");
    $stmt->execute();
    $adoptionPets = $stmt->fetchAll();
} catch (Exception $e) {
    $adoptionPets = [];
}

// Get user's pets that could be put up for adoption
try {
    $stmt = $conn->prepare("
        SELECT * FROM pets
        WHERE owner_id = ? AND available_for_adoption = 0 AND archived = 0 AND status = 'approved' AND deceased = 0
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
    .adoption {
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
        color: var(--color-primary);
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
        background: var(--color-success);
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
        background: var(--color-primary);
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

    .pet-actions {
        display: flex;
        gap: var(--spacing-sm);
    }

    .btn-pet {
        flex: 1;
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
    }

    .btn-pet.primary {
        background: var(--color-primary);
        color: white;
    }

    .btn-pet.primary:hover {
        background: var(--color-primary-dark, #2c5282);
        transform: translateY(-1px);
    }

    .btn-pet.secondary {
        background: var(--color-bg-secondary);
        color: var(--color-text);
        border: 1px solid var(--color-border);
    }

    .btn-pet.secondary:hover {
        background: var(--color-bg);
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
        background: var(--color-primary);
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
        border-color: var(--color-primary);
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
        .adoption {
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

        .pet-actions {
            flex-direction: column;
        }

        .btn-pet {
            width: 100%;
        }
    }
</style>

<div class="adoption">
    <div class="page-header">
        <h1 class="page-title">Pet Adoption</h1>
        <p class="page-subtitle">Find loving homes for pets in need</p>
    </div>

    <div class="quick-actions">
        <div class="action-card" onclick="document.getElementById('adoptionInfoModal').style.display='flex'">
            <div class="action-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="action-title">How It Works</div>
            <div class="action-description">Learn about our adoption process</div>
        </div>

        <?php if (!empty($userPets)): ?>
        <div class="action-card" onclick="document.getElementById('offerModal').style.display='flex'">
            <div class="action-icon">
                <i class="fas fa-plus"></i>
            </div>
            <div class="action-title">List Your Pet</div>
            <div class="action-description">Offer a pet for adoption</div>
        </div>
        <?php endif; ?>
    </div>

    <div class="section-divider"></div>

    <div class="pets-section">
        <h2 class="section-title">Available Pets</h2>

        <div class="pets-grid">
            <?php if (empty($adoptionPets)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>No Pets Available</h3>
                    <p>There are currently no pets available for adoption. Check back later!</p>
                </div>
            <?php else: ?>
                <?php foreach ($adoptionPets as $pet): ?>
                    <div class="pet-card">
                        <div class="pet-image">
                            <?php
                            $photo = $pet['photo_path'] ?? ($pet['photo_url'] ?? '');
                            $photo = is_string($photo) ? $photo : '';
                            ?>
                            <?php if (!empty($photo) && file_exists('../uploads/' . $photo)): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-paw no-image"></i>
                            <?php endif; ?>
                            <div class="pet-badge">For Adoption</div>
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

                            <div class="pet-actions">
                                <a href="user/adoption_application.php?pet_id=<?php echo $pet['id']; ?>" class="btn-pet primary">
                                    <i class="fas fa-heart"></i>
                                    Apply to Adopt
                                </a>
                                <button class="btn-pet secondary" onclick="contactOwner('<?php echo htmlspecialchars($pet['owner_email']); ?>', '<?php echo htmlspecialchars($pet['name']); ?>')">
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

<!-- Information Modal -->
<div class="modal-overlay" id="adoptionInfoModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">How Adoption Works</h3>
        </div>
        <div class="modal-body">
            <div style="margin-bottom: var(--spacing-lg);">
                <h4 style="color: var(--color-text); margin-bottom: var(--spacing-md);">For Potential Adopters:</h4>
                <ul style="color: var(--color-text-secondary); line-height: 1.6;">
                    <li>Browse available pets and submit formal adoption applications</li>
                    <li>Provide detailed information about yourself and your living situation</li>
                    <li>Pet owners and administrators review all applications</li>
                    <li>You'll be contacted if selected for the next steps</li>
                </ul>
            </div>
            <div style="margin-bottom: var(--spacing-lg);">
                <h4 style="color: var(--color-text); margin-bottom: var(--spacing-md);">For Pet Owners:</h4>
                <ul style="color: var(--color-text-secondary); line-height: 1.6;">
                    <li>List your pet for adoption through your dashboard</li>
                    <li>Review applications from potential adopters</li>
                    <li>Administrators oversee the final adoption process</li>
                    <li>Ensure your pet goes to the best possible home</li>
                </ul>
            </div>
            <div>
                <h4 style="color: var(--color-text); margin-bottom: var(--spacing-md);">Why Choose Our Process:</h4>
                <ul style="color: var(--color-text-secondary); line-height: 1.6;">
                    <li>Thorough background checks and home visits</li>
                    <li>Vetted adopters committed to pet welfare</li>
                    <li>Administrative oversight for quality assurance</li>
                    <li>Lifelong support for successful adoptions</li>
                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeInfoModal()">Close</button>
        </div>
    </div>
</div>

<!-- Offer Pet Modal -->
<div class="modal-overlay" id="offerModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Offer Pet for Adoption</h3>
        </div>
        <form id="adoptionForm">
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
                        Adoption Details
                    </label>
                    <textarea name="comment" rows="4" style="width: 100%; padding: var(--spacing-md); border: 1px solid var(--color-border); border-radius: var(--radius-md); font-family: inherit;" required
                              placeholder="Describe the pet's personality, any special needs, reason for adoption, etc."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeOfferModal()">Cancel</button>
                <button type="submit" style="background: var(--color-primary); color: white; border: none; padding: var(--spacing-sm) var(--spacing-lg); border-radius: var(--radius-md); cursor: pointer;">Offer for Adoption</button>
            </div>
        </form>
    </div>
</div>

<script>
function closeInfoModal() {
    document.getElementById('adoptionInfoModal').style.display = 'none';
}

function offerForAdoption() {
    document.getElementById('offerModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeOfferModal() {
    document.getElementById('offerModal').style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('adoptionForm').reset();
}

function contactOwner(email, petName) {
    const subject = encodeURIComponent(`Adoption Inquiry: ${petName}`);
    const body = encodeURIComponent(`Hello,\n\nI'm interested in adopting ${petName}. Please contact me to discuss the adoption process.\n\nBest regards,`);
    window.open(`mailto:${email}?subject=${subject}&body=${body}`);
}

document.getElementById('adoptionForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const petSelect = document.getElementById('petSelect');
    if (!petSelect.value) {
        alert('Please select a pet to offer for adoption.');
        return;
    }

    const formData = new FormData(this);

    fetch('user/offer_adoption.php', {
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
            alert('Pet offered for adoption successfully! (Note: Admin approval required)');
            closeOfferModal();
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
document.getElementById('adoptionInfoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeInfoModal();
    }
});

document.getElementById('offerModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeOfferModal();
    }
});

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (document.getElementById('adoptionInfoModal').style.display === 'flex') {
            closeInfoModal();
        }
        if (document.getElementById('offerModal').style.display === 'flex') {
            closeOfferModal();
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>
