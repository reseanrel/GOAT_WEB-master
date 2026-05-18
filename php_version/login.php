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
            if (!empty($user['archived'])) {
                $_SESSION['error'] = 'This account has been archived and cannot be used to login. Please contact administration.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['is_admin'] = $user['is_admin'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_contact'] = $user['contact_number'];
                $_SESSION['user_address'] = $user['address'];
                $_SESSION['user_age'] = $user['age'];

                $_SESSION['success'] = 'Welcome back, ' . ($user['is_admin'] ? 'Administrator' : $user['full_name']);

                if (!empty($user['is_admin'])) {
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
    /* Login - warm modern rescue UI */
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
    }

    .auth-hero-inner {
        position: relative;
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
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

    input.field-input {
        width: 100%;
        padding: 12px 12px;
        border: 1px solid var(--color-border);
        border-radius: 12px;
        background: #fff;
        font-family: inherit;
        font-size: 14px;
        outline: none;
    }

    input.field-input:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.12);
    }

    .auth-actions {
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

    .btn-secondary-cta {
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
        justify-content: center;
        gap: 10px;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        flex: 1;
    }

    .btn-secondary-cta:hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,0.92);
        box-shadow: var(--shadow-md);
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

    .demo-info {
        margin-top: var(--spacing-lg);
        background: rgba(255,255,255,0.75);
        border: 1px dashed rgba(245,158,11,0.35);
        border-radius: var(--radius-xl, 20px);
        padding: var(--spacing-lg);
        text-align: center;
    }

    .demo-info h6 {
        margin: 0 0 var(--spacing-sm);
        font-size: 12px;
        font-weight: 950;
        color: rgba(17,24,39,0.62);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .demo-info p {
        margin: 0;
        font-size: 14px;
        color: rgba(17,24,39,0.88);
        font-weight: 800;
        font-family: 'Monaco','Menlo','Ubuntu Mono',monospace;
    }

    @media (max-width: 900px) {
        .auth-hero-inner { grid-template-columns: 1fr; }
    }
</style>

<div class="auth-page">
    <section class="auth-hero" aria-label="Login hero">
        <div class="auth-hero-inner">
            <div>
                <div class="auth-kicker">
                    <i class="fas fa-paw"></i>
                    Welcome back to Pila Pet
                </div>

                <h1 class="auth-title">Sign In</h1>
                <p class="auth-subtitle">
                    Log in to manage your pets, report lost animals, and track adoption applications.
                </p>
            </div>

            <div class="auth-card">
                <h2 class="auth-form-title">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </h2>

                <form method="POST">
                    <div class="field">
                        <label class="field-label" for="email">Email Address</label>
                        <input
                            class="field-input"
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter your email"
                            required
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars((string)$_POST['email']) : ''; ?>"
                        />
                    </div>

                    <div class="field">
                        <label class="field-label" for="password">Password</label>
                        <input
                            class="field-input"
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        />
                    </div>

                    <div class="auth-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-sign-in-alt"></i>
                            Sign In
                        </button>
                    </div>
                </form>

                <div class="auth-links">
                    New to Pila Pet Registration?
                    <div style="margin-top:8px;">
                        <a href="register.php">Create an account</a>
                    </div>
                </div>

                <div class="demo-info">
                    <h6>Demo Account</h6>
                    <p>admin@pila.pets / admin123!</p>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
