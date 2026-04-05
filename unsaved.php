<?php
include 'auth.php';
include 'db.php';

$product_id = intval($_POST['product_id'] ?? 0);
$user_id = $_SESSION['user_id'] ?? 0;

if($product_id === 0 || $user_id === 0) die("Invalid request.");

// Delete saved product
$stmt = $conn->prepare("DELETE FROM saved_products WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$stmt->close();

// Redirect back to detail page
header("Location: detail.php?id=$product_id&unsaved=1");
exit();
?>