<?php
require_once __DIR__ . '/../config/db_config.php';
if (!$auth->isAdminLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}
$admin_id = $auth->getAdminId();
$current_admin = $db->selectOne('cafe_admins', ['id' => $admin_id]);
$current_page = basename($_SERVER['PHP_SELF'], '.php');
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Admin - Cloud 9 Cafe'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="/cloud_9_cafe_rebuild/assets/css/theme.css">
    <link rel="stylesheet" href="/cloud_9_cafe_rebuild/assets/css/layout/admin_layout.css">
</head>
<body>
    <aside class="admin-sidebar">
        
        <!-- Sidebar Header - Logo -->
        <div class="sidebar-header">
            <!-- Logo - Click redirects to: dashboard.php -->
            <a href="dashboard.php" class="sidebar-brand">
                <i class="fas fa-mug-hot fa-lg"></i>
                <span>Cloud 9 Cafe</span>
            </a>
        </div>
        
        <!-- Admin Profile Display -->
        <div class="admin-profile">
            <!-- Admin Avatar - Shows default if no profile picture -->
            <div class="admin-info">
                <h6 class="mb-0 text-white"><?php echo htmlspecialchars($current_admin['fullname'] ?? 'Admin'); ?></h6>
                <!-- Display admin role (super_admin, manager, staff) -->
                <small><?php echo $auth->getAdminRole() ?? 'Administrator'; ?></small>
            </div>
        </div>
        
        <!-- Admin Navigation Menu -->
        <nav class="admin-nav text">
            <!-- Main Section Header -->
            <div class="nav-section text-white">Main</div>
            
            <!-- Dashboard Link - Click redirects to: dashboard.php -->
            <a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-th-large text-white"></i>
                <div class="text-white">Dashboard</div>
            </a>
            
            <!-- Users Link - Click redirects to: users.php -->
            <a href="users.php" class="nav-link <?php echo $current_page == 'users' ? 'active' : ''; ?>" style="text:white;">
                <i class="fas fa-users text-white"></i>
                <div class="text-white">Users</div>
            </a>
            
            <!-- Orders Link - Click redirects to: orders.php -->
            <a href="orders.php" class="nav-link <?php echo $current_page == 'orders' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-bag text-white"></i>
                <div class="text-white">Orders</div>
            </a>
            
            <!-- Menu Link - Click redirects to: menu.php -->
            <a href="menu.php" class="nav-link <?php echo $current_page == 'menu' ? 'active' : ''; ?>">
                <i class="fas fa-coffee text-white"></i>
                <div class="text-white">Menu</div>
            </a>
            
            <!-- Management Section Header -->
            <div class="nav-section  text-white">Management</div>
            
            <!-- Messages Link - Click redirects to: messages.php -->
            <a href="messages.php" class="nav-link <?php echo $current_page == 'messages' ? 'active' : ''; ?>">
                <i class="fas fa-envelope text-white"></i>
                <div class="text-white">Messages</div>
            </a>
            
            <!-- Profile Link - Click redirects to: profile.php -->
            <a href="profile.php" class="nav-link <?php echo $current_page == 'profile' ? 'active' : ''; ?>">
                <i class="fas fa-user-cog text-white"></i>
                <div class="text-white">My Profile</div>
            </a>
            
            <!-- Logout Link - Click redirects to: ../auth/logout.php -->
            <a href="../auth/logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt text-white"></i>
                <div class="text-white">Logout</div>
            </a>
        </nav>
    </aside>
    <main class="admin-main">
        
        <!-- Admin Header Bar -->
        <header class="admin-header">
            <!-- Mobile Sidebar Toggle Button -->
            <button class="btn btn-link d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            
            <!-- Header Action Buttons -->
            <div class="header-actions">
                <!-- View Website - Opens homepage in new tab -->
                <a href="../pages/index.php" class="header-btn" target="_blank" title="View Website">
                    <i class="fas fa-external-link-alt"></i>
                </a>
                
                <!-- Messages - Click redirects to: messages.php -->
                <a href="messages.php" class="header-btn" title="Messages">
                    <i class="fas fa-envelope"></i>
                </a>
                
                <!-- Logout - Click redirects to: ../auth/logout.php -->
                <a href="../auth/logout.php" class="header-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </header>
        
        <!-- Admin Page Content Area -->
        <!-- Each admin page sets $dashboard_content variable before including this layout -->
        <div class="admin-content">
            <?php echo isset($dashboard_content) ? $dashboard_content : ''; ?>
        </div>
        
        <!-- Admin Footer -->
        <footer class="admin-footer">
            <p>&copy; <?php echo date('Y'); ?> Cloud 9 Cafe. All rights reserved.</p>
        </footer>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.admin-sidebar').classList.toggle('show');
        });
    </script>
</body>
</html>
