<?php
session_start();
require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = sanitizeInput($_POST['first_name']);
    $lastName = sanitizeInput($_POST['last_name']);
    $birthDate = trim((string)($_POST['birth_date'] ?? ''));
    $age = null;
    $contactNumber = sanitizeInput($_POST['contact_number']);

    if ($birthDate !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $birthDate);
        if ($dt !== false) {
            // Age calculation based on birthday
            $now = new DateTime('today');
            $age = (int)$dt->diff($now)->y;
        }
    }
    $address = sanitizeInput($_POST['address']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    $errors = [];

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $errors[] = 'Please fill all required fields';
    }

    if ($birthDate === '') {
        $errors[] = 'Please select your date of birth';
    } elseif ($age === null || $age < 1 || $age > 120) {
        $errors[] = 'Age must be between 1 and 120 (based on date of birth)';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }

    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
        $errors[] = 'Password must contain at least one symbol';
    }

    if ($contactNumber && (!is_numeric($contactNumber) || strlen($contactNumber) !== 11)) {
        $errors[] = 'Contact number must be exactly 11 digits';
    }

    if (empty($errors)) {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered';
        } else {
            try {
                $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

                $fullName = $firstName . ' ' . $lastName;
                session_regenerate_id(true);
                $_SESSION['pending_registration'] = [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'full_name' => $fullName,
                    'age' => $age,
                    'contact_number' => $contactNumber,
                    'address' => $address,
                    'email' => $email,
                    'password' => $password,
                    'verification_code' => $verificationCode,
                    'expires' => time() + 3600
                ];

                require_once 'includes/email.php';
                $emailSent = sendVerificationEmail($email, $verificationCode);

                if ($emailSent) {
                    $_SESSION['success'] = 'Registration successful! Please check your Gmail inbox for the verification code.';
                } else {
                    $_SESSION['show_verification'] = true;
                    $_SESSION['success'] = 'Registration successful! Please check the saved email file for your verification code.';
                    error_log("[WARNING] Gmail SMTP failed, falling back to file storage");
                }

                header('Location: verify_email.php');
                exit();

            } catch (Exception $e) {
                $errors[] = 'An error occurred during registration. Please try again.';
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}
?>

<?php include 'includes/header.php'; ?>

<style>
    /* Register - warm modern rescue UI */
    .auth-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--spacing-lg);
    }

    .auth-hero {
        position: relative;
        border-radius: var(--radius-xl, 20px);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: var(--shadow-lg);
        background: #fff7ed;
        margin-bottom: var(--spacing-2xl);
    }

    .auth-hero::before {
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

    .auth-hero-inner {
        position: relative;
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: var(--spacing-xl);
        padding: var(--spacing-2xl);
        align-items: center;
    }

    .auth-kicker {
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

    .auth-title {
        font-size: 44px;
        line-height: 1.05;
        font-weight: 900;
        color: rgba(17,24,39,0.95);
        margin: 0 0 var(--spacing-md);
        letter-spacing: -0.5px;
    }

    .auth-subtitle {
        margin: 0 0 var(--spacing-lg);
        color: rgba(17,24,39,0.72);
        font-size: 16px;
        max-width: 620px;
        font-weight: 650;
        line-height: 1.6;
    }

    .auth-card {
        background: rgba(255,255,255,0.92);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        padding: var(--spacing-xl);
    }

    .auth-form-title {
        margin: 0 0 var(--spacing-lg);
        font-size: 22px;
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-lg);
    }

    .field {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: var(--spacing-md);
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

    textarea.field-input { min-height: 80px; resize: vertical; }

    input.field-input:focus, textarea.field-input:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.12);
    }

    .form-actions {
        display: flex;
        gap: var(--spacing-md);
        margin-top: var(--spacing-lg);
        align-items: center;
    }

    .btn-primary {
        border: none;
        background: var(--color-primary);
        color: white;
        border-radius: var(--radius-lg, 18px);
        padding: 13px 16px;
        font-size: 15px;
        font-weight: 1000;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
        flex: 1;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(26,115,232,0.18);
        filter: saturate(1.05);
    }

    .auth-links {
        margin-top: var(--spacing-lg);
        padding-top: var(--spacing-lg);
        border-top: 1px solid var(--color-border);
        text-align: center;
        color: rgba(17,24,39,0.62);
        font-weight: 650;
    }

    .auth-links a {
        color: var(--color-primary);
        text-decoration: none;
        font-weight: 900;
    }

    .auth-links a:hover {
        text-decoration: underline;
    }

    .verification-notice {
        margin-top: var(--spacing-lg);
        background: rgba(255,255,255,0.75);
        border: 1px dashed rgba(245,158,11,0.35);
        border-radius: var(--radius-xl, 20px);
        padding: var(--spacing-lg);
        text-align: center;
    }

    .verification-notice h5 {
        margin: 0 0 var(--spacing-sm);
        color: rgba(17,24,39,0.95);
        font-size: 16px;
        font-weight: 1000;
    }

    .verification-notice p {
        margin: 0 0 var(--spacing-md);
        color: rgba(17,24,39,0.72);
        font-weight: 650;
        line-height: 1.5;
        font-size: 14px;
    }

    .verification-code {
        background: rgba(255,255,255,0.9);
        border: 2px dashed rgba(245,158,11,0.5);
        border-radius: var(--radius-lg, 16px);
        padding: var(--spacing-md);
        font-family: 'Monaco','Menlo','Ubuntu Mono',monospace;
        font-size: 18px;
        font-weight: 1000;
        color: rgba(154,52,18,1);
        letter-spacing: 2px;
        display: inline-block;
    }

    @media (max-width: 900px) {
        .auth-hero-inner { grid-template-columns: 1fr; }
        .grid-2 { grid-template-columns: 1fr; }
    }
</style>

<div class="auth-page">
    <section class="auth-hero" aria-label="Register hero">
        <div class="auth-hero-inner">
            <div>
                <div class="auth-kicker">
                    <i class="fas fa-paw"></i>
                    Join Pila Pet Community
                </div>

                <h1 class="auth-title">Create Your Account</h1>
                <p class="auth-subtitle">
                    Register your details so you can manage pets, report lost animals, and track adoption applications.
                </p>
            </div>

            <div class="auth-card">
                <h2 class="auth-form-title">
                    <i class="fas fa-user-plus"></i>
                    Register
                </h2>

                <form method="POST">
                    <div class="grid-2">
                        <div class="field">
                            <label class="field-label" for="first_name">First Name *</label>
                            <input class="field-input" type="text" id="first_name" name="first_name"
                                   placeholder="Enter your first name" required
                                   value="<?php echo isset($_POST['first_name']) ? htmlspecialchars((string)$_POST['first_name']) : ''; ?>">
                        </div>

                        <div class="field">
                            <label class="field-label" for="last_name">Last Name *</label>
                            <input class="field-input" type="text" id="last_name" name="last_name"
                                   placeholder="Enter your last name" required
                                   value="<?php echo isset($_POST['last_name']) ? htmlspecialchars((string)$_POST['last_name']) : ''; ?>">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label class="field-label" for="birth_date">Date of Birth *</label>
                            <input
                                class="field-input"
                                type="date"
                                id="birth_date"
                                name="birth_date"
                                required
                                value="<?php echo isset($_POST['birth_date']) ? htmlspecialchars((string)$_POST['birth_date']) : ''; ?>"
                            >
                            <div class="field-note" style="font-size:12px;color:rgba(17,24,39,0.6);font-weight:650;">
                                Age will be calculated automatically.
                            </div>
                        </div>

                        <div class="field">
                            <label class="field-label" for="contact_number">Contact Number</label>
                            <input class="field-input" type="text" id="contact_number" name="contact_number"
                                   placeholder="11-digit number"
                                   value="<?php echo isset($_POST['contact_number']) ? htmlspecialchars((string)$_POST['contact_number']) : ''; ?>">
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label" for="province">Address</label>

                        <input type="hidden" name="address" id="address" value="<?php echo htmlspecialchars(isset($_POST['address']) ? (string)$_POST['address'] : ''); ?>" />

                        <select class="field-input" id="province" name="province" style="cursor:pointer;">
                            <option value="">Select Province</option>
                        </select>

                        <select class="field-input" id="city" name="city" style="cursor:pointer; margin-top:10px;" disabled>
                            <option value="">Select City/Municipality</option>
                        </select>

                        <select class="field-input" id="barangay" name="barangay" style="cursor:pointer; margin-top:10px;" disabled>
                            <option value="">Select Barangay</option>
                        </select>

                        <div class="field-note" style="font-size:12px;color:rgba(17,24,39,0.6);font-weight:650;">
                            Province, city, and barangay will be combined into your full address.
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label" for="email">Email Address *</label>
                        <input class="field-input" type="email" id="email" name="email"
                               placeholder="Enter your email address" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars((string)$_POST['email']) : ''; ?>">
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label class="field-label" for="password">Password *</label>
                            <input class="field-input" type="password" id="password" name="password"
                                   placeholder="Create a password" required>
                            <div class="field-note" style="font-size:12px;color:rgba(17,24,39,0.6);font-weight:650;">
                                Min 8 chars + 1 symbol required
                            </div>
                        </div>

                        <div class="field">
                            <label class="field-label" for="confirm_password">Confirm Password *</label>
                            <input class="field-input" type="password" id="confirm_password" name="confirm_password"
                                   placeholder="Confirm your password" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-rocket"></i>
                            Create Account
                        </button>
                    </div>
                </form>

                <div class="auth-links">
                    Already have an account?
                    <div style="margin-top:8px;">
                        <a href="login.php">Sign in instead</a>
                    </div>
                </div>

                <?php if (isset($_SESSION['show_verification'])): ?>
                    <div class="verification-notice">
                        <h5><i class="fas fa-envelope"></i> Verification Required</h5>
                        <p>Email service is currently unavailable. For testing purposes, use this verification code:</p>
                        <div class="verification-code"><?php echo htmlspecialchars((string)$_SESSION['pending_registration']['verification_code']); ?></div>
                        <a href="verify_email.php" class="btn-primary" style="margin-top: var(--spacing-md); width:auto; display:inline-flex;">
                            <i class="fas fa-check-circle"></i>
                            Continue to Verification
                        </a>
                    </div>
                    <?php unset($_SESSION['show_verification']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['pending_registration']) && !empty($_SESSION['pending_registration']) && !empty($_SESSION['pending_registration']['verification_code']) && empty($_SESSION['show_verification'])): ?>
                    <div class="verification-notice">
                        <h5><i class="fas fa-check-circle"></i> Registration Submitted!</h5>
                        <p>Please check your email for the verification code to complete your registration.</p>
                        <a href="verify_email.php" class="btn-primary" style="margin-top: var(--spacing-md); width:auto; display:inline-flex;">
                            <i class="fas fa-key"></i>
                            Enter Verification Code
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<script>
    // Simple dropdown hierarchy for UI (demo data - replace with real LGU dataset if needed)
    const data = {
        "Laguna": {
            "Pila": [
                "Aplaya",
                "Bagong Pook",
                "Bukal",
                "Bulilan Norte (Poblacion)",
                "Bulilan Sur (Poblacion)",
                "Concepcion",
                "Labuin",
                "Linga",
                "Masico",
                "Mojon",
                "Pansol",
                "Pinagbayanan",
                "San Antonio",
                "San Miguel",
                "Santa Clara Norte (Poblacion)",
                "Santa Clara Sur (Poblacion)",
                "Tubuan"
            ]
        }
    };

    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');
    const barangaySelect = document.getElementById('barangay');
    const addressInput = document.getElementById('address');

    function setAddress() {
        const province = provinceSelect.value;
        const city = citySelect.value;
        const barangay = barangaySelect.value;

        if (province && city && barangay) {
            addressInput.value = `${barangay}, ${city}, ${province}`;
        } else {
            addressInput.value = '';
        }
    }

    // Populate provinces
    if (provinceSelect) {
        Object.keys(data).forEach(p => {
            const opt = document.createElement('option');
            opt.value = p;
            opt.textContent = p;
            provinceSelect.appendChild(opt);
        });
    }

    provinceSelect?.addEventListener('change', () => {
        citySelect.disabled = !provinceSelect.value;
        barangaySelect.disabled = true;

        citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        if (!provinceSelect.value) {
            setAddress();
            return;
        }

        Object.keys(data[provinceSelect.value] || {}).forEach(c => {
            const opt = document.createElement('option');
            opt.value = c;
            opt.textContent = c;
            citySelect.appendChild(opt);
        });

        setAddress();
    });

    citySelect?.addEventListener('change', () => {
        barangaySelect.disabled = !citySelect.value;

        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        const province = provinceSelect.value;
        const city = citySelect.value;

        const barangays = (data[province] && data[province][city]) ? data[province][city] : [];
        barangays.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b;
            opt.textContent = b;
            barangaySelect.appendChild(opt);
        });

        setAddress();
    });

    barangaySelect?.addEventListener('change', setAddress);

    // Initial address value if hidden input already has data
    setAddress();
</script>

<?php include 'includes/footer.php'; ?>
