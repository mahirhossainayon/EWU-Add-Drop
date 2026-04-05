<?php
include 'auth.php';
include 'db.php';

// Get product ID from URL and sanitize
$product_id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'] ?? 0;

// Validate inputs
if ($product_id === 0 || $user_id === 0) {
    die("Invalid request.");
}

// Check if the product exists
$stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $stmt->close();
    die("Product not found.");
}
$stmt->close();

// Check if the product is already saved
$stmt = $conn->prepare("SELECT id FROM saved_products WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Insert into saved_products table
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO saved_products (user_id, product_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}

$stmt->close();

// Redirect back to the product page with a success message
header("Location: detail.php?id=$product_id&saved=1");
exit();

?>