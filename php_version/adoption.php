<?php
session_start();
require_once 'includes/auth.php';
requireLogin();

$db = Database::getInstance();
$conn = $db->getConnection();

// Pagination setup for Available Adoption Pets
$perPage = 15;
$currentPage = max(1, (int)($_GET['page'] ?? 1));

// Count total adoption pets
try {
    $countSql = "
        SELECT COUNT(*) FROM pets p
        WHERE p.available_for_adoption = 1 AND p.archived = 0 AND p.status = 'approved' AND p.deceased = 0
    ";
    $totalAdoption = (int)$conn->query($countSql)->fetchColumn();
    $totalPages = max(1, (int)ceil($totalAdoption / $perPage));
    $currentPage = min($currentPage, $totalPages);
    $offset = ($currentPage - 1) * $perPage;
} catch (Exception $e) {
    $totalAdoption = 0;
    $totalPages = 1;
    $currentPage = 1;
    $offset = 0;
}

// Get paged adoption pets
try {
    $stmt = $conn->prepare("
        SELECT p.*, p.photo_url AS photo_path, u.full_name as owner_name, u.email as owner_email,
               u.contact_number as owner_contact
        FROM pets p
        JOIN users u ON p.owner_id = u.id
        WHERE p.available_for_adoption = 1 AND p.archived = 0 AND p.status = 'approved' AND p.deceased = 0
        ORDER BY p.registered_on DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $adoptionPets = $stmt->fetchAll();
} catch (Exception $e) {
    $adoptionPets = [];
    $totalAdoption = 0;
    $totalPages = 1;
}

// Get user's pets that could be put up for adoption
try {
    $stmt = $conn->prepare("
        SELECT * FROM pets
        WHERE owner_id = ? AND available_for_adoption = 0 AND archived = 0 AND status = 'approved' AND deceased = 0
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
    /* Adoption - warm modern rescue UI (image-first cards) */
    .adoption {
        max-width: 1320px;
        margin: 0 auto;
        padding: var(--spacing-lg);
    }

    .adoption-hero {
        position: relative;
        border-radius: var(--radius-xl, 20px);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: var(--shadow-lg);
        background: #fff7ed;
        margin-bottom: var(--spacing-2xl);
    }

    .adoption-hero::before {
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

    .adoption-hero-inner {
        position: relative;
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: var(--spacing-xl);
        padding: var(--spacing-2xl);
        align-items: center;
    }

    .adoption-hero-title {
        font-size: 44px;
        line-height: 1.05;
        font-weight: 900;
        margin: 0 0 var(--spacing-md);
        letter-spacing: -0.5px;
        color: rgba(17,24,39,0.95);
    }

    .adoption-hero-subtitle {
        margin: 0 0 var(--spacing-lg);
        color: rgba(17,24,39,0.72);
        font-size: 16px;
        max-width: 620px;
        font-weight: 600;
    }

    .lost-hero-actions,
    .adoption-hero-actions {
        display: flex;
        gap: var(--spacing-md);
        flex-wrap: wrap;
        align-items: center;
    }

    .btn-primary-cta {
        background: #f59e0b;
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
        box-shadow: 0 10px 20px rgba(245,158,11,0.22);
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
        min-width: 240px;
        justify-content: center;
    }

    .btn-primary-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(245,158,11,0.28);
        filter: saturate(1.05);
    }

    .btn-secondary-cta {
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(0,0,0,0.08);
        color: rgba(17,24,39,0.86);
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
    }

    .btn-secondary-cta:hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,0.9);
        box-shadow: var(--shadow-md);
    }

    .adoption-hero-stat {
        width: 100%;
        max-width: 320px;
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: var(--radius-xl, 20px);
        padding: var(--spacing-lg);
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .adoption-hero-stat .label {
        font-weight: 900;
        color: rgba(17,24,39,0.66);
        margin-bottom: 8px;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .adoption-hero-stat .value {
        font-size: 28px;
        font-weight: 1000;
        color: rgba(17,24,39,0.92);
        line-height: 1.1;
    }

    .adoption-hero-stat .hint {
        margin-top: 10px;
        font-size: 13px;
        color: rgba(17,24,39,0.62);
        font-weight: 700;
    }

    /* Image-first pet finder cards */
    .adoption-section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-lg);
        padding: 0 4px;
    }

    .adoption-section-head h2 {
        margin: 0;
        font-size: 26px;
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
    }

    .adoption-section-head p {
        margin: 0;
        color: rgba(17,24,39,0.62);
        font-weight: 700;
        max-width: 560px;
    }

    .adoption-pets-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        align-items: stretch;
    }

    @media (min-width: 768px) {
        .adoption-pets-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }
    }

    @media (min-width: 1200px) {
        .adoption-pets-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
        }
    }

    .pet-adoption-card {
        background: rgba(255,255,255,0.92);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        display: flex;
        flex-direction: column;
        min-height: 320px;
    }

    .pet-adoption-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-xl);
        border-color: rgba(245,158,11,0.30);
    }

    .pet-adoption-media {
        height: 128px;
        position: relative;
        background: #fff;
    }

    .pet-adoption-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .pet-adoption-media .media-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(17,24,39,0.03);
        color: rgba(17,24,39,0.45);
        font-size: 32px;
    }

    /* badges */
    .status-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 1000;
        letter-spacing: 0.3px;
        color: #fff;
        background: #f59e0b;
        box-shadow: 0 6px 12px rgba(245,158,11,0.18);
        text-transform: uppercase;
        border: 1px solid rgba(255,255,255,0.18);
    }

    .pet-type-pill {
        position: absolute;
        top: 8px;
        left: 8px;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 1000;
        letter-spacing: 0.2px;
        color: #0f172a;
        background: rgba(255,255,255,0.78);
        border: 1px solid rgba(0,0,0,0.06);
        backdrop-filter: blur(6px);
    }

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

    .pet-adoption-body {
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1;
    }

    .pet-adoption-name {
        font-size: 15px;
        font-weight: 1000;
        color: rgba(17,24,39,0.95);
        margin: 0;
        line-height: 1.15;
    }

    .pet-adoption-brief {
        margin: 0;
        color: rgba(17,24,39,0.62);
        font-weight: 750;
        font-size: 11px;
        line-height: 1.35;
    }

    .adoption-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px 8px;
        margin-top: 4px;
    }

    .meta-item {
        border: 1px solid rgba(0,0,0,0.05);
        background: rgba(255,255,255,0.65);
        border-radius: 10px;
        padding: 5px 8px;
    }

    .meta-label {
        font-size: 9px;
        font-weight: 1000;
        color: rgba(17,24,39,0.55);
        text-transform: uppercase;
        letter-spacing: .3px;
        margin-bottom: 2px;
    }

    .meta-value {
        font-size: 11px;
        font-weight: 900;
        color: rgba(17,24,39,0.9);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pet-adoption-actions {
        margin-top: auto;
        padding-top: 8px;
        border-top: 1px solid rgba(0,0,0,0.06);
        display: grid;
        gap: 6px;
    }

    .apply-btn {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 12px;
        padding: 8px 10px;
        background: #111827;
        color: #fff;
        text-decoration: none;
        font-weight: 1000;
        font-size: 12px;
        border: 1px solid rgba(0,0,0,0.08);
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    }

    .contact-btn {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 12px;
        padding: 8px 10px;
        background: rgba(255,255,255,0.7);
        color: rgba(17,24,39,0.9);
        text-decoration: none;
        font-weight: 1000;
        font-size: 12px;
        border: 1px solid rgba(0,0,0,0.08);
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    }

    .your-pet-indicator {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 10px;
        padding: 6px 10px;
        background: rgba(245, 158, 11, 0.15);
        color: #92400e;
        font-weight: 800;
        font-size: 12px;
        border: 1px solid rgba(245, 158, 11, 0.3);
        text-align: center;
    }

    .apply-btn:hover {
        transform: translateY(-2px);
        background: #0f172a;
        box-shadow: var(--shadow-md);
    }

    .contact-btn:hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,0.92);
        box-shadow: var(--shadow-md);
    }

    .empty-state {
        text-align: center;
        padding: var(--spacing-xl) var(--spacing-lg);
        background: rgba(255,255,255,0.7);
        border: 2px dashed rgba(0,0,0,0.12);
        border-radius: 16px;
        grid-column: 1 / -1;
        box-shadow: 0 8px 20px rgba(0,0,0,0.03);
    }

    .empty-state .empty-icon {
        font-size: 48px;
        color: rgba(17,24,39,0.40);
        margin-bottom: var(--spacing-md);
    }

    .empty-state h3 {
        font-size: 18px;
        font-weight: 1000;
        margin: 0 0 var(--spacing-sm);
        color: rgba(17,24,39,0.95);
    }

    .empty-state p {
        margin: 0;
        color: rgba(17,24,39,0.62);
        font-weight: 750;
    }

    /* Pagination - modern compact dashboard style (adoption amber theme) */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 4px;
        margin: 24px auto 0;
        flex-wrap: wrap;
    }
    .pagination .page-link,
    .pagination .page-ellipsis {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 10px;
        border: 1px solid rgba(0,0,0,0.08);
        background: #fff;
        color: #374151;
        text-decoration: none;
        transition: all .12s ease;
    }
    .pagination .page-link:hover {
        background: #fffbeb;
        border-color: #fcd34d;
        transform: translateY(-1px);
    }
    .pagination .page-link.active {
        background: #d97706;
        color: #fff;
        border-color: #b45309;
        box-shadow: 0 2px 6px rgba(217,119,6,0.25);
    }
    .pagination .page-ellipsis {
        border: none;
        background: transparent;
        min-width: 20px;
        color: #9ca3af;
        font-weight: 600;
    }
    .pagination .prev,
    .pagination .next {
        font-weight: 800;
        padding: 0 14px;
    }

    /* Modal */
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
        max-width: 500px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        padding: var(--spacing-xl);
        border-bottom: 1px solid var(--color-border);
        background: var(--color-primary);
        color: white;
        border-radius: var(--radius-xl) var(--radius-xl) 0 0;
    }

    .modal-title {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
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

    .modal-body textarea:focus {
        outline: none;
        border-color: var(--color-primary);
    }

    .modal-body label {
        display: block;
        margin-bottom: var(--spacing-md);
        font-weight: 600;
        color: var(--color-text);
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

    .modal-control {
        width: 100%;
        padding: var(--spacing-md);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-md);
        font-family: inherit;
        font-size: 15px;
        box-sizing: border-box;
        background: var(--color-bg);
        color: var(--color-text);
    }

    .modal-control:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .modal-textarea {
        resize: vertical;
        min-height: 120px;
        line-height: 1.5;
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

    /* Offer modal - use page's warm amber to align with adoption branding */
    #offerModal .modal-header {
        background: #f59e0b;
    }

    #offerModal button[type="submit"] {
        background: #f59e0b;
    }

    /* Modal primary action button hover */
    #offerModal button[type="submit"]:hover {
        background: #d97706;
        transform: translateY(-1px);
    }

    /* Small screens */
    @media (max-width: 900px) {
        .adoption-hero-inner {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .adoption {
            padding: var(--spacing-md);
        }

        .adoption-hero-inner {
            padding: var(--spacing-xl);
        }

        .adoption-hero-title {
            font-size: 34px;
        }
    }
</style>

<div class="adoption">
    <section class="adoption-hero" aria-label="Pet Adoption hero">
        <div class="adoption-hero-inner">
            <div>
                <h1 class="adoption-hero-title">Adopt a Loving Future.</h1>
                <p class="adoption-hero-subtitle">
                    Browse pets available for adoption. Image-first cards help you choose quickly—and reach out with kindness.
                </p>

                <div class="adoption-hero-actions">
                    <button class="btn-secondary-cta" type="button" onclick="document.getElementById('adoptionInfoModal').style.display='flex'">
                        <i class="fas fa-info-circle"></i>
                        How It Works
                    </button>

                    <?php if (!empty($userPets)): ?>
                        <button class="btn-primary-cta" type="button" onclick="document.getElementById('offerModal').style.display='flex'">
                            <i class="fas fa-plus"></i>
                            List Your Pet
                        </button>
                    <?php else: ?>
                        <button class="btn-primary-cta" type="button" onclick="alert('List your pet for adoption from your dashboard when eligible.')">
                            <i class="fas fa-plus"></i>
                            List Your Pet
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="adoption-hero-stat">
                <div class="label">
                    <i class="fas fa-heart"></i>
                    Available pets
                </div>
                    <div class="value"><?php echo (int)$totalAdoption; ?></div>
                <div class="hint">Ready for loving homes in our community.</div>
            </div>
        </div>
    </section>

    <div class="adoption-section-head" id="adoptionPetsList">
        <div>
            <h2>Available Pets</h2>
            <p>Modern adoption cards with quick info—so you can move fast when your heart finds “the one.”</p>
        </div>
    </div>

    <div class="adoption-pets-grid">
        <?php if (empty($adoptionPets)): ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-heart"></i></div>
                <h3>No Pets Available</h3>
                <p>There are currently no pets available for adoption. Check back later!</p>
            </div>
        <?php else: ?>
            <?php foreach ($adoptionPets as $pet): ?>
                <?php
                    $photoSrc = getPetPhotoSrc($pet);
                    $photoOk = !empty($photoSrc);

                    $category = $pet['category'] ?? 'PET';
                    $petType = $pet['pet_type'] ?? 'Unknown';
                    $color = $pet['color'] ?? 'Unknown';
                    $gender = $pet['gender'] ?? 'Unknown';
                    $ageYears = $pet['age'] ? htmlspecialchars($pet['age']) . ' years' : 'Unknown';

                    $registeredOn = !empty($pet['registered_on']) ? date('m/d/Y', strtotime($pet['registered_on'])) : 'Unknown';

                    // “Last seen location” equivalent: pets table doesn’t store a dedicated field,
                    // but we can show owner address only if provided by current query. We didn't join it here.
                    // Fallback to "Location not provided".
                    $lastSeenLocation = 'Location not provided';

                    $typeClass = 'type-other';
                    if (strtolower((string)$category) === 'dog') $typeClass = 'type-dog';
                    if (strtolower((string)$category) === 'cat') $typeClass = 'type-cat';
                ?>
                <div class="pet-adoption-card">
                    <div class="pet-adoption-media">
                        <?php if ($photoOk): ?>
                            <img src="<?php echo htmlspecialchars($photoSrc); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                        <?php else: ?>
                            <div class="media-fallback"><i class="fas fa-paw"></i></div>
                        <?php endif; ?>

                        <div class="pet-type-pill <?php echo htmlspecialchars($typeClass); ?>">
                            <?php echo htmlspecialchars($category); ?>
                        </div>
                        <div class="status-badge">FOR ADOPTION</div>
                    </div>

                    <div class="pet-adoption-body">
                        <h3 class="pet-adoption-name"><?php echo htmlspecialchars($pet['name']); ?></h3>

                        <p class="pet-adoption-brief">
                            <strong>Location:</strong> <?php echo htmlspecialchars($lastSeenLocation); ?><br>
                            <strong>Date:</strong> <?php echo htmlspecialchars($registeredOn); ?>
                        </p>

                        <div class="adoption-meta">
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

                           <div class="pet-adoption-actions">
                               <?php if ((int)$pet['owner_id'] === (int)$_SESSION['user_id']): ?>
                                   <div class="your-pet-indicator">
                                       <i class="fas fa-user-check"></i>
                                       This is your pet
                                   </div>
                               <?php else: ?>
                                   <?php if (!isAdmin()): ?>
                                       <a href="user/adoption_application.php?pet_id=<?php echo $pet['id']; ?>" class="apply-btn">
                                           <i class="fas fa-heart"></i>
                                           Apply to Adopt
                                       </a>
                                   <?php endif; ?>

                                   <button class="contact-btn" onclick="openContactModal(<?php echo $pet['id']; ?>, '<?php echo htmlspecialchars($pet['name'], ENT_QUOTES); ?>', 'adoption')">
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

     <?php if ($totalAdoption > 0 && $totalPages > 1): ?>
     <div class="pagination" aria-label="Pagination for adoption pets">
         <?php if ($currentPage > 1): ?>
             <a href="?page=<?= $currentPage - 1 ?>" class="page-link prev">← Prev</a>
         <?php endif; ?>

         <?php
         $start = max(1, $currentPage - 2);
         $end = min($totalPages, $currentPage + 2);
         if ($start > 1) echo '<span class="page-ellipsis">…</span>';
         for ($i = $start; $i <= $end; $i++) {
             if ($i === $currentPage) {
                 echo '<span class="page-link active">' . $i . '</span>';
             } else {
                 echo '<a href="?page=' . $i . '" class="page-link">' . $i . '</a>';
             }
         }
         if ($end < $totalPages) echo '<span class="page-ellipsis">…</span>';
         ?>

         <?php if ($currentPage < $totalPages): ?>
             <a href="?page=<?= $currentPage + 1 ?>" class="page-link next">Next →</a>
         <?php endif; ?>
     </div>
     <?php endif; ?>
 </div>
 
 <!-- Information Modal -->
<div class="modal-overlay" id="adoptionInfoModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">How Adoption Works</h3>
        </div>
        <div class="modal-body">
            <div style="margin-bottom: var(--spacing-lg);">
                <h4 style="color: var(--color-text); margin-bottom: var(--spacing-md);">For Potential Adopters:</h4>
                <ul style="color: var(--color-text-secondary); line-height: 1.6;">
                    <li>Browse available pets and submit formal adoption applications</li>
                    <li>Provide detailed information about yourself and your living situation</li>
                    <li>Pet owners and administrators review all applications</li>
                    <li>You'll be contacted if selected for the next steps</li>
                </ul>
            </div>
            <div style="margin-bottom: var(--spacing-lg);">
                <h4 style="color: var(--color-text); margin-bottom: var(--spacing-md);">For Pet Owners:</h4>
                <ul style="color: var(--color-text-secondary); line-height: 1.6;">
                    <li>List your pet for adoption through your dashboard</li>
                    <li>Review applications from potential adopters</li>
                    <li>Administrators oversee the final adoption process</li>
                    <li>Ensure your pet goes to the best possible home</li>
                </ul>
            </div>
            <div>
                <h4 style="color: var(--color-text); margin-bottom: var(--spacing-md);">Why Choose Our Process:</h4>
                <ul style="color: var(--color-text-secondary); line-height: 1.6;">
                    <li>Thorough background checks and home visits</li>
                    <li>Vetted adopters committed to pet welfare</li>
                    <li>Administrative oversight for quality assurance</li>
                    <li>Lifelong support for successful adoptions</li>
                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeInfoModal()">Close</button>
        </div>
    </div>
</div>

<!-- Offer Pet Modal -->
<div class="modal-overlay" id="offerModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-hand-holding-heart"></i> Offer Pet for Adoption</h3>
        </div>
        <form id="adoptionForm">
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
                        Adoption Details
                    </label>
                    <textarea name="comment" rows="4" class="modal-control modal-textarea" required
                              placeholder="Describe the pet's personality, any special needs, reason for adoption, etc."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeOfferModal()">Cancel</button>
                <button type="submit" style="color: white; border: none; padding: var(--spacing-sm) var(--spacing-lg); border-radius: var(--radius-md); cursor: pointer; font-weight: 800; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-paw"></i> Offer for Adoption
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function closeInfoModal() {
    document.getElementById('adoptionInfoModal').style.display = 'none';
}

function offerForAdoption() {
    document.getElementById('offerModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeOfferModal() {
    document.getElementById('offerModal').style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('adoptionForm').reset();
}

document.getElementById('adoptionForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const petSelect = document.getElementById('petSelect');
    if (!petSelect.value) {
        alert('Please select a pet to offer for adoption.');
        return;
    }

    const formData = new FormData(this);

    fetch('user/offer_adoption.php', {
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
            alert('Pet offered for adoption successfully! (Note: Admin approval required)');
            closeOfferModal();
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

// Close modals when clicking outside
document.getElementById('adoptionInfoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeInfoModal();
    }
});

document.getElementById('offerModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeOfferModal();
    }
});

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (document.getElementById('adoptionInfoModal').style.display === 'flex') {
            closeInfoModal();
        }
        if (document.getElementById('offerModal').style.display === 'flex') {
            closeOfferModal();
        }
    }
});
</script>

<?php include 'includes/contact_owner_modal.php'; ?>
<?php include 'includes/footer.php'; ?>
