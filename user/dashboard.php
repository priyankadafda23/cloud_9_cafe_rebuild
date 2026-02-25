<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db_config.php';

// Check if user is logged in using cafe_user_id
if (!isset($_SESSION['cafe_user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['cafe_user_id'];
$user_name = $_SESSION['cafe_user_name'] ?? 'Guest';

// Get user stats
$stats = [];

// Total orders
$orders_result = mysqli_query($con, "SELECT COUNT(*) as count FROM cafe_orders WHERE user_id = $user_id");
$stats['total_orders'] = mysqli_fetch_assoc($orders_result)['count'];

// Pending orders
$pending_result = mysqli_query($con, "SELECT COUNT(*) as count FROM cafe_orders WHERE user_id = $user_id AND status = 'Pending'");
$stats['pending_orders'] = mysqli_fetch_assoc($pending_result)['count'];

// Reward points
$points_result = mysqli_query($con, "SELECT reward_points FROM cafe_users WHERE id = $user_id");
$stats['reward_points'] = mysqli_fetch_assoc($points_result)['reward_points'] ?? 0;

// Cart count
$cart_result = mysqli_query($con, "SELECT COUNT(*) as count FROM cafe_cart WHERE user_id = $user_id");
$stats['cart_items'] = mysqli_fetch_assoc($cart_result)['count'];

// Recent orders
$recent_orders = mysqli_query($con, "SELECT o.*, oi.menu_item_id, m.name as item_name, m.image 
                                     FROM cafe_orders o
                                     JOIN cafe_order_items oi ON o.id = oi.order_id
                                     JOIN menu_items m ON oi.menu_item_id = m.id
                                     WHERE o.user_id = $user_id
                                     GROUP BY o.id
                                     ORDER BY o.order_date DESC
                                     LIMIT 5");

$title = "Dashboard - Cloud 9 Cafe";
ob_start();
?>

<!-- Welcome Banner -->
<div class="card border-0 mb-4" style="background: var(--gradient-primary);">
    <div class="card-body p-4 text-white">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h4 class="fw-bold mb-2">Welcome back, <?php echo htmlspecialchars($user_name); ?>! ☕</h4>
                <p class="mb-0 opacity-75">Here's what's happening with your account today.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="edit_profile.php" class="btn btn-light">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['total_orders']; ?></h3>
                <p>Total Orders</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['pending_orders']; ?></h3>
                <p>Pending Orders</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #D4A574 0%, #E8C9A0 100%); color: var(--cafe-primary-dark);">
            <div class="stat-icon" style="background: rgba(107, 79, 75, 0.1); color: var(--cafe-primary);">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['reward_points']; ?></h3>
                <p>Reward Points</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <a href="cart.php" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon accent" style="background: rgba(212, 165, 116, 0.2); color: var(--cafe-accent);">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['cart_items']; ?></h3>
                    <p>Cart Items</p>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-shopping-bag me-2 text-primary"></i>Recent Orders</h5>
                <a href="orders.php" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (mysqli_num_rows($recent_orders) === 0): ?>
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-box-open fa-3x text-muted"></i>
                    </div>
                    <p class="text-muted mb-3">No orders yet</p>
                    <a href="../pages/menu/menu.php" class="btn btn-primary">
                        <i class="fas fa-utensils me-2"></i>Browse Menu
                    </a>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Order</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-light rounded p-2">
                                                <img src="<?php echo $order['image'] ? '../assets/images/' . $order['image'] : 'https://via.placeholder.com/50'; ?>" 
                                                     alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 fw-medium"><?php echo htmlspecialchars($order['item_name']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($order['order_number']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></small>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4 fw-bold">
                                    ₹<?php echo number_format($order['total_amount'], 2); ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions & Info -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="../pages/menu/menu.php" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-utensils me-2"></i>Browse Menu
                </a>
                <a href="cart.php" class="btn btn-outline-primary w-100 mb-3">
                    <i class="fas fa-shopping-cart me-2"></i>View Cart
                </a>
                <a href="orders.php" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-list me-2"></i>Order History
                </a>
            </div>
        </div>
        
        <!-- Reward Points Card -->
        <div class="card border-0 text-white" style="background: linear-gradient(135deg, var(--cafe-primary) 0%, var(--cafe-primary-dark) 100%);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background: rgba(255,255,255,0.1);">
                            <i class="fas fa-gift fa-2x text-accent"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h5 class="fw-bold mb-0">Reward Points</h5>
                        <small class="opacity-75">Earn points with every order</small>
                    </div>
                </div>
                <div class="bg-white bg-opacity-10 rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Your Points</span>
                        <span class="fw-bold fs-4"><?php echo $stats['reward_points']; ?></span>
                    </div>
                </div>
                <p class="small mb-0 opacity-75">
                    <i class="fas fa-info-circle me-1"></i>Earn 10 points for every ₹1 spent!
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Override stat-card for dashboard */
    .stat-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: var(--shadow-sm);
        transition: all var(--transition-normal);
        height: 100%;
    }
    
    .stat-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-4px);
    }
    
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    
    .stat-content h3 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0;
        color: var(--text-dark);
    }
    
    .stat-content p {
        font-size: var(--font-size-sm);
        color: var(--text-medium);
        margin-bottom: 0;
    }
</style>

<?php
$dashboard_content = ob_get_clean();
include '../includes/dashboard_layout.php';
?>
