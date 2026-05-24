<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

$db = Database::getInstance();
$conn = $db->getConnection();

$err = '';
$success = '';

$uid = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim((string)($_POST['first_name'] ?? ''));
    $lastName = trim((string)($_POST['last_name'] ?? ''));
    $age = isset($_POST['age']) ? (int)$_POST['age'] : null;
    $contactNumber = trim((string)($_POST['contact_number'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));

    if ($firstName === '' || $lastName === '') {
        $err = 'First name and last name are required.';
    } elseif ($contactNumber !== '' && !preg_match('/^\d{11}$/', $contactNumber)) {
        $err = 'Contact number must be exactly 11 digits.';
    } elseif ($age !== null && ($age < 1 || $age > 120)) {
        $err = 'Age must be between 1 and 120.';
    } else {
        $fullName = trim($firstName . ' ' . $lastName);

        $stmt = $conn->prepare("
            UPDATE users
            SET full_name = ?, age = ?, contact_number = ?, address = ?
            WHERE id = ? AND archived = 0
        ");
        $stmt->execute([
            $fullName,
            $age,
            $contactNumber,
            $address,
            $uid
        ]);

        $_SESSION['full_name'] = $fullName;
        $success = 'Profile updated successfully.';
    }
}

// Load current user data for the form
$stmt = $conn->prepare("SELECT full_name, age, contact_number, address, email FROM users WHERE id = ? AND archived = 0");
$stmt->execute([$uid]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: dashboard.php');
    exit();
}

$fullName = (string)($user['full_name'] ?? '');
$parts = preg_split('/\s+/', trim($fullName)) ?: [];
$firstNameVal = $parts[0] ?? '';
$lastNameVal = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
$ageVal = $user['age'] ?? '';
$contactVal = $user['contact_number'] ?? '';
$addressVal = $user['address'] ?? '';
$emailVal = $user['email'] ?? '';

$residentStatus = getResidencyStatus($uid);

include '../includes/header.php';
?>

<style>
    /* Profile - warm modern rescue UI */
    .profile-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--spacing-lg);
    }

    .profile-hero {
        position: relative;
        border-radius: var(--radius-xl, 20px);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: var(--shadow-lg);
        background: #fff7ed;
        margin-bottom: var(--spacing-2xl);
    }

    .profile-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 15% 20%, rgba(245,158,11,0.35) 0%, rgba(245,158,11,0) 45%),
            radial-gradient(circle at 80% 30%, rgba(16,185,129,0.25) 0%, rgba(16,185,129,0) 45%),
            radial-gradient(circle at 60% 90%, rgba(37,99,235,0.12) 0%, rgba(37,99,235,0) 55%),
            linear-gradient(180deg, rgba(255,255,255,0.7), rgba(255,255,255,0.35));
        pointer-events: none;
    }

    .profile-hero-inner {
        position: relative;
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: var(--spacing-xl);
        padding: var(--spacing-2xl);
        align-items: center;
    }

    .profile-kicker {
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-xs);
        padding: 8px 12px;
        border-radius: var(--radius-lg, 16px);
        background: rgba(245,158,11,0.16);
        border: 1px solid rgba(245,158,11,0.25);
        width: fit-content;
        font-weight: 800;
        color: #9a3412;
        margin-bottom: var(--spacing-md);
    }

    .profile-title {
        font-size: 42px;
        line-height: 1.05;
        font-weight: 900;
        color: rgba(17,24,39,0.95);
        margin: 0 0 var(--spacing-md);
        letter-spacing: -0.5px;
    }

    .profile-subtitle {
        margin: 0 0 var(--spacing-lg);
        color: rgba(17,24,39,0.72);
        font-size: 16px;
        max-width: 620px;
        font-weight: 600;
    }

    .profile-hero-actions {
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
        font-weight: 900;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 20px rgba(217,119,6,0.22);
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
        min-width: 220px;
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
        font-weight: 900;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        min-width: 180px;
        justify-content: center;
    }

    .btn-secondary-cta:hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,0.92);
        box-shadow: var(--shadow-md);
    }

    .profile-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: var(--spacing-lg);
    }

    .profile-card {
        background: rgba(255,255,255,0.9);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        padding: var(--spacing-xl);
    }

    .profile-card-head {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-lg);
        flex-wrap: wrap;
    }

    .profile-card-title {
        margin: 0;
        font-size: 24px;
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .profile-card-hint {
        margin: 0;
        color: rgba(17,24,39,0.62);
        font-weight: 650;
        font-size: 14px;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    label.field-label {
        font-weight: 900;
        font-size: 13px;
        color: rgba(17,24,39,0.72);
    }

    input.field-input, textarea.field-input {
        width: 100%;
        padding: 12px 12px;
        border: 1px solid var(--color-border);
        border-radius: 12px;
        background: #fff;
        font-family: inherit;
        font-size: 14px;
        outline: none;
    }

    textarea.field-input {
        min-height: 98px;
        resize: vertical;
    }

    .field-note {
        font-size: 12px;
        color: rgba(17,24,39,0.6);
        font-weight: 650;
    }

    .profile-actions {
        display: flex;
        gap: var(--spacing-md);
        justify-content: flex-end;
        margin-top: var(--spacing-lg);
        flex-wrap: wrap;
    }

    .btn-save {
        border: none;
        background: var(--color-warning);
        color: #fff;
        border-radius: var(--radius-lg, 18px);
        padding: 13px 16px;
        font-size: 15px;
        font-weight: 1000;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
        box-shadow: 0 10px 20px rgba(217,119,6,0.22);
        min-width: 220px;
        justify-content: center;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(217,119,6,0.28);
        filter: saturate(1.05);
    }

    .btn-cancel {
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
        min-width: 180px;
        justify-content: center;
    }

    .btn-cancel:hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,0.92);
        box-shadow: var(--shadow-md);
    }

    @media (max-width: 900px) {
        .profile-hero-inner {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .profile-page {
            padding: var(--spacing-md);
        }

        .profile-hero-inner {
            padding: var(--spacing-xl);
        }

        .profile-title {
            font-size: 34px;
        }

        .profile-card {
            padding: var(--spacing-lg);
        }

        .form-grid-2 {
            grid-template-columns: 1fr;
        }

        .profile-actions {
            justify-content: stretch;
        }

        .btn-save, .btn-cancel {
            width: 100%;
        }
    }
</style>

<div class="profile-page">
    <section class="profile-hero" aria-label="Edit Profile hero">
        <div class="profile-hero-inner">
            <div>
                <div class="profile-kicker">
                    <i class="fas fa-user-edit"></i>
                    Keep your details current
                </div>

                <h1 class="profile-title">Edit Profile</h1>
                <p class="profile-subtitle">
                    Update your contact information so the community can reach you quickly when it matters.
                </p>

                <div class="profile-hero-actions">
                    <a class="btn-secondary-cta" href="dashboard.php">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <div>
                <div class="profile-card" style="padding: var(--spacing-lg); margin: 0; background: rgba(255,255,255,0.75);">
                    <div style="font-weight: 1000; color: rgba(17,24,39,0.95); font-size: 18px; display:flex; align-items:center; gap:10px;">
                        <i class="fas fa-id-card"></i>
                        Your Info Preview
                    </div>
                    <div style="margin-top: 12px; display:grid; gap: 8px;">
                        <div class="field-note"><b>Name:</b> <?php echo htmlspecialchars($fullName); ?></div>
                        <div class="field-note"><b>Phone:</b> <?php echo htmlspecialchars((string)$contactVal); ?></div>
                        <div class="field-note"><b>Age:</b> <?php echo htmlspecialchars((string)$ageVal); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="profile-layout">
        <div class="profile-card">
            <div class="profile-card-head">
                <h2 class="profile-card-title">
                    <i class="fas fa-user-edit"></i>
                    Personal Information
                </h2>
                <p class="profile-card-hint">Changes are saved to your account.</p>
            </div>

            <?php if ($err !== ''): ?>
                <div class="alert alert-error" style="margin-bottom: var(--spacing-lg);">
                    <i class="fas fa-exclamation-circle"></i>
                    <div><?php echo htmlspecialchars($err); ?></div>
                </div>
            <?php endif; ?>

            <?php if ($success !== ''): ?>
                <div class="alert alert-success" style="margin-bottom: var(--spacing-lg);">
                    <i class="fas fa-check-circle"></i>
                    <div><?php echo htmlspecialchars($success); ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="edit_profile.php">
                <div class="form-grid-2">
                    <div class="field">
                        <label class="field-label">First Name *</label>
                        <input class="field-input" type="text" name="first_name" value="<?php echo htmlspecialchars($firstNameVal); ?>" required />
                    </div>

                    <div class="field">
                        <label class="field-label">Last Name *</label>
                        <input class="field-input" type="text" name="last_name" value="<?php echo htmlspecialchars($lastNameVal); ?>" required />
                    </div>

                    <div class="field">
                        <label class="field-label">Age</label>
                        <input class="field-input" type="number" name="age" min="1" max="120" value="<?php echo htmlspecialchars((string)$ageVal); ?>" />
                    </div>

                    <div class="field">
                        <label class="field-label">Contact Number</label>
                        <input class="field-input" type="text" name="contact_number" maxlength="11"
                               value="<?php echo htmlspecialchars((string)$contactVal); ?>"
                               placeholder="11-digit mobile number"
                               pattern="\d{11}" />
                        <div class="field-note">11-digit mobile number (e.g., 09123456789)</div>
                    </div>
                </div>

                <div class="field" style="margin-top: var(--spacing-lg);">
                    <label class="field-label">Address</label>
                    <textarea class="field-input" name="address" rows="3"><?php echo htmlspecialchars((string)$addressVal); ?></textarea>
                </div>

                <div class="field" style="margin-top: var(--spacing-lg);">
                    <label class="field-label">Email Address</label>
                    <input class="field-input" type="email" value="<?php echo htmlspecialchars((string)$emailVal); ?>" readonly style="background:#f6f7f8;" />
                    <div class="field-note">Email address cannot be changed.</div>
                </div>

                <div class="profile-actions">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i>
                        Update Profile
                    </button>

                    <a href="dashboard.php" class="btn-cancel">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Residency Verification Section -->
        <div class="profile-card" style="margin-top: var(--spacing-xl);">
            <div class="profile-card-head">
                <h2 class="profile-card-title">
                    <i class="fas fa-id-card"></i>
                    Residency Verification
                </h2>
                <p class="profile-card-hint">Confirm you are a resident of Pila, Laguna.</p>
            </div>

            <?php
                $statusClass = 'status-unverified';
                $statusText = 'Not Verified';
                $statusIcon = 'fa-question-circle';
                if ($residentStatus === 'pending') { $statusClass = 'status-pending'; $statusText = 'Pending Review'; $statusIcon = 'fa-clock'; }
                if ($residentStatus === 'verified') { $statusClass = 'status-approved'; $statusText = 'Verified Resident'; $statusIcon = 'fa-check-circle'; }
                if ($residentStatus === 'rejected') { $statusClass = 'status-rejected'; $statusText = 'Rejected'; $statusIcon = 'fa-times-circle'; }
            ?>
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                <span class="status-badge <?php echo $statusClass; ?>" style="font-size:14px; padding:8px 16px;">
                    <i class="fas <?php echo $statusIcon; ?>"></i> <?php echo $statusText; ?>
                </span>
            </div>

            <a href="verify_residency.php" class="btn-save" style="display:inline-flex; width:auto; padding:10px 20px;">
                <i class="fas fa-id-card"></i> Manage Residency Verification
            </a>
            <p style="font-size:13px; color:var(--color-text-secondary); margin-top:10px;">
                Upload proof of residency (Barangay Certificate, Cedula, etc.) to unlock full features.
            </p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
