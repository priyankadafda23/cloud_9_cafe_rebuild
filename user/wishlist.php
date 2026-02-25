<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Check if user is logged in using cafe_user_id
if (!isset($_SESSION['cafe_user_id'])) {
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
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0 text-white">My Wishlist <span class="text-white fs-4">(4 items)</span></h2>
    <button class="btn btn-sm rounded-pill text-white" data-bs-toggle="modal" data-bs-target="#clearWishlistModal">
        <i class="fas fa-trash-alt me-2"></i>Clear Wishlist
    </button>
</div>

<div class="row g-4">
    <!-- Wishlist Item 1 -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 product-card">
            <div class="position-relative">
                <img src="../assets/images/product-1.jpg" class="card-img-top p-4 bg-light" alt="Product"
                    style="height: 200px; object-fit: contain;">
                <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle"
                    data-bs-toggle="modal" data-bs-target="#removeWishlistModal" title="Remove from wishlist">
                    <i class="fas fa-times"></i>
                </button>
                <button class="btn btn-light btn-sm position-absolute bottom-0 end-0 m-2 rounded-circle"
                    title="Move to cart">
                    <i class="fas fa-shopping-cart"></i>
                </button>
            </div>
            <div class="card-body">
                <h6 class="fw-bold mb-1">Wireless Headphones</h6>
                <p class="text-muted small mb-2">Electronics</p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 mb-0 fw-bold" style="color: #667eea;">₹9,999</span>
                    <span class="text-decoration-line-through text-muted small">₹12,499</span>
                </div>
                <button class="btn btn-gradient w-100 mt-3 btn-sm">
                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                </button>
            </div>
        </div>
    </div>

    <!-- Wishlist Item 2 -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 product-card">
            <div class="position-relative">
                <img src="../assets/images/product-2.jpg" class="card-img-top p-4 bg-light" alt="Product"
                    style="height: 200px; object-fit: contain;">
                <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle"
                    data-bs-toggle="modal" data-bs-target="#removeWishlistModal" title="Remove from wishlist">
                    <i class="fas fa-times"></i>
                </button>
                <button class="btn btn-light btn-sm position-absolute bottom-0 end-0 m-2 rounded-circle"
                    title="Move to cart">
                    <i class="fas fa-shopping-cart"></i>
                </button>
            </div>
            <div class="card-body">
                <h6 class="fw-bold mb-1">Smart Fitness Watch</h6>
                <p class="text-muted small mb-2">Wearables</p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 mb-0 fw-bold" style="color: #667eea;">₹14,999</span>
                </div>
                <button class="btn btn-gradient w-100 mt-3 btn-sm">
                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                </button>
            </div>
        </div>
    </div>

    <!-- Wishlist Item 3 -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 product-card">
            <div class="position-relative">
                <img src="../assets/images/product-3.jpg" class="card-img-top p-4 bg-light" alt="Product"
                    style="height: 200px; object-fit: contain;">
                <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle"
                    data-bs-toggle="modal" data-bs-target="#removeWishlistModal" title="Remove from wishlist">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="card-body">
                <h6 class="fw-bold mb-1">Premium Backpack</h6>
                <p class="text-muted small mb-2">Accessories</p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 mb-0 fw-bold" style="color: #667eea;">₹7,499</span>
                    <span class="text-decoration-line-through text-muted small">₹8,999</span>
                </div>
                <button class="btn btn-gradient w-100 mt-3 btn-sm">
                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                </button>
            </div>
        </div>
    </div>

    <!-- Wishlist Item 4 -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 product-card">
            <div class="position-relative">
                <img src="../assets/images/product-4.jpg" class="card-img-top p-4 bg-light" alt="Product"
                    style="height: 200px; object-fit: contain;">
                <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle"
                    data-bs-toggle="modal" data-bs-target="#removeWishlistModal" title="Remove from wishlist">
                    <i class="fas fa-times"></i>
                </button>
                <button class="btn btn-light btn-sm position-absolute bottom-0 end-0 m-2 rounded-circle"
                    title="Move to cart">
                    <i class="fas fa-shopping-cart"></i>
                </button>
            </div>
            <div class="card-body">
                <h6 class="fw-bold mb-1">Bluetooth Speaker</h6>
                <p class="text-muted small mb-2">Audio</p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 mb-0 fw-bold" style="color: #667eea;">₹6,299</span>
                </div>
                <button class="btn btn-gradient w-100 mt-3 btn-sm">
                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Remove from Wishlist Modal -->
<div class="modal fade" id="removeWishlistModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-5 text-center">
                <div class="mb-4">
                    <i class="fas fa-heart-broken fa-4x text-danger"></i>
                </div>
                <h4 class="fw-bold mb-3">Remove from Wishlist?</h4>
                <p class="text-muted mb-4">Are you sure you want to remove this item from your wishlist?</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-danger py-3">Yes, Remove</button>
                    <button type="button" class="btn btn-cancel py-3" data-bs-dismiss="modal">Keep it</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clear Wishlist Modal -->
<div class="modal fade" id="clearWishlistModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-5 text-center">
                <div class="mb-4">
                    <i class="fas fa-trash-alt fa-4x text-danger"></i>
                </div>
                <h4 class="fw-bold mb-3">Clear Entire Wishlist?</h4>
                <p class="text-muted mb-4">This will remove all items from your wishlist. This action cannot be undone.
                </p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-danger py-3">Yes, Clear All</button>
                    <button type="button" class="btn btn-cancel py-3" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$dashboard_content = ob_get_clean();
include '../includes/dashboard_layout.php';
?>
