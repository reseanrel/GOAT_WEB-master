<?php
// Simple page to view saved emails for testing
$emailsDir = __DIR__ . '/emails';

if (!is_dir($emailsDir)) {
    echo "Emails directory not found.";
    exit;
}

$emailFiles = glob($emailsDir . '/*.html');
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2>Email Testing - Saved Emails</h2>
        <p class="text-muted">This page shows emails that were saved instead of sent (for development testing).</p>

        <?php if (empty($emailFiles)): ?>
            <div class="alert alert-info">
                No emails have been saved yet.
            </div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($emailFiles as $emailFile): ?>
                    <a href="view_email.php?file=<?php echo basename($emailFile); ?>"
                       class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><?php echo basename($emailFile); ?></h5>
                            <small><?php echo date('M d, Y H:i:s', filemtime($emailFile)); ?></small>
                        </div>
                        <p class="mb-1">Click to view the email content</p>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="test_email.php" class="btn btn-primary">Send Test Email</a>
            <a href="index.php" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>