<?php
session_start();
include 'db.php';

// 🚫 Block guests
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<?php
session_start();
include 'db.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$products = [];

if($query !== '') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? ORDER BY id DESC");
    $searchTerm = "%".$query."%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) $products[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Search Results - EWU Add & Drop</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style>
/* ================= GLOBAL STYLES ================= */
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background: #f4f6fa;
    color: #333;
}

a {
    text-decoration: none;
    color: inherit;
}

header {
    background: #1c3a70; /* branding color */
    color: #fff;
    padding: 25px 20px;
    text-align: center;
    font-size: 28px;
    font-weight: 600;
    letter-spacing: 1px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

h1 {
    text-align: center;
    margin: 30px 0 10px;
    color: #1c3a70;
}

p.search-info {
    text-align: center;
    color: #555;
    margin-bottom: 40px;
    font-size: 16px;
}

/* ================= PRODUCT GRID ================= */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 25px;
    padding: 0 20px 50px;
    max-width: 1200px;
    margin: 0 auto;
}

.card {
    background: linear-gradient(145deg, #ffffff, #e6f0ff);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.15);
}

.card img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s;
}

.card:hover img {
    transform: scale(1.05);
}

.card h3 {
    margin: 15px;
    font-size: 18px;
    color: #1c3a70; /* branding color for titles */
}

.card .price {
    margin: 0 15px 15px;
    font-weight: 600;
    color: #ff6600; /* your orange theme for prices */
}

/* ================= NO RESULTS ================= */
.no-results {
    text-align: center;
    margin: 50px 0;
    font-size: 18px;
    color: #999;
}

/* ================= BACK BUTTON ================= */
.back-btn {
    display: block;
    width: max-content;
    margin: 0 auto 50px;
    padding: 12px 25px;
    background: linear-gradient(135deg, #ff6600, #ff9900);
    color: #fff;
    border-radius: 10px;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: linear-gradient(135deg, #ff8800, #ffb733);
    transform: translateY(-3px);
}

/* ================= RESPONSIVE ================= */
@media (max-width: 600px) {
    header {
        font-size: 22px;
        padding: 20px 10px;
    }
    .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 15px;
        padding: 0 10px 40px;
    }
    .card img {
        height: 180px;
    }
}
</style>
</head>
<body>

<header>
    EWU Add & Drop - Search
</header>

<h1>Search Results for "<?= htmlspecialchars($query); ?>"</h1>
<p class="search-info"><?= count($products) ?> product(s) found</p>

<?php if(count($products) > 0): ?>
    <div class="product-grid">
        <?php foreach($products as $product): 
            $image_path = !empty($product['image']) ? 'uploads/' . $product['image'] : 'uploads/default.png';
        ?>
        <a href="detail.php?id=<?= $product['id']; ?>" class="card">
            <img src="<?= $image_path; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
            <h3><?= htmlspecialchars($product['name']); ?></h3>
            <p class="price">৳ <?= number_format($product['price'], 2); ?></p>
        </a>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="no-results">No products found.</p>
<?php endif; ?>

<a href="index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>

</body>
</html>