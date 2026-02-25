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

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $price = floatval($_POST['price']);
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $stock = intval($_POST['stock_quantity']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // First insert to get the menu item ID
    $query = "INSERT INTO menu_items (name, description, price, category, image, stock_quantity, featured) 
              VALUES ('$name', '$description', $price, '$category', '', $stock, $featured)";
    
    if (mysqli_query($con, $query)) {
        $menu_id = mysqli_insert_id($con);
        
        // Handle image upload to new path: assets/uploads/menu_images/{menu_id}/
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../assets/uploads/menu_images/' . $menu_id . '/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Get file extension
            $file_info = pathinfo($_FILES['image']['name']);
            $extension = strtolower($file_info['extension']);
            
            // Validate allowed extensions
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($extension, $allowed)) {
                // Save as image1.{extension}
                $filename = 'image1.' . $extension;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                    $image = 'assets/uploads/menu_images/' . $menu_id . '/' . $filename;
                    
                    // Update the menu item with image path
                    mysqli_query($con, "UPDATE menu_items SET image = '$image' WHERE id = $menu_id");
                }
            }
        }
        
        header("Location: menu.php");
        exit();
    } else {
        $error = 'Failed to add menu item';
    }
}

$title = "Add Menu Item - Cloud 9 Cafe";
$active_sidebar = 'menu';
ob_start();
?>

<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
    }

    .form-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }

    .image-preview {
        width: 100%;
        height: 200px;
        background: #f8f9fa;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .form-label {
        font-weight: 500;
        color: #333;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold mb-2"><i class="fas fa-plus me-2"></i>Add Menu Item</h3>
            <p class="mb-0 opacity-75">Create a new menu item for the cafe</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="menu.php" class="btn btn-light rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to Menu
            </a>
        </div>
    </div>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card form-card shadow-sm">
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Item Name *</label>
                            <input type="text" name="name" class="form-control form-control-lg" required placeholder="e.g., Cappuccino">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Category *</label>
                            <select name="category" class="form-select" required>
                                <option value="">Select Category</option>
                                <option value="Coffee">Coffee</option>
                                <option value="Snack">Snack</option>
                                <option value="Dessert">Dessert</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Price ($) *</label>
                            <input type="number" name="price" step="0.01" min="0" class="form-control" required placeholder="0.00">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" name="stock_quantity" min="0" class="form-control" value="100">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Featured</label>
                            <div class="form-check form-switch mt-2">
                                <input type="checkbox" name="featured" class="form-check-input" id="featuredSwitch" style="width: 3rem; height: 1.5rem;">
                                <label class="form-check-label ms-2" for="featuredSwitch">Show on homepage</label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Describe the item..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Item Image</label>
                    <div class="image-preview mb-3" id="imagePreview" style="display: flex; align-items: center; justify-content: center; flex-direction: column; background: linear-gradient(135deg, var(--cafe-primary-light) 0%, var(--cafe-primary) 100%);">
                        <i class="fas fa-coffee fa-3x text-white mb-2"></i>
                        <span class="text-white small">Upload Image</span>
                    </div>
                    <input type="file" name="image" class="form-control" id="imageInput" accept="image/*">
                    <small class="text-muted">Recommended size: 400x400px</small>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="menu.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-save me-2"></i>Save Item
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
            }
            reader.readAsDataURL(file);
        }
    });
</script>

<?php
$dashboard_content = ob_get_clean();
include 'admin_layout.php';
?>
