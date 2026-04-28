<?php
session_start();
require_once '../includes/auth.php';
requireLogin();

if (isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

$userPets = getUserPets($_SESSION['user_id']);
?>

<?php include '../includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Pets</h2>
            <a href="register_pet.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Register New Pet
            </a>
        </div>
    </div>
</div>

<?php if (empty($userPets)): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-paw fa-4x text-muted mb-3"></i>
                    <h4>No pets registered yet</h4>
                    <p class="text-muted">Start by registering your first pet!</p>
                    <a href="register_pet.php" class="btn btn-primary">Register Pet</a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($userPets as $pet): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if ($pet['photo_url']): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($pet['photo_url']); ?>"
                             class="card-img-top" alt="Pet Photo"
                             style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                             style="height: 200px;">
                            <i class="fas fa-paw fa-4x text-muted"></i>
                        </div>
                    <?php endif; ?>

                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($pet['name']); ?></h5>
                        <p class="card-text">
                            <strong>Category:</strong> <?php echo htmlspecialchars($pet['category'] ?? 'Not specified'); ?><br>
                            <strong>Type:</strong> <?php echo htmlspecialchars($pet['pet_type'] ?? 'Not specified'); ?><br>
                            <strong>Age:</strong> <?php echo $pet['age'] ? $pet['age'] . ' years' : 'Not specified'; ?><br>
                            <strong>Color:</strong> <?php echo htmlspecialchars($pet['color'] ?? 'Not specified'); ?><br>
                            <strong>Gender:</strong> <?php echo htmlspecialchars($pet['gender'] ?? 'Not specified'); ?>
                        </p>
                    </div>

                    <div class="card-footer">
                        <div class="btn-group w-100">
                            <a href="pet_details.php?id=<?php echo $pet['id']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="medical_records.php?pet_id=<?php echo $pet['id']; ?>" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-notes-medical"></i> Records
                            </a>
                        </div>

                        <div class="btn-group w-100 mt-2">
                            <?php if ($pet['lost']): ?>
                                <button class="btn btn-warning btn-sm" onclick="markFound(<?php echo $pet['id']; ?>)">
                                    <i class="fas fa-check"></i> Mark Found
                                </button>
                            <?php else: ?>
                                <button class="btn btn-outline-warning btn-sm" onclick="reportLost(<?php echo $pet['id']; ?>)">
                                    <i class="fas fa-exclamation-triangle"></i> Report Lost
                                </button>
                            <?php endif; ?>

                            <?php if ($pet['available_for_adoption']): ?>
                                <span class="badge bg-success">For Adoption</span>
                            <?php endif; ?>

                            <?php if ($pet['deceased']): ?>
                                <span class="badge bg-secondary">Deceased</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Lost Pet Modal -->
<div class="modal fade" id="lostPetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report Pet as Lost</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="lostPetForm">
                <div class="modal-body">
                    <input type="hidden" id="lostPetId" name="pet_id">
                    <div class="mb-3">
                        <label for="lostComment" class="form-label">Details about how your pet was lost</label>
                        <textarea class="form-control" id="lostComment" name="comment" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Report as Lost</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function reportLost(petId) {
    document.getElementById('lostPetId').value = petId;
    new bootstrap.Modal(document.getElementById('lostPetModal')).show();
}

function markFound(petId) {
    if (confirm('Are you sure you want to mark this pet as found?')) {
        fetch('mark_found.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                pet_id: petId,
                comment: 'Pet has been found and returned home.'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

document.getElementById('lostPetForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const petId = formData.get('pet_id');
    const comment = formData.get('comment');

    fetch('report_lost.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            pet_id: petId,
            comment: comment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('lostPetModal')).hide();
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
</script>

<?php include '../includes/footer.php'; ?>