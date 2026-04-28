<?php
session_start();
require_once 'includes/auth.php';
requireLogin();
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2>Pet Adoption</h2>
        <p class="text-muted">Find loving homes for pets or adopt pets in need.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Coming Soon:</strong> Pet adoption listings and application system.
            <br>
            Check back later for available pets!
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>