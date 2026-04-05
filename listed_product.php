<?php
include 'auth.php';
include 'db.php';

// Fetch current user's listed products
$stmt = $conn->prepare("
    SELECT id, name, price, image 
    FROM products 
    WHERE user_id = ?
    ORDER BY id DESC
");

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$listedProducts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Listed Products - EWU Add & Drop</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
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
    transition: background 0.2s ease;
}
.card a:hover {
    background: #163058;
}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<section style="padding: 40px 20px; max-width: 1000px; margin: auto;">
    <h1>Your Listed Products</h1>

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
</section>

<?php include 'footer.php'; ?>

</body>
</html>