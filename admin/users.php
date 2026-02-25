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

// Handle status toggle
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $user_id = intval($_GET['toggle']);
    $current_status = mysqli_query($con, "SELECT status FROM cafe_users WHERE id = $user_id");
    $status = mysqli_fetch_assoc($current_status)['status'];
    $new_status = ($status == 'Active') ? 'Inactive' : 'Active';
    mysqli_query($con, "UPDATE cafe_users SET status = '$new_status' WHERE id = $user_id");
    header("Location: users.php");
    exit();
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    mysqli_query($con, "DELETE FROM cafe_users WHERE id = $user_id");
    header("Location: users.php");
    exit();
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$where = '';
if ($search) {
    $where = "WHERE fullname LIKE '%$search%' OR email LIKE '%$search%' OR mobile LIKE '%$search%'";
}

// Get total count
$count_result = mysqli_query($con, "SELECT COUNT(*) as count FROM cafe_users $where");
$total_users = mysqli_fetch_assoc($count_result)['count'];
$total_pages = ceil($total_users / $limit);

// Get users
$users = mysqli_query($con, "SELECT * FROM cafe_users $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

$title = "Users - Cloud 9 Cafe";
$page_title = "User Management";
ob_start();
?>

<!-- Page Header -->
<div class="page-header-card">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2><i class="fas fa-users me-2"></i>User Management</h2>
            <p>Manage cafe customers and their accounts</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span class="badge bg-white text-primary fs-6 px-3 py-2">Total: <?php echo $total_users; ?> Users</span>
        </div>
    </div>
</div>

<!-- Search Bar -->
<div class="admin-card mb-4">
    <div class="admin-card-body">
        <form method="GET" class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email or phone..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Search
                </button>
                <?php if ($search): ?>
                <a href="users.php" class="btn btn-outline-secondary ms-2">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">User</th>
                        <th>Contact</th>
                        <th>Points</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($users) === 0): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No users found</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php while ($user = mysqli_fetch_assoc($users)): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <?php if ($user['profile_picture']): ?>
                                    <img src="../assets/images/profile_pictures/<?php echo $user['profile_picture']; ?>" alt="" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 fw-medium"><?php echo htmlspecialchars($user['fullname']); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($user['mobile']): ?>
                            <small><i class="fas fa-phone me-1 text-muted"></i><?php echo $user['mobile']; ?></small>
                            <?php else: ?>
                            <small class="text-muted">-</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-star me-1"></i><?php echo $user['reward_points']; ?> pts
                            </span>
                        </td>
                        <td>
                            <span class="badge <?php echo $user['status'] == 'Active' ? 'bg-success' : 'bg-danger'; ?> bg-opacity-10 text-<?php echo $user['status'] == 'Active' ? 'success' : 'danger'; ?>">
                                <?php echo $user['status']; ?>
                            </span>
                        </td>
                        <td>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></small>
                        </td>
                        <td class="text-end pe-4">
                            <a href="?toggle=<?php echo $user['id']; ?>" class="action-btn toggle" title="Toggle Status" onclick="return confirm('Change user status?')">
                                <i class="fas fa-<?php echo $user['status'] == 'Active' ? 'ban' : 'check'; ?>"></i>
                            </a>
                            <a href="?delete=<?php echo $user['id']; ?>" class="action-btn delete ms-1" title="Delete" onclick="return confirm('Delete this user?')">
                                <i class="fas fa-trash"></i>
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
                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">Previous</a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">Next</a>
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
