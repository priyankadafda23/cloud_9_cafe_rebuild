<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Unset all cafe session variables
unset($_SESSION['cafe_user_id']);
unset($_SESSION['cafe_user_name']);
unset($_SESSION['cafe_admin_id']);

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
