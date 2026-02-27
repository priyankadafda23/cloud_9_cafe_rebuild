<?php
require_once '../config/db_config.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['fname'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    $messageData = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'status' => 'New'
    ];
    
    $db->insert('contact_messages', $messageData);
    $success = "Thank you for your message! We'll get back to you soon.";
}

$title = "Contact Us - Cloud 9 Cafe";
ob_start();
?>

<!-- Page Header -->
<section class="py-5" style="background: linear-gradient(135deg, var(--cafe-primary) 0%, var(--cafe-primary-dark) 100%);">
    <div class="container">
        <div class="row justify-content-center text-center text-white">
            <div class="col-lg-8 animate-fade-in-up">
                <h1 class="fw-bold mb-3">Get In Touch</h1>
                <p class="lead opacity-75 mb-0">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5" style="background: var(--bg-cream);">
    <div class="container">
        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-lg-8 animate-on-scroll">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="fw-bold mb-4">
                            <i class="fas fa-paper-plane me-2 text-primary"></i>Send us a Message
                        </h4>
                        
                        <?php if (isset($success)): ?>
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $success; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (isset($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                        <?php endif; ?>
                        
                        <form action="" method="POST" id="contactForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="fname" class="form-label fw-medium">Your Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="fname" name="fname" 
                                               placeholder="John Doe" 
                                               data-validation="required,alphabetic,min" data-min="2">
                                    </div>
                                    <div id="fname_error" class="invalid-feedback d-block"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-medium">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" 
                                               placeholder="john@example.com" 
                                               data-validation="required,email">
                                    </div>
                                    <div id="email_error" class="invalid-feedback d-block"></div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <label for="subject" class="form-label fw-medium">Subject</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-tag text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="subject" name="subject" 
                                           placeholder="How can we help?" 
                                           data-validation="required,min" data-min="3">
                                </div>
                                <div id="subject_error" class="invalid-feedback d-block"></div>
                            </div>
                            
                            <div class="mt-3">
                                <label for="message" class="form-label fw-medium">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" 
                                          placeholder="Tell us more about your inquiry..." 
                                          data-validation="required,min" data-min="10"></textarea>
                                <div id="message_error" class="invalid-feedback d-block"></div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-4">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-4 animate-on-scroll stagger-1">
                <div class="card border-0 shadow-lg h-100" style="background: linear-gradient(135deg, var(--cafe-primary) 0%, var(--cafe-primary-dark) 100%);">
                    <div class="card-body p-4 p-md-5 text-white">
                        <h4 class="fw-bold mb-4">
                            <i class="fas fa-address-card me-2 text-accent"></i>Contact Info
                        </h4>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-start mb-4">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(255,255,255,0.1);">
                                        <i class="fas fa-map-marker-alt text-accent"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold mb-1">Address</h6>
                                    <p class="mb-0 opacity-75">123 Coffee Street<br>Cafe City, CC 12345</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-4">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(255,255,255,0.1);">
                                        <i class="fas fa-phone text-accent"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold mb-1">Phone</h6>
                                    <p class="mb-0 opacity-75">+1 (555) 123-4567</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-4">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(255,255,255,0.1);">
                                        <i class="fas fa-envelope text-accent"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold mb-1">Email</h6>
                                    <p class="mb-0 opacity-75">hello@cloud9cafe.com</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(255,255,255,0.1);">
                                        <i class="fas fa-clock text-accent"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold mb-1">Hours</h6>
                                    <p class="mb-0 opacity-75">Mon - Fri: 7AM - 10PM<br>Sat - Sun: 8AM - 11PM</p>
                                </div>
                            </div>
                        </div>
                        
                        <hr style="border-color: rgba(255,255,255,0.1);">
                        
                        <h6 class="fw-bold mb-3">Follow Us</h6>
                        <div class="d-flex gap-2">
                            <a href="#" class="d-flex align-items-center justify-content-center rounded-circle text-white" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); transition: all 0.3s;">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="d-flex align-items-center justify-content-center rounded-circle text-white" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); transition: all 0.3s;">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="d-flex align-items-center justify-content-center rounded-circle text-white" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); transition: all 0.3s;">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="d-flex align-items-center justify-content-center rounded-circle text-white" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); transition: all 0.3s;">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Validation styles */
    .is-valid {
        border-color: #198754 !important;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
    }
</style>

<script src="../assets/js/jquery.js"></script>
<script src="../assets/js/validate.js"></script>

<?php
$content = ob_get_clean();
include '../includes/layout.php';
?>
