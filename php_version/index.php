<?php
session_start();
require_once 'includes/auth.php';
?>

<?php include 'includes/header.php'; ?>

<style>
    /* Index - warm modern rescue UI (like lost/adoption) */
    .index-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--spacing-lg);
    }

    .index-hero {
        position: relative;
        border-radius: var(--radius-xl, 20px);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: var(--shadow-lg);
        background: #fff7ed;
        margin: calc(-1 * var(--spacing-lg)) 0 var(--spacing-2xl);
    }

    .index-hero::before {
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

    .index-hero-inner {
        position: relative;
        display: grid;
        grid-template-columns: 1.25fr 0.75fr;
        gap: var(--spacing-xl);
        padding: var(--spacing-2xl);
        align-items: center;
    }

    .index-kicker {
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

    .index-title {
        font-size: 46px;
        line-height: 1.05;
        font-weight: 900;
        color: rgba(17,24,39,0.95);
        margin: 0 0 var(--spacing-md);
        letter-spacing: -0.5px;
    }

    .index-subtitle {
        margin: 0 0 var(--spacing-lg);
        color: rgba(17,24,39,0.72);
        font-size: 16px;
        max-width: 620px;
        font-weight: 650;
        line-height: 1.6;
    }

    .index-hero-actions {
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
        min-width: 240px;
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
        min-width: 200px;
        justify-content: center;
    }

    .btn-secondary-cta:hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,0.92);
        box-shadow: var(--shadow-md);
    }

    .index-stat {
        width: 100%;
        max-width: 320px;
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        padding: var(--spacing-lg);
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
        margin-left: auto;
    }

    .index-stat .label {
        font-weight: 950;
        color: rgba(17,24,39,0.66);
        margin-bottom: 10px;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .index-stat .value {
        font-size: 30px;
        font-weight: 1000;
        color: rgba(17,24,39,0.92);
        line-height: 1.1;
    }

    .index-stat .hint {
        margin-top: 10px;
        font-size: 13px;
        color: rgba(17,24,39,0.62);
        font-weight: 650;
    }

    .index-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: var(--spacing-lg);
        align-items: stretch;
        margin-bottom: var(--spacing-2xl);
    }

    .index-card {
        background: rgba(255,255,255,0.92);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        padding: var(--spacing-xl);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-height: 260px;
    }

    .index-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(217,119,6,0.25);
    }

    .index-card .icon {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(245,158,11,0.14);
        border: 1px solid rgba(245,158,11,0.22);
        color: #9a3412;
        font-size: 18px;
        margin-bottom: 8px;
    }

    .index-card h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
    }

    .index-card p {
        margin: 0;
        color: rgba(17,24,39,0.62);
        font-weight: 650;
        line-height: 1.6;
        font-size: 14px;
    }

    .index-section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-lg);
    }

    .index-section-head h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
    }

    .index-section-head p {
        margin: 0;
        color: rgba(17,24,39,0.62);
        font-weight: 650;
        max-width: 560px;
        line-height: 1.5;
    }

    .index-stats {
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        padding: var(--spacing-2xl);
        box-shadow: 0 10px 25px rgba(0,0,0,0.03);
        margin-bottom: var(--spacing-2xl);
    }

    .index-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-lg);
    }

    .index-stat-item {
        text-align: center;
        padding: var(--spacing-lg);
        background: rgba(255,255,255,0.9);
        border-radius: var(--radius-lg);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .index-stat-item .num {
        font-size: 36px;
        font-weight: 1000;
        color: rgba(245,158,11,1);
        margin-bottom: 10px;
        display: block;
    }

    .index-stat-item .lbl {
        font-size: 13px;
        font-weight: 950;
        color: rgba(17,24,39,0.62);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    @media (max-width: 900px) {
        .index-hero-inner { grid-template-columns: 1fr; }
        .index-stat { margin-left: 0; max-width: 100%; }
    }

    @media (max-width: 768px) {
        .index-page { padding: var(--spacing-md); }
        .index-hero-inner { padding: var(--spacing-xl); }
        .index-title { font-size: 34px; }
        .index-stat-item .num { font-size: 30px; }
    }
</style>

<?php
$db = Database::getInstance();
$conn = $db->getConnection();

$totalPets = 0;
$totalUsers = 0;
$lostPets = 0;
$adoptionPets = 0;

if ($conn instanceof PDO) {
    $totalPets = (int)$conn->query("SELECT COUNT(*) as total FROM pets WHERE archived = 0 AND status = 'approved'")->fetch()['total'];
    $totalUsers = (int)$conn->query("SELECT COUNT(*) as total FROM users WHERE archived = 0")->fetch()['total'];
    $lostPets = (int)$conn->query("SELECT COUNT(*) as total FROM pets WHERE lost = 1 AND archived = 0 AND status = 'approved' AND deceased = 0")->fetch()['total'];
    $adoptionPets = (int)$conn->query("SELECT COUNT(*) as total FROM pets WHERE available_for_adoption = 1 AND archived = 0 AND status = 'approved' AND deceased = 0")->fetch()['total'];
}
?>

<div class="index-page">
    <section class="index-hero" aria-label="Index hero">
        <div class="index-hero-inner">
            <div>
                <div class="index-kicker">
                    <i class="fas fa-paw"></i>
                    Pila Pet Registration • Lost & Adoption
                </div>

                <h1 class="index-title">Welcome to Pila Pet</h1>
                <p class="index-subtitle">
                    Register, track, and protect your beloved companions — plus help reunite lost pets and find loving homes through adoption.
                </p>

                <div class="index-hero-actions">
                    <?php if (!isLoggedIn()): ?>
                        <a href="register.php" class="btn-primary-cta">
                            <i class="fas fa-rocket"></i>
                            Get Started Free
                        </a>
                        <a href="login.php" class="btn-secondary-cta">
                            <i class="fas fa-sign-in-alt"></i>
                            Login
                        </a>
                    <?php else: ?>
                        <a href="<?php echo isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'; ?>" class="btn-primary-cta">
                            <i class="fas fa-tachometer-alt"></i>
                            Go to Dashboard
                        </a>
                        <a href="lost_pets.php" class="btn-secondary-cta">
                            <i class="fas fa-search"></i>
                            Browse Lost Pets
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="index-stat">
                <div class="label">
                    <i class="fas fa-users"></i>
                    Community Snapshot
                </div>
                <div class="value"><?php echo $totalUsers; ?></div>
                <div class="hint">Pet owners currently using the platform</div>
            </div>
        </div>
    </section>

    <div class="index-section-head">
        <div>
            <h2>How it helps</h2>
            <p>Everything you need to keep pets safe, reunite lost pets, and support adoption.</p>
        </div>
    </div>

    <div class="index-grid">
        <div class="index-card">
            <div class="icon"><i class="fas fa-user-plus"></i></div>
            <h3>Easy Registration</h3>
            <p>Register pets with details, photos, and vaccination records.</p>
        </div>

        <div class="index-card">
            <div class="icon"><i class="fas fa-search"></i></div>
            <h3>Lost & Found</h3>
            <p>Report lost pets and help reunite them with their owners.</p>
        </div>

        <div class="index-card">
            <div class="icon"><i class="fas fa-heart"></i></div>
            <h3>Pet Adoption</h3>
            <p>Browse available pets and apply to adopt through verified listings.</p>
        </div>
    </div>

    <section class="index-stats" aria-label="Index stats">
        <div class="index-section-head" style="margin-bottom: var(--spacing-lg);">
            <div>
                <h2 style="font-size: 28px;">Community Impact</h2>
                <p>Real numbers from our pet registration system.</p>
            </div>
        </div>

        <div class="index-stats-grid">
            <div class="index-stat-item">
                <span class="num"><?php echo $totalPets; ?></span>
                <p class="lbl">Registered Pets</p>
            </div>

            <div class="index-stat-item">
                <span class="num"><?php echo $lostPets; ?></span>
                <p class="lbl">Lost Pets Reported</p>
            </div>

            <div class="index-stat-item">
                <span class="num"><?php echo $adoptionPets; ?></span>
                <p class="lbl">Pets Available for Adoption</p>
            </div>

            <div class="index-stat-item">
                <span class="num"><?php echo $totalUsers; ?></span>
                <p class="lbl">Pet Owners</p>
            </div>
        </div>
    </section>

    <section style="text-align:center; margin-bottom: var(--spacing-2xl);">
        <h2 style="font-size: 26px; font-weight: 1000; color: rgba(17,24,39,0.95); margin: 0 0 var(--spacing-md);">
            Ready to get started?
        </h2>
        <p style="color: rgba(17,24,39,0.62); font-weight: 650; margin: 0 0 var(--spacing-lg); line-height: 1.6;">
            Join pet owners in Pila who trust this platform to keep pets safe and connected.
        </p>

        <div class="index-hero-actions" style="justify-content:center;">
            <?php if (!isLoggedIn()): ?>
                <a href="register.php" class="btn-primary-cta">
                    <i class="fas fa-user-plus"></i>
                    Register Your Pet
                </a>
                <a href="login.php" class="btn-secondary-cta">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </a>
            <?php else: ?>
                <a href="<?php echo isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'; ?>" class="btn-primary-cta">
                    <i class="fas fa-tachometer-alt"></i>
                    Go to Dashboard
                </a>
                <a href="adoption.php" class="btn-secondary-cta">
                    <i class="fas fa-heart"></i>
                    Browse Adoption
                </a>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
