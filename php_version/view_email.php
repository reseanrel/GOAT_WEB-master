<?php
$emailsDir = __DIR__ . '/emails';
$filename = $_GET['file'] ?? '';

if (empty($filename) || !preg_match('/^[a-zA-Z0-9_]+\.html$/', $filename)) {
    die('Invalid file name');
}

$emailFile = $emailsDir . '/' . $filename;

if (!file_exists($emailFile)) {
    die('Email file not found');
}

$content = file_get_contents($emailFile);
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2>Email Preview: <?php echo htmlspecialchars($filename); ?></h2>
        <div class="mb-3">
            <a href="view_emails.php" class="btn btn-secondary">← Back to Email List</a>
            <a href="index.php" class="btn btn-primary">Home</a>
        </div>

        <div class="card">
            <div class="card-body">
                <?php echo $content; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>