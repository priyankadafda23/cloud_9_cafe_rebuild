<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../config/db_config.php';
$title = "Register - Cloud 9 Cafe";

// Registration processing - KEEP EXISTING LOGIC
if (isset($_POST['reg_btn'])) {
    $fname = $_POST['firstName'];
    $lname = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $profile_picture = $_FILES['profile_picture']['name'];
    $tmp_name = $_FILES['profile_picture']['tmp_name'];
    $upload_dir = "uploads/";
    $address = "Rajkot";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir);
    }
    $fullname = $fname . " " . $lname;
    $token = uniqid();
    $insert_query = "INSERT INTO `cafe_users`(`fullname`, `email`, `password`, `gender`, `mobile`, `profile_picture`, `address`,`token`) values ('$fullname','$email','$password','$gender',$phone,'$profile_picture','$address','$token')";

    if (mysqli_query($con, $insert_query)) {
        move_uploaded_file($tmp_name, $upload_dir . $profile_picture);
        $_SESSION['cafe_user_id'] = mysqli_insert_id($con);
        $_SESSION['cafe_user_name'] = $fullname;
        echo "<script>alert('Registration successful'); window.location.href='../user/dashboard.php';</script>";
    } else {
        echo "<script>alert('Error in registration');</script>";
    }
}

ob_start();
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <!-- Register Card -->
            <div class="card border-0 shadow-lg animate-fade-in-up">
                <div class="card-body p-4 p-md-5">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: var(--gradient-primary);">
                                <i class="fas fa-user-plus fa-2x text-white"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold text-primary mb-2">Create Account</h2>
                        <p class="text-muted mb-0">Join Cloud 9 Cafe and start your coffee journey</p>
                    </div>

                    <!-- Registration Form -->
                    <form action="register.php" method="post" id="regform" enctype="multipart/form-data">
                        <div class="row g-3">
                            <!-- First Name -->
                            <div class="col-md-6">
                                <label for="firstName" class="form-label fw-medium">First Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="firstName" name="firstName" placeholder="John" required>
                                </div>
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6">
                                <label for="lastName" class="form-label fw-medium">Last Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="lastName" name="lastName" placeholder="Doe" required>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-medium">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" placeholder="john@example.com" required>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-medium">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-phone text-muted"></i>
                                    </span>
                                    <input type="tel" class="form-control border-start-0 ps-0" id="phone" name="phone" placeholder="1234567890" required>
                                </div>
                            </div>

                            <!-- Gender -->
                            <div class="col-md-6">
                                <label for="gender" class="form-label fw-medium">Gender</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-venus-mars text-muted"></i>
                                    </span>
                                    <select class="form-select border-start-0 ps-0" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Profile Picture -->
                            <div class="col-md-6">
                                <label for="profile_picture" class="form-label fw-medium">Profile Picture</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-camera text-muted"></i>
                                    </span>
                                    <input type="file" class="form-control border-start-0 ps-0" id="profile_picture" name="profile_picture" accept="image/*">
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-medium">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" placeholder="Create password" required>
                                    <button type="button" class="btn btn-outline-secondary border-start-0 toggle-password" data-target="#password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <label for="confirmPassword" class="form-label fw-medium">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 ps-0" id="confirmPassword" name="confirmPassword" placeholder="Confirm password" required>
                                </div>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="mt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label text-muted small" for="terms">
                                    I agree to the <a href="terms_of_service.php" class="text-primary">Terms of Service</a> and <a href="privacy_policy.php" class="text-primary">Privacy Policy</a>
                                </label>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" name="reg_btn" class="btn btn-primary w-100 btn-lg mt-4">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>

                        <!-- Social Register -->
                        <div class="text-center mt-4">
                            <p class="text-muted small mb-3">Or register with</p>
                            <div class="d-flex gap-2 justify-content-center mb-4">
                                <button type="button" class="btn btn-outline-secondary btn-icon" style="width: 44px; height: 44px;">
                                    <i class="fab fa-google"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-icon" style="width: 44px; height: 44px;">
                                    <i class="fab fa-facebook-f"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-icon" style="width: 44px; height: 44px;">
                                    <i class="fab fa-apple"></i>
                                </button>
                            </div>
                            
                            <p class="text-muted mb-0">
                                Already have an account? 
                                <a href="login.php" class="text-primary fw-bold">Sign In</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Back to Home -->
            <div class="text-center mt-4 mb-4">
                <a href="../pages/index.php" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    
    .form-check-input:checked {
        background-color: var(--cafe-primary);
        border-color: var(--cafe-primary);
    }
</style>

<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = document.querySelector(this.dataset.target);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Password match validation
    document.getElementById('regform').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirmPassword').value;
        
        if (password !== confirm) {
            e.preventDefault();
            alert('Passwords do not match!');
        }
    });
</script>

<?php
$content = ob_get_clean();
include '../includes/layout.php';
?>
