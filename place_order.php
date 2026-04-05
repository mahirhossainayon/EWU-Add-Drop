<?php
include 'auth.php';
include 'db.php';

// Validate input
if(!isset($_POST['product_id'], $_POST['payment'], $_POST['meeting'])){
    die("Invalid request");
}

$product_id = intval($_POST['product_id']);
$buyer_id = $_SESSION['user_id'];
$payment = $_POST['payment'];
$meeting = $_POST['meeting'];

// Get seller id
$stmt = $conn->prepare("SELECT user_id FROM products WHERE id=?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows == 0){
    die("Product not found");
}

$row = $res->fetch_assoc();
$seller_id = $row['user_id'];
$stmt->close();

// Insert order
$stmt = $conn->prepare("
INSERT INTO orders (product_id, buyer_id, seller_id, payment_method, meeting_place, status)
VALUES (?, ?, ?, ?, ?, 'Pending')
");

$stmt->bind_param("iiiss", $product_id, $buyer_id, $seller_id, $payment, $meeting);

if($stmt->execute()){
    header("Location: user.php?order=success");
    exit();
} else {
    die("Error placing order");
}