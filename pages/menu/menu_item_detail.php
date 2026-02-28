<?php
require_once '../../config/db_config.php';

// Get product ID from URL
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$error = '';
$product = null;

// Fetch product from database
if ($productId > 0) {
    $product = $db->selectOne('menu_items', ['id' => $productId]);
}

// Check if product exists and is available
if (!$product) {
    $error = 'Product not found. The item you are looking for does not exist or has been removed.';
} elseif ($product['availability'] !== 'Available') {
    $error = 'This product is currently not available.';
}

// Set page title
$title = $product ? htmlspecialchars($product['name']) . ' - Cloud 9 Cafe' : 'Product Not Found - Cloud 9 Cafe';

ob_start();
?>

<?php if ($error): ?>
<!-- Error State -->
<section class="py-5" style="background: var(--bg-cream); min-height: 60vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center animate-fade-in-up">
                <div class="card border-0 shadow-lg p-5">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 100px; height: 100px; background: var(--danger-light);">
                            <i class="fas fa-exclamation-triangle fa-3x" style="color: var(--danger);"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-3">Oops!</h2>
                    <p class="text-muted mb-4"><?php echo htmlspecialchars($error); ?></p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="menu.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-utensils me-2"></i>Browse Menu
                        </a>
                        <a href="../index.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-home me-2"></i>Go Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php else: ?>
<!-- Product Detail -->
<section class="py-5" style="background: var(--bg-cream);">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4 animate-on-scroll">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="menu.php">Menu</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>
        
        <div class="row g-5">
            <!-- Product Images -->
            <div class="col-lg-6 animate-on-scroll">
                <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: var(--radius-lg);">
                    <!-- Main Product Image -->
                    <div class="position-relative" style="min-height: 400px; background: linear-gradient(135deg, var(--cafe-primary-light) 0%, var(--cafe-primary) 100%);">
                        <img 
                            src="<?php echo !empty($product['image']) ? '/' . htmlspecialchars($product['image']) : ''; ?>" 
                            class="img-fluid w-100" 
                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                            style="min-height: 400px; object-fit: cover;"
                            onerror="this.style.display='none'; document.getElementById('imagePlaceholder').style.display='flex';"
                            onload="if(this.naturalWidth === 0 || this.naturalHeight === 0) { this.style.display='none'; document.getElementById('imagePlaceholder').style.display='flex'; }"
                        >
                        <!-- Image Placeholder (shown when image fails to load) -->
                        <div id="imagePlaceholder" class="position-absolute top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center flex-column text-white" style="background: linear-gradient(135deg, var(--cafe-primary) 0%, var(--cafe-primary-dark) 100%);">
                            <i class="fas fa-mug-hot fa-5x mb-3 opacity-75"></i>
                            <h4 class="fw-bold"><?php echo htmlspecialchars($product['category']); ?></h4>
                            <p class="mb-0 opacity-75"><?php echo htmlspecialchars($product['name']); ?></p>
                        </div>
                        
                        <!-- Badges -->
                        <?php if ($product['featured']): ?>
                        <span class="badge bg-accent text-dark position-absolute top-0 start-0 m-3 px-3 py-2">
                            <i class="fas fa-star me-1"></i>Featured
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($product['stock_quantity'] < 10 && $product['stock_quantity'] > 0): ?>
                        <span class="badge bg-warning position-absolute top-0 end-0 m-3 px-3 py-2">
                            <i class="fas fa-exclamation-circle me-1"></i>Only <?php echo $product['stock_quantity']; ?> left
                        </span>
                        <?php elseif ($product['stock_quantity'] <= 0): ?>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-3 px-3 py-2">
                            <i class="fas fa-times-circle me-1"></i>Out of Stock
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6 animate-on-scroll stagger-1">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 p-lg-5">
                        <!-- Category Badge -->
                        <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2">
                            <?php echo htmlspecialchars($product['category']); ?>
                        </span>
                        
                        <!-- Product Name -->
                        <h1 class="fw-bold mb-3 text-dark" style="font-size: 2.5rem;">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h1>
                        
                        <!-- Rating -->
                        <div class="d-flex align-items-center mb-4">
                            <div class="text-warning me-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="text-muted small">4.5 (120+ reviews)</span>
                        </div>
                        
                        <!-- Price -->
                        <h2 class="fw-bold text-primary mb-4" style="font-size: 2rem;">
                            ₹<?php echo number_format($product['price'], 2); ?>
                        </h2>
                        
                        <!-- Description -->
                        <p class="text-secondary lh-lg mb-4">
                            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                        </p>
                        
                        <!-- Stock Info -->
                        <div class="d-flex align-items-center mb-4 p-3 rounded" style="background: var(--bg-cream);">
                            <i class="fas fa-box text-primary me-3 fa-lg"></i>
                            <div>
                                <span class="fw-medium">Availability:</span>
                                <?php if ($product['stock_quantity'] > 10): ?>
                                    <span class="text-success fw-bold">In Stock (<?php echo $product['stock_quantity']; ?> available)</span>
                                <?php elseif ($product['stock_quantity'] > 0): ?>
                                    <span class="text-warning fw-bold">Low Stock (<?php echo $product['stock_quantity']; ?> left)</span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Add to Cart Section -->
                        <?php if ($product['stock_quantity'] > 0): ?>
                        <div class="mb-4">
                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <div class="input-group input-group-lg">
                                        <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(-1)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" readonly>
                                        <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(1)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <button class="btn btn-primary btn-lg w-100" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>')">
                                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="mb-4">
                            <button class="btn btn-secondary btn-lg w-100" disabled>
                                <i class="fas fa-times-circle me-2"></i>Out of Stock
                            </button>
                        </div>
                        <?php endif; ?>

                        <!-- Additional Info Accordion -->
                        <div class="accordion" id="productInfo">
                            <div class="accordion-item border-0 mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetails">
                                        <i class="fas fa-info-circle me-2 text-primary"></i>Product Details
                                    </button>
                                </h2>
                                <div id="collapseDetails" class="accordion-collapse collapse show" data-bs-parent="#productInfo">
                                    <div class="accordion-body text-secondary">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2 d-flex justify-content-between">
                                                <span class="text-muted">Category:</span>
                                                <span class="fw-medium"><?php echo htmlspecialchars($product['category']); ?></span>
                                            </li>
                                            <li class="d-flex justify-content-between">
                                                <span class="text-muted">Availability:</span>
                                                <span class="fw-medium"><?php echo $product['availability']; ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Back to Menu Button -->
        <div class="row mt-5">
            <div class="col-12 text-center animate-on-scroll">
                <a href="menu.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Back to Menu
                </a>
            </div>
        </div>
    </div>
</section>

<?php endif; ?>

<!-- Toast Notification -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="cartToast" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>
                <span id="cartToastMessage">Added to cart!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<style>
/* Breadcrumb styling */
.breadcrumb {
    background: white;
    padding: 1rem 1.5rem;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
}

.breadcrumb-item a {
    color: var(--cafe-primary);
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: var(--cafe-primary-dark);
}

.breadcrumb-item.active {
    color: var(--text-medium);
}

/* Quantity input styling */
#quantity {
    font-weight: 600;
    font-size: 1.1rem;
}

/* Accordion styling */
.accordion-button {
    background: var(--bg-cream);
    border-radius: var(--radius-md) !important;
}

.accordion-button:not(.collapsed) {
    background: var(--cafe-primary);
    color: white;
}

.accordion-button:not(.collapsed)::after {
    filter: invert(1);
}

.accordion-button:focus {
    box-shadow: none;
}

/* Toast animation */
.toast {
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<script>
// Update quantity function
function updateQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    const maxQuantity = parseInt(quantityInput.getAttribute('max'));
    let newValue = parseInt(quantityInput.value) + change;
    
    if (newValue >= 1 && newValue <= maxQuantity) {
        quantityInput.value = newValue;
    }
}

// Add to cart function
function addToCart(itemId, itemName) {
    const quantity = parseInt(document.getElementById('quantity').value);
    const submitBtn = document.querySelector('button[onclick^="addToCart"]');
    const originalHtml = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
    
    // Make AJAX request
    fetch('../../api/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            item_id: itemId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        // Check if redirect is needed (user not logged in)
        if (data.redirect) {
            // Redirect to login page with return URL
            window.location.href = '../../auth/login.php?redirect=' + encodeURIComponent(window.location.href);
            return;
        }
        
        const cartToast = new bootstrap.Toast(document.getElementById('cartToast'));
        const cartToastMessage = document.getElementById('cartToastMessage');
        
        if (data.success) {
            // Success - item added to cart
            cartToastMessage.textContent = data.message + ' (' + data.item_name + ' x' + quantity + ')';
            document.getElementById('cartToast').classList.remove('bg-danger');
            document.getElementById('cartToast').classList.add('bg-success');
            cartToast.show();
            
            // Update navbar cart count
            const navbarCartCount = document.getElementById('navbarCartCount');
            if (navbarCartCount) {
                navbarCartCount.textContent = data.cart_count;
            }
        } else {
            // Error
            cartToastMessage.textContent = data.message;
            document.getElementById('cartToast').classList.remove('bg-success');
            document.getElementById('cartToast').classList.add('bg-danger');
            cartToast.show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const cartToast = new bootstrap.Toast(document.getElementById('cartToast'));
        const cartToastMessage = document.getElementById('cartToastMessage');
        cartToastMessage.textContent = 'Something went wrong. Please try again.';
        document.getElementById('cartToast').classList.remove('bg-success');
        document.getElementById('cartToast').classList.add('bg-danger');
        cartToast.show();
    })
    .finally(() => {
        // Restore button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHtml;
        
        // Reset toast color after 3 seconds
        setTimeout(() => {
            document.getElementById('cartToast').classList.remove('bg-danger');
            document.getElementById('cartToast').classList.add('bg-success');
        }, 3000);
    });
}
</script>

<?php
$content = ob_get_clean();
include '../../includes/layout.php';
?>
