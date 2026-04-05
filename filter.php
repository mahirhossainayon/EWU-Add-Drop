<?php
include 'auth.php';
include 'db.php';

if (!isset($_GET['id'])) {
    echo "Category not specified!";
    exit;
}

$category_id = intval($_GET['id']);
if($category_id <= 0){
    echo "Invalid category!";
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch category
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");


$stmt->bind_param("i", $category_id);
$stmt->execute();
$cat_result = $stmt->get_result();
if($cat_result->num_rows == 0){
    echo "Category not found!";
    exit;
}
$category = $cat_result->fetch_assoc();
$stmt->close();

// Fetch products in this category
$stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY id DESC");

$stmt->bind_param("i", $category_id);
$stmt->execute();
$prod_result = $stmt->get_result();
$products = $prod_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Products in <?= htmlspecialchars($category['name']); ?> | EWU Add & Drop</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style>
/* Global */
body { font-family: Arial,sans-serif; margin:0; padding:0; background:#f4f4f4; }

/* Navbar */
.navbar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 20px;
    background:#1c3a70;
    color:#fff;
    position:relative;
}
.navbar .logo { font-size:24px; font-weight:bold; }
.navbar .nav-links {
    display:flex;
    gap:15px;
}
.navbar .nav-links a { color:#fff; text-decoration:none; font-weight:500; }
.navbar .nav-links a:hover { text-decoration:underline; }

/* Hamburger */
.navbar .hamburger {
    display:none;
    font-size:24px;
    cursor:pointer;
}

/* Hero */
.categories-hero {
    text-align:center;
    padding:40px 20px;
    background:#1c3a70;
    color:#fff;
}
.categories-hero h1 { margin-bottom:10px; font-size:2rem; }
.categories-hero p { font-size:1.1rem; }

/* Products grid */
.products {
    max-width:1200px;
    margin:auto;
    padding:20px;
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap:20px;
}
/* Remove underline for product card links */
.category-card a {
    text-decoration: none; /* no underline */
    color: inherit; /* keep the text color as it is */
    
}

/* Optional: keep hover effect clean */
.category-card a:hover {
    text-decoration: none;
    color: inherit; /* or add a small color change if you like */
}
.category-card {
    background:#fff;
    border-radius:12px;
    padding:10px;
    text-align:center;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}
.category-card:hover {
    transform:translateY(-5px);
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}
.category-card img {
    width:100%;
    border-radius:8px;
    aspect-ratio:1/1;
    object-fit:cover;
    margin-bottom:10px;
}
.category-card h3 { font-size: 14px; margin:5px 0;     font-weight: 600;
    color: #192F59; }
.price { font-weight:bold; color:#1c3a70; font-size:1rem; }

/* Responsive */

/* Tablets */
@media screen and (max-width: 900px) {
    .products { grid-template-columns: repeat(auto-fit, minmax(150px,1fr)); }
    .categories-hero h1 { font-size:1.8rem; }
}

/* Mobile */
@media screen and (max-width: 600px) {
    .navbar { flex-direction:column; align-items:flex-start; padding:10px 15px; }
    .navbar .nav-links { display:none; flex-direction:column; width:100%; margin-top:10px; }
    .navbar .nav-links a { margin:10px 0; }
    .navbar .hamburger { display:block; }

    .products { grid-template-columns: 1fr; padding:10px; }
    .categories-hero { padding:30px 10px; }
    .categories-hero h1 { font-size:1.5rem; }
    .categories-hero p { font-size:1rem; }
}

/* Show nav links when hamburger clicked (JS required) */
.navbar .nav-links.active { display:flex; }
</style>
</head>
<body>

<header class="navbar">
    <div class="logo">EWU Add & Drop</div>
    <div class="hamburger"><i class="fa-solid fa-bars"></i></div>
    <div class="nav-links">
        <a href="index.php"><i class="fa-solid fa-home"></i> Home</a>
        <a href="user.php"><i class="fa-solid fa-user"></i> Dashboard</a>
        <a href="products.php"><i class="fa-solid fa-list"></i> All Products</a>
    </div>
</header>

<section class="categories-hero">
    <h1>Category: <?= htmlspecialchars($category['name']); ?></h1>
    <p>All products under <?= htmlspecialchars($category['name']); ?> category</p>
</section>

<section class="products">
    <?php if(!empty($products)): ?>
        <?php foreach($products as $product):
            $image = !empty($product['image']) ? 'uploads/' . $product['image'] : 'uploads/default.png';
        ?>
            <div class="category-card">
                <a href="detail.php?id=<?= $product['id']; ?>">
                    <img src="<?= $image; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                    <h3><?= htmlspecialchars($product['name']); ?></h3>
                    <p class="price">৳ <?= number_format($product['price'],2); ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; color:red; font-weight:bold;">No products found in this category.</p>
    <?php endif; ?>
</section>

<script>
// Hamburger toggle
document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
    });
});
</script>

</body>
</html>