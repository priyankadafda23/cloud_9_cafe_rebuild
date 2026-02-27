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

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $item_id = intval($_GET['delete']);
    $db->delete('menu_items', ['id' => $item_id]);
    header("Location: menu.php");
    exit();
}

// Handle availability toggle
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $item_id = intval($_GET['toggle']);
    $item = $db->selectOne('menu_items', ['id' => $item_id]);
    if ($item) {
        $new_avail = ($item['availability'] == 'Available') ? 'Out of Stock' : 'Available';
        $db->update('menu_items', ['availability' => $new_avail], ['id' => $item_id]);
    }
    header("Location: menu.php");
    exit();
}

// Handle featured toggle
if (isset($_GET['featured']) && is_numeric($_GET['featured'])) {
    $item_id = intval($_GET['featured']);
    $item = $db->selectOne('menu_items', ['id' => $item_id]);
    if ($item) {
        $new_feat = $item['featured'] ? 0 : 1;
        $db->update('menu_items', ['featured' => $new_feat], ['id' => $item_id]);
    }
    header("Location: menu.php");
    exit();
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filter
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Get all menu items
$allItems = $db->select('menu_items', [], ['category' => 'ASC', 'name' => 'ASC']);

// Filter by category
if ($category) {
    $allItems = array_filter($allItems, function($item) use ($category) {
        return $item['category'] === $category;
    });
}

// Filter by search
if ($search) {
    $allItems = array_filter($allItems, function($item) use ($search) {
        return stripos($item['name'], $search) !== false || 
               stripos($item['description'], $search) !== false;
    });
}

$total_items = count($allItems);
$total_pages = ceil($total_items / $limit);

// Get paginated items
$items = array_slice($allItems, $offset, $limit);

$title = "Menu Items - Cloud 9 Cafe";
$page_title = "Menu Management";
ob_start();
?>

<!-- Page Header -->
<div class="page-header-card">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2><i class="fas fa-coffee me-2"></i>Menu Management</h2>
            <p>Manage cafe menu items and inventory</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="menu_add.php" class="btn btn-light">
                <i class="fas fa-plus me-2"></i>Add Item
            </a>
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
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search menu items..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <option value="Coffee" <?php echo $category == 'Coffee' ? 'selected' : ''; ?>>Coffee</option>
                    <option value="Snack" <?php echo $category == 'Snack' ? 'selected' : ''; ?>>Snack</option>
                    <option value="Dessert" <?php echo $category == 'Dessert' ? 'selected' : ''; ?>>Dessert</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
                <?php if ($search || $category): ?>
                <a href="menu.php" class="btn btn-outline-secondary ms-2">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Menu Items Table -->
<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Item</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-center">Featured</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-coffee fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No menu items found</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <?php 
                                    // Check image in priority order: new path -> old path -> placeholder
                                    $image_path = '';
                                    if ($item['image']) {
                                        if (file_exists("../{$item['image']}")) {
                                            $image_path = "../{$item['image']}";
                                        } elseif (file_exists("../assets/images/{$item['image']}")) {
                                            $image_path = "../assets/images/{$item['image']}";
                                        }
                                    }
                                    ?>
                                    <?php if ($image_path): ?>
                                    <img src="<?php echo $image_path; ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 8px; background: linear-gradient(135deg, var(--cafe-primary-light) 0%, var(--cafe-primary) 100%);">
                                        <i class="fas fa-coffee text-white"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 fw-medium"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted text-truncate d-block" style="max-width: 200px;"><?php echo htmlspecialchars($item['description']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <?php echo $item['category']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold">₹<?php echo number_format($item['price'], 2); ?></span>
                        </td>
                        <td>
                            <span class="<?php echo $item['stock_quantity'] < 10 ? 'text-danger fw-bold' : ''; ?>">
                                <?php echo $item['stock_quantity']; ?> units
                            </span>
                        </td>
                        <td>
                            <span class="badge <?php echo $item['availability'] == 'Available' ? 'bg-success' : 'bg-danger'; ?> bg-opacity-10 text-<?php echo $item['availability'] == 'Available' ? 'success' : 'danger'; ?>">
                                <?php echo $item['availability']; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="?featured=<?php echo $item['id']; ?>" class="text-warning" style="font-size: 1.2rem;">
                                <i class="fas fa-star<?php echo $item['featured'] ? '' : '-o'; ?>"></i>
                            </a>
                        </td>
                        <td class="text-end pe-4">
                            <a href="menu_edit.php?id=<?php echo $item['id']; ?>" class="action-btn edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?toggle=<?php echo $item['id']; ?>" class="action-btn toggle ms-1" title="Toggle Availability">
                                <i class="fas fa-<?php echo $item['availability'] == 'Available' ? 'eye-slash' : 'eye'; ?>"></i>
                            </a>
                            <a href="?delete=<?php echo $item['id']; ?>" class="action-btn delete ms-1" title="Delete" onclick="return confirm('Delete this item?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
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
                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $category ? '&category='.urlencode($category) : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">Previous</a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $category ? '&category='.urlencode($category) : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $category ? '&category='.urlencode($category) : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">Next</a>
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
