<?php
/**
 * =============================================================================
 * CLOUD 9 CAFE - USER DASHBOARD LAYOUT FILE
 * =============================================================================
 * 
 * ROLE: This is the layout wrapper for USER DASHBOARD pages.
 *       It provides a sidebar navigation specific to user accounts, a header
 *       with search and notifications, and wraps user dashboard content.
 *       Requires user login - redirects to login if not authenticated.
 * 
 * USED BY: All pages in /user/ folder (dashboard.php, orders.php, cart.php, etc.)
 * 
 * FLOW: 1. Includes database config
 *       2. Checks if user is logged in (redirects if not)
 *       3. Fetches user profile data
 *       4. Outputs HTML with sidebar navigation and content area
 *       5. Injects page content via $dashboard_content variable
 */

// =============================================================================
// SECTION: Database & Authentication Setup
// DESCRIPTION: Includes database config and checks user authentication
//              If not logged in, redirects to login page
// =============================================================================
require_once __DIR__ . '/../config/db_config.php';

// Check if user is logged in using cookie-based auth
// FUNCTION: $auth->isUserLoggedIn() - Returns true if valid user auth cookie exists
if (!$auth->isUserLoggedIn()) {
    // Not logged in - redirect to login page
    header("Location: ../auth/login.php");
    exit();  // Stop script execution
}
// =============================================================================
// END SECTION: Database & Authentication Setup
// =============================================================================

// =============================================================================
// SECTION: User Data Fetching
// DESCRIPTION: Fetch current user's profile data from database
// =============================================================================
$user_id = $auth->getUserId();  // Get user ID from auth cookie

// FUNCTION: $db->selectOne() - Fetches single record matching conditions
// PARAMETERS: 'cafe_users' = table name, ['id' => $user_id] = WHERE conditions
$current_user = $db->selectOne('cafe_users', ['id' => $user_id]);

// Get current page filename for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Start output buffering to capture page content
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Dynamic page title - set by each page via $title variable -->
    <title><?php echo isset($title) ? $title : 'Dashboard - Cloud 9 Cafe'; ?></title>
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Theme CSS - Global theme variables -->
    <link rel="stylesheet" href="/cloud_9_cafe_rebuild/assets/css/theme.css">
    
    <!-- Dashboard Layout CSS - User dashboard specific styles -->
    <link rel="stylesheet" href="/cloud_9_cafe_rebuild/assets/css/layout/dashboard_layout.css">
</head>
<body>
    <!-- ========================================================================= -->
    <!-- SECTION: Dashboard Wrapper -->
    <!-- DESCRIPTION: Main container with flex layout for sidebar + content -->
    <!-- ========================================================================= -->
    <div class="dashboard-wrapper">
        
        <!-- ===================================================================== -->
        <!-- SECTION: Sidebar Navigation -->
        <!-- DESCRIPTION: Fixed left sidebar with user profile, navigation links -->
        <!-- ===================================================================== -->
        <aside class="dashboard-sidebar" id="sidebar">
            
            <!-- Sidebar Header - Logo -->
            <div class="sidebar-header">
                <!-- Logo - Click redirects to: ../pages/index.php (Homepage) -->
                <a href="../pages/index.php" class="brand-link">
                    <i class="fas fa-mug-hot text-accent"></i>
                    Cloud 9 Cafe
                </a>
            </div>
            
            <!-- User Profile Summary -->
            <div class="user-profile-summary">
                <!-- User Avatar - Shows default image if no profile picture -->
                <img src="<?php echo $current_user['profile_picture'] ? '/' . $current_user['profile_picture'] : '/cloud_9_cafe_rebuild/assets/uploads/Profile/default.png'; ?>" 
                     alt="User" class="user-avatar" 
                     onerror="this.src='/cloud_9_cafe_rebuild/assets/uploads/Profile/default.png'">
                <div class="user-info">
                    <h6 class="mb-0"><?php echo htmlspecialchars($current_user['fullname'] ?? 'User'); ?></h6>
                    <small>Member</small>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="sidebar-nav">
                <!-- Main Section Header -->
                <div class="nav-section">Main</div>
                
                <!-- Dashboard Link - Click redirects to: dashboard.php -->
                <!-- Active state: 'active' class added when on dashboard.php -->
                <a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-th-large"></i>
                    Dashboard
                </a>
                
                <!-- Orders Link - Click redirects to: orders.php -->
                <a href="orders.php" class="nav-link <?php echo $current_page == 'orders' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-bag"></i>
                    My Orders
                </a>
                
                <!-- Cart Link - Click redirects to: cart.php -->
                <a href="cart.php" class="nav-link <?php echo $current_page == 'cart' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i>
                    Cart
                </a>
                
                <!-- Wishlist/Favorites Link - Click redirects to: wishlist.php -->
                <a href="wishlist.php" class="nav-link <?php echo $current_page == 'wishlist' ? 'active' : ''; ?>">
                    <i class="fas fa-heart"></i>
                    Favorites
                </a>
                
                <!-- Account Section Header -->
                <div class="nav-section">Account</div>
                
                <!-- Profile Link - Click redirects to: profile.php -->
                <a href="profile.php" class="nav-link <?php echo $current_page == 'profile' ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i>
                    Profile
                </a>
                
                <!-- Addresses Link - Click redirects to: addresses.php -->
                <a href="addresses.php" class="nav-link <?php echo $current_page == 'addresses' ? 'active' : ''; ?>">
                    <i class="fas fa-map-marker-alt"></i>
                    Addresses
                </a>
                
                <!-- Change Password Link - Click redirects to: change_password.php -->
                <a href="change_password.php" class="nav-link <?php echo $current_page == 'change_password' ? 'active' : ''; ?>">
                    <i class="fas fa-lock"></i>
                    Change Password
                </a>
            </nav>
            
            <!-- Sidebar Footer - Logout Button -->
            <div class="sidebar-footer">
                <!-- Logout - Click redirects to: ../auth/logout.php -->
                <a href="../auth/logout.php" class="btn btn-outline-danger w-100">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </aside>
        <!-- ===================================================================== -->
        <!-- END SECTION: Sidebar Navigation -->
        <!-- ===================================================================== -->
        
        <!-- Mobile Overlay - Dark overlay behind sidebar on mobile devices -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- ===================================================================== -->
        <!-- SECTION: Main Content Area -->
        <!-- DESCRIPTION: Right side content area with header and page content -->
        <!-- ===================================================================== -->
        <main class="dashboard-main">
            
            <!-- Top Header Bar -->
            <header class="dashboard-header">
                <!-- Search Box -->
                <div class="header-search">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control" placeholder="Search...">
                </div>
                
                <!-- Header Action Buttons -->
                <div class="header-actions">
                    <!-- New Order Button - Click redirects to: ../pages/menu/menu.php -->
                    <a href="../pages/menu/menu.php" class="btn btn-primary btn-sm d-none d-md-inline-flex">
                        <i class="fas fa-plus me-2"></i>New Order
                    </a>
                    
                    <!-- Cart Button - Click redirects to: cart.php -->
                    <a href="cart.php" class="header-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge">0</span>
                    </a>
                    
                    <!-- Notifications Dropdown -->
                    <div class="dropdown">
                        <button class="header-btn" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="badge">3</span>
                        </button>
                        <!-- Dropdown Menu - Shows notification items -->
                        <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="width: 300px;">
                            <div class="dropdown-header">Notifications</div>
                            <a class="dropdown-item py-2" href="#">
                                <i class="fas fa-check-circle text-success me-2"></i>Order #123 delivered
                            </a>
                            <a class="dropdown-item py-2" href="#">
                                <i class="fas fa-gift text-primary me-2"></i>New offer available!
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="#">View All</a>
                        </div>
                    </div>
                    
                    <!-- User Quick Menu Dropdown -->
                    <div class="dropdown">
                        <button class="header-btn" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                            <!-- Profile - Click redirects to: profile.php -->
                            <a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a>
                            <!-- Logout - Click redirects to: ../auth/logout.php -->
                            <a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content Area -->
            <!-- Each page sets $dashboard_content variable before including this layout -->
            <div class="dashboard-content">
                <?php if (isset($dashboard_content)) echo $dashboard_content; ?>
            </div>
        </main>
        <!-- ===================================================================== -->
        <!-- END SECTION: Main Content Area -->
        <!-- ===================================================================== -->
        
        <!-- Mobile Toggle Button - Floating button to open sidebar on mobile -->
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <!-- ========================================================================= -->
    <!-- END SECTION: Dashboard Wrapper -->
    <!-- ========================================================================= -->
    
    <!-- Toast Container - For notification messages -->
    <div class="toast-container" id="toastContainer"></div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme JS -->
    <script src="/cloud_9_cafe_rebuild/assets/js/theme.js"></script>
    
    <script>
        // =====================================================================
        // SECTION: Sidebar Toggle JavaScript
        // DESCRIPTION: Handles mobile sidebar open/close functionality
        // =====================================================================
        
        // Get DOM elements
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        // Toggle sidebar on button click
        // FUNCTION: Adds/removes 'show' class to slide sidebar in/out
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });
        
        // Close sidebar when clicking overlay (mobile)
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
        
        // Close sidebar when clicking a nav link on mobile
        // Only applies when screen width is less than 992px (mobile breakpoint)
        document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });
        });
        // =====================================================================
        // END SECTION: Sidebar Toggle JavaScript
        // =====================================================================
    </script>
</body>
</html>
