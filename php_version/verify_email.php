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

// Check if there's pending registration
if (!isset($_SESSION['pending_registration'])) {
    $_SESSION['error'] = 'No pending registration found. Please register first.';
    header('Location: register.php');
    exit();
}

$pendingData = $_SESSION['pending_registration'];

// Check if verification code has expired
if (time() > $pendingData['expires']) {
    unset($_SESSION['pending_registration']);
    unset($_SESSION['show_verification']);
    $_SESSION['error'] = 'Verification code has expired. Please register again.';
    header('Location: register.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredCode = trim($_POST['verification_code']);

    if ($enteredCode === $pendingData['verification_code']) {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();

            // Hash the password before storing
            $hashedPassword = password_hash($pendingData['password'], PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $conn->prepare("
                INSERT INTO users (full_name, age, contact_number, address, email, password, is_admin)
                VALUES (?, ?, ?, ?, ?, ?, 0)
            ");
            $stmt->execute([
                $pendingData['full_name'],
                $pendingData['age'],
                $pendingData['contact_number'],
                $pendingData['address'],
                $pendingData['email'],
                $hashedPassword
            ]);

            // Clear pending registration data
            unset($_SESSION['pending_registration']);
            unset($_SESSION['show_verification']);

            $_SESSION['success'] = 'Email verified successfully! You can now login.';
            header('Location: login.php');
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = 'An error occurred during account creation. Please try again.';
        }
    } else {
        $_SESSION['error'] = 'Invalid verification code. Please try again.';
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
        max-width: 420px;
        overflow: hidden;
    }

    .auth-header {
        background: linear-gradient(135deg, var(--color-warning) 0%, var(--color-primary) 100%);
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

    .info-notice {
        background: linear-gradient(135deg, rgba(251, 188, 4, 0.1), rgba(26, 115, 232, 0.05));
        border: 1px solid rgba(251, 188, 4, 0.3);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
        text-align: center;
    }

    .info-notice h5 {
        color: var(--color-warning);
        margin: 0 0 var(--spacing-sm);
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--spacing-sm);
    }

    .info-notice p {
        color: var(--color-text-secondary);
        margin: 0;
        font-size: 14px;
    }

    .form-group {
        margin-bottom: var(--spacing-xl);
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: var(--color-text);
        margin-bottom: var(--spacing-md);
        letter-spacing: 0.2px;
        text-align: center;
    }

    .code-input {
        width: 100%;
        text-align: center;
        font-size: 32px;
        font-weight: 600;
        letter-spacing: 8px;
        padding: var(--spacing-lg);
        border: 3px solid var(--color-border);
        border-radius: var(--radius-lg);
        background: var(--color-bg);
        color: var(--color-text);
        transition: all 0.2s ease;
        outline: none;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    }

    .code-input:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 4px rgba(26, 115, 232, 0.1);
        letter-spacing: 12px;
    }

    .form-hint {
        text-align: center;
        font-size: 12px;
        color: var(--color-text-muted);
        margin-top: var(--spacing-sm);
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

    .resend-section {
        text-align: center;
        margin-top: var(--spacing-xl);
        padding-top: var(--spacing-xl);
        border-top: 1px solid var(--color-border);
    }

    .resend-link {
        color: var(--color-primary);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-xs);
    }

    .resend-link:hover {
        color: var(--color-primary-hover);
        text-decoration: underline;
    }

    .back-link {
        display: block;
        text-align: center;
        margin-top: var(--spacing-md);
        color: var(--color-text-secondary);
        text-decoration: none;
        font-size: 14px;
        transition: color 0.2s ease;
    }

    .back-link:hover {
        color: var(--color-text);
    }

    .testing-notice {
        background: var(--color-bg-tertiary);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-md);
        padding: var(--spacing-md);
        margin-top: var(--spacing-lg);
        text-align: center;
    }

    .testing-notice h6 {
        margin: 0 0 var(--spacing-sm);
        font-size: 12px;
        font-weight: 600;
        color: var(--color-text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .testing-code {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 18px;
        font-weight: 600;
        color: var(--color-primary);
        letter-spacing: 2px;
        background: var(--color-bg);
        padding: var(--spacing-sm) var(--spacing-md);
        border-radius: var(--radius-sm);
        border: 2px solid var(--color-primary);
        display: inline-block;
    }

    @media (max-width: 480px) {
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

        .code-input {
            font-size: 28px;
            letter-spacing: 6px;
        }

        .code-input:focus {
            letter-spacing: 10px;
        }
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-envelope-open-text"></i>
            <h1>Verify Your Email</h1>
        </div>

        <div class="auth-body">
            <?php if (isset($_SESSION['pending_registration'])): ?>
                <div class="info-notice">
                    <h5><i class="fas fa-paper-plane"></i> Verification Email Sent!</h5>
                    <p>We've sent a 6-digit verification code to <strong><?php echo htmlspecialchars($pendingData['email']); ?></strong></p>
                    <p>Please check your Gmail inbox (and spam folder) for the email.</p>
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label for="verification_code" class="form-label">Enter Verification Code</label>
                        <input type="text" class="code-input" id="verification_code"
                               name="verification_code" maxlength="6" required
                               placeholder="000000" autocomplete="off">
                        <div class="form-hint">6-digit code sent to your email</div>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-check-circle"></i>
                        Verify & Complete
                    </button>
                </form>

                <div class="resend-section">
                    <a href="#" class="resend-link" id="resendLink">
                        <i class="fas fa-redo"></i>
                        Didn't receive the code? Resend
                    </a>
                    <a href="register.php" class="back-link">
                        ← Back to Registration
                    </a>
                </div>

                <!-- For testing purposes, show the code only if email service failed -->
                <?php if (isset($_SESSION['show_verification'])): ?>
                <div class="testing-notice">
                    <h6>Testing Code (Email service unavailable)</h6>
                    <div class="testing-code"><?php echo $pendingData['verification_code']; ?></div>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="info-notice" style="background: linear-gradient(135deg, rgba(234, 67, 53, 0.1), rgba(26, 115, 232, 0.05)); border-color: rgba(234, 67, 53, 0.3);">
                    <h5 style="color: var(--color-error);"><i class="fas fa-exclamation-triangle"></i> No Pending Registration</h5>
                    <p>Please register first to receive a verification code.</p>
                    <a href="register.php" class="btn-primary" style="margin-top: var(--spacing-md); width: auto; padding: var(--spacing-sm) var(--spacing-lg); font-size: 14px;">
                        Go to Registration
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Auto-format verification code input
    const codeInput = document.getElementById('verification_code');

    codeInput.addEventListener('input', function(e) {
        // Remove non-numeric characters
        let value = e.target.value.replace(/\D/g, '');

        // Limit to 6 digits
        if (value.length > 6) {
            value = value.substring(0, 6);
        }

        // Update spacing based on input length
        const spacing = value.length > 3 ? 12 : 8;
        e.target.style.letterSpacing = spacing + 'px';

        e.target.value = value;
    });

    // Focus input on page load
    codeInput.focus();

    // Resend functionality
    document.getElementById('resendLink').addEventListener('click', function(e) {
        e.preventDefault();

        const link = this;
        const originalText = link.innerHTML;

        link.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        link.style.pointerEvents = 'none';

        fetch('resend_verification.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                link.innerHTML = '<i class="fas fa-check"></i> Code Resent!';
                link.style.color = 'var(--color-success)';

                // Update testing code if provided
                if (data.code) {
                    const testingCode = document.querySelector('.testing-code');
                    if (testingCode) {
                        testingCode.textContent = data.code;
                    }
                }

                setTimeout(() => {
                    link.innerHTML = originalText;
                    link.style.color = '';
                    link.style.pointerEvents = '';
                }, 3000);
            } else {
                link.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Failed to resend';
                link.style.color = 'var(--color-error)';

                setTimeout(() => {
                    link.innerHTML = originalText;
                    link.style.color = '';
                    link.style.pointerEvents = '';
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            link.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Network error';
            link.style.color = 'var(--color-error)';

            setTimeout(() => {
                link.innerHTML = originalText;
                link.style.color = '';
                link.style.pointerEvents = '';
            }, 3000);
        });
    });
</script>

<?php include 'includes/footer.php'; ?>

<script>
document.getElementById('verification_code').addEventListener('input', function(e) {
    // Auto-format the verification code
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 6) {
        value = value.substring(0, 6);
    }
    e.target.value = value;
});

document.getElementById('resendLink').addEventListener('click', function(e) {
    e.preventDefault();

    const link = this;
    link.textContent = 'Sending...';
    link.style.pointerEvents = 'none';

    fetch('resend_verification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the testing code display if email service is unavailable
            if (data.code) {
                let testingAlert = document.querySelector('.alert-warning');
                if (!testingAlert) {
                    // Create testing alert if it doesn't exist
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-warning mt-3';
                    alertDiv.innerHTML = '<small><strong>Testing Code (Email service unavailable):</strong> ' + data.code + '</small>';
                    document.querySelector('.card-body').appendChild(alertDiv);
                } else {
                    // Update existing testing alert
                    testingAlert.querySelector('small strong').nextSibling.textContent = ' ' + data.code;
                }
            }

            link.textContent = 'Code Resent!';
            link.style.color = 'green';

            setTimeout(() => {
                link.textContent = 'Resend Code';
                link.style.color = '';
                link.style.pointerEvents = '';
            }, 3000);
        } else {
            alert('Error: ' + data.message);
            link.textContent = 'Resend Code';
            link.style.pointerEvents = '';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        link.textContent = 'Resend Code';
        link.style.pointerEvents = '';
    });
});
</script>

<?php include 'includes/footer.php'; ?>