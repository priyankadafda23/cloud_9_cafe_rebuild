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

// Get filter status
$filter_status = $_GET['status'] ?? 'all';

// Build query
$order_query = "SELECT * FROM cafe_orders WHERE user_id = $user_id";
if ($filter_status !== 'all' && in_array($filter_status, ['Pending', 'Preparing', 'Completed', 'Cancelled'])) {
    $order_query .= " AND status = '$filter_status'";
}
$order_query .= " ORDER BY order_date DESC";
$order_result = mysqli_query($con, $order_query);

$title = "My Orders - Cloud 9 Cafe";
$active_sidebar = 'orders';
ob_start();
?>

<style>
    .order-row {
        transition: background 0.3s ease;
    }

    .order-row:hover {
        background: rgba(102, 126, 234, 0.02);
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: white;
        border: 3px solid #667eea;
    }

    .timeline-item.completed::before {
        background: #667eea;
    }
    
    .status-pending { background: #fff3cd; color: #856404; }
    .status-preparing { background: #cce5ff; color: #004085; }
    .status-completed { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
</style>

<div class="card border-0 shadow-lg mb-4">
    <div class="card-body p-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0" style="color: #667eea;">My Orders</h2>
            <form method="get" class="d-flex gap-2">
                <select name="status" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>All Orders</option>
                    <option value="Pending" <?php echo $filter_status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Preparing" <?php echo $filter_status === 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                    <option value="Completed" <?php echo $filter_status === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="Cancelled" <?php echo $filter_status === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </form>
        </div>

        <div class="list-group list-group-flush">
            <?php if (mysqli_num_rows($order_result) === 0): ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No orders found</h5>
                <a href="../pages/menu/menu.php" class="btn btn-gradient mt-3">
                    <i class="fas fa-utensils me-2"></i>Order Now
                </a>
            </div>
            <?php else: ?>
            <?php while ($order = mysqli_fetch_assoc($order_result)): 
                // Get order items count
                $items_query = "SELECT COUNT(*) as count FROM cafe_order_items WHERE order_id = {$order['id']}";
                $items_result = mysqli_query($con, $items_query);
                $items_count = mysqli_fetch_assoc($items_result)['count'];
                
                // Get first item image
                $first_item_query = "SELECT m.image, m.name 
                                    FROM cafe_order_items oi 
                                    JOIN menu_items m ON oi.menu_item_id = m.id 
                                    WHERE oi.order_id = {$order['id']} 
                                    LIMIT 1";
                $first_item_result = mysqli_query($con, $first_item_query);
                $first_item = mysqli_fetch_assoc($first_item_result);
                
                // Status badge class
                $status_class = '';
                $status_icon = '';
                switch ($order['status']) {
                    case 'Pending':
                        $status_class = 'status-pending';
                        $status_icon = 'fa-clock';
                        break;
                    case 'Preparing':
                        $status_class = 'status-preparing';
                        $status_icon = 'fa-fire';
                        break;
                    case 'Completed':
                        $status_class = 'status-completed';
                        $status_icon = 'fa-check-circle';
                        break;
                    case 'Cancelled':
                        $status_class = 'status-cancelled';
                        $status_icon = 'fa-times-circle';
                        break;
                }
            ?>
            <div class="list-group-item border-0 p-4 order-row">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($order['order_number']); ?></h5>
                        <p class="text-muted small mb-0">Placed on <?php echo date('M d, Y - h:i A', strtotime($order['order_date'])); ?></p>
                    </div>
                    <span class="badge <?php echo $status_class; ?> px-3 py-2 rounded-pill">
                        <i class="fas <?php echo $status_icon; ?> me-1"></i><?php echo $order['status']; ?>
                    </span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo $first_item['image'] ? '../assets/images/' . $first_item['image'] : '../assets/images/product-1.jpg'; ?>" 
                         alt="" class="rounded me-3"
                         style="width: 60px; height: 60px; object-fit: cover; background: #f8f9fa;">
                    <div class="flex-grow-1">
                        <h6 class="fw-semibold mb-1"><?php echo htmlspecialchars($first_item['name'] ?? 'Item'); ?></h6>
                        <p class="text-muted small mb-0"><?php echo $items_count; ?> item<?php echo $items_count > 1 ? 's' : ''; ?></p>
                    </div>
                    <h5 class="fw-bold mb-0">₹<?php echo number_format($order['total_amount'], 2); ?></h5>
                </div>
                
                <?php if ($order['order_note']): ?>
                <div class="mb-3 p-2 bg-light rounded">
                    <small class="text-muted"><i class="fas fa-sticky-note me-1"></i>Note: <?php echo htmlspecialchars($order['order_note']); ?></small>
                </div>
                <?php endif; ?>
                
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal"
                        data-bs-target="#orderDetailsModal<?php echo $order['id']; ?>">
                        <i class="fas fa-eye me-1"></i>View Details
                    </button>
                    <?php if ($order['status'] === 'Pending'): ?>
                    <a href="?cancel_order=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                       onclick="return confirm('Cancel this order?');">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <?php endif; ?>
                    <?php if ($order['status'] === 'Completed'): ?>
                    <button class="btn btn-sm btn-outline-success rounded-pill px-3">
                        <i class="fas fa-redo me-1"></i>Reorder
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Details Modal -->
            <div class="modal fade" id="orderDetailsModal<?php echo $order['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header border-0">
                            <div>
                                <h5 class="modal-title fw-bold">Order Details</h5>
                                <p class="text-muted small mb-0"><?php echo htmlspecialchars($order['order_number']); ?></p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <!-- Order Status -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Order Status</h6>
                                <div class="d-flex justify-content-between">
                                    <div class="text-center flex-fill">
                                        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center <?php echo in_array($order['status'], ['Pending', 'Preparing', 'Completed']) ? 'bg-success text-white' : 'bg-light text-muted'; ?>"
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-clipboard-check"></i>
                                        </div>
                                        <small>Pending</small>
                                    </div>
                                    <div class="text-center flex-fill">
                                        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center <?php echo in_array($order['status'], ['Preparing', 'Completed']) ? 'bg-success text-white' : 'bg-light text-muted'; ?>"
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-fire"></i>
                                        </div>
                                        <small>Preparing</small>
                                    </div>
                                    <div class="text-center flex-fill">
                                        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center <?php echo $order['status'] === 'Completed' ? 'bg-success text-white' : 'bg-light text-muted'; ?>"
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <small>Completed</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Order Items</h6>
                                <?php
                                $detail_items = mysqli_query($con, "SELECT oi.*, m.name, m.image 
                                                                  FROM cafe_order_items oi 
                                                                  JOIN menu_items m ON oi.menu_item_id = m.id 
                                                                  WHERE oi.order_id = {$order['id']}");
                                while ($item = mysqli_fetch_assoc($detail_items)):
                                ?>
                                <div class="card border mb-2">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $item['image'] ? '../assets/images/' . $item['image'] : '../assets/images/product-1.jpg'; ?>" 
                                                 alt="" class="rounded me-3"
                                                 style="width: 60px; height: 60px; object-fit: cover; background: #f8f9fa;">
                                            <div class="flex-grow-1">
                                                <h6 class="fw-semibold mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                <p class="text-muted small mb-0">Qty: <?php echo $item['quantity']; ?> × ₹<?php echo number_format($item['unit_price'], 2); ?></p>
                                                <?php if ($item['customization']): ?>
                                                <small class="text-info"><i class="fas fa-info-circle me-1"></i><?php echo htmlspecialchars($item['customization']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <h6 class="fw-bold mb-0">₹<?php echo number_format($item['subtotal'], 2); ?></h6>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>

                            <!-- Delivery Address -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Delivery Address</h6>
                                <div class="card border">
                                    <div class="card-body p-3">
                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Info -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Payment Information</h6>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Method:</span>
                                    <span class="fw-semibold"><?php echo $order['payment_method']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Status:</span>
                                    <span class="badge bg-<?php echo $order['payment_status'] === 'Paid' ? 'success' : 'warning'; ?>">
                                        <?php echo $order['payment_status']; ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div>
                                <h6 class="fw-bold mb-3">Order Summary</h6>
                                <div class="card border">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Subtotal</span>
                                            <span class="fw-semibold">₹<?php echo number_format($order['total_amount'] / 1.08, 2); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Tax (8%)</span>
                                            <span class="fw-semibold">₹<?php echo number_format($order['total_amount'] - ($order['total_amount'] / 1.08), 2); ?></span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold">Total</span>
                                            <span class="fw-bold text-primary">₹<?php echo number_format($order['total_amount'], 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-cancel px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$dashboard_content = ob_get_clean();
include '../includes/dashboard_layout.php';

// Handle order cancellation
if (isset($_GET['cancel_order'])) {
    $order_id = intval($_GET['cancel_order']);
    
    // Verify order belongs to user and is pending
    $check_query = "SELECT status FROM cafe_orders WHERE id = $order_id AND user_id = $user_id";
    $check_result = mysqli_query($con, $check_query);
    $order = mysqli_fetch_assoc($check_result);
    
    if ($order && $order['status'] === 'Pending') {
        // Restore stock
        $items = mysqli_query($con, "SELECT menu_item_id, quantity FROM cafe_order_items WHERE order_id = $order_id");
        while ($item = mysqli_fetch_assoc($items)) {
            mysqli_query($con, "UPDATE menu_items SET stock_quantity = stock_quantity + {$item['quantity']} WHERE id = {$item['menu_item_id']}");
        }
        
        // Cancel order
        mysqli_query($con, "UPDATE cafe_orders SET status = 'Cancelled' WHERE id = $order_id");
        
        header("Location: orders.php?msg=order_cancelled");
        exit();
    }
}
?>
