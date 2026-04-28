<?php
session_start();
require_once 'includes/auth.php';
?>

<?php include 'includes/header.php'; ?>

<style>
    .hero-section {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);
        color: white;
        padding: var(--spacing-2xl) 0;
        margin: calc(-1 * var(--spacing-lg)) calc(-1 * var(--spacing-lg)) var(--spacing-2xl);
        border-radius: 0 0 var(--radius-xl) var(--radius-xl);
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.5;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        text-align: center;
        max-width: 600px;
        margin: 0 auto;
    }

    .hero-title {
        font-size: clamp(32px, 5vw, 48px);
        font-weight: 700;
        margin-bottom: var(--spacing-md);
        letter-spacing: -1px;
        line-height: 1.1;
    }

    .hero-subtitle {
        font-size: 18px;
        font-weight: 400;
        opacity: 0.9;
        line-height: 1.6;
        margin-bottom: var(--spacing-xl);
    }

    .hero-cta {
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-sm);
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        color: white;
        padding: var(--spacing-md) var(--spacing-xl);
        border-radius: var(--radius-lg);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .hero-cta:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: var(--spacing-xl);
        margin: var(--spacing-2xl) 0;
    }

    .feature-card {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        padding: var(--spacing-xl);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--color-border);
        transition: all 0.3s ease;
        text-align: center;
    }

    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .feature-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
        border-radius: var(--radius-xl);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto var(--spacing-lg);
        color: white;
        font-size: 24px;
        box-shadow: var(--shadow-md);
    }

    .feature-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: var(--spacing-sm);
    }

    .feature-description {
        color: var(--color-text-secondary);
        line-height: 1.6;
        margin: 0;
    }

    .stats-section {
        background: var(--color-bg-secondary);
        border-radius: var(--radius-xl);
        padding: var(--spacing-2xl);
        margin: var(--spacing-2xl) 0;
        border: 1px solid var(--color-border);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-lg);
    }

    .stat-item {
        text-align: center;
        padding: var(--spacing-lg);
        background: var(--color-bg);
        border-radius: var(--radius-lg);
        border: 1px solid var(--color-border);
        transition: transform 0.2s ease;
    }

    .stat-item:hover {
        transform: translateY(-2px);
    }

    .stat-number {
        font-size: 36px;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: var(--spacing-sm);
        display: block;
    }

    .stat-label {
        font-size: 14px;
        font-weight: 500;
        color: var(--color-text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .cta-section {
        background: linear-gradient(135deg, var(--color-bg-tertiary) 0%, var(--color-bg) 100%);
        border-radius: var(--radius-xl);
        padding: var(--spacing-2xl);
        text-align: center;
        margin: var(--spacing-2xl) 0;
        border: 1px solid var(--color-border);
    }

    .cta-title {
        font-size: 24px;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: var(--spacing-md);
    }

    .cta-description {
        font-size: 16px;
        color: var(--color-text-secondary);
        margin-bottom: var(--spacing-xl);
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    .cta-buttons {
        display: flex;
        gap: var(--spacing-md);
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-secondary {
        background: var(--color-bg);
        color: var(--color-text);
        border: 2px solid var(--color-border);
        padding: var(--spacing-md) var(--spacing-xl);
        border-radius: var(--radius-lg);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .btn-secondary:hover {
        background: var(--color-text);
        color: var(--color-bg);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: var(--spacing-xl) 0;
            margin: calc(-1 * var(--spacing-md)) calc(-1 * var(--spacing-md)) var(--spacing-xl);
        }

        .features-grid {
            grid-template-columns: 1fr;
            gap: var(--spacing-lg);
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .cta-buttons {
            flex-direction: column;
            align-items: center;
        }

        .btn-secondary {
            width: 100%;
            max-width: 300px;
            justify-content: center;
        }
    }
</style>

<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Welcome to Pila Pet Registration</h1>
            <p class="hero-subtitle">
                Modern pet management for the Pila community. Register, track, and protect your beloved companions with our digital platform.
            </p>
            <?php if (!isLoggedIn()): ?>
                <a href="register.php" class="hero-cta">
                    <i class="fas fa-rocket"></i>
                    Get Started Free
                </a>
            <?php else: ?>
                <a href="<?php echo isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'; ?>" class="hero-cta">
                    <i class="fas fa-tachometer-alt"></i>
                    Go to Dashboard
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="features-grid">
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <h3 class="feature-title">Easy Registration</h3>
        <p class="feature-description">
            Register your pets with detailed information, photos, and vaccination records in minutes.
        </p>
    </div>

    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-search"></i>
        </div>
        <h3 class="feature-title">Lost & Found</h3>
        <p class="feature-description">
            Report lost pets or help reunite lost pets with their owners through our community platform.
        </p>
    </div>

    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-heart"></i>
        </div>
        <h3 class="feature-title">Pet Adoption</h3>
        <p class="feature-description">
            Find loving homes for pets or adopt pets in need through our verified adoption system.
        </p>
    </div>
</section>

<section class="stats-section">
    <div class="container">
        <div style="text-align: center; margin-bottom: var(--spacing-xl);">
            <h2 style="font-size: 28px; font-weight: 600; color: var(--color-text); margin-bottom: var(--spacing-sm);">
                Community Impact
            </h2>
            <p style="color: var(--color-text-secondary); font-size: 16px; margin: 0;">
                Real numbers from our pet registration system
            </p>
        </div>

        <div class="stats-grid">
            <?php
            $db = Database::getInstance();
            $conn = $db->getConnection();

            // Get statistics
            $stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE archived = 0 AND status = 'approved'");
            $totalPets = $stmt->fetch()['total'];

            $stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE lost = 1 AND archived = 0 AND status = 'approved' AND deceased = 0");
            $lostPets = $stmt->fetch()['total'];

            $stmt = $conn->query("SELECT COUNT(*) as total FROM pets WHERE available_for_adoption = 1 AND archived = 0 AND status = 'approved' AND deceased = 0");
            $adoptionPets = $stmt->fetch()['total'];

            $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE archived = 0");
            $totalUsers = $stmt->fetch()['total'];
            ?>

            <div class="stat-item">
                <span class="stat-number"><?php echo $totalPets; ?></span>
                <p class="stat-label">Registered Pets</p>
            </div>

            <div class="stat-item">
                <span class="stat-number"><?php echo $totalUsers; ?></span>
                <p class="stat-label">Pet Owners</p>
            </div>

            <div class="stat-item">
                <span class="stat-number"><?php echo $lostPets; ?></span>
                <p class="stat-label">Lost Pets Reported</p>
            </div>

            <div class="stat-item">
                <span class="stat-number"><?php echo $adoptionPets; ?></span>
                <p class="stat-label">Pets Available for Adoption</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Ready to Get Started?</h2>
        <p class="cta-description">
            Join thousands of pet owners in Pila who trust our platform to keep their pets safe and registered.
        </p>

        <div class="cta-buttons">
            <?php if (!isLoggedIn()): ?>
                <a href="register.php" class="btn-primary" style="text-decoration: none;">
                    <i class="fas fa-user-plus"></i>
                    Register Your Pet
                </a>
                <a href="login.php" class="btn-secondary">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </a>
            <?php else: ?>
                <a href="<?php echo isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'; ?>" class="btn-primary" style="text-decoration: none;">
                    <i class="fas fa-tachometer-alt"></i>
                    Go to Dashboard
                </a>
                <a href="lost_pets.php" class="btn-secondary">
                    <i class="fas fa-search"></i>
                    Browse Lost Pets
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>