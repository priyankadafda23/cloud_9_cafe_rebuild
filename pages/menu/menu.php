<?php
require_once '../../config/db_config.php';

// Get filter parameters
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Get all available menu items
$allItems = $db->select('menu_items', ['availability' => 'Available'], ['category' => 'ASC', 'name' => 'ASC']);

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

$items = array_values($allItems);

// Get categories from available items
$categories = [];
$allMenuItems = $db->select('menu_items', ['availability' => 'Available']);
foreach ($allMenuItems as $item) {
    if (!in_array($item['category'], $categories)) {
        $categories[] = $item['category'];
    }
}
sort($categories);

$title = "Menu - Cloud 9 Cafe";
ob_start();
?>

<!-- Page Header -->
<section class="py-5" style="background: linear-gradient(135deg, var(--cafe-primary) 0%, var(--cafe-primary-dark) 100%);">
    <div class="container">
        <div class="row justify-content-center text-center text-white">
            <div class="col-lg-8 animate-fade-in-up">
                <h1 class="fw-bold mb-3">Our Menu</h1>
                <p class="lead opacity-75 mb-0">Discover our handcrafted selection of premium coffees, delicious snacks, and delightful desserts.</p>
            </div>
        </div>
    </div>
</section>

<!-- Menu Section -->
<section class="py-5" style="background: var(--bg-cream);">
    <div class="container">
        <!-- Filters -->
        <div class="row g-3 mb-5">
            <div class="col-lg-6">
                <form method="GET" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search menu..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <?php if ($category): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search || $category): ?>
                    <a href="menu.php" class="btn btn-outline-secondary">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-lg-6">
                <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                    <a href="menu.php" class="btn <?php echo !$category ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                        All
                    </a>
                    <?php foreach ($categories as $cat): ?>
                    <a href="?category=<?php echo urlencode($cat); ?>" class="btn <?php echo $category == $cat ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                        <?php echo $cat; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Menu Grid -->
        <?php if (empty($items)): ?>
        <div class="text-center py-5 animate-fade-in">
            <div class="mb-4">
                <i class="fas fa-search fa-4x text-muted"></i>
            </div>
            <h4 class="fw-bold mb-2">No items found</h4>
            <p class="text-muted mb-4">Try adjusting your search or filter criteria.</p>
            <a href="menu.php" class="btn btn-primary">View All Items</a>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php 
            $delay = 0;
            foreach ($items as $item): 
                $delay = ($delay + 1) % 5;
            ?>
            <div class="col-md-6 col-lg-4 col-xl-3 animate-on-scroll stagger-<?php echo $delay; ?>">
                <div class="card product-card card-hover h-100">
                    <div class="product-image" style="position: relative;">
                        <?php 
                        // Check image in priority order: new path -> old path -> placeholder
                        $image_path = '';
                        if ($item['image']) {
                            if (file_exists("../../assets/uploads/menu_images/{$item['id']}/" . basename($item['image']))) {
                                $image_path = "../../assets/uploads/menu_images/{$item['id']}/" . basename($item['image']);
                            } elseif (file_exists("../../assets/images/{$item['image']}")) {
                                $image_path = "../../assets/images/{$item['image']}";
                            }
                        }
                        ?>
                        <?php if ($image_path): ?>
                        <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <?php endif; ?>
                        <!-- Placeholder shown when no image or on error -->
                        <div class="product-placeholder" style="display: <?php echo $image_path ? 'none' : 'flex'; ?>; 
                             position: absolute; top: 0; left: 0; right: 0; bottom: 0; 
                             background: linear-gradient(135deg, var(--cafe-primary-light) 0%, var(--cafe-primary) 100%);
                             align-items: center; justify-content: center; flex-direction: column;">
                            <i class="fas fa-coffee fa-4x text-white mb-2"></i>
                            <span class="text-white small">Cloud 9 Cafe</span>
                        </div>
                        <div class="product-overlay">
                            <form method="POST" action="../../user/cart.php" class="d-inline">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" name="add_to_cart" class="btn btn-accent rounded-pill">
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                            </form>
                        </div>
                        <?php if ($item['featured']): ?>
                        <span class="badge bg-accent text-dark position-absolute top-0 end-0 m-3">
                            <i class="fas fa-star me-1"></i>Featured
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.7rem;">
                                <?php echo $item['category']; ?>
                            </span>
                            <?php if ($item['stock_quantity'] < 10): ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger" style="font-size: 0.7rem;">
                                Only <?php echo $item['stock_quantity']; ?> left
                            </span>
                            <?php endif; ?>
                        </div>
                        <h5 class="product-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo htmlspecialchars($item['description']); ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">₹<?php echo number_format($item['price'], 2); ?></span>
                            <a href="menu_item_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                Details <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5">
    <div class="container">
        <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, var(--cafe-accent) 0%, #E8C9A0 100%);">
            <div class="card-body p-5 text-center">
                <h3 class="fw-bold mb-3" style="color: var(--cafe-primary-dark);">
                    <i class="fas fa-percent me-2"></i>Special Offer
                </h3>
                <p class="mb-4" style="color: var(--cafe-primary-dark);">Get 10% off when you order 3 or more items. Use code <strong>BUNDLE10</strong></p>
                <a href="../../user/cart.php" class="btn btn-primary btn-lg">
                    View Cart <i class="fas fa-shopping-cart ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include '../../includes/layout.php';
?>
