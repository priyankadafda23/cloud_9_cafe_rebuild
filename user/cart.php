<?php
require_once '../config/db_config.php';

// Check if user is logged in
if (!$auth->isUserLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $auth->getUserId();

// Handle cart actions
if (isset($_GET['remove_item'])) {
    $item_id = intval($_GET['remove_item']);
    $db->delete('cafe_cart', ['id' => $item_id, 'user_id' => $user_id]);
    header("Location: cart.php");
    exit();
}

if (isset($_GET['clear_cart'])) {
    $db->delete('cafe_cart', ['user_id' => $user_id]);
    header("Location: cart.php");
    exit();
}

if (isset($_GET['update_qty']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = intval($_GET['update_qty']);
    $quantity = intval($_POST['quantity']);
    if ($quantity > 0) {
        $db->update('cafe_cart', ['quantity' => $quantity], ['id' => $item_id, 'user_id' => $user_id]);
    }
    header("Location: cart.php");
    exit();
}

// Handle add to cart from menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $menu_item_id = intval($_POST['item_id']);
    $quantity = intval($_POST['quantity'] ?? 1);
    
    // Check if item already in cart
    $existing = $db->selectOne('cafe_cart', ['user_id' => $user_id, 'menu_item_id' => $menu_item_id]);
    
    if ($existing) {
        $newQty = $existing['quantity'] + $quantity;
        $db->update('cafe_cart', ['quantity' => $newQty], ['id' => $existing['id']]);
    } else {
        $db->insert('cafe_cart', [
            'user_id' => $user_id,
            'menu_item_id' => $menu_item_id,
            'quantity' => $quantity,
            'customization' => ''
        ]);
    }
    header("Location: cart.php");
    exit();
}

// Get cart items
$cart_items = [];
$cartData = $db->select('cafe_cart', ['user_id' => $user_id]);

$total_amount = 0;
$item_count = 0;

foreach ($cartData as $cartItem) {
    $menuItem = $db->selectOne('menu_items', ['id' => $cartItem['menu_item_id']]);
    if ($menuItem) {
        $cart_items[] = array_merge($cartItem, $menuItem);
        $total_amount += $menuItem['price'] * $cartItem['quantity'];
        $item_count += $cartItem['quantity'];
    }
}

// Get user address
$user = $db->selectOne('cafe_users', ['id' => $user_id]);
$user_address = $user['address'] ?? '';

$title = "Order Cart - Cloud 9 Cafe";
ob_start();
?>

<style>
    .cart-item {
        transition: background 0.3s ease;
    }

    .cart-item:hover {
        background: rgba(102, 126, 234, 0.02);
    }

    .quantity-btn {
        width: 35px;
        height: 35px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .empty-cart {
        text-align: center;
        padding: 4rem 2rem;
    }
    
    .empty-cart i {
        font-size: 5rem;
        color: #dee2e6;
        margin-bottom: 1.5rem;
    }
</style>

<div class="container fade-in-up">
    <?php if (empty($cart_items)): ?>
    <!-- Empty Cart -->
    <div class="card border-0 shadow-sm">
        <div class="card-body empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h3 class="fw-bold mb-3">Your Cart is Empty</h3>
            <p class="text-muted mb-4">Looks like you haven't added any items yet.</p>
            <a href="../pages/menu/menu.php" class="btn btn-gradient btn-lg px-5">
                <i class="fas fa-utensils me-2"></i>Browse Menu
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0 text-white">Shopping Cart <span class="fs-4 text-white">(<?php echo $item_count; ?> items)</span></h2>
                <a href="?clear_cart=1" class="btn text-white btn-danger btn-sm p-2" onclick="return confirm('Clear your cart?');">
                    <i class="fas fa-trash me-2"></i>Clear Cart
                </a>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <?php foreach ($cart_items as $index => $item): ?>
                    <div class="cart-item p-4 <?php echo $index < count($cart_items) - 1 ? 'border-bottom' : ''; ?>">
                        <div class="row align-items-center g-3">
                            <div class="col-md-2">
                                <img src="<?php echo $item['image'] ? '../assets/images/' . $item['image'] : '../assets/images/product-1.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="img-fluid rounded shadow-sm"
                                     style="background-color: #f8f9fa; height: 80px; object-fit: cover;">
                            </div>
                            <div class="col-md-4">
                                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h5>
                                <?php if ($item['customization']): ?>
                                <p class="text-muted small mb-2"><i class="fas fa-info-circle me-1"></i><?php echo htmlspecialchars($item['customization']); ?></p>
                                <?php endif; ?>
                                <a href="?remove_item=<?php echo $item['id']; ?>" class="btn btn-link text-danger p-0 small">
                                    <i class="fas fa-trash-alt me-1"></i>Remove
                                </a>
                            </div>
                            <div class="col-md-3">
                                <form method="post" action="?update_qty=<?php echo $item['id']; ?>" class="d-inline">
                                    <div class="input-group input-group-sm" style="width: 130px;">
                                        <button type="button" class="btn btn-outline-secondary quantity-btn" onclick="this.parentNode.querySelector('input').stepDown(); this.form.submit();">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" name="quantity" class="form-control text-center bg-white" 
                                               value="<?php echo $item['quantity']; ?>" min="1" max="99" 
                                               onchange="this.form.submit()">
                                        <button type="button" class="btn btn-outline-secondary quantity-btn" onclick="this.parentNode.querySelector('input').stepUp(); this.form.submit();">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-3 text-end">
                                <h5 class="fw-bold mb-0 text-dark">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></h5>
                                <small class="text-muted">₹<?php echo number_format($item['price'], 2); ?> each</small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="../pages/menu/menu.php" class="btn btn-gradient w-30 py-3 fw-bold shadow-sm mb-2">
                    <i class="fas fa-arrow-left me-2 text-white"></i>Continue Shopping
                </a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-lg sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">Order Summary</h4>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal (<?php echo $item_count; ?> items)</span>
                        <span class="fw-semibold">₹<?php echo number_format($total_amount, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Tax (8%)</span>
                        <span class="fw-semibold">₹<?php echo number_format($total_amount * 0.08, 2); ?></span>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-4 text-primary">₹<?php echo number_format($total_amount * 1.08, 2); ?></span>
                    </div>

                    <button class="btn btn-gradient w-100 py-3 fw-bold shadow-sm mb-2" data-bs-toggle="modal"
                        data-bs-target="#checkoutModal" <?php echo empty($cart_items) ? 'disabled' : ''; ?>>
                        <i class="fas fa-lock me-2"></i>Proceed to Checkout
                    </button>
                    <p class="text-center text-muted small mb-0">
                        <i class="fas fa-shield-alt me-1"></i>Secure checkout
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Complete Your Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="checkout.php">
                <div class="modal-body p-4">
                    <!-- Delivery Address -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>Delivery Address
                        </label>
                        <textarea name="delivery_address" class="form-control" rows="3" required
                            placeholder="Enter your delivery address"><?php echo htmlspecialchars($user_address); ?></textarea>
                    </div>
                    
                    <!-- Order Note -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-sticky-note me-2 text-primary"></i>Order Note (Optional)
                        </label>
                        <textarea name="order_note" class="form-control" rows="2"
                            placeholder="Any special instructions? (e.g., less sugar, extra spicy)"></textarea>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-credit-card me-2 text-primary"></i>Payment Method
                        </label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check card p-3 border">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay_cash" value="Cash" checked>
                                    <label class="form-check-label w-100" for="pay_cash">
                                        <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                        <strong>Cash on Delivery</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check card p-3 border">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay_card" value="Card">
                                    <label class="form-check-label w-100" for="pay_card">
                                        <i class="fas fa-credit-card me-2 text-primary"></i>
                                        <strong>Card Payment</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary in Modal -->
                    <div class="bg-light p-3 rounded">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>₹<?php echo number_format($total_amount, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (8%):</span>
                            <span>₹<?php echo number_format($total_amount * 0.08, 2); ?></span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span class="text-primary">₹<?php echo number_format($total_amount * 1.08, 2); ?></span>
                        </div>
                    </div>
                    
                    <!-- Reward Points Notice -->
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-star me-2"></i>
                        <strong>Earn 10 Reward Points!</strong> Complete this order to earn points.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-cancel py-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="place_order" class="btn btn-gradient py-3 px-5">
                        <i class="fas fa-check me-2"></i>Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$dashboard_content = ob_get_clean();
include '../includes/dashboard_layout.php';
?>
