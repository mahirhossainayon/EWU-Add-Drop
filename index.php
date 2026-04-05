<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EWU Add & Drop</title>
<link rel="icon" href="logo.png" type="image/png">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style>
/* ==================== CAROUSEL STYLES ==================== */
.product-carousel, .category-carousel {
    overflow: hidden;
    width: 100%;
}

.product-track, .category-track {
    display: flex;
    gap: 20px;
}

.product-track a, .category-track a {
    flex: 0 0 auto;
    text-decoration: none; /* Remove underline */
    color: inherit;
}

/* Animate products */
.product-track {
    animation: scrollProducts 20s linear infinite;
}

@keyframes scrollProducts {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* Animate categories */
.category-track {
    animation: scrollCategories 25s linear infinite;
}

@keyframes scrollCategories {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* Admin button style */
.admin-btn {
    background: #ff6600;
    color: #fff;
    padding: 6px 12px;
    border-radius: 5px;
    text-decoration: none;
    margin-left: 10px;
}

.admin-btn:hover {
    background: #e65c00;
}

/* ==================== LOGO & BRAND ALIGNMENT ==================== */
.logo {
    display: flex;
    align-items: center; /* vertically center logo and brand name */
    gap: 10px;           /* space between logo and text */
}

.logo img {
    height: 45px; /* adjust logo size */
    width: auto;
}

.logo span {
    font-size: 20px;
    font-weight: bold;
    color: inherit; /* keep text color same as navbar */
}
</style>
</head>
<body>

<!-- ================= NAVBAR ================= -->
<?php
// Fetch user info if logged in
$user_name = '';
$user_role = '';
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT name, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $user_name = $user_data['name'];
        $user_role = $user_data['role'];
    }
    $stmt->close();
}
?>

<header>
    <div class="navbar">
        <div class="logo">
            <img src="uploads/logow.png" alt="EWU Logo">
            <span>EWU Add & Drop</span>
        </div>
        <nav class="nav-links">
            <a href="index.php">Home</a>

            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="user.php" class="user-link">
                    <i class="fa-solid fa-user"></i>
                    <span class="user-name">Hi, <?= htmlspecialchars($user_name); ?></span>
                </a>

                <a href="add.php"><i class="fa-solid fa-plus"></i> Sell It</a>

                <?php if($user_role === 'admin'): ?>
                    <a href="admin.php" class="admin-btn"><i class="fa-solid fa-cogs"></i> Admin Panel</a>
                <?php endif; ?>

                <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            <?php else: ?>
                <a href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                <a href="register.php"><i class="fa-solid fa-user-plus"></i> Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<!-- ================= HERO ================= -->
<section class="hero">
    <h1>Buy & Sell Easily within EWU</h1>
    <p>Find textbooks, electronics, and more from trusted EWU students</p>

<!-- ================= SEARCH BAR ================= -->
<section class="search-section">
    <div class="search-container">
        <form action="search.php" method="GET">
            <input type="text" name="query" placeholder="Search products..." required>
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        </form>
    </div>
</section>
</section>

<!-- ================= LATEST PRODUCTS ================= -->
<section class="products latest-products">
    <h2>Latest Products</h2>
    <div class="product-carousel">
        <div class="product-track">
            <?php
            $sql = "SELECT * FROM products ORDER BY id DESC LIMIT 5";
            $result = $conn->query($sql);
            $products = [];
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) $products[] = $row;
            }

            foreach(array_merge($products, $products) as $row):
                $image_path = !empty($row['image']) ? 'uploads/' . $row['image'] : 'uploads/default.png';
            ?>
            <a href="detail.php?id=<?= $row['id']; ?>">
                <div class="card">
                    <img src="<?= $image_path; ?>" alt="<?= htmlspecialchars($row['name']); ?>" style="aspect-ratio:3/4; width:100%; object-fit:cover; border-radius:8px;">
                    <h3><?= htmlspecialchars($row['name']); ?></h3>
                    <p class="price">৳ <?= number_format($row['price'], 2); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="view-all-btn-container">
        <a href="products.php" class="view-all-btn">View All Products</a>
    </div>
</section>

<!-- ================= CATEGORIES ================= -->
<section class="products categories-home">
    <h2>Product Categories</h2>
    <div class="category-carousel">
        <div class="category-track">
            <?php
            $sql = "SELECT * FROM categories ORDER BY id ASC";
            $result = $conn->query($sql);
            $categories = [];
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) $categories[] = $row;
            }

            foreach(array_merge($categories, $categories) as $cat):
                $cat_image = !empty($cat['image']) ? 'uploads/' . $cat['image'] : 'uploads/default-category.png';
            ?>
            <a href="filter.php?id=<?= $cat['id']; ?>">
                <div class="category-card">
                    <img src="<?= $cat_image; ?>" alt="<?= htmlspecialchars($cat['name']); ?>" style="aspect-ratio:1/1; width:100%; object-fit:cover; border-radius:8px;">
                    <h3><?= htmlspecialchars($cat['name']); ?></h3>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="view-all-btn-container">
        <a href="category.php" class="view-all-btn">View All Categories</a>
    </div>
</section>

<!-- ================= FOOTER ================= -->
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