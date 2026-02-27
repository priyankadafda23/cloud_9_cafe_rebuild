<?php
/**
 * Cloud 9 Cafe - Common Functions
 */

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    global $auth;
    return $auth->isUserLoggedIn();
}

/**
 * Get current user ID
 * @return int|null
 */
function getCurrentUserId() {
    global $auth;
    return $auth->getUserId();
}

/**
 * Get current user name
 * @return string|null
 */
function getCurrentUserName() {
    global $auth;
    return $auth->getUserName();
}

/**
 * Redirect to login page if not logged in
 */
function requireLogin() {
    global $auth;
    $auth->requireUser();
}

/**
 * Format price
 * @param float $price
 * @return string
 */
function formatPrice($price) {
    return '₹' . number_format($price, 2);
}

/**
 * Generate order number
 * @return string
 */
function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
}

/**
 * Sanitize input
 * @param string $data
 * @return string
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
?>
