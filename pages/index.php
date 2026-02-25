<?php
$title = "Home - Cloud 9 Cafe";
ob_start();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-80">
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
                    <a href="menu/menu.php" class="btn btn-accent btn-lg">
                        <i class="fas fa-coffee me-2"></i>Explore Menu
                    </a>
                    <a href="about.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-play-circle me-2"></i>Learn More
                    </a>
                </div>
                
                <!-- Stats -->
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
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-image floating text-center">
                    <img src="../assets/images/hero-coffee.png" alt="Coffee" class="img-fluid rounded-4 shadow-lg" style="max-height: 500px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600'">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Decorative Elements -->
    <div class="position-absolute bottom-0 start-0 w-100" style="height: 100px; background: linear-gradient(to top, var(--bg-cream), transparent);"></div>
</section>

<!-- Features Section -->
<section class="py-5 my-5">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-3">Why Choose Us</span>
            <h2 class="fw-bold">The Cloud 9 Experience</h2>
            <p class="text-muted max-w-600 mx-auto">We're passionate about delivering exceptional coffee experiences that brighten your day.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4 animate-on-scroll stagger-1">
                <div class="card feature-card card-hover h-100">
                    <div class="icon-wrapper">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Premium Beans</h4>
                    <p class="text-muted mb-0">Ethically sourced from sustainable farms across Colombia, Ethiopia, and Brazil.</p>
                </div>
            </div>
            
            <div class="col-md-4 animate-on-scroll stagger-2">
                <div class="card feature-card card-hover h-100">
                    <div class="icon-wrapper" style="background: linear-gradient(135deg, #D4A574 0%, #E8C9A0 100%);">
                        <i class="fas fa-fire"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Freshly Roasted</h4>
                    <p class="text-muted mb-0">Small-batch roasting daily to ensure maximum flavor and aroma in every cup.</p>
                </div>
            </div>
            
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

<!-- Popular Menu Section -->
<section class="py-5" style="background: white;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-md-8 animate-on-scroll">
                <span class="badge bg-accent text-dark mb-2">Our Menu</span>
                <h2 class="fw-bold mb-0">Popular Picks</h2>
                <p class="text-muted mb-0">Customer favorites that keep them coming back</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="menu/menu.php" class="btn btn-outline-primary">
                    View Full Menu <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
        
        <div class="row g-4">
            <?php
            $popular_items = [
                ['name' => 'Caramel Macchiato', 'price' => '₹450', 'category' => 'Coffee', 'image' => 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=400'],
                ['name' => 'Cappuccino', 'price' => '₹380', 'category' => 'Coffee', 'image' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400'],
                ['name' => 'Chocolate Croissant', 'price' => '₹280', 'category' => 'Snack', 'image' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400'],
                ['name' => 'Cheesecake', 'price' => '₹420', 'category' => 'Dessert', 'image' => 'https://images.unsplash.com/photo-1524351199678-941a58a3df26?w=400'],
            ];
            
            foreach ($popular_items as $index => $item):
            ?>
            <div class="col-md-6 col-lg-3 animate-on-scroll stagger-<?php echo $index + 1; ?>">
                <div class="card product-card card-hover h-100">
                    <div class="product-image">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="product-placeholder" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, var(--cafe-primary-light) 0%, var(--cafe-primary) 100%); align-items: center; justify-content: center; flex-direction: column;">
                            <i class="fas fa-coffee fa-3x text-white mb-2"></i>
                            <span class="text-white small"><?php echo $item['category']; ?></span>
                        </div>
                        <div class="product-overlay">
                            <a href="menu/menu.php" class="btn btn-light rounded-pill">
                                <i class="fas fa-plus me-1"></i> Add to Cart
                            </a>
                        </div>
                        <span class="badge bg-primary position-absolute top-0 start-0 m-3"><?php echo $item['category']; ?></span>
                    </div>
                    <div class="product-info">
                        <h5 class="product-title"><?php echo $item['name']; ?></h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price"><?php echo $item['price']; ?></span>
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

<!-- Testimonials Section -->
<section class="py-5" style="background: white;">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-3">Testimonials</span>
            <h2 class="fw-bold">What Our Customers Say</h2>
        </div>
        
        <div class="row g-4">
            <?php
            $testimonials = [
                ['name' => 'Sarah Johnson', 'role' => 'Coffee Enthusiast', 'text' => 'Best coffee shop in town! The atmosphere is amazing and the baristas really know their craft.', 'avatar' => 'SJ'],
                ['name' => 'Michael Chen', 'role' => 'Regular Customer', 'text' => 'I order from Cloud 9 every morning. Their delivery is always on time and the coffee is consistently great.', 'avatar' => 'MC'],
                ['name' => 'Emily Davis', 'role' => 'Food Blogger', 'text' => 'The desserts here are absolutely divine. Their cheesecake rivals any high-end bakery in the city.', 'avatar' => 'ED'],
            ];
            
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

<!-- CTA Section -->
<section class="py-5 my-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center animate-on-scroll">
                <h2 class="fw-bold mb-3">Ready to Experience Cloud 9?</h2>
                <p class="text-muted mb-4">Join thousands of coffee lovers who have made us their daily destination for premium coffee and delicious treats.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="/cloud_9_cafe_rebuild/auth/register.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </a>
                    <a href="menu/menu.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-utensils me-2"></i>Browse Menu
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .min-vh-80 {
        min-height: 80vh;
    }
    
    .max-w-600 {
        max-width: 600px;
    }
    
    .text-white-50 {
        color: rgba(255, 255, 255, 0.6) !important;
    }
    
    /* Override body background for home page hero */
    body {
        background: var(--bg-cream);
    }
</style>

<?php
$content = ob_get_clean();
include '../includes/layout.php';
?>
