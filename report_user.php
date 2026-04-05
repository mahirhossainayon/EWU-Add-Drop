<?php
include 'auth.php'; // ensures user is logged in
include 'db.php';

$id = intval($_GET['id'] ?? 0); // product id

if ($id === 0) die("No product selected.");

// Fetch product and seller info
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) die("Product not found.");

$product = $result->fetch_assoc();
$seller_id = $product['user_id'] ?? 0; // assuming products table has user_id column
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = trim($_POST['reason'] ?? '');
    if (empty($reason)) {
        $error = "Please provide a reason for reporting.";
    } else {
        $stmt = $conn->prepare("INSERT INTO user_reports (user_id, reported_by, reason) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $seller_id, $_SESSION['user_id'], $reason);
        $stmt->execute();
        $stmt->close();

        // Redirect back to product page with success message
        header("Location: detail.php?id=$id&reported=success");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Report Seller - EWU Add & Drop</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="categories-hero">
    <p>
        <a href="index.php">Home</a> /
        <a href="products.php">Latest Products</a> /
        <a href="detail.php?id=<?= $product['id'] ?>">Product Detail</a> /
        <span>Report Seller</span>
    </p>
</section>

<section class="report-section">
    <div class="report-container">
        <h1>Report Seller for "<?= htmlspecialchars($product['name']) ?>"</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="reason">Reason for reporting:</label>
            <textarea name="reason" id="reason" rows="5" required></textarea>

            <div class="buttons-container">
                <button type="submit" class="explore-btn"><i class="fa-solid fa-flag"></i> Submit Report</button>
                <a href="detail.php?id=<?= $product['id'] ?>" class="explore-btn"><i class="fa-solid fa-arrow-left"></i> Back to Product</a>
            </div>
        </form>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>