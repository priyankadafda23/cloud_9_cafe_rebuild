<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once(__DIR__ . "/../config/db_config.php");

// Fetch current user data if logged in
$current_user_data = null;
if (isset($_SESSION['cafe_user_id'])) {
    $uid = $_SESSION['cafe_user_id'];
    $user_result = mysqli_query($con, "SELECT * FROM cafe_users WHERE id = $uid");
    $current_user_data = mysqli_fetch_assoc($user_result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Cloud 9 Cafe'; ?></title>
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Theme CSS -->
    <link rel="stylesheet" href="/cloud_9_cafe_rebuild/assets/css/theme.css">
    
    <style>
        /* Additional layout-specific styles */
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1;
        }
        
        /* Navbar Avatar */
        .nav-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--cafe-accent);
        }
        
        /* Cart Badge */
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--cafe-accent);
            color: var(--cafe-primary-dark);
            font-size: 0.65rem;
            font-weight: 700;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Mobile menu button */
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        /* Active link indicator */
        .nav-link {
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--cafe-primary);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 60%;
        }
        
        /* Dropdown styling */
        .dropdown-menu {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: 0.5rem;
        }
        
        .dropdown-item {
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            font-weight: 500;
        }
        
        .dropdown-item:hover {
            background: rgba(107, 79, 75, 0.05);
            color: var(--cafe-primary);
        }
        
        /* Footer styling */
        .main-footer {
            background: var(--bg-dark);
            color: rgba(255, 255, 255, 0.7);
            padding: 4rem 0 2rem;
        }
        
        .main-footer a {
            color: rgba(255, 255, 255, 0.7);
            transition: color 0.3s ease;
        }
        
        .main-footer a:hover {
            color: var(--cafe-accent);
        }
        
        .footer-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: white !important;
        }
        
        .footer-title {
            color: white;
            font-weight: 600;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }
        
        .social-links {
            display: flex;
            gap: 0.75rem;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background: var(--cafe-accent);
            color: var(--cafe-primary-dark);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/cloud_9_cafe_rebuild/pages/index.php">
                <i class="fas fa-mug-hot"></i>
                Cloud 9 Cafe
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <i class="fas fa-bars text-primary"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="/cloud_9_cafe_rebuild/pages/index.php">
                            <i class="fas fa-home me-1 d-lg-none"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'menu') !== false ? 'active' : ''; ?>" href="/cloud_9_cafe_rebuild/pages/menu/menu.php">
                            <i class="fas fa-coffee me-1 d-lg-none"></i>Menu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="/cloud_9_cafe_rebuild/pages/about.php">
                            <i class="fas fa-info-circle me-1 d-lg-none"></i>About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="/cloud_9_cafe_rebuild/pages/contact.php">
                            <i class="fas fa-envelope me-1 d-lg-none"></i>Contact
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav align-items-center">
                    <?php if (isset($_SESSION['cafe_user_id'])): ?>
                        <!-- Cart Icon -->
                        <li class="nav-item me-3">
                            <a class="nav-link position-relative" href="/cloud_9_cafe_rebuild/user/cart.php">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-badge">0</span>
                            </a>
                        </li>
                        
                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                                <img src="<?php echo ($current_user_data && $current_user_data['profile_picture']) ? '/' . $current_user_data['profile_picture'] : '/cloud_9_cafe_rebuild/assets/uploads/Profile/default.png'; ?>" 
                                     alt="Profile" class="nav-avatar" onerror="this.src='/cloud_9_cafe_rebuild/assets/uploads/Profile/default.png'">
                                <span class="d-none d-lg-inline fw-medium"><?php echo htmlspecialchars($current_user_data['fullname'] ?? 'User'); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/cloud_9_cafe_rebuild/user/dashboard.php"><i class="fas fa-th-large me-2 text-primary"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="/cloud_9_cafe_rebuild/user/profile.php"><i class="fas fa-user me-2 text-primary"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="/cloud_9_cafe_rebuild/user/orders.php"><i class="fas fa-shopping-bag me-2 text-primary"></i>My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/cloud_9_cafe_rebuild/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-outline-primary me-2" href="/cloud_9_cafe_rebuild/auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="/cloud_9_cafe_rebuild/auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Spacer for fixed navbar -->
    <div style="height: var(--navbar-height);"></div>

    <!-- Main Content -->
    <main>
        <?php echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <a href="/cloud_9_cafe_rebuild/pages/index.php" class="footer-brand text-decoration-none">
                        <i class="fas fa-mug-hot me-2"></i>Cloud 9 Cafe
                    </a>
                    <p class="mt-3 mb-4">Your perfect destination for premium coffee, delicious snacks, and delightful desserts. Experience the taste of perfection.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/cloud_9_cafe_rebuild/pages/index.php">Home</a></li>
                        <li class="mb-2"><a href="/cloud_9_cafe_rebuild/pages/menu/menu.php">Menu</a></li>
                        <li class="mb-2"><a href="/cloud_9_cafe_rebuild/pages/about.php">About Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">Support</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/cloud_9_cafe_rebuild/pages/contact.php">Contact</a></li>
                        <li class="mb-2"><a href="/cloud_9_cafe_rebuild/pages/faq.php">FAQ</a></li>
                        <li class="mb-2"><a href="/cloud_9_cafe_rebuild/pages/privacy_policy.php">Privacy Policy</a></li>
                        <li class="mb-2"><a href="/cloud_9_cafe_rebuild/pages/terms_of_service.php">Terms</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5 class="footer-title">Contact Us</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-accent"></i>123 Coffee Street, Cafe City</li>
                        <li class="mb-2"><i class="fas fa-phone me-2 text-accent"></i>+1 (555) 123-4567</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2 text-accent"></i>hello@cloud9cafe.com</li>
                        <li><i class="fas fa-clock me-2 text-accent"></i>Open Daily: 7AM - 10PM</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Cloud 9 Cafe. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">Made with <i class="fas fa-heart text-danger"></i> for coffee lovers</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme JS -->
    <script src="/cloud_9_cafe_rebuild/assets/js/theme.js"></script>
</body>
</html>
