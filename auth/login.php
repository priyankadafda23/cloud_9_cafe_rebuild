<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../config/db_config.php';

// Login processing
if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // First check if it's an admin login (cafe_admins table)
    $admin_query = "SELECT * FROM cafe_admins WHERE email = '$email' AND password = '$password'";
    $admin_result = mysqli_query($con, $admin_query);
    
    if (mysqli_num_rows($admin_result) > 0) {
        // Admin login successful
        $admin = mysqli_fetch_assoc($admin_result);
        $_SESSION['cafe_admin_id'] = $admin['id'];
        $_SESSION['cafe_admin_name'] = $admin['fullname'];
        $_SESSION['cafe_admin_role'] = $admin['role'];
        header("Location: ../admin/dashboard.php");
        exit();
    }
    
    // If not admin, check regular user (cafe_users table)
    $user_query = "SELECT * FROM cafe_users WHERE email = '$email' AND password = '$password'";
    $user_result = mysqli_query($con, $user_query);
    
    if (mysqli_num_rows($user_result) > 0) {
        $user = mysqli_fetch_assoc($user_result);
        // Set session variables with cafe_ prefix
        $_SESSION['cafe_user_id'] = $user['id'];
        $_SESSION['cafe_user_name'] = $user['fullname'];
        header("Location: ../user/dashboard.php");
        exit();
    } else {
        $login_error = "Invalid email or password";
    }
}

$title = "Login - Cloud 9 Cafe";
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center align-items-center min-vh-75">
        <div class="col-lg-5 col-md-6">
            <!-- Login Card -->
            <div class="card border-0 shadow-lg animate-fade-in-up">
                <div class="card-body p-4 p-md-5">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: var(--gradient-primary);">
                                <i class="fas fa-mug-hot fa-2x text-white"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold text-primary mb-2">Welcome Back!</h2>
                        <p class="text-muted mb-0">Sign in to your Cloud 9 account</p>
                    </div>
                    
                    <!-- Error Message -->
                    <?php if (isset($login_error)): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $login_error; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form action="login.php" method="POST" id="loginForm">
                        <div class="mb-4">
                            <label for="email" class="form-label fw-medium">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                                <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" placeholder="Enter your password" required>
                                <button type="button" class="btn btn-outline-secondary border-start-0 toggle-password" data-target="#password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label text-muted small" for="remember">
                                    Remember me
                                </label>
                            </div>
                            <a href="forgot_password.php" class="text-primary small fw-medium">Forgot Password?</a>
                        </div>

                        <button type="submit" name="login_btn" class="btn btn-primary w-100 btn-lg mb-4">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>

                        <!-- Social Login -->
                        <div class="text-center">
                            <p class="text-muted small mb-3">Or continue with</p>
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
                                Don't have an account? 
                                <a href="register.php" class="text-primary fw-bold">Sign Up</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Back to Home -->
            <div class="text-center mt-4">
                <a href="../pages/index.php" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>Back to Home
                </a>
            </div>
        </div>
        
        <!-- Side Image (Desktop) -->
        <div class="col-lg-7 d-none d-lg-block">
            <div class="position-relative">
                <img src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800" alt="Coffee Shop" class="img-fluid rounded-4 shadow-lg" style="height: 600px; object-fit: cover; width: 100%;">
                <div class="position-absolute bottom-0 start-0 end-0 p-4 m-4 rounded-4" style="background: rgba(107, 79, 75, 0.95);">
                    <h4 class="fw-bold text-white mb-2">
                        <i class="fas fa-quote-left me-2 text-accent"></i>
                        Life begins after coffee
                    </h4>
                    <p class="text-white-50 mb-0">Join thousands of coffee lovers who have made Cloud 9 their daily destination.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .min-vh-75 {
        min-height: 75vh;
    }
    
    .text-white-50 {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    
    /* Custom checkbox styling */
    .form-check-input:checked {
        background-color: var(--cafe-primary);
        border-color: var(--cafe-primary);
    }
    
    /* Button icon sizing */
    .btn-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
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
</script>

<?php
$content = ob_get_clean();
include '../includes/layout.php';
?>
