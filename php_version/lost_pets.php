<?php
session_start();
require_once 'includes/auth.php';
requireLogin();

$db = Database::getInstance();
$conn = $db->getConnection();

// Get lost pets
try {
    $stmt = $conn->prepare("
        SELECT p.*, p.photo_url AS photo_path, u.full_name as owner_name, u.email as owner_email,
               u.contact_number as owner_contact, u.address as last_seen_location
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

// Load current user for contact modal pre-fill
$currentUser = getUserById($_SESSION['user_id']);
if ($currentUser) {
    $_SESSION['email'] = $currentUser['email'] ?? '';
    $_SESSION['contact_number'] = $currentUser['contact_number'] ?? '';
}
?>

<?php include 'includes/header.php'; ?>

<style>
    /* Lost Pets - warm modern rescue UI */
    .lost-pets-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--spacing-lg);
    }

    .lost-hero {
        position: relative;
        border-radius: var(--radius-xl, 20px);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: var(--shadow-lg);
        background: #fff7ed;
        margin-bottom: var(--spacing-2xl);
    }

    /* Soft illustration (pure CSS shapes) */
    .lost-hero::before {
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

    .lost-hero-inner {
        position: relative;
        display: grid;
        grid-template-columns: 1.25fr 0.75fr;
        gap: var(--spacing-xl);
        padding: var(--spacing-2xl);
        align-items: center;
    }

    .lost-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-xs);
        padding: 8px 12px;
        border-radius: var(--radius-lg, 16px);
        background: rgba(245,158,11,0.16);
        border: 1px solid rgba(245,158,11,0.25);
        width: fit-content;
        font-weight: 700;
        color: #9a3412;
        margin-bottom: var(--spacing-md);
    }

    .lost-hero-title {
        font-size: 42px;
        line-height: 1.05;
        font-weight: 800;
        color: var(--color-text, #111827);
        margin: 0 0 var(--spacing-md);
        letter-spacing: -0.5px;
    }

    .lost-hero-subtitle {
        margin: 0 0 var(--spacing-lg);
        color: rgba(17,24,39,0.72);
        font-size: 16px;
        max-width: 620px;
        font-weight: 500;
    }

    .lost-hero-actions {
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
        font-weight: 800;
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
        color: rgba(17,24,39,0.86);
        border-radius: var(--radius-lg, 18px);
        padding: 13px 16px;
        font-size: 15px;
        font-weight: 800;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    }

    .btn-secondary-cta:hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,0.9);
        box-shadow: var(--shadow-md);
    }

    .hero-side {
        display: grid;
        gap: var(--spacing-md);
        justify-items: end;
    }

    .hero-stat {
        width: 100%;
        max-width: 320px;
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        padding: var(--spacing-lg);
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .hero-stat .label {
        font-weight: 800;
        color: rgba(17,24,39,0.66);
        margin-bottom: 8px;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .hero-stat .value {
        font-size: 28px;
        font-weight: 900;
        color: rgba(17,24,39,0.92);
        line-height: 1.1;
    }

    .hero-stat .hint {
        margin-top: 10px;
        font-size: 13px;
        color: rgba(17,24,39,0.62);
        font-weight: 600;
    }

    .lost-how {
        margin: 0 0 var(--spacing-2xl);
        background: rgba(255,255,255,0.65);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        padding: var(--spacing-xl);
        box-shadow: var(--shadow-md);
    }

    .lost-how-head {
        display: flex;
        justify-content: space-between;
        gap: var(--spacing-xl);
        flex-wrap: wrap;
        margin-bottom: var(--spacing-lg);
        align-items: baseline;
    }

    .lost-how-title {
        font-size: 24px;
        font-weight: 900;
        color: rgba(17,24,39,0.92);
        margin: 0;
    }

    .lost-how-subtitle {
        margin: 0;
        color: rgba(17,24,39,0.62);
        font-weight: 600;
        max-width: 620px;
    }

    .lost-steps {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: var(--spacing-lg);
    }

    .lost-step {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        padding: var(--spacing-lg);
        transition: transform .15s ease, box-shadow .15s ease;
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
    }

    .lost-step:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
    }

    .lost-step .icon {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(245,158,11,0.14);
        border: 1px solid rgba(245,158,11,0.22);
        margin-bottom: 14px;
        color: #9a3412;
        font-size: 18px;
    }

    .lost-step:nth-child(2) .icon {
        background: rgba(16,185,129,0.14);
        border-color: rgba(16,185,129,0.22);
        color: #065f46;
    }

    .lost-step:nth-child(3) .icon {
        background: rgba(37,99,235,0.12);
        border-color: rgba(37,99,235,0.22);
        color: #1e3a8a;
    }

    .lost-step h4 {
        margin: 0 0 10px;
        font-size: 16px;
        font-weight: 900;
        color: rgba(17,24,39,0.92);
    }

    .lost-step ol {
        margin: 0;
        padding-left: 18px;
        color: rgba(17,24,39,0.70);
        font-weight: 600;
        line-height: 1.6;
        font-size: 14px;
    }

    .lost-section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-lg);
        padding: 0 4px;
    }

    .lost-section-head h2 {
        margin: 0;
        font-size: 26px;
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
    }

    .lost-section-head p {
        margin: 0;
        color: rgba(17,24,39,0.62);
        font-weight: 600;
        max-width: 560px;
    }

    .lost-pets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: var(--spacing-lg);
        align-items: stretch;
    }

    .lost-pet-card {
        background: rgba(255,255,255,0.9);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        display: flex;
        flex-direction: column;
        min-height: 460px;
    }

    .lost-pet-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-xl);
        border-color: rgba(217,119,6,0.25);
    }

    .lost-pet-media {
        height: 190px;
        position: relative;
        background: #fff;
    }

    .lost-pet-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .lost-pet-media .media-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(17,24,39,0.03);
        color: rgba(17,24,39,0.45);
        font-size: 52px;
    }

    .status-badge {
        position: absolute;
        top: 14px;
        right: 14px; /* LOST badge in top-right */
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.3px;
        color: #fff;
        background: #dc2626;
        box-shadow: 0 10px 20px rgba(220,38,38,0.20);
        text-transform: uppercase;
        border: 1px solid rgba(255,255,255,0.18);
    }

    .pet-type-pill {
        position: absolute;
        top: 14px;
        left: 14px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.2px;
        color: #0f172a;
        background: rgba(255,255,255,0.78);
        border: 1px solid rgba(0,0,0,0.06);
        backdrop-filter: blur(6px);
    }

    /* Pet type color pills */
    .type-dog {
        background: rgba(37,99,235,0.12);
        border-color: rgba(37,99,235,0.25);
        color: #1d4ed8;
    }
    .type-cat {
        background: rgba(16,185,129,0.12);
        border-color: rgba(16,185,129,0.25);
        color: #0f766e;
    }
    .type-other {
        background: rgba(148,163,184,0.18);
        border-color: rgba(148,163,184,0.28);
        color: #334155;
    }

    .lost-pet-body {
        padding: var(--spacing-lg);
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
    }

    .lost-pet-name {
        font-size: 18px;
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
        margin: 0;
        line-height: 1.2;
    }

    .lost-pet-brief {
        margin: 0;
        color: rgba(17,24,39,0.62);
        font-weight: 650;
        font-size: 13px;
        line-height: 1.5;
    }

    .lost-pet-meta {
        margin-top: 8px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 12px;
    }

    .meta-item {
        border: 1px solid rgba(0,0,0,0.05);
        background: rgba(255,255,255,0.65);
        border-radius: 14px;
        padding: 8px 10px;
    }

    .meta-label {
        font-size: 11px;
        font-weight: 900;
        color: rgba(17,24,39,0.55);
        text-transform: uppercase;
        letter-spacing: .3px;
        margin-bottom: 4px;
    }

    .meta-value {
        font-size: 13px;
        font-weight: 850;
        color: rgba(17,24,39,0.9);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .lost-pet-owner {
        margin-top: auto;
        padding-top: var(--spacing-md);
        border-top: 1px solid rgba(0,0,0,0.06);
        display: grid;
        gap: 10px;
    }

    .owner-line {
        font-size: 13px;
        color: rgba(17,24,39,0.72);
        font-weight: 650;
    }

    .owner-line strong {
        color: rgba(17,24,39,0.95);
    }

    .contact-owner-btn {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        border-radius: var(--radius-lg, 18px);
        padding: 12px 14px;
        background: #111827;
        color: #fff;
        text-decoration: none;
        font-weight: 900;
        border: 1px solid rgba(0,0,0,0.08);
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    }

    .contact-owner-btn:hover {
        transform: translateY(-2px);
        background: #0f172a;
        box-shadow: var(--shadow-md);
    }

    .empty-state {
        text-align: center;
        padding: var(--spacing-3xl) var(--spacing-2xl);
        background: rgba(255,255,255,0.7);
        border: 2px dashed rgba(0,0,0,0.12);
        border-radius: var(--radius-xl, 20px);
        grid-column: 1 / -1;
        box-shadow: 0 10px 25px rgba(0,0,0,0.03);
    }

    .empty-state .empty-icon {
        font-size: 64px;
        color: rgba(17,24,39,0.40);
        margin-bottom: var(--spacing-lg);
    }

    .empty-state h3 {
        font-size: 22px;
        font-weight: 1000;
        margin: 0 0 var(--spacing-md);
        color: rgba(17,24,39,0.95);
    }

    .empty-state p {
        margin: 0;
        color: rgba(17,24,39,0.62);
        font-weight: 650;
    }

    /* Modal controls (existing report modal kept) */
    .modal-control {
        width: 100%;
        padding: var(--spacing-md);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-md);
        font-family: inherit;
        box-sizing: border-box;
    }

    .modal-textarea {
        resize: vertical;
        min-height: 120px;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: var(--spacing-lg);
    }

    .modal-content {
        background: var(--color-bg);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--color-border);
        max-width: 520px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        padding: var(--spacing-xl);
        border-bottom: 1px solid var(--color-border);
        background: var(--color-warning);
        color: white;
        border-radius: var(--radius-xl) var(--radius-xl) 0 0;
    }

    .modal-title {
        margin: 0;
        font-size: 20px;
        font-weight: 900;
        text-align: center;
    }

    .modal-body {
        padding: var(--spacing-xl);
    }

    .modal-body textarea {
        width: 100%;
        padding: var(--spacing-md);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-md);
        font-size: 16px;
        font-family: inherit;
        resize: vertical;
        min-height: 100px;
    }

    .modal-footer {
        padding: var(--spacing-xl);
        border-top: 1px solid var(--color-border);
        display: flex;
        gap: var(--spacing-md);
        justify-content: flex-end;
        background: var(--color-bg-secondary);
        border-radius: 0 0 var(--radius-xl) var(--radius-xl);
    }

    .btn-cancel {
        background: var(--color-bg);
        color: var(--color-text);
        border: 1px solid var(--color-border);
        padding: var(--spacing-sm) var(--spacing-lg);
        border-radius: var(--radius-md);
        cursor: pointer;
        font-weight: 800;
    }

    .btn-cancel:hover {
        background: var(--color-text);
        color: var(--color-bg);
    }

    @media (max-width: 900px) {
        .lost-hero-inner {
            grid-template-columns: 1fr;
        }

        .hero-side {
            justify-items: start;
        }

        .lost-steps {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .lost-pets-page {
            padding: var(--spacing-md);
        }

        .lost-hero-inner {
            padding: var(--spacing-xl);
        }

        .lost-hero-title {
            font-size: 34px;
        }
    }
</style>

<div class="lost-pets-page">
    <!-- HERO -->
    <section class="lost-hero" aria-label="Lost Pets hero">
        <div class="lost-hero-inner">
            <div>
                <div class="lost-hero-kicker">
                    <i class="fas fa-paw"></i>
                    Reunite with love • Report with care
                </div>

                <h1 class="lost-hero-title">Lost Pets, Found Futures.</h1>
                <p class="lost-hero-subtitle">
                    Help us bring missing pets back home. Browse reported lost pets below — and if you have a missing pet,
                    report it right away so your community can step in.
                </p>

                <div class="lost-hero-actions">
                    <?php if (!empty($userPets)): ?>
                        <button class="btn-primary-cta" type="button" onclick="reportLostPet()">
                            <i class="fas fa-exclamation-triangle"></i>
                            Report Lost Pet
                        </button>
                    <?php else: ?>
                        <a class="btn-primary-cta" href="dashboard.php">
                            <i class="fas fa-exclamation-triangle"></i>
                            Report Lost Pet
                        </a>
                    <?php endif; ?>

                    <button class="btn-secondary-cta" type="button" onclick="document.getElementById('lostPetsList').scrollIntoView({behavior:'smooth', block:'start'})">
                        <i class="fas fa-search"></i>
                        View Reported Pets
                    </button>
                </div>
            </div>

            <div class="hero-side">
                <div class="hero-stat">
                    <div class="label">
                        <i class="fas fa-house-chimney"></i>
                        Community is looking
                    </div>
                    <div class="value"><?php echo (int)count($lostPets); ?></div>
                    <div class="hint">Reported lost pets currently in the system.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW TO HELP (secondary info section with icons + steps) -->
    <section class="lost-how" aria-label="How to Help">
        <div class="lost-how-head">
            <div>
                <h2 class="lost-how-title">How to Help (Fast & Kind)</h2>
            </div>
            <p class="lost-how-subtitle">
                A few small actions can make a big difference. Here’s what to do — whether you found a pet or your pet is missing.
            </p>
        </div>

        <div class="lost-steps">
            <div class="lost-step">
                <div class="icon">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <h4>If You Found a Lost Pet</h4>
                <ol>
                    <li>Look for tags or microchip info.</li>
                    <li>Share safe updates with shelters/vets.</li>
                    <li>Contact the owner using the info below.</li>
                </ol>
            </div>

            <div class="lost-step">
                <div class="icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h4>If Your Pet Is Lost</h4>
                <ol>
                    <li>Report your pet immediately.</li>
                    <li>Search the area + ask neighbors.</li>
                    <li>Post flyers and community updates.</li>
                </ol>
            </div>

            <div class="lost-step">
                <div class="icon">
                    <i class="fas fa-heart-circle-check"></i>
                </div>
                <h4>Prevention Tips</h4>
                <ol>
                    <li>Keep leashes secure when outside.</li>
                    <li>Microchip + keep tags updated.</li>
                    <li>Store recent photos for quick sharing.</li>
                </ol>
            </div>
        </div>
    </section>

    <div class="lost-section-head" id="lostPetsList">
        <div>
            <h2>Reported Lost Pets</h2>
            <p>Image-first cards showing key details so you can contact the owner quickly.</p>
        </div>
    </div>

    <div class="lost-pets-grid">
        <?php if (empty($lostPets)): ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-search"></i></div>
                <h3>No Lost Pets Reported</h3>
                <p>Check back later — and thank you for helping.</p>
            </div>
        <?php else: ?>
            <?php foreach ($lostPets as $pet): ?>
                <?php
                    $category = $pet['category'] ?? 'PET';
                    $petType = $pet['pet_type'] ?? 'Unknown';
                    $color = $pet['color'] ?? 'Unknown';
                    $gender = $pet['gender'] ?? 'Unknown';
                    $ageYears = $pet['age'] ? htmlspecialchars($pet['age']) . ' years' : 'Unknown';
                    $registeredOn = !empty($pet['registered_on']) ? date('m/d/Y', strtotime($pet['registered_on'])) : 'Unknown';

                    $photoPath = isset($pet['photo_path']) ? (string)$pet['photo_path'] : '';
                    $photoOk = $photoPath !== '';

                    // photo_path may be a filename (e.g. pet_1.png) OR a path/fragment (e.g. uploads/pet_1.png or static/uploads/...)
                    if ($photoOk) {
                        $photoSrc = $photoPath;

                        if (str_starts_with($photoSrc, 'uploads/')) {
                            $photoSrc = '../' . $photoSrc;
                        } elseif (str_starts_with($photoSrc, 'static/uploads/')) {
                            $photoSrc = '../' . $photoSrc;
                        } elseif (str_contains($photoSrc, '/uploads/')) {
                            // already contains an uploads path fragment
                            $photoSrc = '../' . ltrim($photoSrc, './');
                        } else {
                            // assume it is just a filename stored in DB
                            $photoSrc = '../uploads/' . $photoSrc;
                        }
                    }

                    $lastSeenLocation = $pet['last_seen_location'] ?? '';
                    $lastSeenLocation = $lastSeenLocation ? $lastSeenLocation : 'Location not provided';

                    $typeClass = 'type-other';
                    if (strtolower((string)$category) === 'dog') $typeClass = 'type-dog';
                    if (strtolower((string)$category) === 'cat') $typeClass = 'type-cat';
                ?>
                <div class="lost-pet-card">
                    <div class="lost-pet-media">
                        <?php if ($photoOk): ?>
                            <img src="<?php echo htmlspecialchars($photoSrc); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                        <?php else: ?>
                            <div class="media-fallback">
                                <i class="fas fa-paw"></i>
                            </div>
                        <?php endif; ?>

                        <div class="pet-type-pill <?php echo htmlspecialchars($typeClass); ?>">
                            <?php echo htmlspecialchars($category); ?>
                        </div>
                        <div class="status-badge">LOST</div>
                    </div>

                    <div class="lost-pet-body">
                        <h3 class="lost-pet-name"><?php echo htmlspecialchars($pet['name']); ?></h3>
                        <p class="lost-pet-brief">
                            <strong>Last seen:</strong> <?php echo htmlspecialchars($lastSeenLocation); ?><br>
                            <strong>Date:</strong> <?php echo htmlspecialchars($registeredOn); ?>
                        </p>

                        <div class="lost-pet-meta">
                            <div class="meta-item">
                                <div class="meta-label">Type</div>
                                <div class="meta-value"><?php echo htmlspecialchars($petType); ?></div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Color</div>
                                <div class="meta-value"><?php echo htmlspecialchars($color); ?></div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Age</div>
                                <div class="meta-value"><?php echo $ageYears; ?></div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Gender</div>
                                <div class="meta-value"><?php echo htmlspecialchars($gender); ?></div>
                            </div>
                        </div>

                        <div class="lost-pet-owner">
                            <div class="owner-line">
                                <strong>Owner:</strong> <?php echo htmlspecialchars($pet['owner_name']); ?>
                                <br>
                                <strong>Contact:</strong> <?php echo htmlspecialchars($pet['owner_contact'] ?? 'Not provided'); ?>
                            </div>

                              <?php if ((int)$pet['owner_id'] !== (int)$_SESSION['user_id']): ?>
                                  <button class="contact-owner-btn" onclick="openContactModal(<?php echo $pet['id']; ?>, '<?php echo htmlspecialchars($pet['name'], ENT_QUOTES); ?>', 'lost_pet')">
                                      <i class="fas fa-envelope"></i>
                                      Contact Owner
                                  </button>
                              <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Report Lost Pet Modal (primary CTA wiring) -->
<div class="modal-overlay" id="reportModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Report Lost Pet</h3>
        </div>
        <form id="lostPetForm">
            <div class="modal-body">
                <input type="hidden" id="modalPetId" name="pet_id">
                <div style="margin-bottom: var(--spacing-lg);">
                    <label style="display: block; margin-bottom: var(--spacing-md); font-weight: 800; color: var(--color-text);">
                        Select Your Pet
                    </label>
                    <select id="petSelect" class="modal-control" required>
                        <option value="">Choose a pet...</option>
                        <?php foreach ($userPets as $pet): ?>
                            <option value="<?php echo $pet['id']; ?>">
                                <?php echo htmlspecialchars($pet['name']); ?> (<?php echo htmlspecialchars($pet['pet_type'] ?? 'Unknown'); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: var(--spacing-md); font-weight: 800; color: var(--color-text);">
                        Details About the Loss
                    </label>
                    <textarea name="comment" rows="4" class="modal-control modal-textarea" required
                              placeholder="Describe when and where you last saw your pet, any distinctive features, etc."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeReportModal()">Cancel</button>
                <button type="submit" style="background: var(--color-warning); color: white; border: none; padding: var(--spacing-sm) var(--spacing-lg); border-radius: var(--radius-md); cursor: pointer; font-weight: 900;">
                    <i class="fas fa-bullhorn"></i>
                    Report Pet as Lost
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function reportLostPet() {
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

    const petSelect = document.getElementById('petSelect');
    if (!petSelect.value) {
        alert('Please select a pet to report as lost.');
        return;
    }

    const formData = new FormData(this);

    fetch('user/report_lost.php', {
        method: 'POST',
        body: JSON.stringify({
            pet_id: petSelect.value,
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

// Close modal when clicking outside
document.getElementById('reportModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReportModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (document.getElementById('reportModal').style.display === 'flex') {
            closeReportModal();
        }
    }
});
</script>

<?php include 'includes/contact_owner_modal.php'; ?>
<?php include 'includes/footer.php'; ?>
