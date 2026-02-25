<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db_config.php';

// Check if admin is logged in using cafe_admin_id
if (!isset($_SESSION['cafe_admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$admin_name = $_SESSION['cafe_admin_name'] ?? 'Admin';

// Get dashboard stats
$stats = [];

// Total cafe_users
$users_result = mysqli_query($con, "SELECT COUNT(*) as count FROM cafe_users");
$stats['total_users'] = mysqli_fetch_assoc($users_result)['count'];

// Total cafe_orders
$orders_result = mysqli_query($con, "SELECT COUNT(*) as count FROM cafe_orders");
$stats['total_orders'] = mysqli_fetch_assoc($orders_result)['count'];

// Total revenue from cafe_orders
$revenue_result = mysqli_query($con, "SELECT SUM(total_amount) as total FROM cafe_orders WHERE payment_status = 'Paid'");
$revenue = mysqli_fetch_assoc($revenue_result)['total'];
$stats['total_revenue'] = $revenue ? $revenue : 0;

// Total menu_items
$menu_result = mysqli_query($con, "SELECT COUNT(*) as count FROM menu_items");
$stats['total_menu_items'] = mysqli_fetch_assoc($menu_result)['count'];

// Pending orders
$pending_orders = mysqli_query($con, "SELECT COUNT(*) as count FROM cafe_orders WHERE status = 'Pending'");
$stats['pending_count'] = mysqli_fetch_assoc($pending_orders)['count'];

// Recent orders
$recent_orders = mysqli_query($con, "SELECT o.*, u.fullname as user_name 
                                     FROM cafe_orders o
                                     JOIN cafe_users u ON o.user_id = u.id
                                     ORDER BY o.order_date DESC
                                     LIMIT 5");

// Recent users
$recent_users = mysqli_query($con, "SELECT id, fullname, email, created_at 
                                    FROM cafe_users 
                                    ORDER BY created_at DESC 
                                    LIMIT 5");

$title = "Admin Dashboard - Cloud 9 Cafe";
$page_title = "Dashboard";
ob_start();
?>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="admin-stat-content">
                <h3><?php echo number_format($stats['total_users']); ?></h3>
                <p>Total Users</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon green">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="admin-stat-content">
                <h3><?php echo number_format($stats['total_orders']); ?></h3>
                <p>Total Orders</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon orange">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="admin-stat-content">
                <h3>₹<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="admin-stat-card">
            <div class="admin-stat-icon red">
                <i class="fas fa-coffee"></i>
            </div>
            <div class="admin-stat-content">
                <h3><?php echo number_format($stats['total_menu_items']); ?></h3>
                <p>Menu Items</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fas fa-shopping-bag me-2 text-primary"></i>Recent Orders</h5>
                <a href="orders.php" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (mysqli_num_rows($recent_orders) === 0): ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No orders yet</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                            <tr>
                                <td class="ps-4 fw-medium"><?php echo htmlspecialchars($order['order_number']); ?></td>
                                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                <td><small class="text-muted"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></small></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4 fw-bold">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions & Recent Users -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <h5><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h5>
            </div>
            <div class="admin-card-body">
                <a href="menu_add.php" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-plus me-2"></i>Add Menu Item
                </a>
                <a href="orders.php" class="btn btn-outline-primary w-100 mb-3">
                    <i class="fas fa-clipboard-check me-2"></i>Manage Orders
                </a>
                <a href="users.php" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-user-friends me-2"></i>View Users
                </a>
            </div>
        </div>
        
        <!-- Recent Users -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fas fa-user-plus me-2 text-success"></i>Recent Users</h5>
            </div>
            <div class="card-body p-0">
                <?php if (mysqli_num_rows($recent_users) === 0): ?>
                <div class="text-center py-4">
                    <p class="text-muted mb-0">No users yet</p>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php while ($user = mysqli_fetch_assoc($recent_users)): ?>
                    <div class="list-group-item border-0 px-4 py-3 d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-user text-muted"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-medium"><?php echo htmlspecialchars($user['fullname']); ?></h6>
                            <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                        </div>
                        <small class="text-muted"><?php echo date('M d', strtotime($user['created_at'])); ?></small>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$dashboard_content = ob_get_clean();
include 'admin_layout.php';
?>
