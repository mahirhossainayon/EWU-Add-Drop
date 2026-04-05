<?php
include 'auth.php';
include 'db.php';

// Determine which user to show
// Admin can view any user via GET parameter ?id=USER_ID
if(isset($_GET['id']) && $_SESSION['role'] === 'admin'){
    $user_id = intval($_GET['id']);
} else {
    // Normal user sees own dashboard
    $user_id = $_SESSION['user_id'];
}

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// If user not found
if(!$user){
    die("User not found.");
}

// Fetch saved products
$stmt = $conn->prepare("
    SELECT p.id, p.name, p.price, p.image 
    FROM saved_products sp 
    JOIN products p ON sp.product_id = p.id 
    WHERE sp.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$savedResult = $stmt->get_result();
$savedProducts = $savedResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch listed products
$stmt = $conn->prepare("
    SELECT id, name, price, image
    FROM products
    WHERE user_id = ?
    ORDER BY id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$listResult = $stmt->get_result();
$listedProducts = $listResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard - EWU Add & Drop</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body {
    background: #f4f4f4;
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

/* NAVBAR */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    background-color: #1c3a70;
    color: #fff;
}
.navbar .logo { font-size: 24px; font-weight: bold; }
.nav-links a, .user-name {
    color: #fff;
    margin-left: 15px;
    text-decoration: none;
    font-weight: 500;
}
.nav-links a:hover { text-decoration: underline; }

/* DASHBOARD LAYOUT */
.dashboard-container {
    display: flex;
    gap: 20px;
    padding: 40px 20px;
    max-width: 1200px;
    margin: auto;
    align-items: flex-start;
}

/* LEFT PANEL: Profile (Sticky) */
.left-panel {
    flex: 1 1 250px;
    min-width: 250px;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 20px;
    background: #fff;
    position: sticky;
    top: 100px;
    height: fit-content;
}

/* RIGHT PANEL: Products */
.right-panel {
    flex: 3 1 650px;
    display: flex;
    flex-direction: column;
    gap: 40px;
}

/* PANEL HEADINGS */
.panel h2 {
    margin-bottom: 15px;
    color: #1c3a70;
}

/* PRODUCT GRID */
.products-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
.card {
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 10px;
    width: 200px;
    text-align: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    background: #fff;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.card img {
    width: 100%;
    border-radius: 8px;
}
.card h3 {
    font-size: 16px;
    margin: 10px 0;
}
.card p {
    font-weight: bold;
    color: #1c3a70;
}
.card a {
    display: inline-block;
    margin-top: 5px;
    padding: 5px 10px;
    border-radius: 8px;
    background: #1c3a70;
    color: #fff;
    text-decoration: none;
    font-size: 14px;
}
.card a:hover { background: #163058; }

/* PROFILE INFO */
.user-info p { margin: 8px 0; }
.explore-btn {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 12px;
    background: #1c3a70;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
}
.explore-btn:hover { background: #ffffff; }

/* RESPONSIVE */
@media (max-width: 900px) {
    .dashboard-container { flex-direction: column; }
    .left-panel { position: relative; top: 0; }
}
</style>
</head>
<body>

<!-- NAVBAR -->
<header>
    <div class="navbar">
        <div class="logo">EWU Add & Drop</div>
        <div class="nav-links">
            <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
            <a href="add.php"><i class="fa-solid fa-plus"></i> Sell It</a>
            <span class="user-name"><i class="fa-solid fa-user"></i> Hi, <?= htmlspecialchars($user['name']); ?></span>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</header>

<!-- DASHBOARD -->
<div class="dashboard-container">

   <!-- LEFT PANEL: Profile -->
<div class="left-panel user-info">
    <h2>Your Profile</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($user['name']); ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']); ?></p>

    <?php if($_SESSION['user_id'] === $user_id): ?>
        <a href="edit_user.php" class="explore-btn"><i class="fa-solid fa-pen"></i> Edit Profile</a>
    <?php endif; ?>
</div>

    <!-- RIGHT PANEL: Products -->
    <div class="right-panel">

        <!-- SAVED PRODUCTS -->
        <div class="panel">
            <h2>Saved Products</h2>
            <?php if(count($savedProducts) === 0): ?>
                <p>You have not saved any products yet.</p>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach($savedProducts as $p): ?>
                        <div class="card">
                            <img src="<?= !empty($p['image']) ? 'uploads/' . htmlspecialchars($p['image']) : 'uploads/default.png'; ?>" alt="<?= htmlspecialchars($p['name']); ?>">
                            <h3><?= htmlspecialchars($p['name']); ?></h3>
                            <p>৳ <?= number_format($p['price'],2); ?></p>
                            <a href="detail.php?id=<?= $p['id']; ?>"><i class="fa-solid fa-eye"></i> View</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- LISTED PRODUCTS -->
        <div class="panel">
            <h2>Listed Products</h2>
            <?php if(count($listedProducts) === 0): ?>
                <p>You have not listed any products yet. <a href="add.php">Add a product</a></p>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach($listedProducts as $p): ?>
                        <div class="card">
                            <img src="<?= !empty($p['image']) ? 'uploads/' . htmlspecialchars($p['image']) : 'uploads/default.png'; ?>" alt="<?= htmlspecialchars($p['name']); ?>">
                            <h3><?= htmlspecialchars($p['name']); ?></h3>
                            <p>৳ <?= number_format($p['price'],2); ?></p>
                            <a href="detail.php?id=<?= $p['id']; ?>"><i class="fa-solid fa-eye"></i> View</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<?php include 'footer.php'; ?>
</body>
</html>