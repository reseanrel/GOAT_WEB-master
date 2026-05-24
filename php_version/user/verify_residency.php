<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

$db = Database::getInstance();
$conn = $db->getConnection();

$userId = (int)$_SESSION['user_id'];

// Get current residency info
$info = getUserResidencyInfo($userId);
$status = $info['residency_status'] ?? 'unverified';
$document = $info['residency_document'] ?? null;
$rejectionReason = $info['residency_rejection_reason'] ?? null;
$verifiedAt = $info['residency_verified_at'] ?? null;

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    if (isset($_FILES['residency_doc']) && $_FILES['residency_doc']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['residency_doc'];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = 'Only JPG, PNG, or PDF files are allowed.';
        } elseif ($file['size'] > $maxSize) {
            $errors[] = 'File size must be less than 5MB.';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = 'residency_' . $userId . '_' . uniqid() . '.' . strtolower($ext);

            $uploadsDir = dirname(__DIR__) . '/uploads';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }

            $destination = $uploadsDir . DIRECTORY_SEPARATOR . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Update DB
                $stmt = $conn->prepare("
                    UPDATE users 
                    SET residency_document = ?, 
                        residency_status = 'pending',
                        residency_rejection_reason = NULL
                    WHERE id = ?
                ");
                $stmt->execute([$newFileName, $userId]);

                $_SESSION['success'] = 'Document uploaded successfully! It is now pending admin review.';
                header('Location: verify_residency.php');
                exit();
            } else {
                $errors[] = 'Failed to save the uploaded file. Please try again.';
            }
        }
    } else {
        $errors[] = 'Please select a valid document to upload.';
    }
}

// Handle re-upload after rejection (same as above, but we already have the form)
?>
<?php include '../includes/header.php'; ?>

<style>
    .verify-page {
        max-width: 800px;
        margin: 0 auto;
        padding: var(--spacing-xl);
    }
    .verify-card {
        background: var(--color-bg);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-xl);
        padding: var(--spacing-2xl);
        box-shadow: var(--shadow-sm);
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 13px;
    }
    .status-unverified { background: #f3f4f6; color: #374151; }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-verified { background: #d1fae5; color: #065f46; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    .doc-preview {
        margin-top: var(--spacing-md);
        padding: var(--spacing-md);
        background: var(--color-bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--color-border);
    }
    .upload-area {
        border: 2px dashed var(--color-border);
        border-radius: var(--radius-xl);
        padding: 40px 20px;
        text-align: center;
        background: var(--color-bg-secondary);
        transition: all 0.2s ease;
    }
    .upload-area:hover {
        border-color: var(--color-primary);
        background: rgba(26,115,232,0.03);
    }
    .upload-icon {
        font-size: 48px;
        color: var(--color-primary);
        margin-bottom: 12px;
    }
    .accepted-types {
        font-size: 12px;
        color: var(--color-text-muted);
        margin-top: 8px;
    }
    .rejection-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: var(--spacing-md);
        border-radius: var(--radius-lg);
        margin-bottom: var(--spacing-lg);
    }
</style>

<div class="verify-page">
    <div style="margin-bottom: var(--spacing-xl);">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
            <i class="fas fa-id-card" style="font-size:28px; color:var(--color-primary);"></i>
            <h1 style="margin:0; font-size:28px; font-weight:900;">Verify Your Residency</h1>
        </div>
        <p style="color:var(--color-text-secondary); font-weight:600; max-width:620px;">
            To fully participate in the Pila Pet Registration System (register pets, adopt, etc.), 
            please confirm you are a resident of <strong>Pila, Laguna</strong>.
        </p>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success" style="margin-bottom:var(--spacing-lg);">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error" style="margin-bottom:var(--spacing-lg);">
            <?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?>
        </div>
    <?php endif; ?>

    <div class="verify-card">
        <!-- Current Status -->
        <div style="margin-bottom: var(--spacing-xl);">
            <div style="font-size:13px; font-weight:800; color:var(--color-text-muted); margin-bottom:6px;">CURRENT STATUS</div>
            <?php
                $statusClass = 'status-unverified';
                $statusIcon = 'fa-question-circle';
                $statusText = 'Not Verified';
                if ($status === 'pending') { $statusClass = 'status-pending'; $statusIcon = 'fa-clock'; $statusText = 'Pending Review'; }
                if ($status === 'verified') { $statusClass = 'status-verified'; $statusIcon = 'fa-check-circle'; $statusText = 'Verified Resident'; }
                if ($status === 'rejected') { $statusClass = 'status-rejected'; $statusIcon = 'fa-times-circle'; $statusText = 'Rejected - Please Re-submit'; }
            ?>
            <span class="status-badge <?php echo $statusClass; ?>">
                <i class="fas <?php echo $statusIcon; ?>"></i>
                <?php echo $statusText; ?>
            </span>

            <?php if ($status === 'verified' && $verifiedAt): ?>
                <div style="margin-top:8px; font-size:13px; color:var(--color-text-secondary);">
                    Verified on <?php echo date('F j, Y', strtotime($verifiedAt)); ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($status === 'rejected' && $rejectionReason): ?>
            <div class="rejection-box">
                <strong>Admin Note:</strong><br>
                <?php echo nl2br(htmlspecialchars($rejectionReason)); ?>
            </div>
        <?php endif; ?>

        <?php if ($document): ?>
            <div class="doc-preview">
                <div style="font-size:13px; font-weight:700; margin-bottom:8px;">Submitted Document</div>
                <?php
                    $docPath = '../uploads/' . htmlspecialchars($document);
                    $isImage = preg_match('/\.(jpg|jpeg|png)$/i', $document);
                ?>
                <?php if ($isImage && file_exists(dirname(__DIR__) . '/uploads/' . $document)): ?>
                    <a href="<?php echo $docPath; ?>" target="_blank">
                        <img src="<?php echo $docPath; ?>" alt="Residency document" 
                             style="max-width:220px; max-height:160px; border-radius:8px; border:1px solid var(--color-border); object-fit:cover;">
                    </a>
                <?php else: ?>
                    <a href="<?php echo $docPath; ?>" target="_blank" class="btn-action btn-view" style="display:inline-flex;">
                        <i class="fas fa-file-pdf"></i> View / Download Document
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($status === 'verified'): ?>
            <div style="margin-top:var(--spacing-xl); padding:16px; background:#ecfdf5; border-radius:var(--radius-lg); border:1px solid #a7f3d0;">
                <i class="fas fa-check-circle" style="color:#059669;"></i>
                <strong style="color:#065f46;">Thank you!</strong> Your residency has been verified. You now have full access to all features.
            </div>

        <?php elseif ($status === 'pending'): ?>
            <div style="margin-top:var(--spacing-xl); padding:16px; background:#fefce8; border-radius:var(--radius-lg); border:1px solid #fde047;">
                <i class="fas fa-clock"></i>
                <strong>Your document is under review.</strong><br>
                An administrator will verify it shortly. You will be notified once approved.
            </div>

        <?php else: ?>
            <!-- Upload Form -->
            <form method="POST" enctype="multipart/form-data" style="margin-top:var(--spacing-xl);">
                <div class="upload-area">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div style="font-weight:800; font-size:16px; margin-bottom:4px;">Upload Proof of Residency</div>
                    <div style="color:var(--color-text-secondary); font-size:14px;">
                        Choose a clear photo or scan of any of the following:
                    </div>
                    <ul style="text-align:left; max-width:320px; margin:12px auto; font-size:13px; color:var(--color-text-secondary);">
                        <li>Barangay Certificate / Clearance</li>
                        <li>Community Tax Certificate (Cedula)</li>
                        <li>Utility Bill (Meralco, Water, etc.) showing Pila address</li>
                        <li>Voter’s ID or PhilID with Pila address</li>
                        <li>Barangay ID</li>
                    </ul>
                    <input type="file" name="residency_doc" id="residency_doc" accept=".jpg,.jpeg,.png,.pdf" required
                           style="margin-top:12px;">
                    <div class="accepted-types">Accepted: JPG, PNG, PDF • Max 5MB</div>
                </div>

                <button type="submit" name="upload" value="1" class="btn-primary" style="margin-top:var(--spacing-lg); width:100%;">
                    <i class="fas fa-paper-plane"></i> Submit for Verification
                </button>
            </form>
            <p style="font-size:12px; color:var(--color-text-muted); text-align:center; margin-top:12px;">
                Your document will only be visible to administrators for verification purposes.
            </p>
        <?php endif; ?>
    </div>

    <div style="margin-top:var(--spacing-xl); text-align:center;">
        <a href="dashboard.php" class="btn-action btn-back">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
