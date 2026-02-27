<?php
require_once '../config/db_config.php';

// Check if admin is logged in
if (!$auth->isAdminLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}
$admin_id = $auth->getAdminId();
$admin_name = $auth->getUserName() ?? 'Admin';
$admin_role = $auth->getAdminRole();

// Get dashboard stats
$stats = [];

// Total users
$users = $db->select('cafe_users');
$stats['total_users'] = count($users);

// Total orders
$orders = $db->select('cafe_orders');
$stats['total_orders'] = count($orders);

// Total revenue
$paidOrders = $db->select('cafe_orders', ['payment_status' => 'Paid']);
$stats['total_revenue'] = 0;
foreach ($paidOrders as $order) {
    $stats['total_revenue'] += $order['total_amount'];
}

// Total menu items
$menuItems = $db->select('menu_items');
$stats['total_menu_items'] = count($menuItems);

// Pending orders
$pendingOrders = $db->select('cafe_orders', ['status' => 'Pending']);
$stats['pending_count'] = count($pendingOrders);

// Recent orders
$recentOrders = $db->select('cafe_orders', [], ['order_date' => 'DESC'], 5);

// Recent users
$recentUsers = $db->select('cafe_users', [], ['created_at' => 'DESC'], 5);

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
                <?php if (empty($recentOrders)): ?>
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
                            <?php foreach ($recentOrders as $order): 
                                $user = $db->selectOne('cafe_users', ['id' => $order['user_id']]);
                            ?>
                            <tr>
                                <td class="ps-4 fw-medium"><?php echo htmlspecialchars($order['order_number']); ?></td>
                                <td><?php echo htmlspecialchars($user['fullname'] ?? 'Unknown'); ?></td>
                                <td><small class="text-muted"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></small></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4 fw-bold">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
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
                <?php if (empty($recentUsers)): ?>
                <div class="text-center py-4">
                    <p class="text-muted mb-0">No users yet</p>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentUsers as $user): ?>
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
                    <?php endforeach; ?>
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
