<?php
// One-time residency verification migration runner
// Visit this file ONCE in your browser (while logged in as admin or not), then delete it.

session_start();
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

$alreadyDone = false;
$message = '';
$success = false;

try {
    // Check if column already exists
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'residency_status'");
    if ($stmt->fetch()) {
        $alreadyDone = true;
        $message = 'Residency verification columns already exist. Nothing to do.';
        $success = true;
    } else {
        // Run the ALTER statements
        $sql = "
            ALTER TABLE users 
              ADD COLUMN residency_status ENUM('unverified','pending','verified','rejected') DEFAULT 'unverified' AFTER archived,
              ADD COLUMN residency_document VARCHAR(255) NULL AFTER residency_status,
              ADD COLUMN residency_rejection_reason TEXT NULL AFTER residency_document,
              ADD COLUMN residency_verified_at DATETIME NULL AFTER residency_rejection_reason,
              ADD COLUMN residency_verified_by INT NULL AFTER residency_verified_at,
              ADD CONSTRAINT fk_residency_verified_by FOREIGN KEY (residency_verified_by) REFERENCES users(id) ON DELETE SET NULL
        ";
        $conn->exec($sql);
        $message = '✅ Residency verification columns added successfully!';
        $success = true;
    }
} catch (PDOException $e) {
    $message = 'Error: ' . $e->getMessage();
    $success = false;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Residency Migration</title>
    <style>
        body { font-family: system-ui; padding: 40px; background: #f8fafc; }
        .card { max-width: 620px; margin: 0 auto; background: white; padding: 32px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .success { color: #166534; background: #dcfce7; padding: 12px; border-radius: 8px; }
        .error { color: #991b1b; background: #fee2e2; padding: 12px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Residency Verification Migration</h1>
        <p>This script adds the necessary columns for the Pila residency verification feature.</p>
        
        <div class="<?php echo $success ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

        <?php if ($success): ?>
            <p style="margin-top:20px;">
                <strong>Next steps:</strong><br>
                1. You can now safely use the new features.<br>
                2. <strong>Delete this file</strong> for security (run_migration_residency.php).<br>
                3. Visit <a href="admin/verify_residents.php">/admin/verify_residents.php</a> to start reviewing users.
            </p>
        <?php else: ?>
            <p style="margin-top:20px; color:#991b1b;">
                Please run the SQL manually from <code>add_residency_verification.sql</code> if the automatic method fails.
            </p>
        <?php endif; ?>

        <p style="margin-top:30px; font-size:13px; color:#666;">
            You may delete this file after running it once.
        </p>
    </div>
</body>
</html>
