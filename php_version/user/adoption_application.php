<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Location: ../adoption.php');
    exit();
}

$petId = $_GET['pet_id'] ?? null;
if (!$petId) {
    header('Location: ../adoption.php');
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

// Get pet details
$stmt = $conn->prepare("
    SELECT p.*, u.full_name as owner_name, u.email as owner_email,
           u.contact_number as owner_contact
    FROM pets p
    JOIN users u ON p.owner_id = u.id
    WHERE p.id = ? AND p.available_for_adoption = 1 AND p.archived = 0 AND p.status = 'approved' AND p.deceased = 0
");
$stmt->execute([$petId]);
$pet = $stmt->fetch();

if (!$pet) {
    header('Location: ../adoption.php');
    exit();
}

// Check if user already applied for this pet
$stmt = $conn->prepare("
    SELECT id FROM adoption_applications
    WHERE pet_id = ? AND applicant_id = ? AND status IN ('pending', 'under_review', 'approved')
");
$stmt->execute([$petId, $_SESSION['user_id']]);
$existingApplication = $stmt->fetch();

if ($existingApplication) {
    header('Location: ../adoption.php?message=already_applied');
    exit();
}
?>

<?php include '../includes/header.php'; ?>

<style>
    .application-form {
        max-width: 800px;
        margin: 0 auto;
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        padding: var(--spacing-2xl);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--color-border);
    }

    .application-header {
        text-align: center;
        margin-bottom: var(--spacing-2xl);
        padding-bottom: var(--spacing-lg);
        border-bottom: 2px solid var(--color-border);
    }

    .pet-summary {
        background: linear-gradient(135deg, var(--color-bg-secondary), var(--color-bg));
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
        border: 1px solid var(--color-border);
    }

    .pet-summary h3 {
        color: var(--color-text);
        margin-bottom: var(--spacing-md);
        font-size: 18px;
    }

    .pet-summary p {
        margin: var(--spacing-xs) 0;
        color: var(--color-text-secondary);
    }

    .form-section {
        margin-bottom: var(--spacing-2xl);
        padding: var(--spacing-lg);
        background: var(--color-bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--color-border);
    }

    .form-section h4 {
        color: var(--color-text);
        margin-bottom: var(--spacing-lg);
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-sm);
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-weight: 600;
        color: var(--color-text);
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-input, .form-select, .form-textarea {
        padding: var(--spacing-md);
        border: 2px solid var(--color-border);
        border-radius: var(--radius-lg);
        background: var(--color-bg);
        color: var(--color-text);
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
        font-family: inherit;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        margin-top: var(--spacing-sm);
    }

    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--color-primary);
    }

    .checkbox-group label {
        font-weight: 500;
        color: var(--color-text-secondary);
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        gap: var(--spacing-md);
        justify-content: center;
        margin-top: var(--spacing-2xl);
        padding-top: var(--spacing-xl);
        border-top: 2px solid var(--color-border);
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
        color: white;
        border: none;
        padding: var(--spacing-lg) var(--spacing-2xl);
        border-radius: var(--radius-lg);
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(74, 144, 226, 0.3);
    }

    .btn-cancel {
        background: var(--color-bg-secondary);
        color: var(--color-text);
        border: 2px solid var(--color-border);
        padding: var(--spacing-lg) var(--spacing-2xl);
        border-radius: var(--radius-lg);
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-cancel:hover {
        background: var(--color-text);
        color: var(--color-bg);
    }

    @media (max-width: 768px) {
        .application-form {
            margin: var(--spacing-md);
            padding: var(--spacing-lg);
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: var(--spacing-md);
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-submit, .btn-cancel {
            width: 100%;
        }
    }
</style>

<div class="application-form">
    <div class="application-header">
        <h1 style="color: var(--color-text); margin-bottom: var(--spacing-sm);">Adoption Application</h1>
        <p style="color: var(--color-text-secondary); margin: 0;">Apply to adopt this wonderful pet</p>
    </div>

    <div class="pet-summary">
        <h3><?php echo htmlspecialchars($pet['name']); ?></h3>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($pet['category'] ?? 'Pet'); ?></p>
        <p><strong>Type:</strong> <?php echo htmlspecialchars($pet['pet_type'] ?? 'Unknown'); ?></p>
        <p><strong>Age:</strong> <?php echo $pet['age'] ? htmlspecialchars($pet['age']) . ' years' : 'Unknown'; ?></p>
        <p><strong>Color:</strong> <?php echo htmlspecialchars($pet['color'] ?? 'Unknown'); ?></p>
        <p><strong>Owner:</strong> <?php echo htmlspecialchars($pet['owner_name']); ?></p>
    </div>

    <form id="adoptionApplicationForm" method="POST" action="submit_adoption_application.php">
        <input type="hidden" name="pet_id" value="<?php echo $petId; ?>">

        <!-- Personal Information -->
        <div class="form-section">
            <h4>📋 Personal Information</h4>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" class="form-input" name="full_name" required
                           value="<?php echo htmlspecialchars($_SESSION['user_full_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" class="form-input" name="email" required
                           value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Phone Number *</label>
                    <input type="tel" class="form-input" name="phone" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Age *</label>
                    <input type="number" class="form-input" name="age" min="18" required>
                </div>
            </div>
            <div class="form-group full-width">
                <label class="form-label">Address *</label>
                <textarea class="form-textarea" name="address" required placeholder="Your complete address"></textarea>
            </div>
        </div>

        <!-- Household Information -->
        <div class="form-section">
            <h4>🏠 Household Information</h4>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Housing Type *</label>
                    <select class="form-select" name="housing_type" required>
                        <option value="">Select housing type</option>
                        <option value="house">House</option>
                        <option value="apartment">Apartment</option>
                        <option value="condo">Condominium</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Household Members *</label>
                    <input type="number" class="form-input" name="household_members" min="1" value="1" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Do you have a yard?</label>
                    <select class="form-select" name="has_yard">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Do you have other pets?</label>
                    <select class="form-select" name="has_other_pets" id="hasOtherPets">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>
            <div class="form-group full-width" id="otherPetsDetails" style="display: none;">
                <label class="form-label">Other Pets Details</label>
                <textarea class="form-textarea" name="other_pets_details" placeholder="Describe your other pets (type, age, etc.)"></textarea>
            </div>
        </div>

        <!-- Adoption Information -->
        <div class="form-section">
            <h4>💝 Adoption Information</h4>
            <div class="form-group full-width">
                <label class="form-label">Why do you want to adopt this pet? *</label>
                <textarea class="form-textarea" name="adoption_reason" required
                          placeholder="Tell us why you want to adopt this specific pet and what you're looking for in a companion"></textarea>
            </div>
            <div class="form-group full-width">
                <label class="form-label">Pet Experience *</label>
                <textarea class="form-textarea" name="pet_experience" required
                          placeholder="Describe your experience with pets. Have you owned pets before? What types?"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Preferred Contact Method *</label>
                    <select class="form-select" name="preferred_contact" required>
                        <option value="email">Email</option>
                        <option value="phone">Phone</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Home Visit Allowed?</label>
                    <select class="form-select" name="home_visit_allowed">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="form-section">
            <h4>🚨 Emergency Contact</h4>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Emergency Contact Name *</label>
                    <input type="text" class="form-input" name="emergency_contact_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Emergency Contact Phone *</label>
                    <input type="tel" class="form-input" name="emergency_contact_phone" required>
                </div>
            </div>
        </div>

        <!-- References -->
        <div class="form-section">
            <h4>👥 References</h4>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Reference Name</label>
                    <input type="text" class="form-input" name="reference_name" placeholder="Optional">
                </div>
                <div class="form-group">
                    <label class="form-label">Reference Phone</label>
                    <input type="tel" class="form-input" name="reference_phone" placeholder="Optional">
                </div>
            </div>
        </div>

        <!-- Additional Notes -->
        <div class="form-section">
            <h4>📝 Additional Notes</h4>
            <div class="form-group full-width">
                <label class="form-label">Anything else you'd like to share?</label>
                <textarea class="form-textarea" name="additional_notes"
                          placeholder="Any additional information that might help with your application"></textarea>
            </div>
        </div>

        <!-- Terms and Agreement -->
        <div class="form-section">
            <div class="checkbox-group">
                <input type="checkbox" id="terms" name="terms_agreed" required>
                <label for="terms">I agree to provide accurate information and understand that this application will be reviewed by the pet owner and administrators. *</label>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="history.back()">Cancel</button>
            <button type="submit" class="btn-submit">Submit Application</button>
        </div>
    </form>
</div>

<script>
// Show/hide other pets details
document.getElementById('hasOtherPets').addEventListener('change', function() {
    const detailsDiv = document.getElementById('otherPetsDetails');
    detailsDiv.style.display = this.value === '1' ? 'block' : 'none';
});

// Form validation
document.getElementById('adoptionApplicationForm').addEventListener('submit', function(e) {
    const age = parseInt(this.age.value);
    if (age < 18) {
        e.preventDefault();
        alert('You must be at least 18 years old to adopt a pet.');
        return;
    }

    if (!this.terms_agreed.checked) {
        e.preventDefault();
        alert('Please agree to the terms before submitting your application.');
        return;
    }

    // Show loading state
    const submitBtn = this.querySelector('.btn-submit');
    submitBtn.textContent = 'Submitting...';
    submitBtn.disabled = true;
});
</script>

<?php include '../includes/footer.php'; ?>