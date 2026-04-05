<?php
include 'auth.php';
include 'db.php';

if(!isset($_POST['order_id'])){
    die("Invalid request");
}

$order_id = intval($_POST['order_id']);
$user_id = $_SESSION['user_id'];

// Make sure seller owns this order
$stmt = $conn->prepare("
    DELETE o FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.id = ? AND p.user_id = ?
");

$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();

$stmt->close();

header("Location: user.php");
exit();