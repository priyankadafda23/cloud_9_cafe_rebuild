<?php
/**
 * =============================================================================
 * CLOUD 9 CAFE - HOMEPAGE
 * =============================================================================
 * 
 * ROLE: This is the main landing page of the Cloud 9 Cafe website.
 *       It displays the hero section, features, popular menu items,
 *       testimonials, and a call-to-action section.
 * 
 * FEATURES:
 * - Hero banner with call-to-action buttons
 * - Features section highlighting cafe strengths
 * - Popular Picks menu items with "Add to Cart" functionality
 * - Customer testimonials
 * - AJAX-based add to cart with toast notifications
 * 
 * FLOW: 1. Includes database config
 *       2. Fetches featured menu items from database
 *       3. Outputs HTML content
 *       4. Includes layout.php to wrap content with navbar/footer
 */

// =============================================================================
// SECTION: Database Configuration Include
// DESCRIPTION: Includes database config which initializes JsonDB and TokenAuth
// =============================================================================
require_once '../config/db_config.php';
// =============================================================================
// END SECTION: Database Configuration Include
// =============================================================================

// =============================================================================
// SECTION: Page Title & Featured Items Fetching
// DESCRIPTION: Set page title and fetch featured menu items from database
// =============================================================================
$title = "Home - Cloud 9 Cafe";  // Page title shown in browser tab

// Fetch featured menu items from database
// FUNCTION: $db->select() - Fetches records matching conditions
// PARAMETERS: 
//   - 'menu_items' = table name
//   - ['featured' => 1, 'availability' => 'Available'] = WHERE conditions
//   - null = no ORDER BY
//   - 4 = LIMIT (fetch only 4 items)
$popular_items = $db->select('menu_items', ['featured' => 1, 'availability' => 'Available'], null, 4);

// If no featured items in database, use default hardcoded items
if (empty($popular_items)) {
    $popular_items = [
        ['id' => 1, 'name' => 'Caramel Macchiato', 'price' => 450, 'category' => 'Coffee', 'image' => 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=400'],
        ['id' => 2, 'name' => 'Cappuccino', 'price' => 380, 'category' => 'Coffee', 'image' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400'],
        ['id' => 3, 'name' => 'Chocolate Croissant', 'price' => 280, 'category' => 'Snack', 'image' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400'],
        ['id' => 4, 'name' => 'Cheesecake', 'price' => 420, 'category' => 'Dessert', 'image' => 'https://images.unsplash.com/photo-1524351199678-941a58a3df26?w=400'],
    ];
}
// =============================================================================
// END SECTION: Page Title & Featured Items Fetching
// =============================================================================

// Start output buffering to capture content for layout
ob_start();
?>

<!-- ========================================================================= -->
<!-- SECTION: Hero Banner -->
<!-- DESCRIPTION: Full-width hero section with headline, description, CTAs, and stats -->
<!-- ========================================================================= -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-80">
            <!-- Hero Content - Text and CTAs -->
            <div class="col-lg-6 hero-content animate-fade-in-up">
                <span class="badge bg-accent text-dark mb-3 px-3 py-2">
                    <i class="fas fa-star me-1"></i>Best Coffee in Town
                </span>
                <h1 class="hero-title">
                    Experience the <span>Perfect</span> Cup of Coffee
                </h1>
                <p class="hero-subtitle">
                    Discover our handcrafted beverages made with premium beans sourced from the world's finest coffee regions. Every sip tells a story.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <!-- CTA: Explore Menu - Click redirects to: menu/menu.php -->
                    <a href="menu/menu.php" class="btn btn-accent btn-lg">
                        <i class="fas fa-coffee me-2"></i>Explore Menu
                    </a>
                    <!-- CTA: Learn More - Click redirects to: about.php -->
                    <a href="about.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-play-circle me-2"></i>Learn More
                    </a>
                </div>
                
                <!-- Stats Row -->
                <div class="row mt-5 pt-3">
                    <div class="col-4">
                        <h4 class="fw-bold text-white mb-1">15K+</h4>
                        <small class="text-white-50">Happy Customers</small>
                    </div>
                    <div class="col-4">
                        <h4 class="fw-bold text-white mb-1">50+</h4>
                        <small class="text-white-50">Menu Items</small>
                    </div>
                    <div class="col-4">
                        <h4 class="fw-bold text-white mb-1">4.9</h4>
                        <small class="text-white-50">Rating</small>
                    </div>
                </div>
            </div>
            
            <!-- Hero Image - Hidden on mobile -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-image floating text-center">
                    <img src="../assets/images/hero-coffee.png" alt="Coffee" class="img-fluid rounded-4 shadow-lg" style="max-height: 500px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600'">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Decorative gradient overlay at bottom -->
    <div class="position-absolute bottom-0 start-0 w-100" style="height: 100px; background: linear-gradient(to top, var(--bg-cream), transparent);"></div>
</section>
<!-- ========================================================================= -->
<!-- END SECTION: Hero Banner -->
<!-- ========================================================================= -->

<!-- ========================================================================= -->
<!-- SECTION: Features Section -->
<!-- DESCRIPTION: Three feature cards highlighting cafe strengths -->
<!-- ========================================================================= -->
<section class="py-5 my-5">
    <div class="container">
        <!-- Section Header -->
        <div class="text-center mb-5 animate-on-scroll">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-3">Why Choose Us</span>
            <h2 class="fw-bold">The Cloud 9 Experience</h2>
            <p class="text-muted max-w-600 mx-auto">We're passionate about delivering exceptional coffee experiences that brighten your day.</p>
        </div>
        
        <div class="row g-4">
            <!-- Feature 1: Premium Beans -->
            <div class="col-md-4 animate-on-scroll stagger-1">
                <div class="card feature-card card-hover h-100">
                    <div class="icon-wrapper">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Premium Beans</h4>
                    <p class="text-muted mb-0">Ethically sourced from sustainable farms across Colombia, Ethiopia, and Brazil.</p>
                </div>
            </div>
            
            <!-- Feature 2: Freshly Roasted -->
            <div class="col-md-4 animate-on-scroll stagger-2">
                <div class="card feature-card card-hover h-100">
                    <div class="icon-wrapper" style="background: linear-gradient(135deg, #D4A574 0%, #E8C9A0 100%);">
                        <i class="fas fa-fire"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Freshly Roasted</h4>
                    <p class="text-muted mb-0">Small-batch roasting daily to ensure maximum flavor and aroma in every cup.</p>
                </div>
            </div>
            
            <!-- Feature 3: Fast Delivery -->
            <div class="col-md-4 animate-on-scroll stagger-3">
                <div class="card feature-card card-hover h-100">
                    <div class="icon-wrapper" style="background: linear-gradient(135deg, #27AE60 0%, #2ECC71 100%);">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Fast Delivery</h4>
                    <p class="text-muted mb-0">Hot and fresh delivered to your doorstep in 30 minutes or less. Guaranteed.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================================================================= -->
<!-- END SECTION: Features Section -->
<!-- ========================================================================= -->

<!-- ========================================================================= -->
<!-- SECTION: Popular Menu Items (Popular Picks) -->
<!-- DESCRIPTION: Grid of featured menu items with "Add to Cart" functionality -->
<!--              Uses AJAX to add items without page reload -->
<!-- ========================================================================= -->
<section class="py-5" style="background: white;">
    <div class="container">
        <!-- Section Header -->
        <div class="row align-items-center mb-5">
            <div class="col-md-8 animate-on-scroll">
                <span class="badge bg-accent text-dark mb-2">Our Menu</span>
                <h2 class="fw-bold mb-0">Popular Picks</h2>
                <p class="text-muted mb-0">Customer favorites that keep them coming back</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <!-- View Full Menu Button - Click redirects to: menu/menu.php -->
                <a href="menu/menu.php" class="btn btn-outline-primary">
                    View Full Menu <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
        
        <!-- Popular Items Grid -->
        <div class="row g-4">
            <?php 
            // Loop through popular items and display each as a card
            foreach ($popular_items as $index => $item): 
            ?>
            <div class="col-md-6 col-lg-3 animate-on-scroll stagger-<?php echo $index + 1; ?>">
                <div class="card product-card card-hover h-100">
                    <!-- Product Image Container -->
                    <div class="product-image">
                        <img src="<?php echo $item['image'] ?? 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400'; ?>" 
                             alt="<?php echo $item['name']; ?>" 
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <!-- Placeholder shown if image fails to load -->
                        <div class="product-placeholder" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, var(--cafe-primary-light) 0%, var(--cafe-primary) 100%); align-items: center; justify-content: center; flex-direction: column;">
                            <i class="fas fa-coffee fa-3x text-white mb-2"></i>
                            <span class="text-white small"><?php echo $item['category']; ?></span>
                        </div>
                        <!-- Add to Cart Overlay - Shows on hover -->
                        <div class="product-overlay">
                            <!-- Add to Cart Button - Triggers AJAX call to add item -->
                            <!-- data-item-id: ID of the menu item -->
                            <!-- data-item-name: Name of the item for toast message -->
                            <button class="btn btn-light rounded-pill add-to-cart-btn" 
                                    data-item-id="<?php echo $item['id']; ?>"
                                    data-item-name="<?php echo htmlspecialchars($item['name']); ?>">
                                <i class="fas fa-plus me-1"></i> Add to Cart
                            </button>
                        </div>
                        <!-- Category Badge -->
                        <span class="badge bg-primary position-absolute top-0 start-0 m-3"><?php echo $item['category']; ?></span>
                    </div>
                    <!-- Product Info -->
                    <div class="product-info">
                        <h5 class="product-title"><?php echo $item['name']; ?></h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">₹<?php echo number_format($item['price'], 0); ?></span>
                            <div class="text-warning small">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- ========================================================================= -->
<!-- END SECTION: Popular Menu Items (Popular Picks) -->
<!-- ========================================================================= -->

<!-- ========================================================================= -->
<!-- SECTION: Testimonials Section -->
<!-- DESCRIPTION: Customer reviews and feedback cards -->
<!-- ========================================================================= -->
<section class="py-5" style="background: white;">
    <div class="container">
        <!-- Section Header -->
        <div class="text-center mb-5 animate-on-scroll">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-3">Testimonials</span>
            <h2 class="fw-bold">What Our Customers Say</h2>
        </div>
        
        <div class="row g-4">
            <?php
            // Testimonial data array
            $testimonials = [
                ['name' => 'Sarah Johnson', 'role' => 'Coffee Enthusiast', 'text' => 'Best coffee shop in town! The atmosphere is amazing and the baristas really know their craft.', 'avatar' => 'SJ'],
                ['name' => 'Michael Chen', 'role' => 'Regular Customer', 'text' => 'I order from Cloud 9 every morning. Their delivery is always on time and the coffee is consistently great.', 'avatar' => 'MC'],
                ['name' => 'Emily Davis', 'role' => 'Food Blogger', 'text' => 'The desserts here are absolutely divine. Their cheesecake rivals any high-end bakery in the city.', 'avatar' => 'ED'],
            ];
            
            // Loop through testimonials
            foreach ($testimonials as $index => $t):
            ?>
            <div class="col-md-4 animate-on-scroll stagger-<?php echo $index + 1; ?>">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <div class="text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-muted mb-4">"<?php echo $t['text']; ?>"</p>
                    <div class="d-flex align-items-center mt-auto">
                        <!-- Avatar with initials -->
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px;">
                            <?php echo $t['avatar']; ?>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-0"><?php echo $t['name']; ?></h6>
                            <small class="text-muted"><?php echo $t['role']; ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- ========================================================================= -->
<!-- END SECTION: Testimonials Section -->
<!-- ========================================================================= -->

<!-- ========================================================================= -->
<!-- SECTION: Call to Action (CTA) -->
<!-- DESCRIPTION: Final section encouraging users to sign up or browse menu -->
<!-- ========================================================================= -->
<section class="py-5 my-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center animate-on-scroll">
                <h2 class="fw-bold mb-3">Ready to Experience Cloud 9?</h2>
                <p class="text-muted mb-4">Join thousands of coffee lovers who have made us their daily destination for premium coffee and delicious treats.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <!-- Create Account Button - Click redirects to: /auth/register.php -->
                    <a href="/cloud_9_cafe_rebuild/auth/register.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </a>
                    <!-- Browse Menu Button - Click redirects to: menu/menu.php -->
                    <a href="menu/menu.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-utensils me-2"></i>Browse Menu
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================================================================= -->
<!-- END SECTION: Call to Action (CTA) -->
<!-- ========================================================================= -->

<!-- ========================================================================= -->
<!-- SECTION: Toast Notification Container -->
<!-- DESCRIPTION: Hidden by default, shown via JavaScript when item added to cart -->
<!-- ========================================================================= -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="cartToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>
                <span id="cartToastMessage">Added to cart successfully!</span>
            </div>
            <!-- Close button -->
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<!-- ========================================================================= -->
<!-- END SECTION: Toast Notification Container -->
<!-- ========================================================================= -->

<!-- ========================================================================= -->
<!-- SECTION: Page Specific Styles -->
<!-- DESCRIPTION: Custom CSS specific to this homepage -->
<!-- ========================================================================= -->
<style>
    /* Minimum height utility */
    .min-vh-80 {
        min-height: 80vh;
    }
    
    /* Max width utility for text containers */
    .max-w-600 {
        max-width: 600px;
    }
    
    /* Semi-transparent white text */
    .text-white-50 {
        color: rgba(255, 255, 255, 0.6) !important;
    }
    
    /* Override body background for home page hero */
    body {
        background: var(--bg-cream);
    }
    
    /* Toast slide-in animation */
    .toast {
        animation: slideInRight 0.3s ease;
    }
    
    /* Keyframes for toast animation */
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
    
    /* Disabled state for add to cart button */
    .add-to-cart-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    /* Spinner inside button */
    .add-to-cart-btn .spinner-border {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }
</style>
<!-- ========================================================================= -->
<!-- END SECTION: Page Specific Styles -->
<!-- ========================================================================= -->

<!-- ========================================================================= -->
<!-- SECTION: Add to Cart JavaScript -->
<!-- DESCRIPTION: Handles AJAX requests when "Add to Cart" button is clicked -->
<!-- ========================================================================= -->
<script>
// Execute when DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Get all "Add to Cart" buttons
    const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');
    
    // Initialize Bootstrap toast component
    const cartToast = new bootstrap.Toast(document.getElementById('cartToast'));
    const cartToastMessage = document.getElementById('cartToastMessage');
    
    // Add click event listener to each button
    addToCartBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();  // Prevent default button behavior
            
            // Get item data from data attributes
            const itemId = this.dataset.itemId;      // Menu item ID
            const itemName = this.dataset.itemName;  // Item name for message
            
            // Disable button and show loading state
            this.disabled = true;
            const originalHtml = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...';
            
            // Make AJAX POST request to add item to cart
            // API Endpoint: ../api/add_to_cart.php
            fetch('../api/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item_id: itemId,
                    quantity: 1
                })
            })
            .then(response => response.json())  // Parse JSON response
            .then(data => {
                if (data.success) {
                    // SUCCESS: Item added to cart
                    // Show success toast with item name
                    cartToastMessage.textContent = data.message + ' (' + data.item_name + ')';
                    cartToast.show();
                    
                    // Update cart badge count in navbar
                    const cartBadges = document.querySelectorAll('.cart-badge');
                    cartBadges.forEach(badge => {
                        badge.textContent = data.cart_count;
                    });
                    
                    // Update navbar cart count specifically
                    const navbarCartCount = document.getElementById('navbarCartCount');
                    if (navbarCartCount) {
                        navbarCartCount.textContent = data.cart_count;
                    }
                } else {
                    // FAILED: Check if redirect needed (not logged in)
                    if (data.redirect) {
                        // User not logged in - redirect to login page
                        window.location.href = data.redirect;
                    } else {
                        // Show error message in toast
                        cartToastMessage.textContent = data.message;
                        document.getElementById('cartToast').classList.remove('bg-success');
                        document.getElementById('cartToast').classList.add('bg-danger');
                        cartToast.show();
                    }
                }
            })
            .catch(error => {
                // ERROR: Network or parsing error
                console.error('Error:', error);
                cartToastMessage.textContent = 'Something went wrong. Please try again.';
                document.getElementById('cartToast').classList.remove('bg-success');
                document.getElementById('cartToast').classList.add('bg-danger');
                cartToast.show();
            })
            .finally(() => {
                // Restore button to original state
                this.disabled = false;
                this.innerHTML = originalHtml;
                
                // Reset toast color after 3 seconds
                setTimeout(() => {
                    document.getElementById('cartToast').classList.remove('bg-danger');
                    document.getElementById('cartToast').classList.add('bg-success');
                }, 3000);
            });
        });
    });
});
</script>
<!-- ========================================================================= -->
<!-- END SECTION: Add to Cart JavaScript -->
<!-- ========================================================================= -->

<?php
// =============================================================================
// SECTION: Include Layout Wrapper
// DESCRIPTION: Capture output buffer and include the main layout file
//              which wraps this content with navbar and footer
// =============================================================================
$content = ob_get_clean();  // Get buffered content and clear buffer
include '../includes/layout.php';  // Include layout wrapper
// =============================================================================
// END SECTION: Include Layout Wrapper
// =============================================================================
?>
