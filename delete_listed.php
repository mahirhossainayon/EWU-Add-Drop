<?php
include 'auth.php'; // Ensure user is logged in
include 'db.php';

session_start();

$product_id = intval($_POST['product_id'] ?? 0);
$user_id = $_SESSION['user_id'] ?? 0;

// Validate input
if ($product_id === 0 || $user_id === 0) {
    die("Invalid request.");
}

// Check if product belongs to the current user
$stmt = $conn->prepare("SELECT id FROM products WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $stmt->close();
    die("You can only delete your own products.");
}
$stmt->close();

// Delete product from products table
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->close();

// Delete product from saved_products table (optional)
$stmt = $conn->prepare("DELETE FROM saved_products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->close();

// Redirect back to dashboard with success message
header("Location: user.php?deleted=1");
exit();
?>