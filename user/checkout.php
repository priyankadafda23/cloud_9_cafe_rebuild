<?php
/**
 * Cloud 9 Cafe - Checkout Processing
 * Handles order creation, moving cart items, and clearing cart
 */

require_once '../config/db_config.php';

// Check if user is logged in
if (!$auth->isUserLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $auth->getUserId();

// Process checkout form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    
    // Get form data
    $delivery_address = $_POST['delivery_address'] ?? '';
    $order_note = $_POST['order_note'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'Cash';
    
    // Validate cart has items
    $cartItems = $db->select('cafe_cart', ['user_id' => $user_id]);
    
    if (empty($cartItems)) {
        header("Location: cart.php?error=empty_cart");
        exit();
    }
    
    // Calculate total amount and prepare cart items
    $total_amount = 0;
    $processedItems = [];
    
    foreach ($cartItems as $cartItem) {
        $menuItem = $db->selectOne('menu_items', ['id' => $cartItem['menu_item_id']]);
        if ($menuItem) {
            $subtotal = $menuItem['price'] * $cartItem['quantity'];
            $total_amount += $subtotal;
            $processedItems[] = array_merge($cartItem, $menuItem);
        }
    }
    
    if (empty($processedItems)) {
        header("Location: cart.php?error=invalid_items");
        exit();
    }
    
    // Generate unique order number
    $order_number = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
    
    try {
        // 1. Create order
        $orderData = [
            'order_number' => $order_number,
            'user_id' => $user_id,
            'total_amount' => $total_amount,
            'order_note' => $order_note,
            'status' => 'Pending',
            'payment_status' => 'Pending',
            'payment_method' => $payment_method,
            'delivery_address' => $delivery_address,
            'order_date' => date('Y-m-d H:i:s')
        ];
        
        $order_id = $db->insert('cafe_orders', $orderData);
        
        if (!$order_id) {
            throw new Exception("Error creating order");
        }
        
        // 2. Move items to cafe_order_items
        foreach ($processedItems as $item) {
            $itemData = [
                'order_id' => $order_id,
                'menu_item_id' => $item['menu_item_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
                'customization' => $item['customization'] ?? ''
            ];
            
            $db->insert('cafe_order_items', $itemData);
            
            // Update stock quantity
            $newStock = max(0, $item['stock_quantity'] - $item['quantity']);
            $db->update('menu_items', ['stock_quantity' => $newStock], ['id' => $item['menu_item_id']]);
        }
        
        // 3. Clear cart
        $db->delete('cafe_cart', ['user_id' => $user_id]);
        
        // 4. Add reward points to user
        $user = $db->selectOne('cafe_users', ['id' => $user_id]);
        if ($user) {
            $newPoints = ($user['reward_points'] ?? 0) + 10;
            $db->update('cafe_users', ['reward_points' => $newPoints], ['id' => $user_id]);
        }
        
        // Redirect to order success page
        header("Location: order_success.php?order_id=$order_id");
        exit();
        
    } catch (Exception $e) {
        error_log($e->getMessage());
        header("Location: cart.php?error=order_failed");
        exit();
    }
}

// If accessed directly without POST, redirect to cart
header("Location: cart.php");
exit();
?>
