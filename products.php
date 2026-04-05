<?php
session_start();
include 'db.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest Products - EWU Add & Drop</title>
    <link rel="stylesheet" href="style.css">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
</head>
<body>

    <!-- NAVBAR -->
    <header>
        <div class="navbar">
            <div class="logo">EWU Add & Drop</div>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="category.php">Categories</a>
                
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="categories-hero">
        <h1>Check Out Our Latest Products</h1>
        <p>Freshly listed items from EWU students, from electronics to books and more!</p>
    </section>

    <!-- LATEST PRODUCTS GRID -->
    <section class="categories">
        <div class="category-grid">

        <?php
        // Fetch products from database, newest first
        $sql = "SELECT * FROM products ORDER BY id DESC";

        
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            while ($product = $result->fetch_assoc()):
                // Use default image if none uploaded
                $image_path = !empty($product['image']) ? 'uploads/' . $product['image'] : 'https://via.placeholder.com/180?text=No+Image';
        ?>
            <div class="category-card">
                <img src="<?= $image_path; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                <h3><?= htmlspecialchars($product['name']); ?></h3>
                <p class="price">৳ <?= number_format($product['price'], 2); ?></p>
                <a href="detail.php?id=<?= $product['id']; ?>" class="explore-btn">View Product</a>
            </div>
        <?php
            endwhile;
        else:
        ?>
            <p style="text-align:center; color:red; font-weight:bold;">No products found.</p>
        <?php endif; ?>

        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <p>© 2026 EWU Add & Drop | Built for EWU Students</p>
        <div class="social-icons">
            <i class="fa-brands fa-facebook-f"></i>
            <i class="fa-brands fa-twitter"></i>
            <i class="fa-brands fa-instagram"></i>
        </div>
    </footer>

</body>
</html>