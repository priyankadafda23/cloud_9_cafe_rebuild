<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db_config.php';

// Check if user is logged in
if (!isset($_SESSION['cafe_user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['cafe_user_id'];
$success = '';
$error = '';

// Get user data
$user_query = mysqli_query($con, "SELECT * FROM cafe_users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_query);

// Helper function to create safe folder name
function createSafeFolderName($name) {
    $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
    $safe = preg_replace('/_+/', '_', $safe);
    $safe = trim($safe, '_');
    return $safe;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $mobile = mysqli_real_escape_string($con, $_POST['mobile']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    
    // Check if email already exists for another user
    $email_check = mysqli_query($con, "SELECT id FROM cafe_users WHERE email = '$email' AND id != $user_id");
    if (mysqli_num_rows($email_check) > 0) {
        $error = 'Email already exists!';
    } else {
        // Handle profile picture upload
        $profile_picture = $user['profile_picture'];
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            // Create user-specific folder: assets/uploads/Profile/{client_name}_{user_id}/
            $safe_name = createSafeFolderName($fullname);
            $user_folder = $safe_name . '_' . $user_id;
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
                if ($user['profile_picture'] && file_exists('../' . $user['profile_picture'])) {
                    unlink('../' . $user['profile_picture']);
                }
                
                // Save with standardized name: profile_picture.{extension}
                $filename = 'profile_picture.' . $extension;
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_path)) {
                    $profile_picture = 'assets/uploads/Profile/' . $user_folder . '/' . $filename;
                }
            } else {
                $error = 'Invalid file type. Only JPG, PNG, and GIF allowed.';
            }
        }
        
        $update_query = "UPDATE cafe_users SET 
                        fullname = '$fullname', 
                        email = '$email', 
                        mobile = '$mobile', 
                        gender = '$gender',
                        address = '$address',
                        profile_picture = '$profile_picture'
                        WHERE id = $user_id";
        
        if (mysqli_query($con, $update_query)) {
            $_SESSION['cafe_user_name'] = $fullname;
            $success = 'Profile updated successfully!';
            // Refresh user data
            $user_query = mysqli_query($con, "SELECT * FROM cafe_users WHERE id = $user_id");
            $user = mysqli_fetch_assoc($user_query);
        } else {
            $error = 'Failed to update profile. Please try again.';
        }
    }
}

$title = "Edit Profile - Cloud 9 Cafe";
$active_sidebar = 'profile';
ob_start();
?>

<style>
    .form-control:focus {
        border-color: var(--cafe-primary);
        box-shadow: 0 0 0 0.25rem rgba(107, 79, 75, 0.25);
    }

    .profile-image-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="card border-0 shadow-lg mb-4">
    <div class="card-body p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h2 class="fw-bold mb-0 text-primary">Edit Profile</h2>
            <a href="profile.php" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to Profile
            </a>
        </div>

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

        <form method="POST" enctype="multipart/form-data">
            <!-- Profile Image -->
            <div class="text-center mb-5">
                <div class="position-relative d-inline-block">
                    <img src="<?php echo $user['profile_picture'] ? '../' . $user['profile_picture'] : '../assets/uploads/Profile/default.png'; ?>" 
                         alt="Profile" class="profile-image-preview" id="profilePreview">
                    <label for="profileImageInput"
                        class="btn btn-primary btn-sm position-absolute bottom-0 end-0 rounded-circle"
                        style="width: 40px; height: 40px; cursor: pointer;">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" name="profile_picture" id="profileImageInput" hidden accept="image/*" onchange="previewImage(this)">
                </div>
                <p class="text-muted small mt-2">Click the camera icon to change photo</p>
            </div>

            <div class="row">
                <div class="col-md-12 mb-4">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="fullname" class="form-control form-control-lg bg-light border-0" 
                           value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" class="form-control form-control-lg bg-light border-0"
                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Phone Number</label>
                <input type="tel" name="mobile" class="form-control form-control-lg bg-light border-0" 
                       value="<?php echo htmlspecialchars($user['mobile'] ?? ''); ?>">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Gender</label>
                <select name="gender" class="form-select form-select-lg bg-light border-0">
                    <option value="">Select Gender</option>
                    <option value="male" <?php echo $user['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo $user['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                    <option value="other" <?php echo $user['gender'] == 'other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Default Address</label>
                <textarea name="address" class="form-control p-3 bg-light border-0" rows="3"
                    placeholder="Enter your address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
                <a href="profile.php" class="btn btn-outline-secondary btn-lg px-5">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php
$dashboard_content = ob_get_clean();
include '../includes/dashboard_layout.php';
?>
