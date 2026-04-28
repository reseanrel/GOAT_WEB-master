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
    $age = !empty($_POST['age']) ? (int)$_POST['age'] : null;
    $contactNumber = sanitizeInput($_POST['contact_number']);
    $address = sanitizeInput($_POST['address']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    $errors = [];

    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $errors[] = 'Please fill all required fields';
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

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered';
        } else {
            try {
                // Generate verification code (6 digits)
                $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

                // Store registration data temporarily in session
                $fullName = $firstName . ' ' . $lastName;
                session_regenerate_id(true); // Regenerate session ID for security
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
                    'expires' => time() + 3600 // 1 hour expiry
                ];

                // Send verification email via Gmail SMTP
                require_once 'includes/email.php';
                $emailSent = sendVerificationEmail($email, $verificationCode);

                if ($emailSent) {
                    $_SESSION['success'] = 'Registration successful! Please check your Gmail inbox for the verification code.';
                } else {
                    // Gmail SMTP failed - show file-based fallback
                    $_SESSION['show_verification'] = true;
                    $_SESSION['success'] = 'Registration successful! Please check the saved email file for your verification code.';
                    error_log("[WARNING] Gmail SMTP failed, falling back to file storage");
                }

                // Redirect to verification page
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
    .auth-container {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: var(--spacing-2xl) var(--spacing-md);
    }

    .auth-card {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--color-border);
        width: 100%;
        max-width: 500px;
        overflow: hidden;
    }

    .auth-header {
        background: linear-gradient(135deg, var(--color-success) 0%, var(--color-primary) 100%);
        padding: var(--spacing-2xl) var(--spacing-xl);
        text-align: center;
        color: white;
    }

    .auth-header i {
        font-size: 48px;
        margin-bottom: var(--spacing-md);
        opacity: 0.9;
    }

    .auth-header h1 {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .auth-body {
        padding: var(--spacing-2xl);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
    }

    .form-group {
        margin-bottom: var(--spacing-lg);
    }

    .form-group.full-width {
        margin-bottom: var(--spacing-lg);
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: var(--color-text);
        margin-bottom: var(--spacing-sm);
        letter-spacing: 0.2px;
    }

    .form-input, .form-textarea {
        width: 100%;
        padding: var(--spacing-md) var(--spacing-lg);
        border: 2px solid var(--color-border);
        border-radius: var(--radius-lg);
        font-size: 16px;
        font-family: var(--font-family);
        background: var(--color-bg);
        color: var(--color-text);
        transition: all 0.2s ease;
        outline: none;
    }

    .form-textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-input:focus, .form-textarea:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
    }

    .form-input::placeholder, .form-textarea::placeholder {
        color: var(--color-text-muted);
    }

    .form-hint {
        font-size: 12px;
        color: var(--color-text-secondary);
        margin-top: var(--spacing-xs);
        display: block;
    }

    .btn-primary {
        width: 100%;
        padding: var(--spacing-md) var(--spacing-lg);
        background: var(--color-primary);
        color: white;
        border: none;
        border-radius: var(--radius-lg);
        font-size: 16px;
        font-weight: 500;
        font-family: var(--font-family);
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--spacing-sm);
    }

    .btn-primary:hover {
        background: var(--color-primary-hover);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .auth-links {
        text-align: center;
        margin-top: var(--spacing-xl);
        padding-top: var(--spacing-xl);
        border-top: 1px solid var(--color-border);
    }

    .auth-links p {
        margin: 0 0 var(--spacing-md);
        color: var(--color-text-secondary);
        font-size: 14px;
    }

    .auth-links a {
        color: var(--color-primary);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .auth-links a:hover {
        color: var(--color-primary-hover);
        text-decoration: underline;
    }

    .verification-notice {
        background: linear-gradient(135deg, var(--color-success), rgba(52, 168, 83, 0.1));
        border: 1px solid rgba(52, 168, 83, 0.3);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-top: var(--spacing-xl);
        text-align: center;
    }

    .verification-notice h5 {
        color: var(--color-success);
        margin: 0 0 var(--spacing-sm);
        font-size: 16px;
        font-weight: 600;
    }

    .verification-notice p {
        color: var(--color-text);
        margin: 0 0 var(--spacing-md);
        font-size: 14px;
    }

    .verification-code {
        background: var(--color-bg-tertiary);
        border: 2px dashed var(--color-success);
        border-radius: var(--radius-md);
        padding: var(--spacing-md);
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 18px;
        font-weight: 600;
        color: var(--color-success);
        letter-spacing: 2px;
        margin: var(--spacing-md) 0;
        display: inline-block;
    }

    @media (max-width: 640px) {
        .auth-container {
            padding: var(--spacing-xl) var(--spacing-md);
        }

        .auth-card {
            max-width: 100%;
        }

        .auth-header {
            padding: var(--spacing-xl);
        }

        .auth-body {
            padding: var(--spacing-xl);
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: var(--spacing-md);
        }
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-user-plus"></i>
            <h1>Join Pila Pet Community</h1>
        </div>

        <div class="auth-body">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name" class="form-label">First Name *</label>
                        <input type="text" class="form-input" id="first_name" name="first_name"
                               placeholder="Enter your first name" required
                               value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name *</label>
                        <input type="text" class="form-input" id="last_name" name="last_name"
                               placeholder="Enter your last name" required
                               value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="age" class="form-label">Age</label>
                        <input type="number" class="form-input" id="age" name="age" min="0" max="120"
                               placeholder="Your age (optional)"
                               value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-input" id="contact_number" name="contact_number"
                               placeholder="11-digit number"
                               value="<?php echo isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number']) : ''; ?>">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-textarea" id="address" name="address" rows="2"
                              placeholder="Your complete address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" class="form-input" id="email" name="email"
                           placeholder="Enter your email address" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-input" id="password" name="password"
                               placeholder="Create a password" required>
                        <span class="form-hint">Min 8 chars, 1 symbol required</span>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-input" id="confirm_password" name="confirm_password"
                               placeholder="Confirm your password" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-rocket"></i>
                    Create Account
                </button>
            </form>

            <div class="auth-links">
                <p>Already have an account?</p>
                <a href="login.php">Sign in instead</a>
            </div>

            <?php if (isset($_SESSION['show_verification'])): ?>
            <div class="verification-notice">
                <h5><i class="fas fa-envelope"></i> Verification Required</h5>
                <p>Email service is currently unavailable. For testing purposes, use this verification code:</p>
                <div class="verification-code"><?php echo $_SESSION['pending_registration']['verification_code']; ?></div>
                <a href="verify_email.php" class="btn-primary" style="margin-top: var(--spacing-md); width: auto; padding: var(--spacing-sm) var(--spacing-lg); font-size: 14px;">
                    Continue to Verification
                </a>
            </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['pending_registration']) && !isset($_SESSION['show_verification'])): ?>
            <div class="verification-notice">
                <h5><i class="fas fa-check-circle"></i> Registration Submitted!</h5>
                <p>Please check your email for the verification code to complete your registration.</p>
                <a href="verify_email.php" class="btn-primary" style="margin-top: var(--spacing-md); width: auto; padding: var(--spacing-sm) var(--spacing-lg); font-size: 14px;">
                    Enter Verification Code
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Password confirmation validation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const submitBtn = document.querySelector('.btn-primary');

    function validatePasswords() {
        if (password.value && confirmPassword.value) {
            if (password.value !== confirmPassword.value) {
                confirmPassword.style.borderColor = 'var(--color-error)';
                confirmPassword.style.boxShadow = '0 0 0 3px rgba(234, 67, 53, 0.1)';
                submitBtn.disabled = true;
                submitBtn.textContent = 'Passwords do not match';
                submitBtn.style.background = 'var(--color-error)';
            } else {
                confirmPassword.style.borderColor = 'var(--color-success)';
                confirmPassword.style.boxShadow = '0 0 0 3px rgba(52, 168, 83, 0.1)';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-rocket"></i> Create Account';
                submitBtn.style.background = 'var(--color-primary)';
            }
        }
    }

    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);

    // Auto-focus first input
    document.getElementById('first_name').focus();
</script>

<?php include 'includes/footer.php'; ?>