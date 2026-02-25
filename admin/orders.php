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

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    mysqli_query($con, "UPDATE cafe_orders SET status = '$status' WHERE id = $order_id");
    header("Location: orders.php");
    exit();
}

// Handle payment status update
if (isset($_POST['update_payment']) && isset($_POST['order_id']) && isset($_POST['payment_status'])) {
    $order_id = intval($_POST['order_id']);
    $payment_status = mysqli_real_escape_string($con, $_POST['payment_status']);
    mysqli_query($con, "UPDATE cafe_orders SET payment_status = '$payment_status' WHERE id = $order_id");
    header("Location: orders.php");
    exit();
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filters
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';

$where = [];
if ($status_filter) {
    $where[] = "o.status = '$status_filter'";
}
if ($search) {
    $where[] = "(o.order_number LIKE '%$search%' OR u.fullname LIKE '%$search%')";
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$count_result = mysqli_query($con, "SELECT COUNT(*) as count FROM cafe_orders o JOIN cafe_users u ON o.user_id = u.id $where_sql");
$total_orders = mysqli_fetch_assoc($count_result)['count'];
$total_pages = ceil($total_orders / $limit);

// Get orders
$orders = mysqli_query($con, "SELECT o.*, u.fullname as user_name, u.email as user_email 
                              FROM cafe_orders o 
                              JOIN cafe_users u ON o.user_id = u.id 
                              $where_sql 
                              ORDER BY o.order_date DESC 
                              LIMIT $limit OFFSET $offset");

$title = "Orders - Cloud 9 Cafe";
$page_title = "Order Management";
ob_start();
?>

<!-- Page Header -->
<div class="page-header-card">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2><i class="fas fa-shopping-bag me-2"></i>Order Management</h2>
            <p>Manage and track customer orders</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span class="badge bg-white text-primary fs-6 px-3 py-2">Total: <?php echo $total_orders; ?> Orders</span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="admin-card mb-4">
    <div class="admin-card-body">
        <form method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search order # or customer..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Confirmed" <?php echo $status_filter == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="Preparing" <?php echo $status_filter == 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                    <option value="Ready" <?php echo $status_filter == 'Ready' ? 'selected' : ''; ?>>Ready</option>
                    <option value="Delivered" <?php echo $status_filter == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="Cancelled" <?php echo $status_filter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
                <?php if ($search || $status_filter): ?>
                <a href="orders.php" class="btn btn-outline-secondary ms-2">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($orders) === 0): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No orders found</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                    <tr>
                        <td class="ps-4 fw-medium"><?php echo htmlspecialchars($order['order_number']); ?></td>
                        <td>
                            <div>
                                <div class="fw-medium"><?php echo htmlspecialchars($order['user_name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($order['user_email']); ?></small>
                            </div>
                        </td>
                        <td>
                            <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></small>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="form-select form-select-sm status-badge status-<?php echo strtolower($order['status']); ?>" style="min-width: 120px; border: none;" onchange="this.form.submit()">
                                    <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Confirmed" <?php echo $order['status'] == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="Preparing" <?php echo $order['status'] == 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                                    <option value="Ready" <?php echo $order['status'] == 'Ready' ? 'selected' : ''; ?>>Ready</option>
                                    <option value="Delivered" <?php echo $order['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="payment_status" class="form-select form-select-sm badge bg-opacity-10 <?php echo $order['payment_status'] == 'Paid' ? 'bg-success text-success' : 'bg-warning text-warning'; ?>" style="min-width: 90px; border: none;" onchange="this.form.submit()">
                                    <option value="Pending" <?php echo $order['payment_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Paid" <?php echo $order['payment_status'] == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                    <option value="Failed" <?php echo $order['payment_status'] == 'Failed' ? 'selected' : ''; ?>>Failed</option>
                                    <option value="Refunded" <?php echo $order['payment_status'] == 'Refunded' ? 'selected' : ''; ?>>Refunded</option>
                                </select>
                                <input type="hidden" name="update_payment" value="1">
                            </form>
                        </td>
                        <td class="text-end fw-bold">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td class="text-end pe-4">
                            <a href="order_view.php?id=<?php echo $order['id']; ?>" class="action-btn view" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="d-flex justify-content-center p-4">
            <nav>
                <ul class="pagination mb-0">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $status_filter ? '&status='.urlencode($status_filter) : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">Previous</a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $status_filter ? '&status='.urlencode($status_filter) : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $status_filter ? '&status='.urlencode($status_filter) : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$dashboard_content = ob_get_clean();
include 'admin_layout.php';
?>
