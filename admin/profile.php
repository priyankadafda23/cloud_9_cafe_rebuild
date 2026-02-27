<?php
require_once '../config/db_config.php';

// Check if admin is logged in
if (!$auth->isAdminLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}
$admin_id = $auth->getAdminId();
$admin_name = $auth->getUserName() ?? 'Admin';
$admin_role = $auth->getAdminRole();
$success = '';
$error = '';

// Get admin info
$admin = $db->selectOne('cafe_admins', ['id' => $admin_id]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    
    // Update profile
    $updateData = [
        'fullname' => $fullname,
        'email' => $email,
        'mobile' => $mobile
    ];
    
    // Update password if provided
    if (!empty($_POST['new_password'])) {
        $updateData['password'] = $_POST['new_password'];
    }
    
    $db->update('cafe_admins', $updateData, ['id' => $admin_id]);
    
    // Session name will be updated by auth class on next request
    $success = 'Profile updated successfully!';
    
    // Refresh admin data
    $admin = $db->selectOne('cafe_admins', ['id' => $admin_id]);
}

$title = "My Profile - Cloud 9 Cafe";
$active_sidebar = 'profile';
ob_start();
?>

<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
    }

    .profile-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }

    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 2rem;
        text-align: center;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid white;
        object-fit: cover;
        margin-bottom: 1rem;
    }

    .role-badge {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .info-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 1rem;
    }

    .info-item i {
        width: 40px;
        height: 40px;
        background: var(--primary-gradient);
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }

    .info-item label {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .info-item span {
        font-weight: 600;
        color: #333;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <h3 class="fw-bold mb-2"><i class="fas fa-user-cog me-2"></i>My Profile</h3>
    <p class="mb-0 opacity-75">Manage your admin account settings</p>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Profile Info Card -->
    <div class="col-lg-4">
        <div class="card profile-card shadow-sm">
            <div class="profile-header">
                <img src="../assets/images/profile_pictures/default.png" alt="Admin" class="profile-avatar">
                <h4 class="fw-bold mb-2"><?php echo htmlspecialchars($admin['fullname']); ?></h4>
                <span class="role-badge"><?php echo ucfirst(str_replace('_', ' ', $admin['role'])); ?></span>
            </div>
            <div class="card-body p-4">
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <label>Email</label>
                        <div><?php echo htmlspecialchars($admin['email']); ?></div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <label>Phone</label>
                        <div><?php echo $admin['mobile'] ? htmlspecialchars($admin['mobile']) : 'Not set'; ?></div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <div>
                        <label>Joined</label>
                        <div><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <label>Last Login</label>
                        <div><?php echo $admin['last_login'] ? date('M d, Y H:i', strtotime($admin['last_login'])) : 'Never'; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Form -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-edit me-2 text-primary"></i>Edit Profile</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                                <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($admin['fullname']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Mobile</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-phone text-muted"></i></span>
                                <input type="text" name="mobile" class="form-control" value="<?php echo htmlspecialchars($admin['mobile'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Role</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-shield-alt text-muted"></i></span>
                                <input type="text" class="form-control" value="<?php echo ucfirst(str_replace('_', ' ', $admin['role'])); ?>" disabled>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3">Change Password</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current">
                            </div>
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$dashboard_content = ob_get_clean();
include 'admin_layout.php';
?>
