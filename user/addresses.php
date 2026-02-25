<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db_config.php';

// Check if user is logged in
if (!isset($_SESSION['cafe_user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['cafe_user_id'];
$success = '';
$error = '';

// Handle Add Address
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_address'])) {
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $address_line1 = mysqli_real_escape_string($con, $_POST['address_line1']);
    $address_line2 = mysqli_real_escape_string($con, $_POST['address_line2']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $state = mysqli_real_escape_string($con, $_POST['state']);
    $zip_code = mysqli_real_escape_string($con, $_POST['zip_code']);
    $address_type = mysqli_real_escape_string($con, $_POST['address_type']);
    
    $insert_query = "INSERT INTO user_addresses (user_id, fullname, phone, address_line1, address_line2, city, state, zip_code, address_type) 
                     VALUES ($user_id, '$fullname', '$phone', '$address_line1', '$address_line2', '$city', '$state', '$zip_code', '$address_type')";
    
    if (mysqli_query($con, $insert_query)) {
        $success = 'Address added successfully!';
    } else {
        $error = 'Failed to add address. Please try again.';
    }
}

// Handle Edit Address
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_address'])) {
    $address_id = intval($_POST['address_id']);
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $address_line1 = mysqli_real_escape_string($con, $_POST['address_line1']);
    $address_line2 = mysqli_real_escape_string($con, $_POST['address_line2']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $state = mysqli_real_escape_string($con, $_POST['state']);
    $zip_code = mysqli_real_escape_string($con, $_POST['zip_code']);
    $address_type = mysqli_real_escape_string($con, $_POST['address_type']);
    
    $update_query = "UPDATE user_addresses SET 
                     fullname = '$fullname', phone = '$phone', address_line1 = '$address_line1', 
                     address_line2 = '$address_line2', city = '$city', state = '$state', 
                     zip_code = '$zip_code', address_type = '$address_type'
                     WHERE id = $address_id AND user_id = $user_id";
    
    if (mysqli_query($con, $update_query)) {
        $success = 'Address updated successfully!';
    } else {
        $error = 'Failed to update address. Please try again.';
    }
}

// Handle Delete Address
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $address_id = intval($_GET['delete']);
    mysqli_query($con, "DELETE FROM user_addresses WHERE id = $address_id AND user_id = $user_id");
    $success = 'Address deleted successfully!';
}

// Get all addresses for this user
$addresses = mysqli_query($con, "SELECT * FROM user_addresses WHERE user_id = $user_id ORDER BY created_at DESC");

// Get address to edit (if edit parameter is set)
$edit_address = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_result = mysqli_query($con, "SELECT * FROM user_addresses WHERE id = $edit_id AND user_id = $user_id");
    $edit_address = mysqli_fetch_assoc($edit_result);
}

$title = "Saved Addresses - Cloud 9 Cafe";
$active_sidebar = 'addresses';
ob_start();
?>

<style>
    .address-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .address-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
</style>

<!-- Success/Error Messages -->
<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-lg mb-4">
    <div class="card-body p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h2 class="fw-bold mb-0 text-primary">Saved Addresses</h2>
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                <i class="fas fa-plus me-2"></i>Add New Address
            </button>
        </div>

        <?php if (mysqli_num_rows($addresses) == 0): ?>
        <div class="text-center py-5">
            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
            <h5>No addresses found</h5>
            <p class="text-muted">Add your first delivery address</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                <i class="fas fa-plus me-2"></i>Add Address
            </button>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php while ($address = mysqli_fetch_assoc($addresses)): ?>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm address-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <?php 
                                $badge_class = '';
                                switch($address['address_type']) {
                                    case 'Home': $badge_class = 'bg-primary'; break;
                                    case 'Work': $badge_class = 'bg-success'; break;
                                    default: $badge_class = 'bg-warning';
                                }
                                ?>
                                <span class="badge <?php echo $badge_class; ?> bg-opacity-10 text-<?php echo str_replace('bg-', '', $badge_class); ?> mb-2"><?php echo $address['address_type']; ?></span>
                                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($address['fullname']); ?></h5>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link text-muted" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="?edit=<?php echo $address['id']; ?>" data-bs-toggle="modal" data-bs-target="#editAddressModal<?php echo $address['id']; ?>">
                                        <i class="fas fa-edit me-2"></i>Edit</a>
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="?delete=<?php echo $address['id']; ?>" onclick="return confirm('Delete this address?')">
                                        <i class="fas fa-trash me-2"></i>Delete</a></li>
                                </ul>
                            </div>
                        </div>
                        <p class="text-muted mb-1"><?php echo htmlspecialchars($address['address_line1']); ?></p>
                        <?php if ($address['address_line2']): ?>
                        <p class="text-muted mb-1"><?php echo htmlspecialchars($address['address_line2']); ?></p>
                        <?php endif; ?>
                        <p class="text-muted mb-1"><?php echo htmlspecialchars($address['city'] . ', ' . $address['state'] . ' ' . $address['zip_code']); ?></p>
                        <p class="text-muted mb-3"><?php echo htmlspecialchars($address['country']); ?></p>
                        <p class="mb-0"><i class="fas fa-phone me-2 text-muted"></i><?php echo htmlspecialchars($address['phone']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Edit Address Modal for each address -->
            <div class="modal fade" id="editAddressModal<?php echo $address['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header border-0">
                            <h5 class="modal-title fw-bold">Edit Address</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form method="POST">
                                <input type="hidden" name="edit_address" value="1">
                                <input type="hidden" name="address_id" value="<?php echo $address['id']; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Full Name</label>
                                        <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($address['fullname']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Phone Number</label>
                                        <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($address['phone']); ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Address Line 1</label>
                                    <input type="text" name="address_line1" class="form-control" value="<?php echo htmlspecialchars($address['address_line1']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Address Line 2</label>
                                    <input type="text" name="address_line2" class="form-control" value="<?php echo htmlspecialchars($address['address_line2'] ?? ''); ?>">
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">City</label>
                                        <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($address['city']); ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">State</label>
                                        <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($address['state']); ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">ZIP Code</label>
                                        <input type="text" name="zip_code" class="form-control" value="<?php echo htmlspecialchars($address['zip_code']); ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Address Type</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="address_type" id="editHomeType<?php echo $address['id']; ?>" value="Home" <?php echo $address['address_type'] == 'Home' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="editHomeType<?php echo $address['id']; ?>">Home</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="address_type" id="editWorkType<?php echo $address['id']; ?>" value="Work" <?php echo $address['address_type'] == 'Work' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="editWorkType<?php echo $address['id']; ?>">Work</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="address_type" id="editOtherType<?php echo $address['id']; ?>" value="Other" <?php echo $address['address_type'] == 'Other' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="editOtherType<?php echo $address['id']; ?>">Other</label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Update Address</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST">
                    <input type="hidden" name="add_address" value="1">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="fullname" class="form-control" placeholder="Enter full name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="Enter phone number" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address Line 1</label>
                        <input type="text" name="address_line1" class="form-control" placeholder="Street address, P.O. box" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address Line 2</label>
                        <input type="text" name="address_line2" class="form-control" placeholder="Apartment, suite, unit, etc.">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" placeholder="City" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">State</label>
                            <input type="text" name="state" class="form-control" placeholder="State" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">ZIP Code</label>
                            <input type="text" name="zip_code" class="form-control" placeholder="ZIP" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address Type</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="address_type" id="homeType" value="Home" checked>
                                <label class="form-check-label" for="homeType">Home</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="address_type" id="workType" value="Work">
                                <label class="form-check-label" for="workType">Work</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="address_type" id="otherType" value="Other">
                                <label class="form-check-label" for="otherType">Other</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Address</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$dashboard_content = ob_get_clean();
include '../includes/dashboard_layout.php';
?>
