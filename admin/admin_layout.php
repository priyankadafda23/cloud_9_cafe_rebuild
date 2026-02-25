<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['cafe_admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Include database and fetch admin data
require_once __DIR__ . '/../config/db_config.php';
$admin_id = $_SESSION['cafe_admin_id'];
$admin_query = mysqli_query($con, "SELECT * FROM cafe_admins WHERE id = $admin_id");
$current_admin = mysqli_fetch_assoc($admin_query);

// Get current page
$current_page = basename($_SERVER['PHP_SELF'], '.php');
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Admin - Cloud 9 Cafe'; ?></title>
    
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
        /* Admin Layout - Dark Sidebar */
        :root {
            --admin-sidebar: #1a1f2e;
            --admin-sidebar-hover: rgba(255,255,255,0.05);
            --admin-sidebar-active: var(--cafe-primary);
        }
        
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Dark Sidebar */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: var(--admin-sidebar);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 1000;
            transition: transform var(--transition-normal);
        }
        
        .sidebar-brand {
            padding: var(--space-lg);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand a {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }
        
        .sidebar-brand i {
            color: var(--cafe-accent);
        }
        
        .admin-profile {
            padding: var(--space-lg);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: var(--space-md);
        }
        
        .admin-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--cafe-accent);
        }
        
        .admin-info h6 {
            color: white;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .admin-info small {
            color: rgba(255,255,255,0.5);
        }
        
        .admin-badge {
            background: var(--cafe-accent);
            color: var(--cafe-primary-dark);
            padding: 0.15rem 0.5rem;
            border-radius: var(--radius-full);
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .admin-nav {
            flex: 1;
            padding: var(--space-md);
            overflow-y: auto;
        }
        
        .admin-nav .nav-section {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.4);
            margin: var(--space-lg) 0 var(--space-sm) var(--space-md);
            font-weight: 600;
        }
        
        .admin-nav .nav-link {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            padding: var(--space-md) var(--space-lg);
            margin-bottom: var(--space-xs);
            border-radius: var(--radius-md);
            color: rgba(255,255,255,0.7);
            font-weight: 500;
            transition: all var(--transition-fast);
        }
        
        .admin-nav .nav-link i {
            width: 24px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .admin-nav .nav-link:hover {
            background: var(--admin-sidebar-hover);
            color: white;
        }
        
        .admin-nav .nav-link.active {
            background: var(--admin-sidebar-active);
            color: white;
            box-shadow: 0 4px 12px rgba(107, 79, 75, 0.4);
        }
        
        .sidebar-footer {
            padding: var(--space-lg);
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        /* Main Content */
        .admin-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            background: #f1f3f4;
            min-height: 100vh;
            transition: margin var(--transition-normal);
        }
        
        /* Admin Header */
        .admin-header {
            background: white;
            padding: var(--space-md) var(--space-xl);
            border-bottom: 1px solid #E5E8E8;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0;
            color: var(--text-dark);
        }
        
        .header-title small {
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
            background: #f1f3f4;
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
            background: var(--danger);
            color: white;
            font-size: 0.6rem;
            padding: 0.2rem 0.4rem;
        }
        
        /* Content Area */
        .admin-content {
            padding: var(--space-xl);
        }
        
        /* Page Header */
        .page-header-card {
            background: var(--gradient-primary);
            border-radius: var(--radius-lg);
            padding: 1.5rem 2rem;
            margin-bottom: var(--space-xl);
            color: white;
        }
        
        .page-header-card h2 {
            color: white;
            margin-bottom: 0.25rem;
        }
        
        .page-header-card p {
            color: rgba(255,255,255,0.7);
            margin-bottom: 0;
        }
        
        /* Admin Cards */
        .admin-card {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        
        .admin-card-header {
            padding: var(--space-lg);
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-card-header h5 {
            margin-bottom: 0;
            font-weight: 600;
        }
        
        .admin-card-body {
            padding: var(--space-lg);
        }
        
        /* Stat Cards for Admin */
        .admin-stat-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-normal);
        }
        
        .admin-stat-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-4px);
        }
        
        .admin-stat-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .admin-stat-icon.blue { background: rgba(52, 152, 219, 0.1); color: #3498DB; }
        .admin-stat-icon.green { background: rgba(39, 174, 96, 0.1); color: #27AE60; }
        .admin-stat-icon.orange { background: rgba(243, 156, 18, 0.1); color: #F39C12; }
        .admin-stat-icon.red { background: rgba(231, 76, 60, 0.1); color: #E74C3C; }
        
        .admin-stat-content h3 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .admin-stat-content p {
            font-size: var(--font-size-sm);
            color: var(--text-medium);
            margin-bottom: 0;
        }
        
        /* Action Buttons */
        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all var(--transition-fast);
            cursor: pointer;
        }
        
        .action-btn.view { background: rgba(52, 152, 219, 0.1); color: #3498DB; }
        .action-btn.edit { background: rgba(243, 156, 18, 0.1); color: #F39C12; }
        .action-btn.delete { background: rgba(231, 76, 60, 0.1); color: #E74C3C; }
        .action-btn.toggle { background: rgba(39, 174, 96, 0.1); color: #27AE60; }
        
        .action-btn:hover {
            transform: translateY(-2px);
            filter: brightness(0.9);
        }
        
        /* Tables */
        .admin-table {
            font-size: var(--font-size-sm);
        }
        
        .admin-table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            color: var(--text-medium);
            border-bottom: 2px solid #E5E8E8;
            padding: var(--space-md);
            background: #f8f9fa;
        }
        
        .admin-table tbody td {
            padding: var(--space-md);
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .admin-table tbody tr:hover {
            background: rgba(107, 79, 75, 0.02);
        }
        
        /* Mobile Toggle */
        .admin-sidebar-toggle {
            display: none;
            position: fixed;
            bottom: var(--space-lg);
            right: var(--space-lg);
            z-index: 1001;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--cafe-primary);
            color: white;
            border: none;
            box-shadow: var(--shadow-lg);
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        
        /* Overlay */
        .admin-overlay {
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
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .admin-sidebar-toggle {
                display: flex;
            }
            
            .admin-overlay.show {
                display: block;
            }
        }
        
        @media (max-width: 767.98px) {
            .admin-content {
                padding: var(--space-md);
            }
            
            .admin-header {
                padding: var(--space-md);
            }
            
            .page-header-card {
                padding: var(--space-lg);
            }
            
            .page-header-card h2 {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-brand">
                <a href="dashboard.php">
                    <i class="fas fa-mug-hot"></i>
                    Cloud 9 <span class="text-accent">Admin</span>
                </a>
            </div>
            
            <div class="admin-profile">
                <img src="<?php echo $current_admin['profile_picture'] ? '/' . $current_admin['profile_picture'] : '/cloud_9_cafe_rebuild/assets/uploads/Profile/default.png'; ?>" 
                     alt="Admin" class="admin-avatar" onerror="this.src='/cloud_9_cafe_rebuild/assets/uploads/Profile/default.png'">
                <div class="admin-info">
                    <h6 class="mb-0"><?php echo htmlspecialchars($current_admin['fullname'] ?? 'Admin'); ?></h6>
                    <small><?php echo $_SESSION['cafe_admin_role'] ?? 'Administrator'; ?></small>
                </div>
            </div>
            
            <nav class="admin-nav">
                <div class="nav-section">Main</div>
                <a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-th-large"></i>
                    Dashboard
                </a>
                <a href="users.php" class="nav-link <?php echo $current_page == 'users' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    Users
                </a>
                <a href="menu.php" class="nav-link <?php echo $current_page == 'menu' ? 'active' : ''; ?>">
                    <i class="fas fa-coffee"></i>
                    Menu Items
                </a>
                <a href="orders.php" class="nav-link <?php echo $current_page == 'orders' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-bag"></i>
                    Orders
                </a>
                
                <div class="nav-section">Management</div>
                <a href="messages.php" class="nav-link <?php echo $current_page == 'messages' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i>
                    Messages
                </a>
                
                <div class="nav-section">Settings</div>
                <a href="profile.php" class="nav-link <?php echo $current_page == 'profile' ? 'active' : ''; ?>">
                    <i class="fas fa-user-cog"></i>
                    Profile
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="../auth/logout.php" class="btn btn-outline-light w-100 btn-sm">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </aside>
        
        <!-- Overlay -->
        <div class="admin-overlay" id="adminOverlay"></div>
        
        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-title">
                    <h1><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
                </div>
                
                <div class="header-actions">
                    <a href="../pages/index.php" class="btn btn-outline-secondary btn-sm" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>View Site
                    </a>
                    <button class="header-btn">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </button>
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
            <div class="admin-content">
                <?php if (isset($dashboard_content)) echo $dashboard_content; ?>
            </div>
        </main>
        
        <!-- Mobile Toggle -->
        <button class="admin-sidebar-toggle" id="adminSidebarToggle">
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
        const sidebar = document.getElementById('adminSidebar');
        const toggleBtn = document.getElementById('adminSidebarToggle');
        const overlay = document.getElementById('adminOverlay');
        
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
        
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
        
        // Close sidebar on link click (mobile)
        document.querySelectorAll('.admin-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>
