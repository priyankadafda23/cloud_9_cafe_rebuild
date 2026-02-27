<?php
require_once '../config/db_config.php';

// Check if user is logged in
if (!$auth->isUserLoggedIn()) {
    header("Location: login.php");
    exit();
}

$title = "My Favorites - Cloud 9 Cafe";
$active_sidebar = 'wishlist';
ob_start();
?>
<style>
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="card border-0 shadow-lg mb-4">
    <div class="card-body p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h2 class="fw-bold mb-0 text-primary">My Favorites</h2>
            <a href="../pages/menu/menu.php" class="btn btn-outline-primary rounded-pill px-4">
                <i class="fas fa-utensils me-2"></i>Browse Menu
            </a>
        </div>

        <!-- Empty State -->
        <div class="text-center py-5">
            <i class="fas fa-heart fa-4x text-muted mb-3"></i>
            <h4>No favorites yet</h4>
            <p class="text-muted">Start adding items you love!</p>
            <a href="../pages/menu/menu.php" class="btn btn-primary mt-2">
                <i class="fas fa-utensils me-2"></i>Explore Menu
            </a>
        </div>
    </div>
</div>

<?php
$dashboard_content = ob_get_clean();
include '../includes/dashboard_layout.php';
?>
