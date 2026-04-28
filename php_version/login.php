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
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all fields';
    } else {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND archived = 0");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Check if user is archived
            if ($user['archived']) {
                $_SESSION['error'] = 'This account has been archived and cannot be used to login. Please contact administration.';
            } else {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['is_admin'] = $user['is_admin'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_contact'] = $user['contact_number'];
                $_SESSION['user_address'] = $user['address'];
                $_SESSION['user_age'] = $user['age'];

                $_SESSION['success'] = 'Welcome back, ' . ($user['is_admin'] ? 'Administrator' : $user['full_name']);

                // Redirect based on user type
                if ($user['is_admin']) {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: user/dashboard.php');
                }
                exit();
            }
        } else {
            $_SESSION['error'] = 'Invalid email or password';
        }
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
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);
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

    .form-group {
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

    .form-input {
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

    .form-input:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
    }

    .form-input::placeholder {
        color: var(--color-text-muted);
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

    .btn-primary:active {
        transform: translateY(0);
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

    .demo-info {
        background: var(--color-bg-tertiary);
        border-radius: var(--radius-md);
        padding: var(--spacing-md);
        margin-top: var(--spacing-lg);
        border: 1px solid var(--color-border);
    }

    .demo-info h6 {
        margin: 0 0 var(--spacing-sm);
        font-size: 12px;
        font-weight: 600;
        color: var(--color-text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .demo-info p {
        margin: 0;
        font-size: 14px;
        color: var(--color-text);
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
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
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-sign-in-alt"></i>
            <h1>Welcome Back</h1>
        </div>

        <div class="auth-body">
            <form method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-input" id="email" name="email"
                           placeholder="Enter your email" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-input" id="password" name="password"
                           placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>

            <div class="auth-links">
                <p>New to Pila Pet Registration?</p>
                <a href="register.php">Create an account</a>
            </div>

            <div class="demo-info">
                <h6>Demo Account</h6>
                <p>admin@pila.pets / admin123!</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Add focus effects
    document.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-1px)';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'translateY(0)';
        });
    });

    // Auto-focus first input
    document.getElementById('email').focus();
</script>

<?php include 'includes/footer.php'; ?>