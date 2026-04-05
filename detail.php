<?php 
include 'auth.php';
include 'db.php';

$id = intval($_GET['id'] ?? 0);
if ($id === 0) die("No product selected.");

// Fetch product with seller name
$stmt = $conn->prepare("SELECT p.*, u.name AS seller_name FROM products p JOIN users u 
ON p.user_id = u.id WHERE p.id = ?");

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) die("Product not found.");

$product = $result->fetch_assoc();
$stmt->close();

// Check if saved
$saved = false;
if ($_SESSION['role'] !== 'admin') {
    $stmt = $conn->prepare("SELECT id FROM saved_products WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $saved = $res->num_rows > 0;
    $stmt->close();
}

$image_path = !empty($product['image']) ? 'uploads/' . htmlspecialchars($product['image']) : 'uploads/default.png';

$saved_msg = isset($_GET['saved']) && $_GET['saved'] == 1 ? "Product saved successfully!" : "";
$unsaved_msg = isset($_GET['unsaved']) && $_GET['unsaved'] == 1 ? "Product removed from saved list!" : "";

// ================= SAME CATEGORY PRODUCTS =================
$cat_id = !empty($product['category_id']) ? intval($product['category_id']) : 0;
$same_category_products = [];

if($cat_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? ORDER BY id DESC LIMIT 4");
    $stmt->bind_param("ii", $cat_id, $product['id']);
    $stmt->execute();
    $same_cat_result = $stmt->get_result();
    while($row = $same_cat_result->fetch_assoc()) $same_category_products[] = $row;
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($product['name']); ?> - EWU Add & Drop</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>

/* ================= GLOBAL ================= */
body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
a { text-decoration: none; color: inherit; } /* Remove underline globally */

/* ================= NAVBAR ================= */
.navbar { display:flex; justify-content:space-between; align-items:center; padding:15px 30px; background:#1c3a70; color:#fff; flex-wrap:wrap; }
.navbar .logo { font-size:24px; font-weight:bold; }
.navbar a { color:#fff; margin-left:15px; font-weight:500; }
.navbar a:hover { color:#e5a845; }

/* ================= BREADCRUMB ================= */
.breadcrumb { max-width:1200px; margin:20px auto; font-size:14px; }
.breadcrumb a:hover { text-decoration:underline; }
.breadcrumb span { color:#555; }

/* ================= PRODUCT DETAIL ================= */
.product-detail-container { display:flex; flex-wrap:wrap; gap:40px; max-width:1200px; margin:20px auto; }
.product-image img { max-width:500px; width:100%; border-radius:12px; }
.product-info { flex:1; min-width:250px; }
.product-info h1 { color:#1c3a70; margin-bottom:10px; }
.product-info .price { font-weight:bold; font-size:20px; color:#ff6600; margin-bottom:20px; }
.explore-btn { display:inline-block; margin:5px 5px 0 0; padding:8px 12px; background:#1c3a70; color:#fff; border-radius:8px; font-size:14px; transition:0.3s; }
.explore-btn:hover { background:#fff; color:#1c3a70; }
.button-danger { background:#ff4d4d; }
.button-danger:hover { background:#cc0000; color:#fff; }
.success-message { background:#d4edda; color:#155724; padding:10px 15px; margin:20px auto; border-radius:8px; max-width:1200px; text-align:center; }
.buttons-container { margin-top:20px; }
.seller-info strong { display:inline-block; min-width:80px; }

/* ================= SAME CATEGORY PRODUCTS ================= */
.products.same-category-products { max-width:1200px; margin:40px auto; padding:0 15px; }
.products.same-category-products h2 { color:#1c3a70; margin-bottom:20px; }

/* Small product cards */
.same-category-grid { display:flex; gap:15px; flex-wrap:wrap; justify-content:flex-start; }
.same-category-card { border:1px solid #ddd; border-radius:8px; padding:8px; text-align:center; width:calc(25% - 12px); box-sizing:border-box; background:#fff; transition: transform 0.2s, box-shadow 0.2s; display:flex; flex-direction:column; align-items:center; }
.same-category-card:hover { transform:translateY(-3px); box-shadow:0 3px 10px rgba(0,0,0,0.1); }
.same-category-card img { width:100%; aspect-ratio:3/4; object-fit:cover; border-radius:6px; margin-bottom:5px; }
.same-category-card h3 { font-size:14px; margin:8px 0 5px; color:#1c3a70; }
.same-category-card .price { font-size:13px; color:#ff6600; font-weight:bold; }

/* View all button */
.view-all-btn-container { margin-top:15px; text-align:right; }
.view-all-btn { padding:8px 15px; background:#1c3a70; color:#fff; border-radius:8px; font-size:14px; transition:0.3s; }
.view-all-btn:hover { background:#fff; color:#1c3a70; }

/* ================= RESPONSIVE ================= */
@media (max-width:1024px) {
    .product-detail-container { gap:30px; padding:0 15px; }
    .same-category-card { width:calc(33.333% - 10px); }
}

@media (max-width:768px) {
    .product-detail-container { flex-direction:column; align-items:center; }
    .same-category-card { width:calc(50% - 10px); }
}

@media (max-width:480px) {
    .same-category-card { width:100%; }
    .explore-btn, .view-all-btn { width:100%; text-align:center; }
    .buttons-container { display:flex; flex-direction:column; gap:10px; }
}

</style>
</head>
<body>

<!-- ================= NAVBAR ================= -->
<header class="navbar">
    <div class="logo">EWU Add & Drop</div>
    <div>
        <a href="index.php"><i class="fa-solid fa-home"></i> Home</a>
        <a href="user.php"><i class="fa-solid fa-user"></i> Dashboard</a>
        <a href="javascript:history.back()"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>
</header>

<!-- ================= BREADCRUMB ================= -->
<section class="breadcrumb">
    <a href="index.php">Home</a> / 
    <a href="products.php">Latest Products</a> / 
    <span><?= htmlspecialchars($product['name']); ?></span>
</section>

<!-- ================= SUCCESS MESSAGES ================= -->
<?php if($saved_msg): ?><div class="success-message" id="savedMessage"><?= htmlspecialchars($saved_msg); ?></div><?php endif; ?>
<?php if($unsaved_msg): ?><div class="success-message" id="unsavedMessage"><?= htmlspecialchars($unsaved_msg); ?></div><?php endif; ?>

<!-- ================= PRODUCT DETAIL ================= -->
<section class="product-detail">
<div class="product-detail-container">

    <!-- Product Image -->
    <div class="product-image">
        <img src="<?= $image_path; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
    </div>

    <!-- Product Info -->
    <div class="product-info">
        <h1><?= htmlspecialchars($product['name']); ?></h1>
        <p class="price">৳ <?= number_format($product['price'], 2); ?></p>

        <h3>Seller Info</h3>
        <p class="seller-info">
            Name: <strong><?= htmlspecialchars($product['seller_name']); ?></strong><br>
            Phone: <strong><?= htmlspecialchars($product['phone']); ?></strong><br>
            WhatsApp: <strong><?= !empty($product['whatsapp']) ? htmlspecialchars($product['whatsapp']) : 'N/A'; ?></strong><br>
            Facebook: <strong><?= !empty($product['facebook']) ? htmlspecialchars($product['facebook']) : 'N/A'; ?></strong><br>
            Instagram: <strong><?= !empty($product['instagram']) ? htmlspecialchars($product['instagram']) : 'N/A'; ?></strong>
        </p>

        <!-- Action Buttons -->
        <div class="buttons-container">
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" class="explore-btn"><i class="fa-solid fa-arrow-left"></i> Back to Admin Panel</a>
                <a href="delete_product.php?id=<?= $product['id']; ?>" class="explore-btn button-danger" onclick="return confirm('Are you sure?')">
                    <i class="fa-solid fa-trash"></i> Delete Product
                </a>
            <?php else: ?>
                <?php if($product['user_id'] == $_SESSION['user_id']): ?>
                    <form method="POST" action="delete_listed.php" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                        <button type="submit" class="explore-btn button-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                            <i class="fa-solid fa-trash"></i> Delete Product
                        </button>
                    </form>
                <?php endif; ?>

                <?php if($saved): ?>
                    <form method="POST" action="unsaved.php" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                        <button type="submit" class="explore-btn button-danger">
                            <i class="fa-solid fa-bookmark"></i> Unsave Product
                        </button>
                    </form>
                <?php else: ?>
                    <a href="save_product.php?id=<?= $product['id']; ?>" class="explore-btn">
                        <i class="fa-solid fa-bookmark"></i> Save Product
                    </a>
                <?php endif; ?>

                <?php if($product['user_id'] != $_SESSION['user_id']): ?>
    <a href="report_product.php?id=<?= $product['id']; ?>" class="explore-btn">
        <i class="fa-solid fa-flag"></i> Report Product
    </a>
    <a href="report_user.php?id=<?= $product['id']; ?>" class="explore-btn">
        <i class="fa-solid fa-user-slash"></i> Report Seller
    </a>
<?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

</div>
</section>

<script>
  // Hide save/unsave messages after 2 seconds
  setTimeout(() => {
    const savedMsg = document.getElementById('savedMessage');
    if(savedMsg) savedMsg.style.display = 'none';
    const unsavedMsg = document.getElementById('unsavedMessage');
    if(unsavedMsg) unsavedMsg.style.display = 'none';
  }, 2000);
</script>

<!-- ================= SAME CATEGORY PRODUCTS ================= -->
<?php if(!empty($same_category_products) && $cat_id > 0): ?>
<section class="products same-category-products">
    <h2>More in this Category</h2>
    <div class="same-category-grid">
        <?php foreach($same_category_products as $row):
            $image_path = !empty($row['image']) ? 'uploads/' . $row['image'] : 'uploads/default.png';
        ?>
        <a href="detail.php?id=<?= $row['id']; ?>" class="same-category-card">
            <img src="<?= $image_path; ?>" alt="<?= htmlspecialchars($row['name']); ?>">
            <h3><?= htmlspecialchars($row['name']); ?></h3>
            <p class="price">৳ <?= number_format($row['price'], 2); ?></p>
        </a>
        <?php endforeach; ?>
    </div>
    <div class="view-all-btn-container">
        <a href="filter.php?id=<?= $cat_id; ?>" class="view-all-btn">View All</a>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>
</body>
</html>