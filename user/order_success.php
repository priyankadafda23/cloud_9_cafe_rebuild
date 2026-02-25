<?php
/**
 * Cloud 9 Cafe - Order Success Page
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db_config.php';

// Check if user is logged in
if (!isset($_SESSION['cafe_user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Check if order was successful
if (!isset($_SESSION['order_success'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['cafe_user_id'];
$order_id = intval($_GET['order_id'] ?? 0);

// Get order details
$order_query = "SELECT * FROM cafe_orders WHERE id = $order_id AND user_id = $user_id";
$order_result = mysqli_query($con, $order_query);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    header("Location: dashboard.php");
    exit();
}

// Get order items
$items_query = "SELECT oi.*, m.name, m.image 
                FROM cafe_order_items oi 
                JOIN menu_items m ON oi.menu_item_id = m.id 
                WHERE oi.order_id = $order_id";
$items_result = mysqli_query($con, $items_query);

// Get user's current reward points
$points_query = "SELECT reward_points FROM cafe_users WHERE id = $user_id";
$points_result = mysqli_query($con, $points_query);
$user_points = mysqli_fetch_assoc($points_result)['reward_points'];

$title = "Order Success - Cloud 9 Cafe";
ob_start();
?>

<style>
    .success-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        animation: scaleIn 0.5s ease;
    }
    
    @keyframes scaleIn {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); opacity: 1; }
    }
    
    .order-number {
        font-family: monospace;
        background: #f8f9fa;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: bold;
        color: #667eea;
    }
    
    .points-badge {
        background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
        color: #333;
        padding: 1rem 2rem;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: bold;
        animation: slideUp 0.5s ease 0.3s both;
    }
    
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

<div class="container fade-in-up">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5 text-center">
                    <!-- Success Icon -->
                    <div class="success-icon">
                        <i class="fas fa-check fa-4x text-white"></i>
                    </div>
                    
                    <h2 class="fw-bold mb-3">Order Placed Successfully!</h2>
                    <p class="text-muted mb-4">Thank you for your order. Your delicious items are being prepared.</p>
                    
                    <!-- Order Number -->
                    <div class="mb-4">
                        <p class="text-muted mb-2">Order Number</p>
                        <span class="order-number fs-5"><?php echo htmlspecialchars($order['order_number']); ?></span>
                    </div>
                    
                    <!-- Reward Points -->
                    <div class="mb-4">
                        <div class="points-badge">
                            <i class="fas fa-star"></i>
                            <span>+10 Reward Points Earned!</span>
                        </div>
                        <p class="text-muted mt-2">Your total points: <strong><?php echo $user_points; ?></strong></p>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Order Summary</h5>
                            
                            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="text-start">
                                    <span class="fw-semibold"><?php echo htmlspecialchars($item['name']); ?></span>
                                    <small class="text-muted d-block">Qty: <?php echo $item['quantity']; ?></small>
                                </div>
                                <span class="fw-bold">₹<?php echo number_format($item['subtotal'], 2); ?></span>
                            </div>
                            <?php endwhile; ?>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-5">Total</span>
                                <span class="fw-bold fs-4" style="color: #667eea;">₹<?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Status -->
                    <div class="mb-4">
                        <span class="badge bg-warning bg-opacity-10 text-warning px-4 py-2 rounded-pill fs-6">
                            <i class="fas fa-clock me-2"></i>Status: Pending
                        </span>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="orders.php" class="btn btn-gradient px-4 py-3">
                            <i class="fas fa-list me-2"></i>View My Orders
                        </a>
                        <a href="dashboard.php" class="btn btn-outline-secondary px-4 py-3">
                            <i class="fas fa-home me-2"></i>Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Additional Info -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2" style="color: #667eea;"></i>What's Next?</h5>
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3">
                                <i class="fas fa-clipboard-check fa-2x text-warning mb-2"></i>
                                <p class="small mb-0"><strong>Pending</strong><br>Order received</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <i class="fas fa-fire fa-2x text-primary mb-2"></i>
                                <p class="small mb-0"><strong>Preparing</strong><br>Chef is cooking</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <p class="small mb-0"><strong>Completed</strong><br>Ready for pickup</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <i class="fas fa-star fa-2x text-warning mb-2"></i>
                                <p class="small mb-0"><strong>Enjoy!</strong><br>Rate your experience</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../includes/layout.php';

// Clear order success session after displaying
unset($_SESSION['order_success']);
unset($_SESSION['order_number']);
unset($_SESSION['points_earned']);
?>
