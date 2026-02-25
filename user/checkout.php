<?php
/**
 * Cloud 9 Cafe - Checkout Processing
 * Handles order creation, moving cart items, and clearing cart
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db_config.php';

// Check if user is logged in
if (!isset($_SESSION['cafe_user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['cafe_user_id'];

// Process checkout form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    
    // Get form data
    $delivery_address = mysqli_real_escape_string($con, $_POST['delivery_address'] ?? '');
    $order_note = mysqli_real_escape_string($con, $_POST['order_note'] ?? '');
    $payment_method = mysqli_real_escape_string($con, $_POST['payment_method'] ?? 'Cash');
    
    // Validate cart has items
    $cart_query = "SELECT c.*, m.name, m.price, m.stock_quantity 
                   FROM cafe_cart c 
                   JOIN menu_items m ON c.menu_item_id = m.id 
                   WHERE c.user_id = $user_id";
    $cart_result = mysqli_query($con, $cart_query);
    
    if (mysqli_num_rows($cart_result) === 0) {
        header("Location: cart.php?error=empty_cart");
        exit();
    }
    
    // Calculate total amount
    $total_amount = 0;
    $cart_items = [];
    
    while ($item = mysqli_fetch_assoc($cart_result)) {
        $subtotal = $item['price'] * $item['quantity'];
        $total_amount += $subtotal;
        $cart_items[] = $item;
    }
    
    // Generate unique order number
    $order_number = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
    
    // Start transaction
    mysqli_begin_transaction($con);
    
    try {
        // 1. Create order in cafe_orders
        $order_query = "INSERT INTO cafe_orders 
                        (order_number, user_id, total_amount, order_note, status, 
                         payment_status, payment_method, delivery_address, order_date) 
                        VALUES 
                        ('$order_number', $user_id, $total_amount, '$order_note', 'Pending',
                         'Pending', '$payment_method', '$delivery_address', NOW())";
        
        if (!mysqli_query($con, $order_query)) {
            throw new Exception("Error creating order: " . mysqli_error($con));
        }
        
        $order_id = mysqli_insert_id($con);
        
        // 2. Move items to cafe_order_items
        foreach ($cart_items as $item) {
            $menu_item_id = $item['menu_item_id'];
            $quantity = $item['quantity'];
            $unit_price = $item['price'];
            $subtotal = $unit_price * $quantity;
            $customization = mysqli_real_escape_string($con, $item['customization'] ?? '');
            
            $item_query = "INSERT INTO cafe_order_items 
                          (order_id, menu_item_id, quantity, unit_price, subtotal, customization) 
                          VALUES 
                          ($order_id, $menu_item_id, $quantity, $unit_price, $subtotal, '$customization')";
            
            if (!mysqli_query($con, $item_query)) {
                throw new Exception("Error adding order item: " . mysqli_error($con));
            }
            
            // Update stock quantity
            $update_stock = "UPDATE menu_items 
                           SET stock_quantity = stock_quantity - $quantity 
                           WHERE id = $menu_item_id";
            mysqli_query($con, $update_stock);
        }
        
        // 3. Clear cafe_cart
        $clear_cart = "DELETE FROM cafe_cart WHERE user_id = $user_id";
        if (!mysqli_query($con, $clear_cart)) {
            throw new Exception("Error clearing cart: " . mysqli_error($con));
        }
        
        // 4. Add 10 reward points to user
        $update_points = "UPDATE cafe_users 
                         SET reward_points = reward_points + 10 
                         WHERE id = $user_id";
        if (!mysqli_query($con, $update_points)) {
            throw new Exception("Error updating reward points: " . mysqli_error($con));
        }
        
        // Commit transaction
        mysqli_commit($con);
        
        // Store success message
        $_SESSION['order_success'] = true;
        $_SESSION['order_number'] = $order_number;
        $_SESSION['points_earned'] = 10;
        
        // Redirect to order success page
        header("Location: order_success.php?order_id=$order_id");
        exit();
        
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($con);
        error_log($e->getMessage());
        header("Location: cart.php?error=order_failed");
        exit();
    }
}

// If accessed directly without POST, redirect to cart
header("Location: cart.php");
exit();
?>
