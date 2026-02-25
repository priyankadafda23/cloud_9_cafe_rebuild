<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db_config.php';

// Check if admin is logged in
if (!isset($_SESSION['cafe_admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = intval($_GET['id']);

// Get order details
$order_result = mysqli_query($con, "SELECT o.*, u.fullname as user_name, u.email as user_email, u.mobile as user_mobile, u.address as user_address 
                                    FROM cafe_orders o 
                                    JOIN cafe_users u ON o.user_id = u.id 
                                    WHERE o.id = $order_id");
if (mysqli_num_rows($order_result) == 0) {
    header("Location: orders.php");
    exit();
}
$order = mysqli_fetch_assoc($order_result);

// Get order items
$items_result = mysqli_query($con, "SELECT oi.*, m.name as item_name, m.image as item_image 
                                    FROM cafe_order_items oi 
                                    JOIN menu_items m ON oi.menu_item_id = m.id 
                                    WHERE oi.order_id = $order_id");

$title = "Order #" . $order['order_number'] . " - Cloud 9 Cafe";
$active_sidebar = 'orders';
ob_start();
?>

<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
    }

    .info-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-pending { background: #fff3cd; color: #856404; }
    .status-confirmed { background: #cce5ff; color: #004085; }
    .status-preparing { background: #d1ecf1; color: #0c5460; }
    .status-ready { background: #d4edda; color: #155724; }
    .status-delivered { background: #c3e6cb; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }

    .payment-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .payment-paid { background: #d4edda; color: #155724; }
    .payment-pending { background: #fff3cd; color: #856404; }
    .payment-failed { background: #f8d7da; color: #721c24; }

    .item-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -24px;
        top: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #667eea;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold mb-2"><i class="fas fa-clipboard-list me-2"></i>Order Details</h3>
            <p class="mb-0 opacity-75"><?php echo htmlspecialchars($order['order_number']); ?></p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="orders.php" class="btn btn-light rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to Orders
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Order Info -->
    <div class="col-lg-8">
        <div class="card info-card shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Order Items</h5>
                    <span class="status-badge <?php echo 'status-' . strtolower($order['status']); ?>">
                        <?php echo $order['status']; ?>
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end pe-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <?php if ($item['item_image']): ?>
                                            <img src="../assets/images/<?php echo $item['item_image']; ?>" alt="" class="item-image">
                                            <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 10px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($item['item_name']); ?></h6>
                                            <?php if ($item['customization']): ?>
                                            <small class="text-muted"><?php echo htmlspecialchars($item['customization']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                <td class="text-end">₹<?php echo number_format($item['unit_price'], 2); ?></td>
                                <td class="text-end pe-4 fw-bold">₹<?php echo number_format($item['subtotal'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end pe-4"><strong>Total Amount:</strong></td>
                                <td class="text-end pe-4"><h5 class="fw-bold mb-0">₹<?php echo number_format($order['total_amount'], 2); ?></h5></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <?php if ($order['order_note']): ?>
        <div class="card info-card shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-sticky-note me-2 text-warning"></i>Order Note</h5>
            </div>
            <div class="card-body p-4">
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['order_note'])); ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Customer & Order Info -->
    <div class="col-lg-4">
        <!-- Customer Info -->
        <div class="card info-card shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-user me-2 text-primary"></i>Customer</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <small class="text-muted d-block">Name</small>
                    <span class="fw-medium"><?php echo htmlspecialchars($order['user_name']); ?></span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Email</small>
                    <span class="fw-medium"><?php echo htmlspecialchars($order['user_email']); ?></span>
                </div>
                <?php if ($order['user_mobile']): ?>
                <div class="mb-3">
                    <small class="text-muted d-block">Phone</small>
                    <span class="fw-medium"><?php echo htmlspecialchars($order['user_mobile']); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="card info-card shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-info-circle me-2 text-info"></i>Order Info</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <small class="text-muted d-block">Order Date</small>
                    <span class="fw-medium"><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Payment Method</small>
                    <span class="fw-medium"><?php echo $order['payment_method']; ?></span>
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block">Payment Status</small>
                    <span class="payment-badge <?php echo 'payment-' . strtolower($order['payment_status']); ?>">
                        <?php echo $order['payment_status']; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Delivery Address -->
        <?php if ($order['delivery_address']): ?>
        <div class="card info-card shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Delivery Address</h5>
            </div>
            <div class="card-body p-4">
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$dashboard_content = ob_get_clean();
include 'admin_layout.php';
?>
