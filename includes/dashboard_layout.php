<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['cafe_user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Include database and fetch user data
require_once __DIR__ . '/../config/db_config.php';
$user_id = $_SESSION['cafe_user_id'];
$user_query = mysqli_query($con, "SELECT * FROM cafe_users WHERE id = $user_id");
$current_user = mysqli_fetch_assoc($user_query);

// Default active sidebar item if not set
if (!isset($active_sidebar)) {
    $active_sidebar = '';
}

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF'], '.php');
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Dashboard - Cloud 9 Cafe'; ?></title>
    
    <!-- Google Fonts -->
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
        /* Dashboard Layout Specific Styles */
        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .dashboard-sidebar {
            width: var(--sidebar-width);
            background: var(--bg-white);
            border-right: 1px solid #E5E8E8;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 1000;
            transition: transform var(--transition-normal);
        }
        
        .sidebar-header {
            padding: var(--space-lg);
            border-bottom: 1px solid #E5E8E8;
        }
        
        .brand-link {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--cafe-primary);
            text-decoration: none;
        }
        
        .brand-link:hover {
            color: var(--cafe-primary-dark);
        }
        
        .user-profile-summary {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            padding: var(--space-lg);
            border-bottom: 1px solid #E5E8E8;
        }
        
        .user-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--cafe-accent);
        }
        
        .user-info h6 {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .user-info small {
            color: var(--text-light);
        }
        
        .sidebar-nav {
            flex: 1;
            padding: var(--space-md);
            overflow-y: auto;
        }
        
        .sidebar-nav .nav-section {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-light);
            margin: var(--space-lg) 0 var(--space-sm) var(--space-md);
            font-weight: 600;
        }
        
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            padding: var(--space-md) var(--space-lg);
            margin-bottom: var(--space-xs);
            border-radius: var(--radius-md);
            color: var(--text-medium);
            font-weight: 500;
            transition: all var(--transition-fast);
        }
        
        .sidebar-nav .nav-link i {
            width: 24px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .sidebar-nav .nav-link:hover {
            background: rgba(107, 79, 75, 0.05);
            color: var(--cafe-primary);
        }
        
        .sidebar-nav .nav-link.active {
            background: var(--gradient-primary);
            color: white;
            box-shadow: var(--shadow-md);
        }
        
        .sidebar-nav .nav-link.active i {
            color: white;
        }
        
        .sidebar-footer {
            padding: var(--space-lg);
            border-top: 1px solid #E5E8E8;
        }
        
        /* Main Content */
        .dashboard-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            background: var(--bg-cream);
            min-height: 100vh;
            transition: margin var(--transition-normal);
        }
        
        /* Top Header */
        .dashboard-header {
            background: var(--bg-white);
            padding: var(--space-md) var(--space-xl);
            border-bottom: 1px solid #E5E8E8;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-search {
            position: relative;
            max-width: 300px;
        }
        
        .header-search input {
            padding-left: 40px;
            border-radius: var(--radius-full);
            background: var(--bg-cream);
            border: none;
        }
        
        .header-search i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: var(--space-md);
        }
        
        .header-btn {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            border: none;
            background: var(--bg-cream);
            color: var(--text-medium);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all var(--transition-fast);
        }
        
        .header-btn:hover {
            background: var(--cafe-primary);
            color: white;
        }
        
        .header-btn .badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: var(--cafe-accent);
            color: var(--cafe-primary-dark);
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
        }
        
        /* Content Area */
        .dashboard-content {
            padding: var(--space-xl);
        }
        
        /* Mobile Toggle Button */
        .sidebar-toggle {
            display: none;
            position: fixed;
            bottom: var(--space-lg);
            right: var(--space-lg);
            z-index: 1001;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--gradient-primary);
            color: white;
            border: none;
            box-shadow: var(--shadow-lg);
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        
        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        
        /* Responsive */
        @media (max-width: 991.98px) {
            .dashboard-sidebar {
                transform: translateX(-100%);
            }
            
            .dashboard-sidebar.show {
                transform: translateX(0);
            }
            
            .dashboard-main {
                margin-left: 0;
            }
            
            .sidebar-toggle {
                display: flex;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }
        
        @media (max-width: 767.98px) {
            .dashboard-content {
                padding: var(--space-md);
            }
            
            .dashboard-header {
                padding: var(--space-md);
            }
            
            .header-search {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="../pages/index.php" class="brand-link">
                    <i class="fas fa-mug-hot text-accent"></i>
                    Cloud 9 Cafe
                </a>
            </div>
            
            <div class="user-profile-summary">
                <img src="<?php echo $current_user['profile_picture'] ? '/' . $current_user['profile_picture'] : '/cloud_9_cafe_rebuild/assets/uploads/Profile/default.png'; ?>" 
                     alt="User" class="user-avatar" onerror="this.src='/cloud_9_cafe_rebuild/assets/uploads/Profile/default.png'">
                <div class="user-info">
                    <h6 class="mb-0"><?php echo htmlspecialchars($current_user['fullname'] ?? 'User'); ?></h6>
                    <small>Member</small>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">Main</div>
                <a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-th-large"></i>
                    Dashboard
                </a>
                <a href="orders.php" class="nav-link <?php echo $current_page == 'orders' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-bag"></i>
                    My Orders
                </a>
                <a href="cart.php" class="nav-link <?php echo $current_page == 'cart' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i>
                    Cart
                </a>
                <a href="wishlist.php" class="nav-link <?php echo $current_page == 'wishlist' ? 'active' : ''; ?>">
                    <i class="fas fa-heart"></i>
                    Favorites
                </a>
                
                <div class="nav-section">Account</div>
                <a href="profile.php" class="nav-link <?php echo $current_page == 'profile' ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i>
                    Profile
                </a>
                <a href="addresses.php" class="nav-link <?php echo $current_page == 'addresses' ? 'active' : ''; ?>">
                    <i class="fas fa-map-marker-alt"></i>
                    Addresses
                </a>
                <a href="change_password.php" class="nav-link <?php echo $current_page == 'change_password' ? 'active' : ''; ?>">
                    <i class="fas fa-lock"></i>
                    Change Password
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="../auth/logout.php" class="btn btn-outline-danger w-100">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </aside>
        
        <!-- Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Main Content -->
        <main class="dashboard-main">
            <!-- Header -->
            <header class="dashboard-header">
                <div class="header-search">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control" placeholder="Search...">
                </div>
                
                <div class="header-actions">
                    <a href="../pages/menu/menu.php" class="btn btn-primary btn-sm d-none d-md-inline-flex">
                        <i class="fas fa-plus me-2"></i>New Order
                    </a>
                    <a href="cart.php" class="header-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge">0</span>
                    </a>
                    <div class="dropdown">
                        <button class="header-btn" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="badge">3</span>
                        </button>
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
                    <div class="dropdown">
                        <button class="header-btn" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                            <a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a>
                            <a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <div class="dashboard-content">
                <?php if (isset($dashboard_content)) echo $dashboard_content; ?>
            </div>
        </main>
        
        <!-- Mobile Toggle -->
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme JS -->
    <script src="/cloud_9_cafe_rebuild/assets/js/theme.js"></script>
    
    <script>
        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });
        
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
        
        // Close sidebar on link click (mobile)
        document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>
