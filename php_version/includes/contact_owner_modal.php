<?php
// Reusable Contact Owner Modal
// Include this file after the main content and before </body>
// Requires: current user info preferably loaded as $currentUser = getUserById($_SESSION['user_id']);
?>
<!-- Contact Owner Modal -->
<div class="modal-overlay" id="contactOwnerModal" style="display: none;">
    <div class="modal-content" style="max-width: 520px;">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-envelope"></i> Contact Pet Owner
            </h3>
        </div>
        
        <form id="contactOwnerForm">
            <div class="modal-body">
                <input type="hidden" id="contactPetId" name="pet_id">
                <input type="hidden" id="contactContext" name="context" value="general">

                <!-- Pet Info -->
                <div style="margin-bottom: var(--spacing-lg); padding: 12px; background: var(--color-bg-secondary); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    <div style="font-size: 13px; color: var(--color-text-secondary);">Pet</div>
                    <div id="contactPetName" style="font-weight: 800; color: var(--color-text); font-size: 18px;"></div>
                </div>

                <!-- Your Info -->
                <div style="margin-bottom: var(--spacing-lg);">
                    <label style="display:block; margin-bottom: 6px; font-weight:700; color: var(--color-text); font-size:13px;">
                        Your Information (will be sent to the owner)
                    </label>
                    <div style="display: grid; gap: 8px;">
                        <input type="text" id="contactSenderName" class="modal-control" 
                               value="<?php echo htmlspecialchars($_SESSION['full_name'] ?? ($_SESSION['user_name'] ?? '')); ?>" 
                               readonly style="background:#f8f9fa;">
                        <input type="email" id="contactSenderEmail" class="modal-control" 
                               value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" 
                               placeholder="Your email" required>
                        <input type="tel" id="contactSenderPhone" class="modal-control" 
                               value="<?php echo htmlspecialchars($_SESSION['contact_number'] ?? ''); ?>" 
                               placeholder="Your phone number (optional)">
                    </div>
                    <small style="color: var(--color-text-secondary); font-size:12px;">The owner will be able to reply directly to your email.</small>
                </div>

                <!-- Message -->
                <div>
                    <label style="display:block; margin-bottom: 6px; font-weight:700; color: var(--color-text); font-size:13px;">
                        Your Message <span style="color:#e11d48;">*</span>
                    </label>
                    <textarea id="contactMessage" name="message" rows="5" class="modal-control modal-textarea" required
                              placeholder="Hi, I'm interested in your pet. Can you tell me more about their personality, vaccination status, and any special needs?"></textarea>
                    <small style="color: var(--color-text-secondary);">Minimum 10 characters</small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeContactModal()">Cancel</button>
                <button type="submit" id="contactSendBtn"
                        style="background: #1a73e8; color: white; border: none; padding: 10px 20px; border-radius: var(--radius-md); cursor: pointer; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentContactPetId = null;

function openContactModal(petId, petName, context = 'general') {
    currentContactPetId = petId;
    
    // Set values
    document.getElementById('contactPetId').value = petId;
    document.getElementById('contactContext').value = context;
    document.getElementById('contactPetName').textContent = petName;
    document.getElementById('contactMessage').value = '';

    // Show modal
    const modal = document.getElementById('contactOwnerModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Focus message
    setTimeout(() => {
        document.getElementById('contactMessage').focus();
    }, 300);
}

function closeContactModal() {
    const modal = document.getElementById('contactOwnerModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('contactOwnerForm').reset();
}

document.getElementById('contactOwnerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const sendBtn = document.getElementById('contactSendBtn');
    const originalText = sendBtn.innerHTML;
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    const formData = new FormData();
    formData.append('pet_id', document.getElementById('contactPetId').value);
    formData.append('context', document.getElementById('contactContext').value);
    formData.append('message', document.getElementById('contactMessage').value.trim());

    try {
        const response = await fetch('user/send_owner_contact.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message || 'Message sent successfully!');
            closeContactModal();
        } else {
            alert(result.message || 'Failed to send message.');
        }
    } catch (err) {
        console.error(err);
        alert('Network error. Please try again.');
    } finally {
        sendBtn.disabled = false;
        sendBtn.innerHTML = originalText;
    }
});

// Close on outside click
document.getElementById('contactOwnerModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeContactModal();
    }
});

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('contactOwnerModal');
        if (modal && modal.style.display === 'flex') {
            closeContactModal();
        }
    }
});
</script>
