<?php
session_start();
require_once 'includes/auth.php';
requireLogin();
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2>Lost Pets</h2>
        <p class="text-muted">Help find lost pets or report your own lost pet.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Coming Soon:</strong> Lost pets listing and search functionality.
            <br>
            For now, you can report lost pets from your <a href="user/dashboard.php">pet dashboard</a>.
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>