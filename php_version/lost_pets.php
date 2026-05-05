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

<link rel="stylesheet" href="admin.css">

<style>
    .lost-pets {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--spacing-lg);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .page-header {
        background: linear-gradient(135deg, #e6f3ff 0%, #cce7ff 100%);
        color: #2b6cb0;
        padding: var(--spacing-2xl) var(--spacing-xl);
        border-radius: 24px;
        margin-bottom: var(--spacing-2xl);
        text-align: center;
        border: 2px solid #bee3f8;
        box-shadow: 0 4px 20px rgba(66, 153, 225, 0.1);
        position: relative;
    }

    .page-header::before {
        content: '🐾';
        position: absolute;
        top: var(--spacing-lg);
        right: var(--spacing-lg);
        font-size: 32px;
        opacity: 0.6;
    }

    .page-title {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: var(--spacing-md);
        color: #2d3748;
    }

    .page-subtitle {
        font-size: 18px;
        opacity: 0.8;
        margin: 0;
        font-weight: 400;
        color: #4a5568;
    }

    .report-button {
        background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
        color: #c53030;
        border: 2px solid #fc8181;
        padding: var(--spacing-lg) var(--spacing-2xl);
        border-radius: 16px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(252, 129, 129, 0.2);
        margin: var(--spacing-2xl) auto var(--spacing-xl);
        display: block;
        text-align: center;
        max-width: 400px;
    }

    .report-button:hover {
        background: linear-gradient(135deg, #feb2b2 0%, #fc8181 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(252, 129, 129, 0.3);
    }

    .report-button:active {
        transform: translateY(0);
    }

    .report-form {
        background: #f7fafc;
        border-radius: 20px;
        padding: var(--spacing-2xl);
        margin-bottom: var(--spacing-2xl);
        border: 2px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .form-group {
        margin-bottom: var(--spacing-lg);
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #4a5568;
        font-size: 16px;
        margin-bottom: var(--spacing-md);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-select {
        width: 100%;
        padding: var(--spacing-lg);
        border: 2px solid #cbd5e0;
        border-radius: 12px;
        background: white;
        color: #2d3748;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        appearance: none;
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%236b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6,9 12,15 18,9"></polyline></svg>');
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 20px;
        padding-right: 48px;
    }

    .form-select:focus {
        outline: none;
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
        border: none;
        padding: var(--spacing-lg) var(--spacing-2xl);
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(66, 153, 225, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
        display: block;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #3182ce 0%, #2c5282 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(66, 153, 225, 0.4);
    }

    .btn-primary:active {
        transform: translateY(0);
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

    .pet-card {
        background: white;
        border-radius: var(--radius-2xl);
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.8);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        backdrop-filter: blur(10px);
    }

    .pet-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ff6b6b, #ee5a24, #ff3838);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .pet-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 60px rgba(255, 107, 107, 0.2);
    }

    .pet-card:hover::before {
        opacity: 1;
        background: linear-gradient(90deg, #fed7d7, #feb2b2, #fed7d7);
    }

    .pet-image {
        height: 220px;
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
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
        transition: transform 0.4s ease;
    }

    .pet-card:hover .pet-image img {
        transform: scale(1.1);
    }

    .pet-image .placeholder {
        color: #a0aec0;
        font-size: 64px;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
    }

    .pet-info {
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

    .pet-name {
        font-size: 24px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: var(--spacing-md);
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .lost-badge {
        background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
        color: #c53030;
        padding: var(--spacing-sm) var(--spacing-md);
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(252, 129, 129, 0.2);
        border: 1px solid #fc8181;
        animation: gentlePulse 3s infinite;
    }

    @keyframes gentlePulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.02); opacity: 0.9; }
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

    .pet-owner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, #ff6b6b, #ee5a24);
        border-radius: 2px;
    }

    .owner-info {
        font-size: 15px;
        color: #e2e8f0;
        margin-bottom: var(--spacing-lg);
        text-align: center;
    }

    .contact-btn {
        background: linear-gradient(135deg, #68d391 0%, #48bb78 100%);
        color: white;
        border: none;
        padding: var(--spacing-md) var(--spacing-xl);
        border-radius: 16px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 0 auto;
        display: block;
        box-shadow: 0 4px 16px rgba(72, 187, 120, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }

    .contact-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .contact-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 32px rgba(72, 187, 120, 0.4);
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    }

    .contact-btn:hover::before {
        left: 100%;
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
        border-bottom: 2px solid #e2e8f0;
        background: linear-gradient(135deg, #bee3f8 0%, #90cdf4 100%);
        border-radius: 20px 20px 0 0;
        color: #2b6cb0;
    }

    .modal-title {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        text-align: center;
    }

    .modal-body {
        padding: var(--spacing-2xl);
        background: #f7fafc;
    }

    .modal-body textarea {
        width: 100%;
        padding: var(--spacing-lg);
        border: 2px solid #cbd5e0;
        border-radius: 12px;
        font-size: 16px;
        font-family: inherit;
        resize: vertical;
        transition: all 0.3s ease;
        background: white;
        line-height: 1.5;
    }

    .modal-body textarea:focus {
        outline: none;
        border-color: #4299e1;
        background: white;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
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
        border-top: 2px solid #e2e8f0;
        display: flex;
        gap: var(--spacing-lg);
        justify-content: space-between;
        background: #f7fafc;
        border-radius: 0 0 20px 20px;
    }

    .btn-cancel {
        background: #e2e8f0;
        color: #4a5568;
        border: none;
        padding: var(--spacing-md) var(--spacing-lg);
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #cbd5e0;
        transform: translateY(-1px);
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

    /* Responsive Design */
    @media (max-width: 768px) {
        .lost-pets {
            padding: var(--spacing-md);
        }

        .page-header {
            padding: var(--spacing-xl) var(--spacing-lg);
        }

        .page-title {
            font-size: 28px;
        }

        .page-subtitle {
            font-size: 16px;
        }

        .report-button {
            font-size: 16px;
            padding: var(--spacing-md) var(--spacing-lg);
            max-width: 100%;
        }

        .report-form {
            padding: var(--spacing-lg);
        }

        .form-select {
            font-size: 16px;
            padding: var(--spacing-md);
        }

        .btn-primary {
            padding: var(--spacing-md) var(--spacing-lg);
            font-size: 15px;
        }

        .pets-grid {
            grid-template-columns: 1fr;
            gap: var(--spacing-lg);
        }

        .pet-details {
            grid-template-columns: 1fr;
        }

        .contact-btn {
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .page-title {
            font-size: 24px;
        }

        .report-button {
            font-size: 14px;
        }

        .empty-state h3 {
            font-size: 24px;
        }

        .empty-state p {
            font-size: 16px;
        }
    }
</style>

<div class="lost-pets">
    <div class="page-header">
        <h1 class="page-title">Lost Pets</h1>
        <p class="page-subtitle">Help reunite lost pets with their owners</p>
    </div>

    <!-- Report Lost Pet Button -->
    <?php if (!empty($userPets)): ?>
    <button class="report-button" onclick="showReportForm()">
        ⚠️ Report a Lost Pet
    </button>

    <!-- Report Form (Hidden by default) -->
    <div class="report-form" id="reportForm" style="display: none;">
        <div class="form-group">
            <label class="form-label">SELECT YOUR PET</label>
            <select class="form-select" id="petSelect" required>
                <option value="">Choose a pet...</option>
                <?php foreach ($userPets as $pet): ?>
                    <option value="<?php echo $pet['id']; ?>">
                        <?php echo htmlspecialchars($pet['name']); ?> (<?php echo htmlspecialchars($pet['pet_type'] ?? 'Unknown'); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="button" class="btn-primary" onclick="reportLostPet()">
            + REPORT LOST
        </button>
    </div>
    <?php endif; ?>

    <!-- Lost Pets Grid -->
    <div class="pets-grid">
        <?php if (empty($lostPets)): ?>
            <div class="empty-state" style="grid-column: 1 / -1;">
                <div class="empty-icon">
                    <i class="fas fa-paw"></i>
                </div>
                <h3>No Lost Pets</h3>
                <p>There are currently no reported lost pets. Check back later!</p>
            </div>
        <?php else: ?>
            <?php foreach ($lostPets as $pet): ?>
                <div class="pet-card">
                    <div class="pet-image">
                        <i class="fas fa-paw"></i>
                    </div>

                    <div class="pet-info">
                        <div class="pet-category"><?php echo htmlspecialchars($pet['category'] ?? 'PET'); ?></div>

                        <h3 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?> <span style="color: #dc3545; font-size: 14px;">(Lost)</span></h3>

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
                    <div class="pet-owner">
                        <div class="owner-info">
                            <strong>Owner:</strong> <?php echo htmlspecialchars($pet['owner_name']); ?><br>
                            <strong>Contact:</strong> <?php echo htmlspecialchars($pet['owner_contact'] ?? 'Not provided'); ?>
                        </div>
                        <button class="contact-btn" onclick="contactOwner('<?php echo htmlspecialchars($pet['owner_email']); ?>', '<?php echo htmlspecialchars($pet['name']); ?>')">
                            <i class="fas fa-envelope"></i>
                            Contact Owner
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Report Modal -->
<div class="modal-overlay" id="reportModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Report Lost Pet</h3>
        </div>
        <form id="lostPetForm">
            <div class="modal-body">
                <input type="hidden" id="modalPetId" name="pet_id">
                <div class="form-group">
                    <label class="form-label">Please provide details about how your pet was lost</label>
                    <textarea class="form-select" name="comment" rows="4" required
                              placeholder="Describe when and where you last saw your pet, any distinctive features, etc."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeReportModal()">Cancel</button>
                <button type="submit" class="btn-primary" style="margin: 0;">Report Pet</button>
            </div>
        </form>
    </div>
</div>

<script>
function showReportForm() {
    const form = document.getElementById('reportForm');
    const button = document.querySelector('.report-button');

    if (form.style.display === 'none') {
        form.style.display = 'block';
        button.textContent = 'Hide Report Form';
        button.style.background = 'linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%)';
    } else {
        form.style.display = 'none';
        button.textContent = '⚠️ Report a Lost Pet';
        button.style.background = 'linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%)';
    }
}

function reportLostPet() {
    const petSelect = document.getElementById('petSelect');
    const selectedPetId = petSelect.value;

    if (!selectedPetId) {
        alert('Please select a pet to report as lost.');
        return;
    }

    // Find pet name
    const selectedOption = petSelect.options[petSelect.selectedIndex];
    const petName = selectedOption.text.split(' (')[0];

    document.getElementById('modalPetId').value = selectedPetId;
    document.querySelector('#reportModal .modal-title').textContent = `Report ${petName} as Lost`;

    document.getElementById('reportModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeReportModal() {
    document.getElementById('reportModal').style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('lostPetForm').reset();
}

document.getElementById('lostPetForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('user/report_lost.php', {
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

function contactOwner(email, petName) {
    const subject = encodeURIComponent(`Regarding Lost Pet: ${petName}`);
    const body = encodeURIComponent(`Hello,\n\nI may have found your lost pet ${petName}. Please contact me to discuss.\n\nBest regards,`);
    window.open(`mailto:${email}?subject=${subject}&body=${body}`);
}

// Close modal when clicking outside
document.getElementById('reportModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReportModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('reportModal').style.display === 'flex') {
        closeReportModal();
    }
});
</script>

<?php include 'includes/footer.php'; ?>