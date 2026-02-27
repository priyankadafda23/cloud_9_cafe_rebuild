<?php
require_once '../config/db_config.php';

// Check if user is logged in
if (!$auth->isUserLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$success = '';
$error = '';

// Get user data
$user = $db->selectOne('cafe_users', ['id' => $user_id]);

// Get user stats
$orders = $db->select('cafe_orders', ['user_id' => $user_id]);
$orders_count = count($orders);
$wishlist_count = 0; // Update when wishlist table exists
$addresses = $db->select('user_addresses', ['user_id' => $user_id]);
$addresses_count = count($addresses);

// Helper function to create safe folder name
function createSafeFolderName($name) {
    // Replace spaces with underscores, remove special characters
    $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
    // Remove multiple underscores
    $safe = preg_replace('/_+/', '_', $safe);
    // Trim underscores from ends
    $safe = trim($safe, '_');
    return $safe;
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $gender = $_POST['gender'] ?? '';
    
    // Check if email already exists for another user
    $existingUser = $db->selectOne('cafe_users', ['email' => $email]);
    if ($existingUser && $existingUser['id'] != $user_id) {
        $error = 'Email already exists!';
    } else {
        // Handle profile picture upload
        $profile_picture = $user['profile_picture'] ?? null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            // Create user-specific folder: assets/uploads/Profile/{client_name}/
            $safe_name = createSafeFolderName($fullname);
            $user_folder = $safe_name . '_' . $user_id; // Add user_id to make it unique
            $upload_dir = '../assets/uploads/Profile/' . $user_folder . '/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Get file extension
            $file_info = pathinfo($_FILES['profile_picture']['name']);
            $extension = strtolower($file_info['extension']);
            
            // Validate allowed extensions
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($extension, $allowed)) {
                // Delete old profile picture if exists
                if ($profile_picture && file_exists('../' . $profile_picture)) {
                    unlink('../' . $profile_picture);
                }
                
                // Save with standardized name: profile_picture.{extension}
                $filename = 'profile_picture.' . $extension;
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_path)) {
                    // Store relative path in database
                    $profile_picture = 'assets/uploads/Profile/' . $user_folder . '/' . $filename;
                }
            } else {
                $error = 'Invalid file type. Only JPG, PNG, and GIF allowed.';
            }
        }
        
        $updateData = [
            'fullname' => $fullname,
            'email' => $email,
            'mobile' => $mobile,
            'gender' => $gender,
            'profile_picture' => $profile_picture
        ];
        
        $updated = $db->update('cafe_users', $updateData, ['id' => $user_id]);
        
        if ($updated) {
            $success = 'Profile updated successfully!';
            // Refresh user data
            $user = $db->selectOne('cafe_users', ['id' => $user_id]);
        } else {
            $error = 'Failed to update profile. Please try again.';
        }
    }
}

$title = "My Profile - Cloud 9 Cafe";
$active_sidebar = 'profile';
ob_start();
?>

<style>
    .profile-header-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.0));
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        border-radius: 20px;
        overflow: hidden;
    }

    .profile-cover {
        height: 200px;
        background: var(--gradient-primary);
        position: relative;
    }

    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid rgba(255, 255, 255, 0.8);
        position: absolute;
        bottom: -75px;
        left: 50px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-stats {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 1.5rem;
        backdrop-filter: blur(5px);
    }

    .stat-item {
        text-align: center;
        padding: 0.5rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
    }

    .stat-label {
        font-size: 0.85rem;
        color: rgba(255,255,255,0.8);
    }

    .info-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        transition: transform 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-5px);
    }

    .edit-btn {
        background: rgba(255,255,255,0.9);
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        color: var(--cafe-primary);
        border: none;
        transition: all 0.3s ease;
    }

    .edit-btn:hover {
        background: white;
        transform: scale(1.05);
    }

    .edit-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(107, 79, 75, 0.8);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: absolute;
        bottom: 10px;
        right: 10px;
        border: 2px solid white;
    }

    .edit-icon:hover {
        background: var(--cafe-primary);
    }

    .profile-info-row {
        padding: 1rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .profile-info-row:last-child {
        border-bottom: none;
    }
</style>

<!-- Success/Error Messages -->
<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="profile-header-card mb-4">
    <div class="profile-cover">
        <div class="profile-avatar">
            <img src="<?php echo $user['profile_picture'] ? '../' . $user['profile_picture'] : '../assets/uploads/Profile/default.png'; ?>" 
                 alt="Profile" id="profileImage">
        </div>
    </div>
    <div class="p-4 pt-5">
        <div class="row align-items-end">
            <div class="col-md-6">
                <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($user['fullname']); ?></h3>
                <p class="text-muted mb-0"><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="profile-stats d-inline-flex gap-4">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $orders_count; ?></div>
                        <div class="stat-label">Orders</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $wishlist_count; ?></div>
                        <div class="stat-label">Wishlist</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $addresses_count; ?></div>
                        <div class="stat-label">Addresses</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-user-circle me-2 text-primary"></i>Personal Information
                </h5>
                <button class="edit-btn" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fas fa-pen me-2"></i>Edit
                </button>
            </div>

            <div class="profile-info-row">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Full Name</span>
                    <span class="fw-semibold"><?php echo htmlspecialchars($user['fullname']); ?></span>
                </div>
            </div>
            <div class="profile-info-row">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Email</span>
                    <span class="fw-semibold"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
            </div>
            <div class="profile-info-row">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Phone</span>
                    <span class="fw-semibold"><?php echo $user['mobile'] ? htmlspecialchars($user['mobile']) : 'Not set'; ?></span>
                </div>
            </div>
            <div class="profile-info-row">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Gender</span>
                    <span class="fw-semibold text-capitalize"><?php echo $user['gender'] ? htmlspecialchars($user['gender']) : 'Not set'; ?></span>
                </div>
            </div>
            <div class="profile-info-row">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Member Since</span>
                    <span class="fw-semibold"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-4">
                <i class="fas fa-shield-alt me-2 text-primary"></i>Account Security
            </h5>

            <div class="d-flex align-items-center p-3 mb-3 rounded-3 bg-light">
                <div class="flex-shrink-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(107, 79, 75, 0.1);">
                        <i class="fas fa-lock text-primary"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fw-bold mb-0">Password</h6>
                    <p class="text-muted small mb-0">Change your account password</p>
                </div>
                <a href="change_password.php" class="btn btn-outline-primary btn-sm rounded-pill">Change</a>
            </div>

            <div class="d-flex align-items-center p-3 mb-3 rounded-3 bg-light">
                <div class="flex-shrink-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(107, 79, 75, 0.1);">
                        <i class="fas fa-map-marker-alt text-primary"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fw-bold mb-0">Addresses</h6>
                    <p class="text-muted small mb-0">Manage delivery addresses</p>
                </div>
                <a href="addresses.php" class="btn btn-outline-primary btn-sm rounded-pill">Manage</a>
            </div>

            <div class="d-flex align-items-center p-3 rounded-3 bg-light">
                <div class="flex-shrink-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(107, 79, 75, 0.1);">
                        <i class="fas fa-crown text-warning"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fw-bold mb-0">Reward Points</h6>
                    <p class="text-muted small mb-0">You have <strong><?php echo $user['reward_points']; ?></strong> points</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <img src="<?php echo $user['profile_picture'] ? '../' . $user['profile_picture'] : '../assets/uploads/Profile/default.png'; ?>" 
                                 alt="Profile" class="rounded-circle" style="width: 120px; height: 120px; border: 4px solid #f8f9fa; object-fit: cover;" id="modalProfilePreview">
                            <label for="profile_picture" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0" style="width: 36px; height: 36px; cursor: pointer;">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" name="profile_picture" id="profile_picture" hidden accept="image/*" onchange="previewImage(this)">
                        </div>
                        <p class="text-muted small mt-2">Click camera to change photo</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="tel" name="mobile" class="form-control" value="<?php echo htmlspecialchars($user['mobile'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select Gender</option>
                            <option value="male" <?php echo $user['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo $user['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo $user['gender'] == 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('modalProfilePreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php
$dashboard_content = ob_get_clean();
include '../includes/dashboard_layout.php';
?>
