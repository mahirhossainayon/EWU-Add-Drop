<?php
include 'auth.php';
include 'db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$id = intval($_GET['id'] ?? 0);
if ($id === 0) die("No product selected.");

// Delete product safely
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Redirect back to admin panel
header("Location: admin.php");
exit();