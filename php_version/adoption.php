<?php
session_start();
require_once 'includes/auth.php';
requireLogin();

$db = Database::getInstance();
$conn = $db->getConnection();

// Get pets available for adoption
try {
    $stmt = $conn->prepare("
        SELECT p.*, u.full_name as owner_name, u.email as owner_email,
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

<link rel="stylesheet" href="admin.css">

<style>
    .adoption {
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        color: white;
        padding: var(--spacing-3xl) var(--spacing-2xl);
        border-radius: 0 0 var(--radius-2xl) var(--radius-2xl);
        margin-bottom: var(--spacing-2xl);
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path d="M30,50 Q50,30 70,50 Q50,70 30,50" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="50" r="3" fill="rgba(255,255,255,0.1)"/></svg>');
        animation: heartFloat 25s ease-in-out infinite;
    }

    @keyframes heartFloat {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        33% { transform: translateY(-15px) rotate(120deg); }
        66% { transform: translateY(10px) rotate(240deg); }
    }

    .page-title {
        font-size: 42px;
        font-weight: 800;
        margin-bottom: var(--spacing-md);
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 2;
    }

    .page-subtitle {
        font-size: 20px;
        opacity: 0.95;
        margin: 0;
        font-weight: 300;
        position: relative;
        z-index: 2;
    }

    .offer-section {
        background: linear-gradient(135deg, #fef5ff 0%, #fce7f3 100%);
        border-radius: var(--radius-2xl);
        padding: var(--spacing-2xl);
        margin-bottom: var(--spacing-2xl);
        border: 2px solid #d53f8c;
        box-shadow: 0 8px 32px rgba(245, 101, 101, 0.15);
        position: relative;
        overflow: hidden;
    }

    .offer-section::before {
        content: '💝';
        position: absolute;
        top: var(--spacing-lg);
        right: var(--spacing-lg);
        font-size: 24px;
        opacity: 0.3;
    }

    .offer-form {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: var(--spacing-xl);
        align-items: end;
        position: relative;
        z-index: 2;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-sm);
    }

    .form-label {
        font-weight: 600;
        color: #97266d;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-select {
        padding: var(--spacing-md) var(--spacing-lg);
        border: 2px solid #d53f8c;
        border-radius: var(--radius-lg);
        background: white;
        color: var(--color-text);
        font-size: 16px;
        box-shadow: 0 2px 8px rgba(213, 63, 140, 0.1);
        transition: all 0.3s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: #97266d;
        box-shadow: 0 0 0 3px rgba(151, 38, 109, 0.1);
        transform: translateY(-2px);
    }

    .btn-offer {
        background: linear-gradient(135deg, #d53f8c 0%, #97266d 100%);
        color: white;
        border: none;
        padding: var(--spacing-md) var(--spacing-xl);
        border-radius: var(--radius-lg);
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(213, 63, 140, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }

    .btn-offer::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-offer:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 32px rgba(213, 63, 140, 0.4);
    }

    .btn-offer:hover::before {
        left: 100%;
    }

    .btn-offer:active {
        transform: translateY(-1px);
    }

    .pets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .pet-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.2s;
    }

    .pet-card:hover {
        transform: translateY(-2px);
    }

    .pet-image {
        height: 150px;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        font-size: 48px;
    }

    .pet-content {
        padding: 15px;
    }

    .pet-name {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        text-align: center;
        margin-bottom: 10px;
    }

    .pet-category {
        display: inline-block;
        background: #007bff;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .pet-details {
        margin-bottom: 15px;
    }

    .pet-detail {
        margin-bottom: 8px;
        font-size: 14px;
        color: #666;
    }

    .pet-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-pet {
        flex: 1;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        text-align: center;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .btn-pet.primary {
        background: #007bff;
        color: white;
    }

    .btn-pet.primary:hover {
        background: #0056b3;
    }

    .empty-state {
        text-align: center;
        padding: var(--spacing-3xl) var(--spacing-2xl);
        color: #718096;
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: var(--radius-2xl);
        border: 2px dashed #cbd5e0;
        margin: var(--spacing-2xl) 0;
    }

    .empty-icon {
        font-size: 80px;
        margin-bottom: var(--spacing-xl);
        opacity: 0.6;
        filter: drop-shadow(0 4px 12px rgba(0,0,0,0.1));
    }

    .empty-state h3 {
        color: #4a5568;
        font-size: 28px;
        font-weight: 700;
        margin-bottom: var(--spacing-md);
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .empty-state p {
        font-size: 18px;
        margin: 0;
        opacity: 0.8;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        backdrop-filter: blur(8px);
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-content {
        background: white;
        border-radius: var(--radius-2xl);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border: none;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .modal-header {
        padding: var(--spacing-2xl) var(--spacing-2xl) var(--spacing-lg);
        border-bottom: 2px solid #f1f5f9;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: var(--radius-2xl) var(--radius-2xl) 0 0;
        color: white;
    }

    .modal-title {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        text-align: center;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .modal-body {
        padding: var(--spacing-2xl);
    }

    .modal-body textarea {
        width: 100%;
        padding: var(--spacing-lg);
        border: 2px solid #e2e8f0;
        border-radius: var(--radius-lg);
        font-size: 16px;
        font-family: inherit;
        resize: vertical;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .modal-body textarea:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .modal-body label {
        display: block;
        margin-bottom: var(--spacing-md);
        font-weight: 600;
        color: #4a5568;
        font-size: 16px;
    }

    .modal-footer {
        padding: var(--spacing-xl) var(--spacing-2xl);
        border-top: 2px solid #f1f5f9;
        display: flex;
        gap: var(--spacing-lg);
        justify-content: flex-end;
        background: #f8fafc;
        border-radius: 0 0 var(--radius-2xl) var(--radius-2xl);
    }

    .btn-cancel {
        background: var(--color-bg-secondary);
        color: var(--color-text);
        border: 1px solid var(--color-border);
    }

    .btn-cancel:hover {
        background: var(--color-text);
        color: var(--color-bg);
    }

    @media (max-width: 768px) {
        .pets-grid {
            grid-template-columns: 1fr;
        }

        .offer-form {
            grid-template-columns: 1fr;
            gap: var(--spacing-md);
        }

        .pet-details {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="adoption">
    <div class="page-header">
        <h1 class="page-title">Pet Adoption</h1>
        <p class="page-subtitle">Find loving homes for pets in need</p>
    </div>

    <!-- Offer Pet for Adoption Section -->
    <?php if (!empty($userPets)): ?>
    <div class="offer-section">
        <h3 style="margin-bottom: var(--spacing-lg); color: var(--color-text);">
            <i class="fas fa-heart" style="color: var(--color-accent); margin-right: var(--spacing-sm);"></i>
            Offer a Pet for Adoption
        </h3>
        <form class="offer-form" id="offerForm">
            <div class="form-group">
                <label class="form-label">Select Your Pet</label>
                <select class="form-select" id="petSelect" required>
                    <option value="">Choose a pet...</option>
                    <?php foreach ($userPets as $pet): ?>
                        <option value="<?php echo $pet['id']; ?>">
                            <?php echo htmlspecialchars($pet['name']); ?> (<?php echo htmlspecialchars($pet['pet_type'] ?? 'Unknown'); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" class="btn-offer" onclick="offerForAdoption()">
                <i class="fas fa-plus"></i>
                Offer for Adoption
            </button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Available Pets Grid -->
    <div class="pets-grid">
        <?php if (empty($adoptionPets)): ?>
            <div class="empty-state" style="grid-column: 1 / -1;">
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
                        <i class="fas fa-paw"></i>
                    </div>

                    <div class="pet-content">
                        <div class="pet-category"><?php echo htmlspecialchars($pet['category'] ?? 'PET'); ?></div>

                        <h3 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?> <span style="color: #28a745; font-size: 14px;">(For Adoption)</span></h3>

                        <div class="pet-details">
                            <div class="pet-detail"><strong>Age:</strong> <?php echo $pet['age'] ? htmlspecialchars($pet['age']) . ' years' : 'Unknown'; ?></div>
                            <div class="pet-detail"><strong>Gender:</strong> <?php echo htmlspecialchars($pet['gender'] ?? 'Unknown'); ?></div>
                            <div class="pet-detail"><strong>Type:</strong> <?php echo htmlspecialchars($pet['pet_type'] ?? 'Unknown'); ?></div>
                            <div class="pet-detail"><strong>Color:</strong> <?php echo htmlspecialchars($pet['color'] ?? 'Unknown'); ?></div>
                        </div>

                        <div class="pet-actions">
                            <button class="btn-pet primary">Contact Owner</button>
                        </div>
                    </div>
                </div>
                    <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Offer Modal -->
<div class="modal-overlay" id="offerModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Offer Pet for Adoption</h3>
        </div>
        <form id="adoptionForm">
            <div class="modal-body">
                <input type="hidden" id="modalPetId" name="pet_id">
                <div class="form-group">
                    <label class="form-label">Please provide information about why you're offering this pet for adoption</label>
                    <textarea class="form-select" name="comment" rows="4" required
                              placeholder="Describe the pet's personality, any special needs, reason for adoption, etc."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeOfferModal()">Cancel</button>
                <button type="submit" class="btn-offer">Offer for Adoption</button>
            </div>
        </form>
    </div>
</div>

<script>
function offerForAdoption() {
    const petSelect = document.getElementById('petSelect');
    const selectedPetId = petSelect.value;

    if (!selectedPetId) {
        alert('Please select a pet to offer for adoption.');
        return;
    }

    // Find pet name
    const selectedOption = petSelect.options[petSelect.selectedIndex];
    const petName = selectedOption.text.split(' (')[0];

    document.getElementById('modalPetId').value = selectedPetId;
    document.querySelector('#offerModal .modal-title').textContent = `Offer ${petName} for Adoption`;

    document.getElementById('offerModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeOfferModal() {
    document.getElementById('offerModal').style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('adoptionForm').reset();
}

document.getElementById('adoptionForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    // For now, we'll just add a comment. In a real implementation, you'd want to add API endpoints
    // to handle adoption offers properly. For this demo, we'll simulate it.
    fetch('user/offer_adoption.php', {
        method: 'POST',
        body: JSON.stringify({
            pet_id: formData.get('pet_id'),
            comment: formData.get('comment')
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // In a real implementation, you'd want to set available_for_adoption = 1
            // For now, we'll just show success
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

function contactForAdoption(email, petName) {
    const subject = encodeURIComponent(`Adoption Inquiry: ${petName}`);
    const body = encodeURIComponent(`Hello,\n\nI'm interested in adopting ${petName}. Please contact me to discuss the adoption process.\n\nBest regards,`);
    window.open(`mailto:${email}?subject=${subject}&body=${body}`);
}

// Close modal when clicking outside
document.getElementById('offerModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeOfferModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('offerModal').style.display === 'flex') {
        closeOfferModal();
    }
});
</script>

<?php include 'includes/footer.php'; ?>