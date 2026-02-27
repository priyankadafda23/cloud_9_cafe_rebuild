<?php
require_once '../config/db_config.php';

// Check if user is logged in
if (!$auth->isUserLoggedIn()) {
    header("Location: login.php");
    exit();
}

$title = "Change Password - Cloud 9 Cafe";
$active_sidebar = 'password';
ob_start();
?>
<style>
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }

    .password-strength {
        height: 4px;
        border-radius: 3px;
        background: #e0e0e0;
        margin-top: 0.5rem;
        overflow: hidden;
    }

    .password-strength-bar {
        height: 100%;
        width: 0;
        transition: all 0.3s ease;
    }
</style>

<div class="card border-0 shadow-lg mb-4">
    <div class="card-body p-5">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h2 class="fw-bold mb-0" style="color: #667eea;">Change Password</h2>
            <a href="profile.php" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to Profile
            </a>
        </div>

        <form>
            <div class="mb-4">
                <label class="form-label fw-semibold">Current Password</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-key text-muted"></i></span>
                    <input type="password" class="form-control bg-light border-0" id="currentPassword"
                        placeholder="Enter current password">
                    <button class="btn btn-outline-secondary border-0" type="button"
                        onclick="togglePassword('currentPassword', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">New Password</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" class="form-control bg-light border-0" id="newPassword"
                        placeholder="Enter new password" oninput="checkPasswordStrength(this.value)">
                    <button class="btn btn-outline-secondary border-0" type="button"
                        onclick="togglePassword('newPassword', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
                <small class="text-muted">Password must be at least 8 characters long</small>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-0"><i
                            class="fas fa-lock text-muted"></i></span>
                    <input type="password" class="form-control bg-light border-0" id="confirmPassword"
                        placeholder="Confirm new password">
                    <button class="btn btn-outline-secondary border-0" type="button"
                        onclick="togglePassword('confirmPassword', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                    <strong>Password Tips:</strong>
                    <ul class="mb-0 ps-3 mt-1">
                        <li>Use at least 8 characters</li>
                        <li>Include uppercase and lowercase letters</li>
                        <li>Include at least one number</li>
                        <li>Include at least one special character (!@#$%^&*)</li>
                    </ul>
                </div>
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-gradient btn-lg px-5">
                    <i class="fas fa-save me-2"></i>Update Password
                </button>
                <button type="reset" class="btn btn-cancel btn-lg px-5">
                    <i class="fas fa-undo me-2"></i>Reset
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function checkPasswordStrength(password) {
        const bar = document.getElementById('strengthBar');
        let strength = 0;

        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;

        const colors = ['#dc3545', '#ffc107', '#17a2b8', '#28a745'];
        const widths = ['25%', '50%', '75%', '100%'];

        bar.style.width = widths[strength - 1] || '0';
        bar.style.backgroundColor = colors[strength - 1] || '#e0e0e0';
    }
</script>

<?php
$dashboard_content = ob_get_clean();
include '../includes/dashboard_layout.php';
?>
